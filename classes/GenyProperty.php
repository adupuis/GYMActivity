<?php
//  Copyright (C) 2012 by GENYMOBILE & Arnaud Dupuis
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

class GenyProperty extends GenyDatabaseTools {
	public $id = -1;
	public $name = '';
	public $label = '';
	public $type_id = -1;
	public function __construct($id = -1){
		parent::__construct("Properties",  "property_id");
		$this->id = -1;
		$this->name = '';
		$this->label = '';
		$this->type_id = -1;
		if($id > -1)
			$this->loadPropertyById($id);
	}
	public function deleteProperty($id=0){
		if(is_numeric($id)){
			if( $id == 0 && $this->id > 0 )
				$id = $this->id;
			if($id <= 0)
				return -1;
			
			// Avant de supprimer la property il faut supprimer toute les options et valeurs
			foreach( $this->getPropertyOptions() as $opt ){
				if( $opt->deletePropertyOption() != GENYMOBILE_TRUE )
					return GENYMOBILE_FALSE;
			}
			
			foreach( $this->getPropertyValues() as $val ){
				if( $val->deletePropertyValue() != GENYMOBILE_TRUE )
					return GENYMOBILE_FALSE;
			}
			
			$query = "DELETE FROM Properties WHERE property_id=$id";
			if( $this->config->debug )
				error_log("[GYMActivity::DEBUG] GenyProperty MySQL DELETE query : $query",0);
			if(mysql_query($query,$this->handle))
				return 1;
			else
				return -1;
		}
		return -1;
	}
	public function insertNewProperty($name,$label,$type_id){
		if( ! is_numeric($type_id) )
			return GENYMOBILE_FALSE;
		$query = "INSERT INTO Properties VALUES(0,'".mysql_real_escape_string($name)."','".mysql_real_escape_string($label)."',$type_id)";
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyProperty MySQL query : $query",0);
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return GENYMOBILE_FALSE;
		}
	}
	public function getPropertiesListWithRestrictions($restrictions,$restriction_type = "AND"){
		$last_index = count($restrictions)-1;
		$query = "SELECT property_id,property_name,property_label,property_type_id FROM Properties";
		if(count($restrictions) > 0){
			$query .= " WHERE ";
			$op = mysql_real_escape_string($restriction_type);
			foreach($restrictions as $key => $value) {
				$query .= $value;
				if($key != $last_index){
					$query .= " $op ";
				}
			}
		}
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyProperty MySQL query : $query",0);
		$result = mysql_query($query, $this->handle);
		$prop_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_prop = new GenyProperty();
				$tmp_prop->id = $row[0];
				$tmp_prop->name = $row[1];
				$tmp_prop->label = $row[2];
				$tmp_prop->type_id = $row[3];
				$prop_list[] = $tmp_prop;
			}
		}
// 		mysql_close();
		return $prop_list;
	}
	public function getAllProperties(){
		return $this->getPropertiesListWithRestrictions( array() );
	}
	public function searchProperties($term){
		$q = mysql_real_escape_string($term);
		return $this->getPropertiesListWithRestrictions( array("property_name LIKE '%$q%'", "property_label LIKE '%$q%'"), 'OR' );
	}
	public function loadPropertyById($id){
		if( ! is_numeric($id) )
			return GENYMOBILE_FALSE;
		$props = $this->getPropertiesListWithRestrictions(array("property_id=$id"));
		$prop = $props[0];
		if(isset($prop) && $prop->id > -1){
			$this->id = $prop->id;
			$this->name = $prop->name;
			$this->label = $prop->label;
			$this->type_id = $prop->type_id;
		}
	}
	public function loadPropertyByName($name){
		$props = $this->getPropertiesListWithRestrictions(array("property_name='".mysql_real_escape_string($name)."'"));
		$prop = $props[0];
		if(isset($prop) && $prop->id > -1){
			$this->id = $prop->id;
			$this->name = $prop->name;
			$this->label = $prop->label;
			$this->type_id = $prop->type_id;
		}
	}
	
	// Méthodes de récupération des options et valeurs
	
	public function getPropertyOptions(){
		// id doit être défini
		if( $this->id <= 0 )
			return GENYMOBILE_ERROR;
		
		$option = new GenyPropertyOption();
		return $option->getPropertyOptionsByPropertyId( $this->id );
	}
	
	public function getPropertyValues(){
		// id doit être défini
		if( $this->id <= 0 )
			return GENYMOBILE_ERROR;
		
		$value = new GenyPropertyValue();
		return $value->getPropertyValuesByPropertyId( $this->id );
	}
	
	public function getPropertyType(){
		// type_id doit être défini
		if( $this->type_id <= 0 )
			return GENYMOBILE_ERROR;
		
		$type = new GenyPropertyType( $this->type_id );
		return $type;
	}
	
	// Méthode permettant de définir le nombre de valeurs de la propriété
	
	public function setNumberOfPropertyValues($nb_wanted_of_values=0, $id=0){
		if( is_numeric( $id ) ){
			if( $id == 0 && $this->id > 0 )
				$id = $this->id;
			if( $id <= 0 )
				return -1;

			$tmp_property = new GenyProperty( $id );
			$tmp_property_value = new GenyPropertyValue();
			$tmp_property_values = $tmp_property->getPropertyValues();
			$orinal_nb_of_values = count( $tmp_property_values );
			
			if( $orinal_nb_of_values < $nb_wanted_of_values ) {
				$property_value_number_to_add = $nb_wanted_of_values - $orinal_nb_of_values;
				for( $cpt=0; $cpt < $property_value_number_to_add; $cpt++ ) {
					$tmp_property_value->insertNewPropertyValue( '-1', $id );
				}
			}
			if( $orinal_nb_of_values > $nb_wanted_of_values ) {
				$property_value_number_to_remove = $orinal_nb_of_values - $nb_wanted_of_values;
				for( $cpt=0; $cpt < $property_value_number_to_remove; $cpt++ ) {
					$tmp_property_values[$cpt]->deletePropertyValue();
				}
			}
		}
	}
}
?>