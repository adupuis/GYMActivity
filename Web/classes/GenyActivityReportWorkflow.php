<?php
//  Copyright (C) 2011 by GENYMOBILE & Jean-Charles Leneveu
//  jcleneveu@genymobile.com
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

class GenyActivityReportWorkflow extends GenyDatabaseTools {
	public $activity_report_id = -1;
	public $profile_id = -1;
	public $profile_firstname = '';
	public $profile_lastname = '';
	public $project_name = '';
	public $task_name = '';
	public $activity_date = '';
	public $client_name = '';
	public $activity_load = -1;
	public $activity_report_status_id = -1;
	public $profile_login = '';
	
	public function __construct(){
		parent::__construct("ActivityReportsWorkflow",  "activity_report_id");
		
		$this->activity_report_id = -1;
		$this->profile_id = -1;
		$this->profile_firstname = '';
		$this->profile_lastname = '';
		$this->project_name = '';
		$this->task_name = '';
		$this->activity_date = '';
		$this->client_name = '';
		$this->activity_load = -1;
		$this->activity_report_status_id = -1;
		$this->profile_login = '';
	}
	
	public function getActivityReportsWorkflowWithRestrictions($restrictions){
		$last_index = count($restrictions)-1;
		$query = "SELECT * FROM ActivityReportWorkflow";
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
			error_log("[GYMActivity::DEBUG] GenyActivityReportsWorkflowWithRestrictions MySQL query : $query",0);
		$result = mysql_query($query, $this->handle);
		$obj_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_obj = new GenyActivityReportWorkflow();
				$tmp_obj->activity_report_id = $row[0];
				$tmp_obj->profile_id = $row[1];
				$tmp_obj->profile_firstname = $row[2];
				$tmp_obj->profile_lastname = $row[3];
				$tmp_obj->project_name = $row[4];
				$tmp_obj->task_name = $row[5];
				$tmp_obj->activity_date = $row[6];
				$tmp_obj->client_name = $row[7];
				$tmp_obj->activity_load = $row[8];
				$tmp_obj->activity_report_status_id = $row[9];
				$tmp_obj->profile_login = $row[10];
				$obj_list[] = $tmp_obj;
			}
		}
		return $obj_list;
	}
	public function getActivityReportsWorkflow(){
		return $this->getActivityReportsWorkflowWithRestrictions( array() );
	}
	
	public function getActivityReportsWorkflowFrom($date){
		if (substr_count($date, '-') == 2) {
			list($y, $m, $d) = explode('-', $date);
			if( checkdate($m, $d, sprintf('%04u', $y)) ){
				return $this->getActivityReportsWorkflowWithRestrictions( array("activity_date >= \"$date\"") );
			}
			else{
				return GENY_FALSE;
			}
		}
	}
	
	public function commitUpdates(){
		return GENY_FALSE;
	}	
}
?>