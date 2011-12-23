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
$required_group_rights = 6;
$auth_granted = false;

header('Content-type:text/javascript;charset=UTF-8');

include_once 'ajax_authent_checking.php';
include_once 'ajax_toolbox.php';

try {
	$ces = array();
	if($auth_granted){
		$tmp_ce = new GenyCareerEvent();
		$results = array();
		$ce_id = getParam("career_event_id",-1);
		$profile_id = getParam("profile_id",-1);
		$ce_type = getParam("type","");
		$ce_title = getParam("title","");
		$ce_text = getParam("text","");
		$ce_attachement = getParam("attachement","");
		$ce_manager_agreement = getParam("manager_agreement","");
		$ce_employee_agreement = getParam("employee_agreement","");
		// Si le profile n'est pas définit et que le requester n'est pas admin ou superuser ou que le profile demandé ne correpsond pas au profile du requester => BANG! *headshot*
		if( $profile->rights_group_id > 2 && ($profile_id <= 0 || $profile->id != $profile_id) ){
			$access_loger->insertSimpleAccessLog(UNAUTHORIZED_ACCESS);
			echo json_encode( array( "error" => "Fatal error: You are not allowed to update this data. Access logged." ) );
			exit;
		}
		
		if( $ce_id == -1 ){
			echo json_encode( array( "error" => "Fatal error: career_event_id is mandatory, please define one." ) );
			exit;
		}
		else{
			$tmp_ce->loadCareerEventById($ce_id);
			if( $tmp_ce->id > 0 ){
				if( $ce_type != $tmp_ce->type && $ce_type != "" ){
					$tmp_ce->updateString("career_event_type",$ce_type);
				}
				if( $ce_title != $tmp_ce->title && $ce_title != "" ){
					$tmp_ce->updateString("career_event_title",$ce_title);
				}
				if( $ce_text != $tmp_ce->text && $ce_text != "" ){
					$tmp_ce->updateString("career_event_text",$ce_text);
				}
				if( $ce_attachement != $tmp_ce->attachement && $ce_attachement != "" ){
					$tmp_ce->updateString("career_event_attachement",$ce_attachement);
				}
				if( $ce_manager_agreement != $tmp_ce->manager_agreement && $ce_manager_agreement != "" ){
					$tmp_ce->updateBool("career_event_manager_agreement",$ce_manager_agreement);
				}
				if( $ce_employee_agreement != $tmp_ce->employee_agreement && $ce_employee_agreement != "" ){
					$tmp_ce->updateBool("career_event_employee_agreement",$ce_employee_agreement);
				}
				$tmp_ce->commitUpdates();
			}
			else{
				echo json_encode( array( "error" => "Fatal error: career_event couldn't be load, please check the parameters." ) );
				exit;
			}
		}
		$data = json_encode($ces);
		echo $data;
	}
} catch (Exception $e) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>