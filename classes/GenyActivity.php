<?php

include_once 'GenyWebConfig.php';

class GenyActivity {
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
			$this->loadActivityById($id);
	}
	public function insertNewActivity($id,$activity_date,$activity_load,$activity_input_date,$assignement_id,$task_id){
		$query = "INSERT INTO Activities VALUES($id,'".mysql_real_escape_string($activity_date)."','".mysql_real_escape_string($activity_input_date)."','".mysql_real_escape_string($assignement_id)."','".mysql_real_escape_string($task_id)."')";
		if( $this->config->debug )
			echo "<!-- DEBUG: GenyActivity MySQL query : $query -->\n";
		if(mysql_query($query,$this->handle))
			return mysql_insert_id($this->handle);
		else
			return -1;
	}
	public function getActivitiesListWithRestrictions($restrictions){
		// $restrictions is in the form of array("project_id=1","project_status_id=2")
		$last_index = count($restrictions)-1;
		$query = "SELECT activity_id,activity_date,activity_load,activity_input_date,assignement_id,task_id FROM Activities";
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
			echo "<!-- DEBUG: GenyActivity MySQL query : $query -->\n";
		$result = mysql_query($query, $this->handle);
		$activityies_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_obj = new GenyActivity();
				$tmp_obj->id = $row[0];
				$tmp_obj->activity_date = $row[1];
				$tmp_obj->load = $row[2];
				$tmp_obj->input_date = $row[3];
				$tmp_obj->assignement_id = $row[4];
				$tmp_obj->task_id = $row[5];
				$activityies_list[] = $tmp_obj;
			}
		}
		mysql_close();
		return $activityies_list;
	}
	public function getAllActivities(){
		return $this->getActivitiesListWithRestrictions( array() );
	}
	public function getActivitiesListByDate($date){
		return $this->getActivitiesListWithRestrictions(array("activity_date='".mysql_real_escape_string($name)."'"));
	}
	public function getActivitiesListByAssignementId($id){
		return $this->getActivitiesListWithRestrictions(array("assignement_id='".mysql_real_escape_string($id)."'"));
	}
	public function getActivitiesListByTaskId($id){
		return $this->getActivitiesListWithRestrictions(array("task_id='".mysql_real_escape_string($id)."'"));
	}
	public function getActivitiesListByTaskAndAssignementId($task_id,$assignement_id){
		return $this->getActivitiesListWithRestrictions(array("task_id='".mysql_real_escape_string($task_id)."'","assignement_id='".mysql_real_escape_string($assignement_id)."'"));
	}
	public function loadActivityById($id){
		$clients = $this->getActivitiesListWithRestrictions(array("activity_id=".mysql_real_escape_string($id)));
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
		$query = "UPDATE Activities SET ";
		foreach($this->updates as $up) {
			$query .= "$up,";
		}
		$query = rtrim($query, ",");
		$query .= " WHERE activity_id=".$this->id;
		if( $this->config->debug )
			echo "<!-- DEBUG: GenyActivity MySQL query : $query -->\n";
		return mysql_query($query, $this->handle);
	}
}
?>