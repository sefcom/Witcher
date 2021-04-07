HTTP/1.1 200 OK
Content-Type: text/xml

<?
//include "/htdocs/phplib/trace.php";
include "/htdocs/mydlink/libservice.php";
function read_result()
{
	$result = "fail";
	$return = fread("", "/var/tmp/mydlink_result");
	
	if(isempty($return) == 1)
		return i18n("Result of mydlink registration is not exist or NULL");	
		
	$cnt = cut_count($return, "\r\n");
	$t = $cnt -1;
	
	$tmp = cut($return , $t, "\r\n");
	
	$L1=strlen($tmp);
	$L2=strstr($tmp, "\n") + strlen("\n");
	
	$result_str = substr($tmp, $L2, $L1-$L2);
	
	$temp = strstr($result_str, "success");
	
	if(isempty($temp) == 1)
		$result = $result_str;	
	else
		$result = "success";
	
	//echo "\n read_result=". $result." \n";
	return $result;
}

function read_cookie()
{
	$return = fread("", "/var/tmp/mydlink_result");
	
	if(isempty($return) == 1)
		return "NULL";	
	$L1=strlen($return);
	$L2=strstr($return, "Set-Cookie: mydlink=") + strlen("Set-Cookie: ");
	
	$temp_str = substr($return, $L2, $L1-$L2);
	
	$L1=strchr($temp_str, ";");
	
	$cookie = substr($temp_str, 0, $L1);
	
	//echo $temp;
	if(isempty($cookie) == 1)
		$result = "";	
	else
		$result = $cookie;
	
	//echo "read_cookie=". $result." \n";
	return $result;
}

function get_value_from_mydlink($value_name)
{
	$name_size = strlen($value_name)+1;  //and =
	$buf = fread("", "/tmp/provision.conf");
	if(isempty($buf) == 1)  //conf not exist
		return "NULL";	
	$buf_len = strlen($buf);
	$target_ptr = strstr($buf, $value_name);
	$substr = substr($buf, $target_ptr, $buf_len - $target_ptr);
	
	$end_ptr =strchr($substr, "\n");
	
	$substr = substr($substr, $name_size, $end_ptr - $name_size);
	
	//echo $substr;
	return $substr;
}

function do_post($post_str, $post_url, $withcookie)
{
	$head_file = "/var/tmp/mydlink_head";
	$body_file = "/var/tmp/mydlink_body";
	
	$f_url = get_value_from_mydlink("portal_url");
	
	if($f_url == "NULL")
		return i18n("Unable to connect Mydlink.");
		
	$http_ptr = strstr($f_url, "http://");
	$https_ptr = strstr($f_url, "https://");
		
	if(isempty($http_ptr) == 0)
		$head = "http://";
	else if(isempty($https_ptr) == 0)
		$head = "https://";
	else
		$head = "";
	
	//force using https to void security issue
	
	$str_len = $http_ptr + strlen($head);
	$url = substr($f_url, $str_len, strlen($f_url) - $str_len);
	$slash_pt = strchr($url, "/");
	if(isempty($slash_pt) == 0)
		$url = substr($url, 0, $slash_pt);	
	$head="https://";
	$f_url = $head.$url;
	$_GLOBALS["URL_MYDLINK"] = $f_url;
		
	//echo "url = ".$url."\n";
	//echo "f_url = ".$f_url."\n";
	
	$len = strlen($post_str)-1;
	
	$req_head = "POST ". $post_url. " HTTP/1.1\r\n\Accept: text/html, */*\r\n\Accept-Language: en\r\n".
				"x-requested-with: XMLHttpRequest\r\n\Content-Type: application/x-www-form-urlencoded\r\n".
				"User-Agent: Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)\r\n".
				"Host: ".$url."\r\n\Content-Length: ".$len."\r\n\Connection: Keep-Alive\r\n".
				"Cache-Control: no-cache".$withcookie."\r\n\r\n";
	
	fwrite(w, $head_file, $req_head);
	fwrite(w, $body_file, $post_str);
	
	$url = "urlget post ".$f_url.$post_url. " ". $body_file. " ". $head_file;
	setattr("/runtime/register", "get", $url." > /var/tmp/mydlink_result");
	get("x", "/runtime/register");
	del("/runtime/register");

	unlink($head_file);
	unlink($body_file);
	return 0;
}

