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

class GenyProjectType extends GenyDatabaseTools {
	public $id = -1;
	public $name = '';
	public function __construct($id = -1){
		parent::__construct("ProjectTypes",  "project_type_id");
		$this->id = -1;
		$this->name = '';
		if($id > -1)
			$this->loadProjectTypeById($id);
	}
	public function insertNewProjectType($id,$name,$description){
		$query = "INSERT INTO ProjectTypes VALUES($id,'".mysql_real_escape_string($name)."','".mysql_real_escape_string($description)."')";
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyProjectType MySQL query : $query",0);
		if(mysql_query($query,$this->handle))
			return mysql_insert_id($this->handle);
		else
			return -1;
	}
	public function getProjectTypesListWithRestrictions($restrictions){
		// $restrictions is in the form of array("project_id=1","project_status_id=2")
		$last_index = count($restrictions)-1;
		$query = "SELECT project_type_id,project_type_name,project_type_description FROM ProjectTypes";
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
			error_log("[GYMActivity::DEBUG] GenyProjectType MySQL query : $query",0);
		$result = mysql_query($query, $this->handle);
		$object_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_object = new GenyProjectType();
				$tmp_object->id = $row[0];
				$tmp_object->name = $row[1];
				$tmp_object->description = $row[2];
				$object_list[] = $tmp_object;
			}
		}
// 		mysql_close();
		return $object_list;
	}
	public function getAllProjectTypes(){
		return $this->getProjectTypesListWithRestrictions( array() );
	}
	public function loadProjectTypeByName($name){
		$objects = $this->getProjectTypesListWithRestrictions(array("project_type_name='".mysql_real_escape_string($name)."'"));
		$object = $objects[0];
		if(isset($object) && $object->id > -1){
			$this->id = $object->id;
			$this->name = $object->name;
			$this->description = $object->description;
		}
	}
	public function loadProjectTypeById($id){
		$objects = $this->getProjectTypesListWithRestrictions(array("project_type_id=".mysql_real_escape_string($id)));
		$object = $objects[0];
		if(isset($object) && $object->id > -1){
			$this->id = $object->id;
			$this->name = $object->name;
			$this->description = $object->description;
		}
	}
	public function getProjectTypeColor($id=0){
		if($id > 0 && is_numeric($id)) {
			$this->loadProjectTypeById($id);
		}
		if($this->id > 0) {
			$geny_property = new GenyProperty();
			$geny_properties = $geny_property->searchProperties( "color_project_type_". $this->id );
			if( sizeof( $geny_properties == 1 ) ) {
				$geny_property = $geny_properties[0];
				$geny_property_values = $geny_property->getPropertyValues();
				if( sizeof( $geny_property_values ) == 1 ) {
					return $geny_property_values[0]->content;
				}
				else if( sizeof( $geny_property_values ) > 1 ) {
					if($this->config->debug) {
						GenyTools::debug("Attention : au moins 2 couleurs sont définies pour le type de projet $geny_project->type_id ! Seule la première couleur a été prise en compte");
					}
					return $geny_property_values[0]->content;
				}
				else {
					if($this->config->debug) {
						GenyTools::debug("Attention : aucune couleur n'est définie pour le type de projet bien qu'il y ait une propriété associée $geny_project->type_id ! Couleur par défaut : blanc");
					}
					return "white";
				}
			}
			else if( sizeof( $geny_properties == 0 ) ) {
				if($this->config->debug) {
					GenyTools::debug("Erreur interne : Aucune propriété associé au type de projet $geny_project->type_id ! Couleur par défaut : blanc");
				}
				return "white";
			}
			else {
				if($this->config->debug) {
					GenyTools::debug("Erreur interne : Plusieurs couleurs sont définies pour le type de projet $geny_project->type_id ! Couleur par défaut : blanc");
				}
				return "white";
			}
		}
	}
}
?>