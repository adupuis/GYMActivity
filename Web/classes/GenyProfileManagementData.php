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
include_once 'GenyProfile.php';

class GenyProfileManagementData {
	private $updates = array();
	public function __construct($id = -1){
		$this->config = new GenyWebConfig();
		$this->handle = mysql_connect($this->config->db_host,$this->config->db_user,$this->config->db_password);
		mysql_select_db($this->config->db_name);
		mysql_query("SET NAMES 'utf8'");
		$this->id = GENYMOBILE_FALSE;
		$this->profile_id = GENYMOBILE_FALSE;
		$this->salary = GENYMOBILE_FALSE;
		$this->recruitement_date = '1979-01-01';
		$this->is_billable = false;
		$this->availability_date = '1979-01-01';
		$this->profile_object = -1;
		if($id > -1)
			$this->loadProfileManagementDataById($id);
	}
	public function insertNewProfileManagementData($profile_id,$pmd_salary,$pmd_recruitement_date,$pmd_is_billable,$pmd_availability_date){
		if( ! is_numeric($profile_id) )
			return GENYMOBILE_FALSE;
		
		if( ! is_numeric($pmd_salary) )
			return GENYMOBILE_FALSE;
		
		$query = "INSERT INTO ProfileManagementData VALUES(0,$profile_id,'".mysql_real_escape_string($pmd_salary)."','".mysql_real_escape_string($pmd_recruitement_date)."','".mysql_real_escape_string($pmd_is_billable)."','".md5(mysql_real_escape_string($pmd_availability_date))."')";
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyProfileManagementData MySQL query : $query",0);
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return -1;
		}
	}
	public function getProfileManagementDataListWithRestrictions($restrictions,$restriction_type = "AND"){
		// $restrictions is in the form of array("profile_id=1","profile_status_id=2")
		$last_index = count($restrictions)-1;
		$query = "SELECT profile_management_data_id,profile_id,profile_management_data_salary,profile_management_data_recruitement_date,profile_management_data_is_billable,profile_management_data_availability_date FROM ProfileManagementData";
		if(count($restrictions) > 0){
			$query .= " WHERE ";
			$op = mysql_real_escape_string($restriction_type);
			foreach($restrictions as $key => $value) {
				$query .= $value;
				if($key != $last_index){
					$query .= " $op ";
				}
			}
		}
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyProfileManagementData MySQL query : $query",0);
		$result = mysql_query($query, $this->handle);
		$pmd_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_pmd = new GenyProfileManagementData();
				$tmp_pmd->id = $row[0];
				$tmp_pmd->profile_id = $row[1];
				$tmp_pmd->salary = $row[2];
				$tmp_pmd->recruitement_date = $row[3];
				$tmp_pmd->is_billable = $row[4];
				$tmp_pmd->availability_date = $row[5];
				$tmp_pmd->profile_object = new GenyProfile( $tmp_pmd->profile_id );
				$pmd_list[] = $tmp_pmd;
			}
		}
// 		mysql_close();
		return $pmd_list;
	}
	public function searchProfileManagementData($term){
		$q = mysql_real_escape_string($term);
		return $this->getProfileManagementDataListWithRestrictions( array("profile_management_data_salary LIKE '%$q%'","profile_management_data_recruitement_date LIKE '%$q%'","profile_management_data_availability_date LIKE '%$q%'"), "OR" );
	}
	public function getAllProfileManagementData(){
		return $this->getProfileManagementDataListWithRestrictions( array() );
	}
	public function loadProfileManagementDataById($id){
		if( ! is_numeric($id) )
			return GENYMOBILE_FALSE;
		$profiles = $this->getProfileManagementDataListWithRestrictions(array("profile_management_data_id=$id"));
		$profile = $profiles[0];
		if(isset($profile) && $profile->id > -1){
			$this->id = $profile->id;
			$this->profile_id = $profile->profile_id;
			$this->salary = $profile->salary;
			$this->recruitement_date = $profile->recruitement_date;
			$this->is_billable = $profile->is_billable;
			$this->availability_date = $profile->availability_date;
		}
	}
	public function getProfile(){
		if( $this->id <= 0 )
			return GENYMOBILE_FALSE;
		if( $this->profile_object == -1 )
			$this->profile_object = new $GenyProfile( $this->profile_id );
		return $this->profile_object ;
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
		$query = "UPDATE ProfileManagementData SET ";
		foreach($this->updates as $up) {
			$query .= "$up,";
		}
		$query = rtrim($query, ",");
		$query .= " WHERE profile_management_data_id=".$this->id;
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyProfileManagementData MySQL query : $query",0);
		return mysql_query($query, $this->handle);
	}
}
?>