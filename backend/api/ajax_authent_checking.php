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

function __autoload($class_name) {
    include '../../classes/'.$class_name . '.php';
}

spl_autoload_register(function ($class_name) {
    include '../../classes/'.$class_name . '.php';
});

date_default_timezone_set('Europe/Paris');
$access_loger = new GenyAccessLog();
$profile = -1;

try {
	$checkId_obj = new CheckIdentity();
	$api_key = "";
	$referer = array_key_exists('HTTP_REFERER', $_SERVER) ? $_SERVER['HTTP_REFERER'] : "";
	
	if(isset($_POST['api_key']))
		$api_key = $_POST['api_key'];
	else if( isset($_GET['api_key']))
		$api_key = $_GET['api_key'];
		
	if( !isset($authorized_auth_method) )
		$authorized_auth_method="all";
	
	if( $api_key != "" )
		session_start();
	
	if(isset($_SESSION['LOGGEDIN']) &&  $_SESSION['LOGGEDIN'] == 1 && ($authorized_auth_method == "session" || $authorized_auth_method=="all")){
		if( $checkId_obj->isAllowed($_SESSION['USERID'],$required_group_rights) ){
			$auth_granted=true;
			$tmp_profile = new GenyProfile();
			$tmp_profile->loadProfileByUsername($_SESSION['USERID']);
			$profile = $tmp_profile;
		}
		else{
			$auth_granted=false;
			$access_loger->insertNewAccessLog(
				$profile->id,
				$_SERVER['REMOTE_ADDR'],
				'false',
				"backend/api/ajax_authent_checking.php",
				UNAUTHORIZED_ACCESS,
				"referer=".$referer.",user_agent=".$_SERVER['HTTP_USER_AGENT']." error=User not allowed.");
			echo json_encode(array('error'=>'User not allowed.'));
			exit();
		}
	}
	else if( $api_key != "" && ($authorized_auth_method == "api_key" || $authorized_auth_method=="all")){
		$ak_object = new GenyApiKey();
		$ak_object->loadApiKeyByData($api_key);
		if( $ak_object->id > 0 ){
			$tmp_profile = new GenyProfile( $ak_object->profile_id );
			if( $tmp_profile->id > 0 ){
// 				echo "Profile: ID=$tmp_profile->id, LOGIN=$tmp_profile->login, MD5(LOGIN)=".md5($tmp_profile->login).", REQUIRED_GROUPS_RIGHTS=$required_group_rights\n";
				if( $tmp_profile->rights_group_id <= $required_group_rights ){
					$auth_granted=true;
					$profile = $tmp_profile;
				}
				else{
					$auth_granted=false;
					echo json_encode(array('error'=>'User not allowed (from API key authent).'));
					$access_loger->insertNewAccessLog(
						$profile->id,
						$_SERVER['REMOTE_ADDR'],
						'false',
						"backend/api/ajax_authent_checking.php",
						UNAUTHORIZED_ACCESS,
						"referer=".$referer.",user_agent=".$_SERVER['HTTP_USER_AGENT']." error=User not allowed (from API key authent).");
					exit();
				}
			}
			else {
				$auth_granted=false;
				echo json_encode(array('error'=>'Invalid user.'));
				$access_loger->insertNewAccessLog(
					$profile->id,
					$_SERVER['REMOTE_ADDR'],
					'false',
					"backend/api/ajax_authent_checking.php",
					UNAUTHORIZED_ACCESS,
					"referer=".$referer.",user_agent=".$_SERVER['HTTP_USER_AGENT']." error=Invalid user.");
				exit();
			}
		}
		else {
			$auth_granted=false;
			echo json_encode(array('error'=>'API key is invalid.'));
			$access_loger->insertNewAccessLog(
				$profile->id,
				$_SERVER['REMOTE_ADDR'],
				'false',
				"backend/api/ajax_authent_checking.php",
				UNAUTHORIZED_ACCESS,
				"referer=".$referer.",user_agent=".$_SERVER['HTTP_USER_AGENT']." error=API key is invalid.");
			exit();
		}
	}
	else {
		$auth_granted=false;
		echo json_encode(array('error'=>'Authentication required.'));
		$access_loger->insertNewAccessLog(
			$profile->id,
			$_SERVER['REMOTE_ADDR'],
			'false',
			"backend/api/ajax_authent_checking.php",
			UNAUTHORIZED_ACCESS,
			"referer=".$referer.",user_agent=".$_SERVER['HTTP_USER_AGENT']." error=Authentication required.");
		exit();
	}
} catch (Exception $e) {
    //echo $e->getMessage(), "\n";
}

if( $api_key != "" )
	session_destroy();

?>
