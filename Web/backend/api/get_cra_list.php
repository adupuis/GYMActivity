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
		$cra_reports = array();
		$query = array();
		// project doit être le MD5 du nom du projet.
		$param_project_md5 = getParam("project_md5");
		$param_start_date = getParam("start_date");
		$param_end_date = getParam("end_date");
		$param_profile_id = getParam("profile_id");
		$param_profile_login = getParam("profile_login");
		$param_client_name = getParam("client_name");
		$param_project_name = getParam("project_name");
		$param_activity_report_status_shortname = getParam("activity_report_status_shortname");
		
		if( $param_project_md5 != "" ){
			$query[] = "MD5(project_name)=\"$param_project_md5\"";
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
		if( $param_profile_login != "" ){
			$query[] = "profile_login=\"$param_profile_login\"";
		}
		if( $param_client_name != "" ){
			$query[] = "client_name=\"$param_client_name\"";
		}
		if( $param_project_name != "" ){
			$query[] = "project_name=\"$param_project_name\"";
		}
		if( $param_activity_report_status_shortname != "" ){
			$ars = new GenyActivityReportStatus();
			$ars->loadActivityReportStatusByShortName($param_activity_report_status_shortname);
			if( $ars->id > 0 ){
				$query[] = "activity_report_status_id=".$ars->id;
			}
		}
		$activity_report_workflow->setDebug(true);
		foreach( $activity_report_workflow->getActivityReportsWorkflowWithRestrictions($query) as $c ){
			$cra_reports[] = array( "activity_report_id" => $c->activity_report_id, "profile_id" => $c->profile_id, "profile_firstname" => $c->profile_firstname, "profile_lastname" => $c->profile_lastname, "project_name" => $c->project_name, "task_name" => $c->task_name, "activity_date" => $c->activity_date, "client_name" => $c->client_name, "activity_load" => $c->activity_load, "activity_report_status_id" => $c->activity_report_status_id, "profile_login" => $c->profile_login );
		}
		$data = json_encode($cra_reports);
		echo $data;
	}
	else{
		echo json_encode( array( "status" => "error", "status_message" => "Fatal error: authorization refused." ) );
		exit;
	}
} catch (Exception $e) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>
