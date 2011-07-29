<?php

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