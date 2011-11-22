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
		if($id > -1)
			$this->loadProfileManagementDataById($id);
	}
	public function insertNewProfileManagementData($profile_id,$pmd_salary,$pmd_recruitement_date,$pmd_is_billable,$pmd_availability_date){
		if( ! is_numeric($profile_id) )
			return GENYMOBILE_FALSE;
		
		if( ! is_numeric($pmd_salary) )
			return GENYMOBILE_FALSE;
		
		$query = "INSERT INTO ProfileManagementData VALUES(0,$profile_id,'".mysql_real_escape_string($profile_login)."','".mysql_real_escape_string($profile_firstname)."','".mysql_real_escape_string($profile_lastname)."','".md5(mysql_real_escape_string($profile_password))."','".mysql_real_escape_string($profile_email)."',".mysql_real_escape_string($profile_is_active).",".mysql_real_escape_string($profile_needs_password_reset).",".mysql_real_escape_string($rights_group_id).")";
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
		$query = "SELECT profile_id,profile_login,profile_firstname,profile_lastname,profile_email,profile_is_active,profile_needs_password_reset,rights_group_id FROM ProfileManagementData";
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
		$profile_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_profile = new GenyProfileManagementData();
				$tmp_profile->id = $row[0];
				$tmp_profile->login = $row[1];
				$tmp_profile->firstname = $row[2];
				$tmp_profile->lastname = $row[3];
				$tmp_profile->email = $row[4];
				$tmp_profile->is_active = $row[5];
				$tmp_profile->needs_password_reset = $row[6];
				$tmp_profile->rights_group_id = $row[7];
				$profile_list[] = $tmp_profile;
			}
		}
// 		mysql_close();
		return $profile_list;
	}
	public function searchProfileManagementData($term){
		$q = mysql_real_escape_string($term);
		return $this->getProfileManagementDataListWithRestrictions( array("profile_login LIKE '%$q%'","profile_firstname LIKE '%$q%'","profile_lastname LIKE '%$q%'"), "OR" );
	}
	public function getAllProfileManagementData(){
		return $this->getProfileManagementDataListWithRestrictions( array() );
	}
	public function getProfileManagementDataByLogin($login){
		return $this->getProfileManagementDataListWithRestrictions( array("profile_login='".mysql_real_escape_string($login)."'") );
	}
	public function loadProfileManagementDataById($id){
		$profiles = $this->getProfileManagementDataListWithRestrictions(array("profile_id=".mysql_real_escape_string($id)));
		$profile = $profiles[0];
		if(isset($profile) && $profile->id > -1){
			$this->id = $profile->id;
			$this->login = $profile->login;
			$this->firstname = $profile->firstname;
			$this->lastname = $profile->lastname;
			$this->email = $profile->email;
			$this->is_active = $profile->is_active;
			$this->needs_password_reset = $profile->needs_password_reset;
			$this->rights_group_id = $profile->rights_group_id;
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
		$query = "UPDATE ProfileManagementData SET ";
		foreach($this->updates as $up) {
			$query .= "$up,";
		}
		$query = rtrim($query, ",");
		$query .= " WHERE profile_id=".$this->id;
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyProfileManagementData MySQL query : $query",0);
		return mysql_query($query, $this->handle);
	}
}
?>