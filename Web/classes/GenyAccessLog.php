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

define("UNAUTHORIZED_ACCESS", "UNAUTHORIZED_ACCESS");
define("BAD_CREDENTIALS","BAD_CREDENTIALS");
define("BAD_USERNAME_FORMAT","BAD_USERNAME_FORMAT");
define("AUTH_REQUIRED","AUTH_REQUIRED");

class GenyAccessLog extends GenyDatabaseTools {
	public function __construct($id = -1){
		parent::__construct("AccessLogs",  "access_log_id");
		$this->id = -1;
		$this->timestamp = 0;
		$this->profile_id = -1;
		$this->ip = "0.0.0.0";
		$this->status = false;
		$this->page_requested = "";
		$this->type = "";
		$this->extra = "";
		if($id > -1)
			$this->loadAccessLogById($id);
	}
	public function deleteAccessLog($id=0){
		if(is_numeric($id)){
			if( $id == 0 && $this->id > 0 )
				$id = $this->id;
			if($id <= 0)
				return -1;
			$query = "DELETE FROM AccessLogs WHERE access_log_id=$id";
			if( $this->config->debug )
				error_log("[GYMActivity::DEBUG] GenyAccessLog MySQL DELETE query : $query",0);
			if(mysql_query($query,$this->handle))
				return 1;
			else
				return -1;
		}
		return -1;
	}
	public function insertNewAccessLog($profile_id,$ip,$status,$page_requested,$type,$extra){
// 		error_log("DEBUG: GenyAccessLog::insertNewAccessLog entering function", 0);
		if( ! is_numeric($profile_id) )
			return -1;
// 		error_log("DEBUG: GenyAccessLog::insertNewAccessLog profile_id ok", 0);
		if( $status != "false" && $status != "true" )
			return -1;
// 		error_log("DEBUG: GenyAccessLog::insertNewAccessLog $status ok", 0);
		if( ! defined($type) )
			return -1;
// 		error_log("DEBUG: GenyAccessLog::insertNewAccessLog type ok", 0);
		$query = "INSERT INTO AccessLogs VALUES(0,".time().",$profile_id,'".mysql_real_escape_string($ip)."',$status,'".mysql_real_escape_string($page_requested)."','".mysql_real_escape_string($type)."','".mysql_real_escape_string($extra)."')" ;
// 		error_log("DEBUG: GenyAccessLog MySQL query : $query\n", 0);
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyAccessLog MySQL query : $query",0);
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return -1;
		}
	}
	public function getAccessLogsListWithRestrictions($restrictions,$restriction_type = "AND"){
		// $restrictions is in the form of array("project_id=1","project_status_id=2")
		$last_index = count($restrictions)-1;
		$query = "SELECT access_log_id,access_log_timestamp,access_log_profile_id,access_log_ip,access_log_status,access_log_page_requested,access_log_type,access_log_extra FROM AccessLogs";
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
			error_log("[GYMActivity::DEBUG] GenyAccessLog MySQL query : $query",0);
		$result = mysql_query($query, $this->handle);
		$access_log_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_access_log = new GenyAccessLog();
				$tmp_access_log->id = $row[0];
				$tmp_access_log->timestamp = $row[1];
				$tmp_access_log->profile_id = $row[2];
				$tmp_access_log->ip = $row[3];
				$tmp_access_log->status = $row[4];
				$tmp_access_log->page_requested = $row[5];
				$tmp_access_log->type = $row[6];
				$tmp_access_log->extra = $row[7];
				$access_log_list[] = $tmp_access_log;
			}
		}
// 		mysql_close();
		return $access_log_list;
	}
	public function getAllAccessLogs(){
		return $this->getAccessLogsListWithRestrictions( array() );
	}
	public function getAccessLogsListByType($type){
		return $this->getAccessLogsListWithRestrictions( array("access_log_type='".mysql_real_escape_string($type)."'") );
	}
	public function getAccessLogsListByStatus($status){
		if($status != "true" && $status != "false")
			return -1;
		return $this->getAccessLogsListWithRestrictions( array("access_log_type=$status") );
	}
	public function getAccessLogsListByProfileId($id){
		if( ! is_numeric($id) )
			return -1;
		return $this->getAccessLogsListWithRestrictions( array("profile_id=$id") );
	}
	public function getAccessLogsListByPageRequested($page){
		return $this->getAccessLogsListWithRestrictions( array("access_log_page_requested='".mysql_real_escape_string($page)."'") );
	}
	public function getAccessLogsListByIp($ip){
		return $this->getAccessLogsListWithRestrictions( array("access_log_ip='".mysql_real_escape_string($ip)."'") );
	}
	public function searchAccessLogs($term){
		$q = mysql_real_escape_string($term);
		return $this->getAccessLogsListWithRestrictions( array("access_log_ip LIKE '%$q%'","access_log_page_requested LIKE '%$q%'","access_log_type LIKE '%$q%'","access_log_extra LIKE '%$q%'"), "OR" );
	}
	public function loadAccessLogById($id){
		if( ! is_numeric($id) )
			return -1;
		$access_logs = $this->getAccessLogsListWithRestrictions(array("access_log_id=".$id));
		$access_log = $access_logs[0];
		if(isset($access_log) && $access_log->id > -1){
			$this->id = $access_log->id;
			$this->timestamp = $access_log->timestamp;
			$this->profile_id = $access_log->profile_id;
			$this->ip = $access_log->ip;
			$this->status = $access_log->status;
			$this->page_requested = $access_log->page_requested;
			$this->type = $access_log->type;
			$this->extra = $access_log->extra;
		}
	}
}
?>