<?php

/**
 * UserDatabase.php
 * 
 * The Database class is meant to simplify the task of accessing
 * User information from the website's database.
 *
 * Written by: Jpmaster77 a.k.a. The Grandmaster of C++ (GMC)
 * Last Updated: August 17, 2004
 */
include_once("/home/rconfig/config/config.inc.php");

class MySQLDB {

    var $connection;         //The MySQL database connection
    var $num_active_users;   //Number of active users viewing site
    var $num_members;        //Number of signed-up users

    /* Note: call getNumMembers() to access $num_members! */

    /* Class constructor */

    public function __construct() {
        /* Make connection to database */
        require_once("/home/rconfig/classes/db2.class.php");
        $this->connection = new db2();
        /**
         * Only query database to find out number of members
         * when getNumMembers() is called for the first time,
         * until then, default value set.
         */
        $this->num_members = -1;

        if (TRACK_VISITORS) {
            /* Calculate number of users at site */
            $this->calcNumActiveUsers();
        }
    }

    /**
     * confirmUserPass - Checks whether or not the given
     * username is in the database, if so it checks if the
     * given password is the same password in the database
     * for that user. If the user doesn't exist or if the
     * passwords don't match up, it returns an error code
     * (1 or 2). On success it returns 0.
     */
    function confirmUserPass($username, $password) {
        /* Verify that user is in database */
        $this->connection->query("SELECT password FROM " . TBL_USERS . " WHERE username = :username");
        $this->connection->bind(':username', $username);
        $result = $this->connection->resultset();
        $num_rows = $this->connection->rowCount();
        if (!$result || $num_rows < 1) {
            return 1; //Indicates username failure
        }
        /* Retrieve password from result, strip slashes */
        $dbarray = $result[0];
        /* Validate that password is correct */
        if ($password == $dbarray['password']) {
            return 0; //Success! Username and password confirmed
        } else {
            return 2; //Indicates password failure
        }
    }

    /**
     * confirmUserID - Checks whether or not the given
     * username is in the database, if so it checks if the
     * given userid is the same userid in the database
     * for that user. If the user doesn't exist or if the
     * userids don't match up, it returns an error code
     * (1 or 2). On success it returns 0.
     */
    function confirmUserID($username, $userid) {
        /* Verify that user is in database */
        $this->connection->query("SELECT userid FROM " . TBL_USERS . " WHERE username = :username");
        $this->connection->bind(':username', $username);
        $result = $this->connection->resultset();
        $num_rows = $this->connection->rowCount();
        if (!$result || $num_rows < 1) {
            return 1; //Indicates username failure
        }

        /* Retrieve userid from result, strip slashes */
        $dbarray = $result[0];
        /* Validate that userid is correct */
        if ($userid == $dbarray['userid']) {
            return 0; //Success! Username and userid confirmed
        } else {
            return 2; //Indicates userid invalid
        }
    }

    /**
     * usernameTaken - Returns true if the username has
     * been taken by another user, false otherwise.
     */
    function usernameTaken($username) {
        $this->connection->query("SELECT username FROM " . TBL_USERS . " WHERE username = :username");
        $this->connection->bind(':username', $username);
        $result = $this->connection->resultsetCols();
        $num_rows = count($result);
        return ($num_rows > 0);
    }

    /**
     * passwordConfirm - Returns password
     */
    function passwordConfirm($username, $password) {
        $this->connection->query("SELECT password FROM " . TBL_USERS . " WHERE username = :username");
        $this->connection->bind(':username', $username);
        $result = $this->connection->resultset();
        $password = $result[0];
        $curpass = $password['password'];
        return $curpass;
    }

