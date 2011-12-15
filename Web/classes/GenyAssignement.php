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

class GenyAssignement extends GenyDatabaseTools {
	public function __construct($id = -1){
		parent::__construct("Assignements",  "assignement_id", $id);
		$this->profile_id = -1;
		$this->project_id = -1;
		$this->overtime_allowed = false;
		$this->is_active = false;
		if($id > -1)
			$this->loadAssignementById($id);
	}
	public function insertNewAssignement($id,$profile_id,$project_id,$overtime_allowed='false',$is_active='true'){
		$query = "INSERT INTO Assignements VALUES($id,$profile_id,$project_id,$overtime_allowed,$is_active)";
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyAssignements MySQL query : $query",0);
		if(mysql_query($query,$this->handle))
			return mysql_insert_id($this->handle);
		else
			return -1;
	}
	public function getAssignementsListWithRestrictions($restrictions){
		// $restrictions is in the form of array("profile_id=1","project_status_id=2")
		$last_index = count($restrictions)-1;
		$query = "SELECT assignement_id,profile_id,project_id,assignement_overtime_allowed,assignement_is_active FROM Assignements";
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
			error_log("[GYMActivity::DEBUG] GenyAssignement MySQL query : $query",0);
		$result = mysql_query($query, $this->handle);
		$object_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_object = new GenyAssignement();
				$tmp_object->id = $row[0];
				$tmp_object->profile_id = $row[1];
				$tmp_object->project_id = $row[2];
				$tmp_object->overtime_allowed = $row[3];
				$tmp_object->is_active = $row[4];
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
	public function getActiveAssignementsListByProfileId($id){
		return $this->getAssignementsListWithRestrictions(array("profile_id=$id","assignement_is_active=true"));
	}
	public function getInactiveAssignementsListByProfileId($id){
		return $this->getAssignementsListWithRestrictions(array("profile_id=$id","assignement_is_active=false"));
	}
	public function getAssignementsListByProjectId($id){
		return $this->getAssignementsListWithRestrictions(array("project_id=$id"));
	}
	public function getActiveAssignementsListByProjectId($id){
		return $this->getAssignementsListWithRestrictions(array("project_id=$id","assignement_is_active=true"));
	}
	public function getInactiveAssignementsListByProjectId($id){
		return $this->getAssignementsListWithRestrictions(array("project_id=$id","assignement_is_active=false"));
	}
	public function getAssignementsListByProjectIdAndProfileId($proj_id,$prof_id){
		if( !is_numeric($proj_id) || ! is_numeric($prof_id) )
			return -1;
		// This should return only one record as the database should not contains 2 assignements for a unique profile and a unique project.
		return $this->getAssignementsListWithRestrictions(array("profile_id=$prof_id","project_id=$proj_id"));
	}
	public function loadAssignementById($id){
		$objects = $this->getAssignementsListWithRestrictions(array("assignement_id=$id"));
		$object = $objects[0];
		if(isset($object) && $object->id > -1){
			$this->id = $object->id;
			$this->profile_id = $object->profile_id;
			$this->project_id = $object->project_id;
			$this->overtime_allowed = $object->overtime_allowed;
			$this->is_active = $object->is_active;
		}
	}
	public function setActive(){
		$this->updateBool("assignement_is_active",'true');
		if($this->commitUpdates())
			return 1;
		else
			return -1;
	}
	public function setInactive(){
		$this->updateBool("assignement_is_active",'false');
		if($this->commitUpdates())
			return 1;
		else
			return -1;
	}
	public function deleteAssignement($id=0){
		if(is_numeric($id)){
			if( $id == 0 && $this->id > 0 )
				$id = $this->id;
			if($id <= 0)
				return -1;
				
			// Avant de supprimer un assignement il faut supprimer toutes les activity qui y sont attachés.
			$tmp_activity = new GenyActivity();
			foreach( $tmp_activity->getActivitiesListByAssignementId($id) as $a ){
				if( $a->deleteActivity() <= 0 )
					return -1;
			}
			
			$query = "DELETE FROM Assignements WHERE assignement_id=$id";
			if( $this->config->debug )
				error_log("[GYMActivity::DEBUG] GenyAssignement MySQL DELETE query : $query",0);
			if(mysql_query($query,$this->handle))
				return 1;
			else
				return -1;
		}
		return -1;
	}
}
?>