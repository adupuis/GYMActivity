<?php
session_start();
$required_group_rights = 6;
$auth_granted = false;

header('Content-type:text/javascript;charset=UTF-8');

include_once 'ajax_authent_checking.php';
include_once 'ajax_toolbox.php';

try {
	$notif = new GenyNotification();
	if($auth_granted){
		$tmp_profile = new GenyProfile();
		$type= getParam("type","info");
		$to = getParam("to");
		$msg = getParam("message");
		$to_type = getParam("to_type");
		if( $msg != "" ){
			if( $to_type == "group" ){
				$g = new GenyRightsGroup();
				$g->loadRightsGroupByName($to);
				if( $g->id > 0 ){
					$notif->insertNewGroupNotification($p->id,$msg,$type);
					echo json_encode( array( "success" => "Group $to notified." ) );
				}
				else
					echo json_encode( array( "error" => "No such group: $to" ) );
			}
			else if( $to_type == "pm" ){
				$p = new GenyProfile();
				$p->loadProfileByLogin($to);
				if( $p->id > 0 ){
					$notif->insertNewNotification($p->id,$msg,$type);
					echo json_encode( array( "success" => "User $to notified." ) );
				}
				else
					echo json_encode( array( "error" => "No such user: $to" ) );
			}
			else
				echo json_encode( array( "error" => "Unknown message type: $to_type" ) );
		}
		else
			echo json_encode( array( "error" => "Message cannot be null." ) );
	}
} catch (Exception $e) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>