    /**
     * addNewUser - Inserts the given (username, password, email)
     * info into the database. Appropriate user level is set.
     * Returns true on success, false otherwise.
     */
    function addNewUser($username, $password, $email, $subulevelid) {
        $status = 1;
        $time = time();
        $this->connection->query("INSERT INTO " . TBL_USERS . " (username, password, userlevel, email, timestamp, status) VALUES (:username, :password, :subulevelid, :email, $time, $status)");
        $this->connection->bind(':username', $username);
        $this->connection->bind(':password', $password);
        $this->connection->bind(':email', $email);
        $this->connection->bind(':subulevelid', $subulevelid);
        return $this->connection->execute();
    }

    /**
     * updateUser - created by SS to fully update user details when submitted by Admin
     *
     */
    function updateUser($id, $username, $password, $email, $ulevelid) {
        $this->connection->query("UPDATE " . TBL_USERS . " SET username = :username,   password = :password, email = :email, userlevel = :ulevelid WHERE username = :username");
        $this->connection->bind(':username', $username);
        $this->connection->bind(':password', $password);
        $this->connection->bind(':email', $email);
        $this->connection->bind(':ulevelid', $ulevelid);
        return $this->connection->execute();
    }

    /**
     * updateUserField - Updates a field, specified by the field
     * parameter, in the user's row of the database.
     */
    function updateUserField($username, $field, $value) {
        $this->connection->query("UPDATE " . TBL_USERS . " SET ".$field." = :value WHERE username = :username");
        $this->connection->bind(':username', $username);        
        $this->connection->bind(':value', $value);        
        return $this->connection->execute();
    }

    /**
     * getUserInfo - Returns the result array from a mysql
     * query asking for all information stored regarding
     * the given username. If query fails, NULL is returned.
     */
    function getUserInfo($username) {
        $this->connection->query("SELECT * FROM " . TBL_USERS . " WHERE username = :username");
        $this->connection->bind(':username', $username);
        $result = $this->connection->resultset();
        $num_rows = $this->connection->rowCount();
        /* Error occurred, return given name by default */
        if (!$result || $num_rows < 1) {
            return NULL;
        }
        /* Return result array */
        $dbarray = $result[0];
        return $dbarray;
    }

    /**
     * getNumMembers - Returns the number of signed-up users
     * of the website, banned members not included. The first
     * time the function is called on page load, the database
     * is queried, on subsequent calls, the stored result
     * is returned. This is to improve efficiency, effectively
     * not querying the database when no call is made.
     */
    function getNumMembers() {
        if ($this->num_members < 0) {
            $this->connection->query("SELECT * FROM " . TBL_USERS);
            $num_rows = $this->connection->rowCount();
            $this->num_members = $num_rows;
        }
        return $this->num_members;
    }

    /**
     * calcNumActiveUsers - Finds out how many active users
     * are viewing site and sets class variable accordingly.
     */
    function calcNumActiveUsers() {
        /* Calculate number of users at site */
        $this->connection->query("SELECT * FROM " . TBL_ACTIVE_USERS);
        $num_rows = $this->connection->rowCount();
        $this->num_active_users = $num_rows;
    }

    /**
     * calcNumActiveGuests - Finds out how many active guests
     * are viewing site and sets class variable accordingly.
     */
    function calcNumActiveGuests() {
        /* Calculate number of guests at site */
        $this->connection->query("SELECT * FROM " . TBL_ACTIVE_GUESTS);
        $num_rows = $this->connection->rowCount();
        $this->num_active_guests = $num_rows;
    }

    /**
     * addActiveUser - Updates username's last active timestamp
     * in the database, and also adds him to the table of
     * active users, or updates timestamp if already there.
     */
    function addActiveUser($username, $time) {
        $this->connection->query("UPDATE " . TBL_USERS . " SET timestamp = :time WHERE username = :username");
        $this->connection->bind(':username', $username);
        $this->connection->bind(':time', $time);
        $this->connection->execute();

        if (!TRACK_VISITORS)
            return;
        $this->connection->query("REPLACE INTO " . TBL_ACTIVE_USERS . " VALUES (:username, :time)");
        $this->connection->bind(':username', $username);
        $this->connection->bind(':time', $time);
        $this->connection->execute();
        $this->calcNumActiveUsers();
    }

