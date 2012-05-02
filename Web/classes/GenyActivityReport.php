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


include_once 'GenyWebConfig.php';
include_once 'GenyDatabaseTools.php';

class GenyActivityReport extends GenyDatabaseTools {
	public $id = -1;
	public $invoice_reference = '';
	public $activity_id = -1;
	public $profile_id = -1;
	public $status_id = -1;
	public function __construct($id = -1){
		parent::__construct("ActivityReports",  "activity_report_id");
		$this->id = -1;
		$this->invoice_reference = '';
		$this->activity_id = -1;
		$this->profile_id = -1;
		$this->status_id = -1;
		if($id > -1)
			$this->loadActivityReportById($id);
	}
	public function deleteActivityReport($id=0){
		// WARNING: cette fonction supprime un rapport en bout de chaîne, il est préférable d'appeler deleteActivity() qui appel automatiquement cette cette fonction. Dans le cas contraire vous devez maintenir l'intégrité de la base en supprimant ensuite l'activity vous même.
		if(is_numeric($id)){
			if( $id == 0 && $this->id > 0 )
				$id = $this->id;
			if($id <= 0)
				return -1;
			
			$query = "DELETE FROM ActivityReports WHERE activity_report_id=$id";
			if( $this->config->debug )
				error_log("[GYMActivity::DEBUG] GenyActivityReport MySQL DELETE query : $query",0);
			if(mysql_query($query,$this->handle))
				return 1;
			else
				return -1;
		}
		return -1;
	}
	public function insertNewActivityReport($id,$invoice_reference,$activity_id,$profile_id,$status_id){
		if( (is_numeric($id) || $id == 'NULL') && is_numeric($activity_id) && is_numeric($profile_id) && is_numeric($status_id) ){
			$query = "INSERT INTO ActivityReports VALUES($id,'".mysql_real_escape_string($invoice_reference)."',$activity_id,$profile_id,$status_id)";
			if( $this->config->debug )
				error_log("[GYMActivity::DEBUG] GenyActivityReport MySQL query : $query",0);
			if(mysql_query($query,$this->handle))
				return mysql_insert_id($this->handle);
			else
				return -1;
		}
		else
			return -1;
	}
	public function getDayLoad($profile_id,$date){
		if( ! is_numeric($profile_id))
			return -1;
		$ars_removed = new GenyActivityReportStatus();
		$ars_removed->loadActivityReportStatusByShortName('REMOVED');
		$ars_refused = new GenyActivityReportStatus();
		$ars_refused->loadActivityReportStatusByShortName('REFUSED');
		$query = "select ifnull(sum(activity_load),0) as activity_day_load from Activities where activity_date='".mysql_real_escape_string($date)."' AND activity_id in (select activity_id from ActivityReports where profile_id=$profile_id AND activity_report_status_id != ".$ars_refused->id." AND activity_report_status_id != ".$ars_removed->id.")";
		if( $this->config->debug )
			error_log( "[GYMActivity::DEBUG] GenyActivityReport::getDayLoad MySQL query : $query", 0 );
		$result = mysql_query($query,$this->handle);
		if( mysql_num_rows($result) != 0 ){
			while ($row = mysql_fetch_row($result)){
				return $row[0];
			}
		}
		else
			return -1;
	}
	public function getDayLoadByProject($profile_id,$date){
		if( ! is_numeric($profile_id))
			return -1;
		$ars_removed = new GenyActivityReportStatus();
		$ars_removed->loadActivityReportStatusByShortName('REMOVED');
		$ars_refused = new GenyActivityReportStatus();
		$ars_refused->loadActivityReportStatusByShortName('REFUSED');
		$query = "select a.activity_date,sum(a.activity_load) as sum_activity_load,p.project_id from Activities a,Assignements ass, Projects p where a.activity_date='".mysql_real_escape_string($date)."' AND activity_id in (select activity_id from ActivityReports where profile_id=$profile_id AND activity_report_status_id != ".$ars_refused->id." AND activity_report_status_id != ".$ars_removed->id.") AND ass.assignement_id=a.assignement_id AND p.project_id=ass.project_id group by project_id";
// 		if( $this->config->debug )
			echo "<!-- DEBUG: GenyActivityReport::getDayLoad MySQL query : $query -->\n";
		$result = mysql_query($query,$this->handle);
		$results = array();
		if( mysql_num_rows($result) != 0 ){
			$i=0;
			while ($row = mysql_fetch_row($result)){
				$results[$i]['activity_date'] = $row[0];
				$results[$i]['sum_activity_load'] = $row[1];
				$results[$i]['project_id'] = $row[2];
				$i++;
			}
			return $results;
		}
		else
			return array();
	}
	public function getDayLoadByAssignement($profile_id,$date){
		if( ! is_numeric($profile_id))
			return -1;
		$ars_removed = new GenyActivityReportStatus();
		$ars_removed->loadActivityReportStatusByShortName('REMOVED');
		$ars_refused = new GenyActivityReportStatus();
		$ars_refused->loadActivityReportStatusByShortName('REFUSED');
		$query = "select a.activity_date,sum(a.activity_load) as sum_activity_load,ass.assignement_id from Activities a,Assignements ass, Projects p where a.activity_date='".mysql_real_escape_string($date)."' AND activity_id in (select activity_id from ActivityReports where profile_id=$profile_id AND activity_report_status_id != ".$ars_refused->id." AND activity_report_status_id != ".$ars_removed->id.") AND ass.assignement_id=a.assignement_id AND p.project_id=ass.project_id group by assignement_id";
// 		if( $this->config->debug )
			echo "<!-- DEBUG: GenyActivityReport::getDayLoad MySQL query : $query -->\n";
		$result = mysql_query($query,$this->handle);
		$results = array();
		if( mysql_num_rows($result) != 0 ){
			$i=0;
			while ($row = mysql_fetch_row($result)){
				$results[$i]['activity_date'] = $row[0];
				$results[$i]['sum_activity_load'] = $row[1];
				$results[$i]['assignement_id'] = $row[2];
				$i++;
			}
			return $results;
		}
		else
			return array();
	}
	
