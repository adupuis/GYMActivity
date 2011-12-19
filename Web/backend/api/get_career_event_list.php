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
		$term = getParam("term");
		$profile_id = getParam("profile_id",-1);
		// Si le profile n'est pas définit et que le requester n'est pas admin ou superuser ou que le profile demandé ne correpsond pas au profile du requester => BANG! *headshot*
		if( $profile->rights_group_id > 2 && ($profile_id <= 0 || $profile->id != $profile_id) ){
			$access_loger->insertNewAccessLog(
				$profile->id,
				$_SERVER['REMOTE_ADDR'],
				'false',
				"backend/api/get_career_event_list.php",
				UNAUTHORIZED_ACCESS,
				"referer=".$referer.",user_agent=".$_SERVER['HTTP_USER_AGENT']);
			echo json_encode( array( "error" => "Fatal error: You are not allowed to retrieve this data. Access logged." ) );
			exit;
		}
		
		if( $term != "" )
			$results = $tmp_ce->searchCareerEvent($term);
		else if($profile_id > 0)
			$results = $tmp_ce->getCareerEventListByProfileId($profile_id);
		else if($profile->rights_group_id <= 2)
			$results = $tmp_ce->getAllCareerEvent();
		
		foreach( $results as $ce ){
			$tmp = array();
			foreach( get_object_vars($tmp_ce) as $field ){
				$tmp[$field] = $ce->$field ;
			}
			$ces[] = $tmp;
		}
		$data = json_encode($ces);
		echo $data;
	}
} catch (Exception $e) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>