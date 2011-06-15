<?php

include_once 'GenyWebConfig.php';

class GenyRightsGroup {
	private $updates = array();
	public function __construct($id = -1){
		$this->config = new GenyWebConfig();
		$this->handle = mysql_connect($this->config->db_host,$this->config->db_user,$this->config->db_password);
		mysql_select_db("GYMActivity");
		mysql_query("SET NAMES 'utf8'");
		$this->id = -1;
		$this->name = '';
		if($id > -1)
			$this->loadRightsGroupById($id);
	}
	public function insertNewRightsGroup($id,$name,$description){
		$query = "INSERT INTO RightsGroups VALUES($id,'".mysql_real_escape_string($name)."','".mysql_real_escape_string($description)."')";
		if( $this->config->debug )
			echo "<!-- DEBUG: GenyRightsGroups MySQL query : $query -->\n";
		if(mysql_query($query,$this->handle))
			return mysql_insert_id($this->handle);
		else
			return -1;
	}
	public function getRightsGroupsListWithRestrictions($restrictions){
		// $restrictions is in the form of array("project_id=1","project_status_id=2")
		$last_index = count($restrictions)-1;
		$query = "SELECT rights_group_id,rights_group_name,rights_group_description FROM RightsGroups";
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
			echo "<!-- DEBUG: GenyRightsGroup MySQL query : $query -->\n";
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
	public function getAllRightsGroups(){
		return $this->getRightsGroupsListWithRestrictions( array() );
	}
	public function loadRightsGroupByName($name){
		$objects = $this->getRightsGroupsListWithRestrictions(array("rights_group_name='".mysql_real_escape_string($id)."'"));
		$object = $objects[0];
		if(isset($object) && $object->id > -1){
			$this->id = $object->id;
			$this->name = $object->name;
			$this->description = $object->description;
		}
	}
	public function loadRightsGroupById($id){
		$objects = $this->getRightsGroupListWithRestrictions(array("rights_group_id=".mysql_real_escape_string($id)));
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
		$query = "UPDATE RightsGroups SET ";
		foreach($this->updates as $up) {
			$query .= "$up,";
		}
		$query = rtrim($query, ",");
		$query .= " WHERE rights_group_id=".$this->id;
		if( $this->config->debug )
			echo "<!-- DEBUG: GenyRightsGroup MySQL query : $query -->\n";
		return mysql_query($query, $this->handle);
	}
}
?>