<?php
session_start();
$required_group_rights = 6;
$auth_granted = false;

header('Content-type:text/javascript;charset=UTF-8');

include_once 'ajax_authent_checking.php';
include_once 'ajax_toolbox.php';

try {
	$profiles = array();
	if($auth_granted){
		$tmp_profile = new GenyProfile();
		$results = array();
		$profile_id = getParam("profile_id");
		
		if( is_numeric($profile_id) && $profile_id > 0 ){
			$notification = new GenyNotification();
			foreach( $notification->getNotificationsByProfileId( $profile_id ) as $notif ){
				if( $notif->is_unread ){
					$notif->updateBool("notification_is_unread","false");
					$notif->commitUpdates();
				}
			}
			echo json_encode( array( "success" => "All notifications marked as read." ) );
		}
		else
			echo json_encode( array( "error" => "Fatal error on Profile ID." ) );
	}
} catch (Exception $e) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>