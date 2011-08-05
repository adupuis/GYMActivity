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

try {
	$checkId_obj = new CheckIdentity();
	$api_key = "";
	if(isset($_POST['api_key']))
		$api_key = $_POST['api_key'];
	else if( isset($_GET['api_key']))
		$api_key = $_GET['api_key'];
	
	if( $api_key != "" )
		session_start();
	
	if(isset($_SESSION['LOGGEDIN']) &&  $_SESSION['LOGGEDIN'] == 1){
		if( $checkId_obj->isAllowed($_SESSION['USERID'],$required_group_rights) ){
			$auth_granted=true;
		}
		else{
			$auth_granted=false;
			echo json_encode(array('error'=>'User not allowed.'));
		}
	}
	else if( $api_key != "" ){
		$ak_object = new GenyApiKey();
		$ak_object->loadApiKeyByData($api_key);
		if( $ak_object->id > 0 ){
			$tmp_profile = new GenyProfile( $ak_object->profile_id );
			if( $tmp_profile->id > 0 ){
// 				echo "Profile: ID=$tmp_profile->id, LOGIN=$tmp_profile->login, MD5(LOGIN)=".md5($tmp_profile->login).", REQUIRED_GROUPS_RIGHTS=$required_group_rights\n";
				if( $tmp_profile->rights_group_id < $required_group_rights ){
					$auth_granted=true;
				}
				else{
					$auth_granted=false;
					echo json_encode(array('error'=>'User not allowed (from API key authent).'));
				}
			}
			else {
				$auth_granted=false;
				echo json_encode(array('error'=>'Invalid user.'));
			}
		}
		else {
			$auth_granted=false;
			echo json_encode(array('error'=>'API key is invalid.'));
		}
	}
	else {
		$auth_granted=false;
		echo json_encode(array('error'=>'Authentication required.'));
	}
} catch (Exception $e) {
    //echo $e->getMessage(), "\n";
}

if( $api_key != "" )
	session_destroy();

?>