    /* addActiveGuest - Adds guest to active guests table */

    function addActiveGuest($ip, $time) {
        if (!TRACK_VISITORS)
            return;
        $this->connection->query("REPLACE INTO " . TBL_ACTIVE_GUESTS . " VALUES ('$ip', '$time')");
        $this->connection->execute();
        $this->calcNumActiveGuests();
    }

    /* These functions are self explanatory, no need for comments */

    /* removeActiveUser */

    function removeActiveUser($username) {
        if (!TRACK_VISITORS)
            return;
        $this->connection->query("DELETE FROM " . TBL_ACTIVE_USERS . " WHERE username = :username");
        $this->connection->bind(':username', $username);
        $this->connection->execute();
        $this->calcNumActiveUsers();
    }

    /* removeActiveGuest */

    function removeActiveGuest($ip) {
        if (!TRACK_VISITORS)
            return;
        $this->connection->query("DELETE FROM " . TBL_ACTIVE_GUESTS . " WHERE ip = '$ip'");
        $this->connection->execute();
        $this->calcNumActiveGuests();
    }

    /* removeInactiveUsers */

    function removeInactiveUsers() {
        if (!TRACK_VISITORS)
            return;
        $timeout = time() - USER_TIMEOUT * 60;
        $this->connection->query("DELETE FROM " . TBL_ACTIVE_USERS . " WHERE timestamp < $timeout");
        $this->connection->execute();
        $this->calcNumActiveUsers();
    }

    /* removeInactiveGuests */

    function removeInactiveGuests() {
        if (!TRACK_VISITORS)
            return;
        $timeout = time() - GUEST_TIMEOUT * 60;
        $this->connection->query("DELETE FROM " . TBL_ACTIVE_GUESTS . " WHERE timestamp < $timeout");
        $this->connection->execute();
        $this->calcNumActiveGuests();
    }

    function checkLdapServer() {
        $this->connection->query("SELECT ldapServer FROM settings WHERE id = 1");
        $result = $this->connection->resultset();
//        var_dump($result);die();
        $num_rows = $this->connection->rowCount();
        return (int)$result[0]['ldapServer'];
//        if (!$result || $num_rows < 1) {
//            return 1; //Indicates LDAP server lookup failure
//        }
//        $dbarray = $result[0];
//        if (!empty($dbarray['ldapServer'])) {
//            return 0; //LDAP server is set
//        } else {
//            return 2; //LDAP server has not been set
//        }
    }

    function getLdapServer() {
        $this->connection->query("SELECT ldap_host FROM settings WHERE id = 1");
        $result = $this->connection->resultset();
        $dbarray = $result[0];
        return $dbarray['ldap_host'];
    }
    function getLdapDomain() {
        $this->connection->query("SELECT ldap_usr_dom FROM settings WHERE id = 1");
        $result = $this->connection->resultset();
        $dbarray = $result[0];
        return $dbarray['ldap_usr_dom'];
    }
    function getLdapDn() {
        $this->connection->query("SELECT ldap_dn FROM settings WHERE id = 1");
        $result = $this->connection->resultset();
        $dbarray = $result[0];
        return $dbarray['ldap_dn'];
    }
    function getldap_admin_group() {
        $this->connection->query("SELECT ldap_admin_group FROM settings WHERE id = 1");
        $result = $this->connection->resultset();
        $dbarray = $result[0];
        return $dbarray['ldap_admin_group'];
    }
    function getldap_user_group() {
        $this->connection->query("SELECT ldap_user_group FROM settings WHERE id = 1");
        $result = $this->connection->resultset();
        $dbarray = $result[0];
        return $dbarray['ldap_user_group'];
    }

    function querySelect($qry){
        $this->connection->query($qry);
        return $this->connection->resultset();
    }
}

/* Create database connection */
$database = new MySQLDB;
