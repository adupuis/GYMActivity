<?php

//  Copyright (C) 2011 by GENYMOBILE & Arnaud Dupuis
//  adupuis@genymobile.com
//  http://www.genymobile.com
// 
//  This program is free software; you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation; either version 3 of the License, or
//  (at your option) any later version.
// 
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
// 
//  You should have received a copy of the GNU General Public License
//  along with this program; if not, write to the
//  Free Software Foundation, Inc.,
//  59 Temple Place - Suite 330, Boston, MA  02111-1307, USA

session_start();

include_once 'classes/GenyWebConfig.php';
include_once 'classes/GenyProfile.php';
include_once 'classes/GenyAccessLog.php';
include_once 'classes/GenyPropertyValue.php';
include_once 'classes/GenyRightsGroup.php';
include_once 'classes/GenyTools.php';


$web_config = new GenyWebConfig();
$gal = new GenyAccessLog();

if (isset($_GET['code'])) {
    $google_client = GenyTools::create_google_client();
    $oauth_email = GenyTools::get_google_email($_GET['code']);
}


if(isset($oauth_email) || isset($_POST['geny_username']) && isset($_POST['geny_password']) ){
	trim($_POST['geny_username']);
	trim($_POST['geny_password']);

	$username = md5($_POST["geny_username"]);
	$passwd = md5($_POST["geny_password"]);

	if (!isset($oauth_email)) {
	    if(!preg_match("/^[-a-z0-9 ']{4,12}+$/i",$_POST['geny_username'])){
		echo "Username error";
		$gal->insertSimpleAccessLog(BAD_USERNAME_FORMAT);
		exit();
	    }
	}

	$handle = mysql_connect($web_config->db_host,$web_config->db_user,$web_config->db_password);
	mysql_select_db($web_config->db_name);
	if (isset($oauth_email)){
	    $query = "SELECT profile_id,profile_login FROM Profiles WHERE profile_email='$oauth_email';";
	} else {
	    $query = "SELECT profile_id,profile_login FROM Profiles WHERE md5(profile_login)='$username' AND profile_password='$passwd';";
	}

	$result = mysql_query($query, $handle);

	if (mysql_num_rows($result)!=0) {
		//mark as valid user
		session_regenerate_id();
		$sqldata = mysql_fetch_assoc($result);
		if(isset($oauth_email)) {
		    $username = md5($sqldata['profile_login']);
		}
		$_SESSION['USERID'] = $username;
		$_SESSION['LOGGEDIN'] = true;
		if(file_exists("styles/".$_POST['geny_theme']."/main.css"))
			$_SESSION['THEME'] = $_POST['geny_theme'];
		else
			$_SESSION['THEME'] = 'genymobile-2012';
		$tmp_profile = new GenyProfile( $sqldata['profile_id'] );
		$tmp_group   = new GenyRightsGroup( $tmp_profile->rights_group_id );
		$pv = new GenyPropertyValue();
		$state_pv = $pv->getPropertyValuesByPropertyId(3);
		$s = array_shift($state_pv);
		error_log("[GYMActivity::DEBUG] check_login.php: \$s->content: $s->content",0);
		if(($s->content == 5 || $s->content == 6 || $s->content == 7) && $tmp_group->shortname != 'ADM' ){
			session_destroy();
			header("Location: index.php");
			exit();
		}
		if( $tmp_profile->needs_password_reset )
			header('Location: user_admin_password_change.php');
		else
			header("Location: loader.php?module=home");
		exit;
	}

	//if the code reaches this part then the login failed
	//wrong username/password
	$gal->insertSimpleAccessLog(BAD_CREDENTIALS);
	header("Location: index.php?reason=badcredentials");
}
else
	echo "Error: Form variables undefined.";

?>