function do_set_status()
{
	/*the register is success,keep this email*/
	$status = query("/mydlink/register_st");
	set("/mydlink/register_st", "1");
	set("/mydlink/regemail", $_POST["outemail"]);
	/*status change ,restart related service.*/
	if($status != "1")
	{
		runservice("MYDLINK.LOG restart");
		runservice("STUNNEL restart");
	}
	event("DBSAVE");
}
//init local parameter
$fwver = query("/runtime/device/firmwareversion");
$modelname = query("/runtime/device/modelname");
$devpasswd = query("/device/account/entry/password");
$action = $_POST["act"];
$wizard_version = $modelname. "_". $fwver;
$result = "success";

$mydlink_num = get_value_from_mydlink("username");
$dlinkfootprint = get_value_from_mydlink("footprint");

/*
//$mydlink_num ="20018537";
//$dlinkfootprint = "03D73F6B33D0B2FC2BCDBC857C4248D6";
//$withcookie = "\r\nCookie: lang=en;";// .$_POST["mydlink_cookie"];

//tmp
$action = "signup";
//$wizard_version = "DIR-605L_1.10";
$_POST["lang"] = "english";
$_POST["outemail"] = "jef%40yzu.edu.tw";
$_POST["passwd"] = "jefjef";
$_POST["firstname"] = "jef";
$_POST["lastname"] = "jef";
//$_POST["mydlink_cookie"] = "";
//$_POST["mydlink_num"] ="123";
//$devpasswd="jef";
//$_POST["dlinkfootprint"]="123";
*/

//sign up
$post_str_signup = "client=wizard&wizard_version=" .$wizard_version. "&lang=" .$_POST["lang"].
                   "&action=sign-up&accept=accept&email=" .$_POST["outemail"]. "&password=" .$_POST["passwd"].
                   "&password_verify=" .$_POST["passwd"]. "&name_first=" .$_POST["firstname"]. "&name_last=" .$_POST["lastname"]." ";

$post_url_signup = "/signin/";

$action_signup = "signup";

//sign in
$post_str_signin = "client=wizard&wizard_version=" .$wizard_version. "&lang=" .$_POST["lang"].
            "&email=" .$_POST["outemail"]. "&password=" .$_POST["passwd"]." ";

$post_url_signin = "/account/?signin";

$action_signin = "signin";

//add dev (bind device)
$post_str_adddev = "client=wizard&wizard_version=" .$wizard_version. "&lang=" .$_POST["lang"].
            "&dlife_no=" .$mydlink_num. "&device_password=" .$devpasswd. "&dfp=" .$dlinkfootprint." ";

$post_url_adddev = "/account/?add";

$action_adddev = "adddev";
          
//main start
if($action == $action_signup)
{
	$post_str = $post_str_signup;
	$post_url = $post_url_signup;
	$withcookie = "";   //signup dont need cookie info
}
else if($action == $action_signin)
{
	$post_str = $post_str_signin;
	$post_url = $post_url_signin;
	$withcookie = "\r\nCookie: lang=en; mydlink=pr2c11jl60i21v9t5go2fvcve2;";
}
else if($action == $action_adddev)
{
	$post_str = $post_str_adddev;
	$post_url = $post_url_adddev;
}
else
	$result = "fail";

if($mydlink_num == "NULL" || $dlinkfootprint == "NULL")
	$result = i18n("Unable to connect Mydlink.");

if($result == "success")
{
	$ret = do_post($post_str, $post_url, $withcookie);
	if($ret == 0)
		$result = read_result();
	else
		$result = $ret;
	
	if($action == $action_signin && $result == "success")
	{
		$cookie = read_cookie();
		if($cookie == "NULL")
			$withcookie = "";
		else
			$withcookie = "\r\nCookie: lang=en;".$cookie;
		
		if($mydlink_num == "NULL" || $dlinkfootprint == "NULL")
			$result = i18n("dlink number or foorprint not exist.");
		else
		{
		//echo "num= ".$mydlink_num." foot= ". $dlinkfootprint;
			$ret = do_post($post_str_adddev, $post_url_adddev, $withcookie);
			if($ret == 0)
			{
				$result = read_result();

				$add_success_ret = $mydlink_num.":";  //compare dlink_num only
				$ret = strstr($result, $add_success_ret);
				if(isempty($ret) == 0)
				{
					$result = "success";
					do_set_status();
				}
			}
			else
				$result = $ret;
		}
	}
}
$url_mydlink = $_GLOBALS["URL_MYDLINK"];

//unlink("/var/tmp/mydlink_result");
echo '<?xml version="1.0"?>\n';
?><register_send>
	<result><?=$result?></result>
	<url><?=$url_mydlink?></url>
</register_send>


