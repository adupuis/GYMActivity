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

class GenyActivityReportStatus {
	private $updates = array();
	public function __construct($id = -1){
		$this->config = new GenyWebConfig();
		$this->handle = mysql_connect($this->config->db_host,$this->config->db_user,$this->config->db_password);
		mysql_select_db($this->config->db_name);
		mysql_query("SET NAMES 'utf8'");
		$this->id = -1;
		$this->name = '';
		$this->description = '';
		if($id > -1)
			$this->loadActivityReportStatusById($id);
	}
	public function insertNewActivityReportStatus($id,$shortname,$name,$description){
		$query = "INSERT INTO ActivityReportStatus VALUES($id,'".mysql_real_escape_string($shortname)."','".mysql_real_escape_string($name)."','".mysql_real_escape_string($description)."')";
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyActivityReportStatus MySQL query : $query",0);
		return mysql_query($query,$this->handle);
	}
	public function getActivityReportStatusListWithRestrictions($restrictions){
		// $restrictions is in the form of array("project_id=1","project_status_id=2")
		$last_index = count($restrictions)-1;
		$query = "SELECT activity_report_status_id,activity_report_status_shortname,activity_report_status_name,activity_report_status_description FROM ActivityReportStatus";
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
			error_log("[GYMActivity::DEBUG] GenyActivityReportStatus MySQL query : $query",0);
		$result = mysql_query($query, $this->handle);
		$activity_report_status_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_obj = new GenyActivityReportStatus();
				$tmp_obj->id = $row[0];
				$tmp_obj->shortname = $row[1];
				$tmp_obj->name = $row[2];
				$tmp_obj->description = $row[3];
				$activity_report_status_list[] = $tmp_obj;
			}
		}
// 		mysql_close();
		return $activity_report_status_list;
	}
	public function getAllActivityReportStatus(){
		return $this->getActivityReportStatusListWithRestrictions( array() );
	}
	public function loadActivityReportStatusByName($name){
		$objects = $this->getActivityReportStatusListWithRestrictions(array("activity_report_status_name='".mysql_real_escape_string($name)."'"));
		$object = $objects[0];
		if(isset($object) && $object->id > -1){
			$this->id = $object->id;
			$this->shortname = $object->shortname;
			$this->name = $object->name;
			$this->description = $object->description;
		}
	}
	public function loadActivityReportStatusByShortName($name){
		$objects = $this->getActivityReportStatusListWithRestrictions(array("activity_report_status_shortname='".mysql_real_escape_string($name)."'"));
		$object = $objects[0];
		if(isset($object) && $object->id > -1){
			$this->id = $object->id;
			$this->shortname = $object->shortname;
			$this->name = $object->name;
			$this->description = $object->description;
		}
	}
	public function loadActivityReportStatusById($id){
		$objects = $this->getActivityReportStatusListWithRestrictions(array("activity_report_status_id=".mysql_real_escape_string($id)));
		$object = $objects[0];
		if(isset($object) && $object->id > -1){
			$this->id = $object->id;
			$this->shortname = $object->shortname;
			$this->name = $object->name;
			$this->description = $object->description;
		}
	}
	public function updateString($key,$value){
		$this->updates[] = "$key='".mysql_real_escape_string($value)."'";
	}
	public function updateInt($key,$value){
		$this->updates[] = "$key=".mysql_real_escape_string($value)."";
	}
	public function updateBool($key,$value){
		$this->updates[] = "$key=".mysql_real_escape_string($value)."";
	}
	public function commitUpdates(){
		$query = "UPDATE ActivityReportStatus SET ";
		foreach($this->updates as $up) {
			$query .= "$up,";
		}
		$query = rtrim($query, ",");
		$query .= " WHERE activity_report_status_id=".$this->id;
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyActivityReportStatus MySQL query : $query",0);
		return mysql_query($query, $this->handle);
	}
}
?>