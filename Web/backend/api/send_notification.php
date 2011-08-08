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

// session_start();
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