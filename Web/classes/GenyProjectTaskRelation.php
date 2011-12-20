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

class GenyProjectTaskRelation extends GenyDatabaseTools {
	public function __construct($id = -1){
		parent::__construct("ProjectTaskRelations",  
				    "project_task_relation_id");
		$this->id = -1;
		$this->project_id = -1;
		$this->task_id = -1;
		if($id > -1)
			$this->loadProjectTaskRelationById($id);
	}
	public function deleteProjectTaskRelation($id=0){
		if(is_numeric($id)){
			if( $id == 0 && $this->id > 0 )
				$id = $this->id;
			if($id <= 0)
				return -1;
			
			$query = "DELETE FROM ProjectTaskRelations WHERE project_task_relation_id=$id";
			if( $this->config->debug )
				error_log("[GYMActivity::DEBUG] GenyProjectTaskRelation MySQL DELETE query : $query",0);
			if(mysql_query($query,$this->handle))
				return 1;
			else
				return -1;
		}
		return -1;
	}
	public function insertNewProjectTaskRelation($project_id,$task_id){
		if(!is_numeric($project_id))
			return -1;
		if(!is_numeric($task_id))
			return -1;
		$query = "INSERT INTO ProjectTaskRelations VALUES(0,$project_id,$task_id)";
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyProjectTaskRelation MySQL query : $query",0);
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
			error_log("[GYMActivity::DEBUG] GenyProjectTaskRelation MySQL query : $query",0);
		$result = mysql_query($query, $this->handle);
		$object_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_object = new GenyProjectTaskRelation();
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
		if(!is_numeric($id))
			return -1;
		return $this->getProjectTaskRelationsListWithRestrictions(array("project_id=$id"));
	}
	public function getProjectTaskRelationsListByTaskId($id){
		if(!is_numeric($id))
			return -1;
		return $this->getProjectTaskRelationsListWithRestrictions(array("task_id=$id"));
	}
	public function loadProjectTaskRelationById($id){
		if(!is_numeric($id))
			return -1;
		$objects = $this->getProjectTaskRelationListWithRestrictions(array("project_task_relation_id=$id"));
		$object = $objects[0];
		if(isset($object) && $object->id > -1){
			$this->id = $object->id;
			$this->project_id = $object->project_id;
			$this->task_id = $object->task_id;
		}
	}
	public function deleteAllProjectTaskRelationsByProjectId($project_id){
		if(!is_numeric($project_id))
			return false;
		$query = "DELETE FROM ProjectTaskRelations WHERE project_id=$project_id";
		return mysql_query($query, $this->handle);
	}
}
?>