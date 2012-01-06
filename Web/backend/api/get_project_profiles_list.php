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

try {
	$profiles = array();
	if( $auth_granted ) {
		$tmp_profile = new GenyProfile();
		$results = array();
		$project_id = getParam( "project_id", -1 );
		// Si le project n'est pas définit et que le requester n'est pas admin ou superuser => BANG! *headshot*
		if( $profile->rights_group_id > 2 && $project_id <= 0 ) {
			$access_loger->insertSimpleAccessLog( UNAUTHORIZED_ACCESS );
			echo json_encode( array( "error" => "Fatal error: You are not allowed to retrieve this data. Access logged." ) );
			exit;
		}
		
		if( $project_id > 0 ) {
			$geny_profile = new GenyProfile();
			$results = $geny_profile->getAllProfilesByProjectId( $project_id );
		}
		
		foreach( $results as $pr ){
			$tmp = array();
			foreach( get_object_vars( $tmp_pr ) as $field => $value ) {
				$tmp[$field] = $pr->$field ;
			}
			$profiles[] = $tmp;
		}
		$data = json_encode( $profiles );
		echo $data;
	}
} catch (Exception $e) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>