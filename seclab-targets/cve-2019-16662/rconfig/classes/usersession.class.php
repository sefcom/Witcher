<?php


/**
 * Session.php
 *
 * The Session class is meant to simplify the task of keeping
 * track of logged in users and also guests.
 *
 * Written by: Jpmaster77 a.k.a. The Grandmaster of C++ (GMC)
 * Last Updated: August 19, 2004
 */
include("userdatabase.class.php");
include("usermailer.class.php");
include("userform.class.php");

class Session {

    var $username;     //Username given on sign-up
    var $userid;       //Random value generated on current login
    var $userlevel;    //The level to which the user pertains
    var $time;         //Time user was last active (page loaded)
    var $logged_in;    //True if user is logged in, false otherwise
    var $userinfo = array();  //The array holding all user info
    var $url;          //The page url current being viewed
    var $referrer;     //Last recorded site page viewed

    /**
     * Note: referrer should really only be considered the actual
     * page referrer in process.php, any other time it may be
     * inaccurate.
     */
    /* Class constructor */

    public function __construct() {
        $this->time = time();
        $this->startSession();
    }

    /**
     * startSession - Performs all the actions necessary to
     * initialize this session object. Tries to determine if the
     * the user has logged in already, and sets the variables
     * accordingly. Also takes advantage of this page load to
     * update the active visitors tables.
     */
    function startSession() {
        global $database;  //The database connection
        session_start();   //Tell PHP to start the session

        /* Determine if user is logged in */
        $this->logged_in = $this->checkLogin();

        /**
         * Set guest value to users not logged in, and update
         * active guests table accordingly.
         */
        if (!$this->logged_in) {
            $this->username = $_SESSION['username'] = GUEST_NAME;
            $this->userlevel = GUEST_LEVEL;
            $database->addActiveGuest($_SERVER['REMOTE_ADDR'], $this->time);
        }
        /* Update users last active timestamp */ else {
            $database->addActiveUser($this->username, $this->time);
        }

        /* Remove inactive visitors from database */
        $database->removeInactiveUsers();
        $database->removeInactiveGuests();

        /* Set referrer page */
        if (isset($_SESSION['url'])) {
            $this->referrer = $_SESSION['url'];
        } else {
            $this->referrer = "/";
        }

        /* Set current url if not an ajax call */
        if (preg_match('/ajax/', $_SERVER['PHP_SELF']) == 0) {
          $this->url = $_SESSION['url'] = $_SERVER['PHP_SELF'];
        }
    }

    /**
     * checkLogin - Checks if the user has already previously
     * logged in, and a session with the user has already been
     * established. Also checks to see if user has been remembered.
     * If so, the database is queried to make sure of the user's
     * authenticity. Returns true if the user has logged in.
     */
    function checkLogin() {
        global $database;  //The database connection
        /* Check if user has been remembered */
        if (isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])) {
            $this->username = $_SESSION['username'] = $_COOKIE['cookname'];
            $this->userid = $_SESSION['userid'] = $_COOKIE['cookid'];
        }

        $ldapServerCheck = $database->checkLdapServer();
