<?php

include_once 'GenyWebConfig.php';

class GenyProject {
	private $updates = array();
	public function __construct($id = -1){
		$this->config = new GenyWebConfig();
		$this->handle = mysql_connect($this->config->db_host,$this->config->db_user,$this->config->db_password);
		mysql_select_db("GYMActivity");
		mysql_query("SET NAMES 'utf8'");
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
	public function insertNewProject($project_name,$project_description,$project_client,$project_location,$project_start_date,$project_end_date,$project_type_id,$project_status_id){
		$query = "INSERT INTO Projects VALUES(NULL,'".mysql_real_escape_string($project_name)."','".mysql_real_escape_string($project_description)."',".mysql_real_escape_string($project_client).",'".mysql_real_escape_string($project_location)."','".mysql_real_escape_string($project_start_date)."','".mysql_real_escape_string($project_end_date)."',".mysql_real_escape_string($project_type_id).",".mysql_real_escape_string($project_status_id).")";
		if( $this->config->debug )
			echo "<!-- DEBUG: GenyProject MySQL query : $query -->\n";
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
			echo "<!-- DEBUG: GenyProject MySQL query : $query -->\n";
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
			echo "<!-- DEBUG: GenyProject MySQL query : $query -->\n";
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
		$query = "UPDATE Projects SET ";
		foreach($this->updates as $up) {
			$query .= "$up,";
		}
		$query = rtrim($query, ",");
		$query .= " WHERE project_id=".$this->id;
		if( $this->config->debug )
			echo "<!-- DEBUG: GenyProject MySQL query : $query -->\n";
		return mysql_query($query, $this->handle);
	}
	public function getAllProjects(){
		return $this->getProjectsListWithRestrictions( array() );
	}
	
	public function getProjectsByStatus($status){
		return $this->getProjectsListWithRestrictions(array("project_status_id=".mysql_real_escape_string($status)));
	}
	public function getProjectsByType($type){
		return $this->getProjectsListWithRestrictions(array("project_type_id=".mysql_real_escape_string($type)));
	}
	public function getProjectsByClient($client_id){
		return $this->getProjectsListWithRestrictions(array("client_id=".mysql_real_escape_string($client_id)));
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