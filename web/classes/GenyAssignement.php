<?php

include_once 'GenyWebConfig.php';

class GenyAssignement {
	private $updates = array();
	public function __construct($id = -1){
		$this->config = new GenyWebConfig();
		$this->handle = mysql_connect($this->config->db_host,$this->config->db_user,$this->config->db_password);
		mysql_select_db("GYMActivity");
		mysql_query("SET NAMES 'utf8'");
		$this->id = -1;
		$this->profile_id = -1;
		$this->project_id = -1;
		$this->overtime_allowed = false;
		if($id > -1)
			$this->loadAssignementById($id);
	}
	public function insertNewAssignement($id,$profile_id,$project_id,$overtime_allowed='false'){
		$query = "INSERT INTO Assignements VALUES($id,$profile_id,$project_id,$overtime_allowed)";
		if( $this->config->debug )
			echo "<!-- DEBUG: GenyAssignements MySQL query : $query -->\n";
		if(mysql_query($query,$this->handle))
			return mysql_insert_id($this->handle);
		else
			return -1;
	}
	public function getAssignementsListWithRestrictions($restrictions){
		// $restrictions is in the form of array("profile_id=1","project_status_id=2")
		$last_index = count($restrictions)-1;
		$query = "SELECT assignement_id,profile_id,project_id,assignement_overtime_allowed FROM Assignements";
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
			echo "<!-- DEBUG: GenyAssignement MySQL query : $query -->\n";
		$result = mysql_query($query, $this->handle);
		$object_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_object = new GenyClient();
				$tmp_object->id = $row[0];
				$tmp_object->profile_id = $row[1];
				$tmp_object->project_id = $row[2];
				$tmp_object->overtime_allowed = $row[3];
				$object_list[] = $tmp_object;
			}
		}
// 		mysql_close();
		return $object_list;
	}
	public function getAllAssignements(){
		return $this->getAssignementsListWithRestrictions( array() );
	}
	public function getAssignementsListByProfileId($id){
		return $this->getAssignementsListWithRestrictions(array("profile_id=$id"));
	}
	public function getAssignementsListByProjectId($id){
		return $this->getAssignementsListWithRestrictions(array("project_id=$id"));
	}
	public function loadAssignementById($id){
		$objects = $this->getAssignementsListWithRestrictions(array("assignement_id=$id"));
		$object = $objects[0];
		if(isset($object) && $object->id > -1){
			$this->id = $object->id;
			$this->profile_id = $object->profile_id;
			$this->project_id = $object->project_id;
			$this->overtime_allowed = $object->overtime_allowed;
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
		$query = "UPDATE Assignements SET ";
		foreach($this->updates as $up) {
			$query .= "$up,";
		}
		$query = rtrim($query, ",");
		$query .= " WHERE assignement_id=".$this->id;
		echo $query;
		return mysql_query($query, $this->handle);
	}
	public function deleteAllAssignementsByProjectId($project_id){
		if(!is_numeric($project_id))
			return false;
		$query = "DELETE FROM Assignements WHERE project_id=$project_id";
		return mysql_query($query, $this->handle);
	}
}
?>