	public function getDayLoadByProfileIdAndTaskId( $profile_id, $task_id ) {
		if( !is_numeric( $profile_id ) ) {
			return -1;
		}
		if( !is_numeric( $task_id ) ) {
			return -1;
		}
		$ars_approved = new GenyActivityReportStatus();
		$ars_approved->loadActivityReportStatusByShortName('APPROVED');
		$ars_billed = new GenyActivityReportStatus();
		$ars_billed->loadActivityReportStatusByShortName('BILLED');
		$ars_paid = new GenyActivityReportStatus();
		$ars_paid->loadActivityReportStatusByShortName('PAID');
		$ars_close = new GenyActivityReportStatus();
		$ars_close->loadActivityReportStatusByShortName('CLOSE');
		
		$query = "SELECT ifnull(sum(a.activity_load),0) FROM Activities a, ActivityReports ar WHERE task_id=".$task_id." AND a.activity_id = ar.activity_id AND ar.profile_id=".$profile_id." AND ( ar.activity_report_status_id=".$ars_approved->id." OR ar.activity_report_status_id=".$ars_billed->id." OR ar.activity_report_status_id=".$ars_paid->id." OR ar.activity_report_status_id=".$ars_close->id." )";
		
		if( $this->config->debug ) {
			error_log( "[GYMActivity::DEBUG] GenyActivityReport MySQL query : $query", 0 );
		}
		$result = mysql_query( $query, $this->handle );
		if( mysql_num_rows( $result ) != 0 ) {
			while( $row = mysql_fetch_row( $result ) ) {
				$day_load = $row[0] / 8;
				return $day_load;
			}
		}
		else {
			return -1;
		}
	}
	
	public function getActivityReportsListWithRestrictions($restrictions){
		// $restrictions is in the form of array("project_id=1","project_status_id=2")
		$last_index = count($restrictions)-1;
		$query = "SELECT activity_report_id,activity_report_invoice_reference,activity_id,profile_id,activity_report_status_id FROM ActivityReports";
		if(count($restrictions) > 0){
			$query .= " WHERE ";
			foreach($restrictions as $key => $value) {
				$query .= $value;
				if($key != $last_index){
					$query .= " AND ";
				}
			}
		}
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyActivityReport MySQL query : $query",0);
		$result = mysql_query($query, $this->handle);
		$obj_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_obj = new GenyActivityReport();
				$tmp_obj->id = $row[0];
				$tmp_obj->invoice_reference = $row[1];
				$tmp_obj->activity_id = $row[2];
				$tmp_obj->profile_id = $row[3];
				$tmp_obj->status_id = $row[4];
				$obj_list[] = $tmp_obj;
			}
		}
// 		mysql_close();
		return $obj_list;
	}
	public function getActivityReportsByProfileId($id){
		if(! is_numeric($id) )
			return false;
		return $this->getActivityReportsListWithRestrictions( array("profile_id=$id") );
	}
	public function getActivityReportsByActivityId($id){
		if(! is_numeric($id) )
			return false;
		return $this->getActivityReportsListWithRestrictions( array("activity_id=$id") );
	}
	public function getActivityReportsByReportStatusId($id){
		if(! is_numeric($id) )
			return false;
		return $this->getActivityReportsListWithRestrictions( array("activity_report_status_id=$id") );
	}
	public function getAllActivityReports(){
		return $this->getActivityReportsListWithRestrictions( array() );
	}
	public function loadActivityReportById($id){
		if(! is_numeric($id) )
			return false;
		$activity_reports = $this->getActivityReportsListWithRestrictions(array("activity_report_id=$id"));
		$activity_report = $activity_reports[0];
		if(isset($activity_report) && $activity_report->id > -1){
			$this->id = $activity_report->id;
			$this->invoice_reference = $activity_report->invoice_reference ;
			$this->activity_id = $activity_report->activity_id ;
			$this->profile_id = $activity_report->profile_id ;
			$this->status_id = $activity_report->status_id ;
		}
	}
	public function loadActivityReportByInvoiceReference($ref){
		$activity_reports = $this->getActivityReportsListWithRestrictions(array("activity_report_invoice_reference=".mysql_real_escape_string($ref)));
		$activity_report = $activity_reports[0];
		if(isset($activity_report) && $activity_report->id > -1){
			$this->id = $activity_report->id;
			$this->invoice_reference = $activity_report->invoice_reference ;
			$this->activity_id = $activity_report->activity_id ;
			$this->profile_id = $activity_report->profile_id ;
			$this->status_id = $activity_report->status_id ;
		}
	}
}
?>