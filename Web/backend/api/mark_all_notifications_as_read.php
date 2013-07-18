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

include_once '../../rights_groups.php';

session_start();
$required_group_rights = array(ADM, TM, USR, TL, REP, EXT, GL);
$auth_granted = false;

header('Content-Type: application/json;charset=UTF-8');

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