//        var_dump($ldapServerCheck);die();
        if ($ldapServerCheck == 1 && isset($_SESSION['username']) && isset($_SESSION['userid']) && $_SESSION['username'] != GUEST_NAME) {
            $this->username = $_SESSION['username'];
            $this->userid = $_SESSION['userid'];
            $this->userlevel = 9;
            return true;
        } else {
            /* Username and userid have been set and not guest */
            if (isset($_SESSION['username']) && isset($_SESSION['userid']) &&
                    $_SESSION['username'] != GUEST_NAME) {
                /* Confirm that username and userid are valid */
                if ($database->confirmUserID($_SESSION['username'], $_SESSION['userid']) != 0) {
                    /* Variables are incorrect, user not logged in */
                    unset($_SESSION['username']);
                    unset($_SESSION['userid']);
                    return false;
                }

                /* User is logged in, set class variables */
                $this->userinfo = $database->getUserInfo($_SESSION['username']);
                $this->username = $this->userinfo['username'];
                $this->userid = $this->userinfo['userid'];
                $this->userlevel = $this->userinfo['userlevel'];
                return true;
            }
            /* User not logged in */ else {
                return false;
            }
        }
    }

    /**
     * login - The user has submitted his username and password
     * through the login form, this function checks the authenticity
     * of that information in the database and creates the session.
     * Effectively logging in the user if all goes well.
     */
    function login($subuser, $subpass, $subremember) {
        global $database, $form;  //The database and form object

        /* Username error checking */
        $field = "user";  //Use field name for username
        if (!$subuser || strlen($subuser = trim($subuser)) == 0) {
            $form->setError($field, "* Username not entered");
        } else {
            /* Check if username is not alphanumeric */
//         if(!preg_match("/^([0-9a-z])*$/", $subuser)){
//            $form->setError($field, "* Username not alphanumeric");
//         }
        }

        /* Password error checking */
        $field = "pass";  //Use field name for password
        if (!$subpass) {
            $form->setError($field, "* Password not entered");
        }

        /* Return if form errors exist */
        if ($form->num_errors > 0) {
            return false;
        }

        /* Checks that username is in database and password is correct */
        $subuser = stripslashes($subuser);
        $ldapServerCheck = $database->checkLdapServer();

        if ($ldapServerCheck == 1) {
            // configure ldap params
            $ldapServer = $database->getLdapServer();
            $ldapDomain = $database->getLdapDomain();
            $ldapDn = $database->getLdapDn();
            $ldap_admin_group = $database->getldap_admin_group();
            $ldap_user_group = $database->getldap_user_group();
            // add two more DC inputs and a tick box for fallback to local auth if all DCs cannot connect
            // check install process for adding LDAP Suppport
            if (!function_exists('ldap_connect')) {
                die ('Missing PHP LDAP support.');
            }
            $ldapConn = ldap_connect($ldapServer) or die('Cannot connect to domain controller:');
            ldap_set_option($ldapConn,LDAP_OPT_PROTOCOL_VERSION,3);
            ldap_set_option($ldapConn,LDAP_OPT_REFERRALS,0);
            $ldapBind = ldap_bind($ldapConn, $subuser.$ldapDomain, $subpass);
            
            if ($ldapBind) {
                
                $filter = "(sAMAccountName=".$subuser.")";
		$attr = array("memberof");
		$result = ldap_search($ldapConn, $ldapDn, $filter, $attr) or exit("Unable to search LDAP server");
		$entries = ldap_get_entries($ldapConn, $result);
		ldap_unbind($ldap);
                
                /* Username and password correct, register session variables */
//			$this->userinfo  = $database->getUserInfo($subuser);
                $this->username = $_SESSION['username'] = $subuser.$ldapDomain;
                $this->userid = $_SESSION['userid'] = $this->generateRandID();
		// check groups for access level
		$access = 0;
		foreach($entries[0]['memberof'] as $grps) {
                    // is manager, break loop
                    if(strpos($grps, $ldap_manager_group)) { $this->userlevel = 9; break; }
                    if(strpos($grps, $ldap_user_group)) { $this->userlevel = 1; }
		}                
                                
            } else {
                $field = "user";
                $form->setError($field, "* Error logging in using LDAP");
            }

            /* Return if form errors exist */
            if ($form->num_errors > 0) {
                return false;
            }
        } else {
            $result = $database->confirmUserPass($subuser, md5($subpass));

            /* Check error codes */
            if ($result == 1) {
                $field = "user";
                $form->setError($field, "* Username not found");
            } else if ($result == 2) {
                $field = "pass";
                $form->setError($field, "* Invalid password");
            }

            /* Return if form errors exist */
            if ($form->num_errors > 0) {
                return false;
            }

            /* Username and password correct, register session variables */
            $this->userinfo = $database->getUserInfo($subuser);
            $this->username = $_SESSION['username'] = $this->userinfo['username'];
            $this->userid = $_SESSION['userid'] = $this->generateRandID();
            $this->userlevel = $this->userinfo['userlevel'];
        }

        /* Insert userid into database and update active users table */
        $database->updateUserField($this->username, "userid", $this->userid);
        $database->addActiveUser($this->username, $this->time);
        $database->removeActiveGuest($_SERVER['REMOTE_ADDR']);

        /**
         * This is the cool part: the user has requested that we remember that
         * he's logged in, so we set two cookies. One to hold his username,
         * and one to hold his random value userid. It expires by the time
         * specified in constants.php. Now, next time he comes to our site, we will
         * log him in automatically, but only if he didn't log out before he left.
         */
        if ($subremember) {
            setcookie("cookname", $this->username, time() + COOKIE_EXPIRE, COOKIE_PATH);
            setcookie("cookid", $this->userid, time() + COOKIE_EXPIRE, COOKIE_PATH);
        }

        /* Login completed successfully */
        return true;
    }

    /**
     * logout - Gets called when the user wants to be logged out of the
     * website. It deletes any cookies that were stored on the users
     * computer as a result of him wanting to be remembered, and also
     * unsets session variables and demotes his user level to guest.
     */
    function logout() {
        global $database;  //The database connection
        /**
         * Delete cookies - the time must be in the past,
         * so just negate what you added when creating the
         * cookie.
         */
        if (isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])) {
            setcookie("cookname", "", time() - COOKIE_EXPIRE, COOKIE_PATH);
            setcookie("cookid", "", time() - COOKIE_EXPIRE, COOKIE_PATH);
        }

        /* Unset PHP session variables */
        unset($_SESSION['username']);
        unset($_SESSION['userid']);

        /* Reflect fact that user has logged out */
        $this->logged_in = false;

        /**
         * Remove from active users table and add to
         * active guests tables.
         */
        $database->removeActiveUser($this->username);
        $database->addActiveGuest($_SERVER['REMOTE_ADDR'], $this->time);

        /* Set user level to guest */
        $this->username = GUEST_NAME;
        $this->userlevel = GUEST_LEVEL;
    }

    /**
     * register - Gets called when the user has just submitted the
     * registration form. Determines if there were any errors with
     * the entry fields, if so, it records the errors and returns
     * 1. If no errors were found, it registers the new user and
     * returns 0. Returns 2 if registration failed.
     */
    function register($subuser, $subpass, $subpassconf, $subemail, $subulevelid) {
        global $database, $form, $mailer;  //The database, form and mailer object

        /* Username error checking */
        $field = "username";  //Use field name for username
        if (!$subuser || strlen($subuser = trim($subuser)) == 0) {
            $form->setError($field, "Username not entered");
        } else {
            /* Spruce up username, check length */
            $subuser = stripslashes($subuser);
            if (strlen($subuser) < 4) {
                $form->setError($field, "Username below 4 characters");
            } else if (strlen($subuser) > 30) {
                $form->setError($field, "Username above 30 characters");
            }
            /* Check if username is not alphanumeric */ else if (!preg_match("/^([0-9a-z])+$/", $subuser)) {
                $form->setError($field, "Username not alphanumeric");
            }
            /* Check if username is reserved */ else if (strcasecmp($subuser, GUEST_NAME) == 0) {
                $form->setError($field, "Username reserved word");
            }
            /* Check if username is already in use */ else if ($database->usernameTaken($subuser)) {
                $form->setError($field, "Username already in use");
            }
        }

        /* Password error checking */
        $field = "password";  //Use field name for password
        $fieldConf = "passconf";  //Use field name for password conf field
        if (!$subpass) {
            $form->setError($field, "Password not entered");
        } else {
            /* Spruce up password and check length */
            $subpass = stripslashes($subpass);
            if (strlen($subpass) < 4) {
                $form->setError($field, "Password too short");
            }
            /* Check if password is not alphanumeric */ else if (!preg_match('/^(?=.*\d)(?=.*[@#\-_$%^&+=§!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=§!\?]{4,30}$/', ($subpass = trim($subpass)))) {
                $form->setError($field, "Password not alphanumeric. Must be: <br />"
                        . "&nbsp;&nbsp;at least one lowercase char <br/>"
                        . "&nbsp;&nbsp;at least one uppercase char <br/>"
                        . "&nbsp;&nbsp;at least one digit <br/>"
                        . "&nbsp;&nbsp;at least one special sign of @#-_$%^&+=§!? <br/> <br/> <br/>");
            }
            /* Check if password fields match */ else if ($subpass != $subpassconf) {
                $form->setError($fieldConf, "Passwords do not match");
            }
            /**
             * Note: I trimmed the password only after I checked the length
             * because if you fill the password field up with spaces
             * it looks like a lot more characters than 4, so it looks
             * kind of stupid to report "password too short".
             */
        }

        /* Email error checking */
        $field = "email";  //Use field name for email
        if (!$subemail || strlen($subemail = trim($subemail)) == 0) {
            $form->setError($field, "Email not entered");
        } else {
            /* Check if valid email address */
            $regex = "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/";
            if (!preg_match($regex, $subemail)) {
                $form->setError($field, "Email invalid");
            }
            $subemail = stripslashes($subemail);
        }

        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            return 1;  //Errors with form
        }
        /* No errors, add the new account to the */ else {
            if ($database->addNewUser($subuser, md5($subpass), $subemail, $subulevelid)) {
                if (EMAIL_WELCOME) {
                    $mailer->sendWelcome($subuser, $subemail, $subpass);
                }
                return 0;  //New user added succesfully
            } else {
                return 2;  //Registration attempt failed
            }
        }
    }

    /**
     * editAccount - Attempts to edit the user's account information
     * including the password, which it first makes sure is correct
     * if entered, if so and the new password is in the right
     * format, the change is made. All other fields are changed
     * automatically.
     */
    function editAccount($id, $subuser, $curpass, $subpass, $subpassconf, $subemail, $subulevelid) {
        global $database, $form;  //The database and form object

        /* Username error checking */
        $field = "username";  //Use field name for username
        if (!$subuser || strlen($subuser = trim($subuser)) == 0) {
            $form->setError($field, "Username not entered");
        } else {
            /* Spruce up username, check length */
            $subuser = stripslashes($subuser);
            if (strlen($subuser) < 5) {
                $form->setError($field, "Username below 5 characters");
            } else if (strlen($subuser) > 30) {
                $form->setError($field, "Username above 30 characters");
            }
//            /* Check if username is not alphanumeric */ else if (!preg_match("/^([0-9a-z])+$/", $subuser)) {
//                $form->setError($field, "Username not alphanumeric");
//            }
            /* Check if username is reserved */ else if (strcasecmp($subuser, GUEST_NAME) == 0) {
                $form->setError($field, "Username reserved word");
            }
        }

        /* Password error checking */
        $fieldCurrent = "curpass";  //Use field name for current password
        $field = "password";  //Use field name for password
        $fieldConf = "passconf";  //Use field name for password conf field
        if (!$curpass) {
            $form->setError($fieldCurrent, "Current password not entered");
        } else {
            if (md5($curpass) != $database->passwordConfirm($subuser, $curpass)) {
                $form->setError($fieldCurrent, "Incorrect password");
            }
        }
        if (!$subpass) {
            $form->setError($field, "Password not entered");
        } else {
            /* Spruce up password and check length */
            $subpass = stripslashes($subpass);
            if (strlen($subpass) < 4) {
                $form->setError($field, "Password too short");
            }
            /* Check if password is not alphanumeric */ else if (!preg_match('/^(?=.*\d)(?=.*[@#\-_$%^&+=§!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=§!\?]{4,30}$/', ($subpass = trim($subpass)))) {
                $form->setError($field, "Passwordsss not alphanumeric<br />"
                        . "at least one lowercase char <br/>"
                        . "at least one uppercase char <br/>"
                        . "at least one digit <br/>"
                        . "at least one special sign of @#-_$%^&+=§!? <br/>");
            }
            /* Check if password fields match */ else if ($subpass != $subpassconf) {
                $form->setError($fieldConf, "Passwords do not match");
            }
            /**
             * Note: I trimmed the password only after I checked the length
             * because if you fill the password field up with spaces
             * it looks like a lot more characters than 4, so it looks
             * kind of stupid to report "password too short".
             */
        }

        /* Email error checking */
        $field = "email";  //Use field name for email
        if ($subemail && strlen($subemail = trim($subemail)) > 0) {
            /* Check if valid email address */
            $regex = "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/";
            if (!preg_match($regex, $subemail)) {
                $form->setError($field, "* Email invalid");
            }
            $subemail = stripslashes($subemail);
        }

        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            return 1;  //Errors with form
        }
        /* No errors, add the new account to the */ else {
            if ($database->updateUser($id, $subuser, md5($subpass), $subemail, $subulevelid)) {
                return 0;  //Edited user  successfully
            } else {
                return 2;  //Registration attempt failed
            }
        }
    }

    /**
     * updateAccount - Attempts to update the user's account information
     * including the password, as inputted/updated from the useradmin.php from
     * as submitted by any rConfig Admin
     */
    function updateAccount($id, $subuser, $subpass, $subpassconf, $subemail, $subulevelid) {

        global $database, $form;  //The database and form object

        /* Username error checking */
        $field = "username";  //Use field name for username
        if (!$subuser || strlen($subuser = trim($subuser)) == 0) {
            $form->setError($field, "Username not entered");
        } else {
            /* Spruce up username, check length */
            $subuser = stripslashes($subuser);
            if (strlen($subuser) < 5) {
                $form->setError($field, "Username below 5 characters");
            } else if (strlen($subuser) > 30) {
                $form->setError($field, "Username above 30 characters");
            }
            /* Check if username is not alphanumeric */ else if (!preg_match("/^([0-9a-z])+$/", $subuser)) {
                $form->setError($field, "Username not alphanumeric");
            }
            /* Check if username is reserved */ else if (strcasecmp($subuser, GUEST_NAME) == 0) {
                $form->setError($field, "Username reserved word");
            }
        }

        /* Password error checking */
        $field = "password";  //Use field name for password
        $fieldConf = "passconf";  //Use field name for password conf field
        if (!$subpass) {
            $form->setError($field, "Password not entered");
        } else {
            /* Spruce up password and check length */
            $subpass = stripslashes($subpass);
            if (strlen($subpass) < 4) {
                $form->setError($field, "Password too short");
            }
            /* Check if password fields match */ else if ($subpass != $subpassconf) {
                $form->setError($fieldConf, "Passwords do not match");
            }
            /**
             * Note: I trimmed the password only after I checked the length
             * because if you fill the password field up with spaces
             * it looks like a lot more characters than 4, so it looks
             * kind of stupid to report "password too short".
             */
        }
        /* Email error checking */
        $field = "email";  //Use field name for email
        if ($subemail && strlen($subemail = trim($subemail)) > 0) {
            /* Check if valid email address */
            $regex = "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/";
            if (!preg_match($regex, $subemail)) {
                $form->setError($field, "* Email invalid");
            }
            $subemail = stripslashes($subemail);
        }

        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            return 1;  //Errors with form
        }
        /* No errors, add the new account to the */ else {
            if ($database->updateUser($id, $subuser, md5($subpass), $subemail, $subulevelid)) {
                return 0;  //Edited user  successfully
            } else {
                return 2;  //Registration attempt failed
            }
        }
    }

//end updateAccount

    /**
     * isAdmin - Returns true if currently logged in user is
     * an administrator, false otherwise.
     */
    function isAdmin() {
        return ($this->userlevel == ADMIN_LEVEL ||
                $this->username == ADMIN_NAME);
    }

    /**
     * generateRandID - Generates a string made up of randomized
     * letters (lower and upper case) and digits and returns
     * the md5 hash of it to be used as a userid.
     */
    function generateRandID() {
        return md5($this->generateRandStr(16));
    }

    /**
     * generateRandStr - Generates a string made up of randomized
     * letters (lower and upper case) and digits, the length
     * is a specified parameter.
     */
    function generateRandStr($length) {
        $randstr = "";
        for ($i = 0; $i < $length; $i++) {
            $randnum = mt_rand(0, 61);
            if ($randnum < 10) {
                $randstr .= chr($randnum + 48);
            } else if ($randnum < 36) {
                $randstr .= chr($randnum + 55);
            } else {
                $randstr .= chr($randnum + 61);
            }
        }
        return $randstr;
    }
}

/**
 * Initialize session object - This must be initialized before
 * the form object because the form uses session variables,
 * which cannot be accessed unless the session has started.
 */
$session = new Session;

/* Initialize form object */
$form = new Form;
