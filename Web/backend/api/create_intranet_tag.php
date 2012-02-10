<?php

//  Copyright (C) 2011 by GENYMOBILE & Quentin Désert
//  qdesert@genymobile.com
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
$required_group_rights = 6;
$auth_granted = false;

header('Content-type:text/javascript;charset=UTF-8');

include_once 'ajax_authent_checking.php';
include_once 'ajax_toolbox.php';
include '../../classes/GenyTools.php';

try {
	$intranet_tags = array(	);
	if( $auth_granted ) {
		$tmp_intranet_tag = new GenyIntranetTag();
		$intranet_tag_name = getParam( "name", "" );
		
		if( $intranet_tag_name == "" ) {
			$access_loger->insertNewAccessLog(
				$profile->id,
				$_SERVER['REMOTE_ADDR'],
				'false',
				"backend/api/create_intranet_tag.php",
				BAD_DATA,
				"referer=".$referer.",user_agent=".$_SERVER['HTTP_USER_AGENT']);
			echo json_encode( array( "error" => "Fatal error: Some required information are not set. Access logged." ) );
			exit;
		}

		if( count( $tmp_intranet_tag->getIntranetTagsByName( $intranet_tag_name ) ) > 0 ) {
			echo json_encode( array( "status" => "error", "status_message" => "Erreur : Le tag [".$intranet_tag_name."] existe déjà." ) );
		}
		else {
			$insert_id = $tmp_intranet_tag->insertNewIntranetTag( 'NULL', $intranet_tag_name );
		
			if( $insert_id > 0 ) {
				$data = json_encode( array("id" => $insert_id, "name" => $intranet_tag_name,"status" => "success", "status_message" => "Tag Intranet [".$intranet_tag_name."] créé avec succès.") );
				echo $data;
			}
			else{
				echo json_encode( array( "status" => "error", "status_message" => "Erreur lors de la création du tag Intranet." ) );
			}
		}
	}
} catch (Exception $e) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>