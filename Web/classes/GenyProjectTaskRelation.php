<?php

include_once 'GenyWebConfig.php';

class GenyProjectTaskRelation {
	private $updates = array();
	public function __construct($id = -1){
		$this->config = new GenyWebConfig();
		$this->handle = mysql_connect($this->config->db_host,$this->config->db_user,$this->config->db_password);
		mysql_select_db($this->config->db_name);
		mysql_query("SET NAMES 'utf8'");
		$this->id = -1;
		$this->project_id = -1;
		$this->task_id = -1;
		if($id > -1)
			$this->loadProjectTaskRelationById($id);
	}
	public function insertNewProjectTaskRelation($id,$project_id,$task_id){
		$query = "INSERT INTO ProjectTaskRelations VALUES($id,$project_id,$task_id)";
		if( $this->config->debug )
			echo "<!-- DEBUG: GenyProjectTaskRelations MySQL query : $query -->\n";
		if(mysql_query($query,$this->handle))
			return mysql_insert_id($this->handle);
		else
			return -1;
	}
	public function getProjectTaskRelationsListWithRestrictions($restrictions){
		// $restrictions is in the form of array("project_id=1","project_status_id=2" )
		$last_index = count($restrictions)-1;
		$query = "SELECT project_task_relation_id,project_id,task_id FROM ProjectTaskRelations";
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
			echo "<!-- DEBUG: GenyProjectTaskRelation MySQL query : $query -->\n";
		$result = mysql_query($query, $this->handle);
		$object_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_object = new GenyClient();
				$tmp_object->id = $row[0];
				$tmp_object->project_id = $row[1];
				$tmp_object->task_id = $row[2];
				$object_list[] = $tmp_object;
			}
		}
// 		mysql_close();
		return $object_list;
	}
	public function getAllProjectTaskRelations(){
		return $this->getProjectTaskRelationsListWithRestrictions( array() );
	}
	public function getProjectTaskRelationsListByProjectId($id){
		return $this->getProjectTaskRelationsListWithRestrictions(array("project_id=".mysql_real_escape_string($id)));
	}
	public function getProjectTaskRelationsListByTaskId($id){
		return $this->getProjectTaskRelationsListWithRestrictions(array("task_id=".mysql_real_escape_string($id)));
	}
	public function loadProjectTaskRelationById($id){
		$objects = $this->getProjectTaskRelationListWithRestrictions(array("project_task_relation_id=".mysql_real_escape_string($id)));
		$object = $objects[0];
		if(isset($object) && $object->id > -1){
			$this->id = $object->id;
			$this->project_id = $object->project_id;
			$this->task_id = $object->task_id;
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
		$query = "UPDATE ProjectTaskRelations SET ";
		foreach($this->updates as $up) {
			$query .= "$up,";
		}
		$query = rtrim($query, ",");
		$query .= " WHERE project_task_relation_id=".$this->id;
		if( $this->config->debug )
			echo "<!-- DEBUG: GenyProjectTaskRelation MySQL query : $query -->\n";
		return mysql_query($query, $this->handle);
	}
	public function deleteAllProjectTaskRelationsByProjectId($project_id){
		if(!is_numeric($project_id))
			return false;
		$query = "DELETE FROM ProjectTaskRelations WHERE project_id=$project_id";
		return mysql_query($query, $this->handle);
	}
}
?>