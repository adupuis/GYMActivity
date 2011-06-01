<?php

include_once 'GenyWebConfig.php';

class GenyProjectStatus {
	private $updates = array();
	public function __construct($id = -1){
		$this->config = new GenyWebConfig();
		$this->handle = mysql_connect($this->config->db_host,$this->config->db_user,$this->config->db_password);
		mysql_select_db("GYMActivity");
		mysql_query("SET NAMES 'utf8'");
		$this->id = -1;
		$this->name = '';
		if($id > -1)
			$this->loadProjectStatusById($id);
	}
	public function insertNewProjectStatus($id,$name,$description){
		$query = "INSERT INTO ProjectStatus VALUES($id,'".mysql_real_escape_string($name)."','".mysql_real_escape_string($description)."')";
		if( $this->config->debug )
			echo "<!-- DEBUG: GenyProjectStatus MySQL query : $query -->\n";
		return mysql_query($query,$this->handle);
	}
	public function getProjectStatusListWithRestrictions($restrictions){
		// $restrictions is in the form of array("project_id=1","project_status_id=2")
		$last_index = count($restrictions)-1;
		$query = "SELECT project_status_id,project_status_name,project_status_description FROM ProjectStatus";
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
			echo "<!-- DEBUG: GenyProjectStatus MySQL query : $query -->\n";
		$result = mysql_query($query, $this->handle);
		$object_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_object = new GenyClient();
				$tmp_object->id = $row[0];
				$tmp_object->name = $row[1];
				$tmp_object->description = $row[2];
				$object_list[] = $tmp_object;
			}
		}
		mysql_close();
		return $object_list;
	}
	public function getAllProjectStatus(){
		return $this->getProjectStatusListWithRestrictions( array() );
	}
	public function loadProjectStatusByName($name){
		$objects = $this->getProjectStatusListWithRestrictions(array("project_status_name='".mysql_real_escape_string($name)."'"));
		$object = $objects[0];
		if(isset($object) && $object->id > -1){
			$this->id = $object->id;
			$this->name = $object->name;
			$this->description = $object->description;
		}
	}
	public function loadProjectStatusById($id){
		$objects = $this->getProjectStatusListWithRestrictions(array("project_status_id=".mysql_real_escape_string($id)));
		$object = $objects[0];
		if(isset($object) && $object->id > -1){
			$this->id = $object->id;
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
		$query = "UPDATE ProjectStatus SET ";
		foreach($this->updates as $up) {
			$query .= "$up,";
		}
		$query = rtrim($query, ",");
		$query .= " WHERE project_status_id=".$this->id;
		if( $this->config->debug )
			echo "<!-- DEBUG: GenyProjectStatus MySQL query : $query -->\n";
		return mysql_query($query, $this->handle);
	}
}
?>