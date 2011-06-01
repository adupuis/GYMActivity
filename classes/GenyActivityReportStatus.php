<?php

include_once 'GenyWebConfig.php';

class GenyActivityReportStatus {
	private $updates = array();
	public function __construct($id = -1){
		$this->config = new GenyWebConfig();
		$this->handle = mysql_connect($this->config->db_host,$this->config->db_user,$this->config->db_password);
		mysql_select_db("GYMActivity");
		mysql_query("SET NAMES 'utf8'");
		$this->id = -1;
		$this->name = '';
		$this->description = '';
		if($id > -1)
			$this->loadActivityReportStatusById($id);
	}
	public function insertNewActivityReportStatus($id,$name,$description){
		$query = "INSERT INTO Activity_Report_Status VALUES($id,'".mysql_real_escape_string($name)."','".mysql_real_escape_string($description)."')";
		if( $this->config->debug )
			echo "<!-- DEBUG: GenyActivityReportStatus MySQL query : $query -->\n";
		return mysql_query($query,$this->handle);
	}
	public function getActivityReportStatusListWithRestrictions($restrictions){
		// $restrictions is in the form of array("project_id=1","project_status_id=2")
		$last_index = count($restrictions)-1;
		$query = "SELECT activity_report_status_id,activity_report_status_name,activity_report_status_description FROM Activity_Report_Status";
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
			echo "<!-- DEBUG: GenyActivityReportStatus MySQL query : $query -->\n";
		$result = mysql_query($query, $this->handle);
		$activity_report_status_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_obj = new GenyActivityReportStatus();
				$tmp_obj->id = $row[0];
				$tmp_obj->name = $row[1];
				$tmp_obj->description = $row[2];
				$activity_report_status_list[] = $tmp_obj;
			}
		}
		mysql_close();
		return $activity_report_status_list;
	}
	public function getAllActivityReportStatus(){
		return $this->getActivityReportStatusListWithRestrictions( array() );
	}
	public function loadActivityReportStatusByName($name){
		$clients = $this->getActivityReportStatusListWithRestrictions(array("activity_report_status_name='".mysql_real_escape_string($name)."'"));
		$client = $clients[0];
		if(isset($client) && $client->id > -1){
			$this->id = $client->id;
			$this->name = $client->name;
		}
	}
	public function loadActivityReportStatusById($id){
		$clients = $this->getActivityReportStatusListWithRestrictions(array("activity_report_status_id=".mysql_real_escape_string($id)));
		$client = $clients[0];
		if(isset($client) && $client->id > -1){
			$this->id = $client->id;
			$this->name = $client->name;
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
		$query = "UPDATE Activity_Report_Status SET ";
		foreach($this->updates as $up) {
			$query .= "$up,";
		}
		$query = rtrim($query, ",");
		$query .= " WHERE activity_report_status_id=".$this->id;
		if( $this->config->debug )
			echo "<!-- DEBUG: GenyActivityReportStatus MySQL query : $query -->\n";
		return mysql_query($query, $this->handle);
	}
}
?>