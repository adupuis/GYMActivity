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

class GenyProject extends GenyDatabaseTools {
	public $id = -1;
	public $name = '';
	public $description = '';
	public $client_id = -1;
	public $location = '';
	public $start_date = '0000-00-00';
	public $end_date = '0000-00-00';
	public $type_id = -1;
	public $status_id = -1;
	public function __construct($id = -1){
		parent::__construct("Projects",  "project_id");
		$this->id = -1;
		$this->name = '';
		$this->description = '';
		$this->client_id = -1;
		$this->location = '';
		$this->start_date = '0000-00-00';
		$this->end_date = '0000-00-00';
		$this->type_id = -1;
		$this->status_id = -1;
		if($id > -1)
			$this->loadProjectById($id);
	}
	public function deleteProject($id=0){
		if(is_numeric($id)){
			if( $id == 0 && $this->id > 0 )
				$id = $this->id;
			if($id <= 0)
				return -1;
			
			// Avant de supprimer le projet il faut supprimer les associations de tâches/projets.
			$project_task_relations = new GenyProjectTaskRelation();
			foreach( $project_task_relations->getProjectTaskRelationsListByProjectId($id) as $ptr ){
				if( $ptr->deleteProjectTaskRelation() <= 0 ){
					return -1;
				}
			}
			
			// Il faut ensuite supprimer les affectations.
			$assignements = new GenyAssignement();
			foreach( $assignements->getAssignementsListByProjectId($id) as $ass ){
				if( $ass->deleteAssignement() <= 0 ) // Celà va déclencher la suppression des activities
					return -1;
			}
			
			$query = "DELETE FROM Projects WHERE project_id=$id";
			if( $this->config->debug )
				error_log("[GYMActivity::DEBUG] GenyProject MySQL DELETE query : $query",0);
			if(mysql_query($query,$this->handle))
				return 1;
			else
				return -1;
		}
		return -1;
	}
	public function insertNewProject($project_name,$project_description,$project_client,$project_location,$project_start_date,$project_end_date,$project_type_id,$project_status_id){
		$query = "INSERT INTO Projects VALUES(NULL,'".mysql_real_escape_string($project_name)."','".mysql_real_escape_string($project_description)."',".mysql_real_escape_string($project_client).",'".mysql_real_escape_string($project_location)."','".mysql_real_escape_string($project_start_date)."','".mysql_real_escape_string($project_end_date)."',".mysql_real_escape_string($project_type_id).",".mysql_real_escape_string($project_status_id).")";
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyProject MySQL query : $query",0);
		if(mysql_query($query,$this->handle))
			return mysql_insert_id($this->handle);
		else
			return -1;
	}
	public function getProjectsListWithRestrictions($restrictions){
		// $restrictions is in the form of array("project_id=1","project_status_id=2")
		$last_index = count($restrictions)-1;
		$query = "SELECT project_id,project_name,project_description,client_id,project_location,project_start_date,project_end_date,project_type_id,project_status_id FROM Projects";
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
			error_log("[GYMActivity::DEBUG] GenyProject MySQL query : $query",0);
		$result = mysql_query($query, $this->handle);
		$project_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_project = new GenyProject();
				$tmp_project->id = $row[0];
				$tmp_project->name = $row[1];
				$tmp_project->description = $row[2];
				$tmp_project->client_id = $row[3];
				$tmp_project->location = $row[4];
				$tmp_project->start_date = $row[5];
				$tmp_project->end_date = $row[6];
				$tmp_project->type_id = $row[7];
				$tmp_project->status_id = $row[8];
				$project_list[] = $tmp_project;
			}
		}
// 		mysql_close();
		return $project_list;
	}
	public function getLocationsList(){
		$query = "SELECT DISTINCT project_location FROM Projects";
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyProject MySQL query : $query",0);
		$result = mysql_query($query, $this->handle);
		$project_location_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$project_location_list[] = $row[0];
			}
		}
// 		mysql_close();
		return $project_location_list;
	}
	public function getAllProjects(){
		return $this->getProjectsListWithRestrictions( array() );
	}
	
	public function getProjectsByStatusId($status){
		if(!is_numeric($status))
			return -1;
		return $this->getProjectsListWithRestrictions(array("project_status_id=$status"));
	}
	public function getProjectsByTypeId($type){
		if(!is_numeric($type))
			return -1;
		return $this->getProjectsListWithRestrictions(array("project_type_id=$type"));
	}
	public function getProjectsByClientId($client_id){
		if(!is_numeric($client_id))
			return -1;
		return $this->getProjectsListWithRestrictions(array("client_id=$client_id"));
	}
	public function loadProjectByName($name){
		$projects = $this->getProjectsListWithRestrictions(array("project_name='".mysql_real_escape_string($name)."'"));
		$project = $projects[0];
		if(isset($project) && $project->id > -1){
			$this->id = $project->id;
			$this->name = $project->name;
			$this->description = $project->description;
			$this->client_id = $project->client_id;
			$this->location = $project->location;
			$this->start_date = $project->start_date;
			$this->end_date = $project->end_date;
			$this->type_id = $project->type_id;
			$this->status_id = $project->status_id;
		}
	}
	public function loadProjectById($id){
		$projects = $this->getProjectsListWithRestrictions(array("project_id=".mysql_real_escape_string($id)));
		$project = $projects[0];
		if(isset($project) && $project->id > -1){
			$this->id = $project->id;
			$this->name = $project->name;
			$this->description = $project->description;
			$this->client_id = $project->client_id;
			$this->location = $project->location;
			$this->start_date = $project->start_date;
			$this->end_date = $project->end_date;
			$this->type_id = $project->type_id;
			$this->status_id = $project->status_id;
		}
	}
}
?>