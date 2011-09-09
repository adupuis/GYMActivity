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
$required_group_rights = 1;
$auth_granted = false;
$authorized_auth_method = "api_key";

header('Content-type:text/javascript;charset=UTF-8');

include_once 'ajax_authent_checking.php';
include_once 'ajax_toolbox.php';

try {
	$time = time();
	$month = date('m', $time);
	$year=date('Y', $time);
	$d_month_previous = date('m', mktime(0,0,0,($month-1),28,$year));
	$start_date="$year-$d_month_previous-01";
	$lastday = date('t',mktime(0,0,0,($d_month_previous-1),28,$year));
	$end_date="$year-$d_month_previous-$lastday";
	
	$purged_cra = array();
	if($auth_granted){
		$geny_ars = new GenyActivityReportStatus();
		$geny_ars->loadActivityReportStatusByShortName('P_USER_VALIDATION');
		$geny_ar = new GenyActivityReport();
		
		$geny_activity = new GenyActivity();
		$geny_assignement = new GenyAssignement();
		$geny_project = new GenyProject();
		$geny_task = new GenyTask();
		$geny_profile = new GenyProfile();
		$geny_notification = new GenyNotification();
		
		$json_messages = array();
		
		foreach( $geny_ar->getActivityReportsByReportStatusId( $geny_ars->id ) as $ar ){
			$geny_activity->loadActivityById( $ar->activity_id );
			if( strtotime( $geny_activity->activity_date ) <= strtotime( "$year-$d_month_previous-$lastday" ) ){
				$geny_assignement->loadAssignementById( $geny_activity->assignement_id );
				$geny_project->loadProjectById( $geny_assignement->project_id );
				$geny_task->loadTaskById( $geny_activity->task_id );
				$geny_profile->loadProfileById( $geny_assignement->profile_id );
// 				echo "Removing unvalidated CRA : ".$geny_profile->login." / ".$geny_project->name." / ".$geny_task->name." / ".$geny_activity->activity_date."\n";
				$geny_notification->insertNewNotification($geny_profile->id,"Votre CRA/congés du ".$geny_activity->activity_date." a été automatiquement supprimé par le système il concernait : ".$geny_project->name." / ".$geny_task->name.".","nok");
				$geny_notification->insertNewGroupNotification($geny_profile->id,"CRA/congé de ".GenyTools." du ".$geny_activity->activity_date." a été automatiquement supprimé par le système il concernait : ".$geny_project->name." / ".$geny_task->name.".","nok");
				if( $geny_activity->deleteActivity() > 0 )
					$json_messages[] = array("status" => "success", "status_message" => "Activity ".$geny_activity->id."and ActivityReport ".$geny_ar->id." successfully removed." );
				else
					$json_messages[] = array("status" => "error", "status_message" => "Error while removing Activity ".$geny_activity->id." and ActivityReport ".$ar->id."." );
			}
		}
		echo json_encode($json_messages);
	}
} catch (Exception $e) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>