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
$required_group_rights = array(ADM, TM, USR, TL, REP, GL);
$auth_granted = false;

header('Content-Type: application/json;charset=UTF-8');

include_once 'ajax_authent_checking.php';
include_once 'ajax_toolbox.php';

// This API call takes 2 parameters :
//   - api_key : your API key as a string
//   - project_id : the id of the project you want to get info from.
//
// It returns, a JSON array of the following shape :
// If successfull :
// {
// 	"status":"success",
// 	"status_message":"Information retrieved successfully.",
// 	"data":{
// 		"id":"84",
// 		"name":"Test",
// 		"description":"test PM",
// 		"status_id":"1",
// 		"location":"Paris",
// 		"type":{
// 			"type_id":"1",
// 			"type_name":"R\u00e9gie",
// 			"type_decription":"Employ\u00e9 \u00e0 disposition du client dans les bureaux du client."
// 		},
// 		"pm1":{
// 			"pm1_id":"14",
// 			"pm1_firstname":"Daniel",
// 			"pm1_lastname":"Fages",
// 			"pm1_login":"dfages",
// 			"pm1_email":"dfages@genymobile.com"
// 		},
// 		"pm2":{
// 			"pm2_id":"11",
// 			"pm2_firstname":"Quentin",
// 			"pm2_lastname":"D\u00e9sert",
// 			"pm2_login":"qdesert",
// 			"pm2_email":"qdesert@genymobile.com"
// 		},
// 		"start_date":"2013-07-24",
// 		"end_date":"2013-07-24"
// 	}
// }
//
// If unsuccessfull :
// {
// 	"status":"error",
// 	"status_message":"<THE ERROR MESSAGE>"
// }


try {
	$ces = array();
	if($auth_granted){
		$tmp_project = new GenyProject();
		$results = array();
		$project_id = getParam( "project_id", -1 );
		
		if( $project_id == -1 ){
			echo json_encode( array( "status" => "error", "status_message" => "Fatal error: project_id is mandatory, please define one." ) );
			exit;
		}
		else{
			$tmp_project->loadProjectById($project_id);
			if( $tmp_project->id > 0 && $tmp_project->id == $project_id ){
				$tmp_pt = new GenyProjectType($tmp_project->type_id);
				$tmp_pm1 = new GenyProfile($tmp_project->pm1_id);
				$tmp_pm2 = new GenyProfile($tmp_project->pm2_id);
				echo json_encode( array(
				    "status" => "success",
				    "status_message" => "Information retrieved successfully.",
				    "data" => array(
				        "id" => "$tmp_project->id",
				        "name" => "$tmp_project->name",
				        "description" => "$tmp_project->description",
				        "status_id" => "$tmp_project->status_id",
				        "location" => "$tmp_project->location",
				        "type" => array(
						"type_id" => "$tmp_project->type_id",
						"type_name" => "$tmp_pt->name",
						"type_decription" => "$tmp_pt->description"
				        ),
				        "pm1" => array(
						"pm1_id" => "$tmp_project->pm1_id",
						"pm1_firstname" => "$tmp_pm1->firstname",
						"pm1_lastname" => "$tmp_pm1->lastname",
						"pm1_login" => "$tmp_pm1->login",
						"pm1_email" => "$tmp_pm1->email"
				        ),
				        "pm2" => array(
						"pm2_id" => "$tmp_project->pm2_id",
						"pm2_firstname" => "$tmp_pm2->firstname",
						"pm2_lastname" => "$tmp_pm2->lastname",
						"pm2_login" => "$tmp_pm2->login",
						"pm2_email" => "$tmp_pm2->email"
				        ),
				        "start_date" => "$tmp_project->start_date",
				        "end_date" => "$tmp_project->end_date",
				    )
				) );
			}
			else{
				echo json_encode( array( "status" => "error", "status_message" => "Fatal error: project couldn't be load, please check the parameters." ) );
				exit;
			}
		}
// 		$data = json_encode($ces);
// 		echo $data;
	}
} catch (Exception $e) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>