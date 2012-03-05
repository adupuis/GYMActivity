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
$required_group_rights = 2;
$auth_granted = false;

header('Content-type:text/javascript;charset=UTF-8');

include_once 'ajax_authent_checking.php';
include_once 'ajax_toolbox.php';
include '../../classes/GenyTools.php';

try {
	$ces = array();
	if($auth_granted){
		$tmp_ce         = new GenyCareerEvent();
		$ce_type        = getParam("type","neutral");
		$ce_title       = getParam("title","");
		$ce_text        = getParam("text","");
		$ce_attachement = getParam("attachement","");
		$ce_profile_id  = getParam("profile_id",-1);
		
		if( $ce_type == "" || $ce_title == "" || $ce_text == ""){
			$access_loger->insertNewAccessLog(
				$profile->id,
				$_SERVER['REMOTE_ADDR'],
				'false',
				"backend/api/create_career_event.php",
				BAD_DATA,
				"referer=".$referer.",user_agent=".$_SERVER['HTTP_USER_AGENT']);
			echo json_encode( array( "error" => "Fatal error: Some required information are not set. Access logged." ) );
			exit;
		}
		$tmp_ce->setDebug(true);
		// Il n'est pas possible de créer un évènement et de l'approuver en même temps. Il faut que le manager puisse avoir un second regard sur ce qu'il vient d'ajouter.
		$new_id = $tmp_ce->insertNewCareerEvent($ce_profile_id,$ce_type,$ce_title,$ce_text,$ce_attachement,"false","false");
		
		if( $new_id > 0 ){
			$data = json_encode( array("status" => "success", "status_message" => "Nouvel évènement inséré avec succès (id=$new_id)") );
			echo $data;
		}
		else{
			echo json_encode( array( "status" => "error", "status_message" => "Erreur à la création de l'évènement." ) );
		}
	}
} catch (Exception $e) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>