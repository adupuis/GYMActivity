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

header('Content-Type: application/json;charset=UTF-8');

include_once 'ajax_authent_checking.php';
include_once 'ajax_toolbox.php';

try {
	$clients = array();
	if($auth_granted){
		$activity_report_workflow = new GenyActivityReportWorkflow();
		$results = array();
		$query = array();
		// project doit être le MD5 du nom du projet.
		$param_project = getParam("project");
		$param_start_date = getParam("start_date");
		$param_end_date = getParam("end_date");
		$param_profile_id = getParam("profile_id");
		$param_client_name = getParam("client_name");
		
		if( $param_project != "" ){
			$query[] = "MD5(project_name)=\"$param_project\"";
		}
		if( $param_start_date != "" ){
			$query[] = "activity_date>=\"$param_start_date\"";
		}
		if( $param_end_date != "" ){
			$query[] = "activity_date<=\"$param_end_date\"";
		}
		if( $param_profile_id != "" ){
			$query[] = "profile_id=$param_profile_id";
		}
		if( $param_client_name != "" ){
			$query[] = "client_name=\"$param_client_name\"";
		}
		
		foreach( $results as $c ){
			$clients[] = array( "value" => $c->id, "label" => $c->name );
		}
		$data = json_encode($clients);
		echo $data;
	}
} catch (Exception $e) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>