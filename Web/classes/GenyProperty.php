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

class GenyProperty {
	private $updates = array();
	public function __construct($id = -1){
		$this->config = new GenyWebConfig();
		$this->handle = mysql_connect($this->config->db_host,$this->config->db_user,$this->config->db_password);
		mysql_select_db($this->config->db_name);
		mysql_query("SET NAMES 'utf8'");
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
	public function searchProperties($term){
		$q = mysql_real_escape_string($term);
		return $this->getPropertiesListWithRestrictions array("property_name LIKE '%$q%'", "property_label LIKE '%$q%'"), 'OR' );
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
	public function loadPropertyTypeByName($name){
		$props = $this->getPropertiesListWithRestrictions(array("property_name=".mysql_real_escape_string($name)));
		$prop = $props[0];
		if(isset($prop) && $prop->id > -1){
			$this->id = $prop->id;
			$this->name = $prop->name;
			$this->label = $prop->label;
			$this->type_id = $prop->type_id;
		}
	}
	public function updateString($key,$value){
		$this->updates[] = "$key='".mysql_real_escape_string($value)."'";
	}
	public function updateInt($key,$value){
		if( ! is_numeric($value) )
			return GENYMOBILE_FALSE;
		$this->updates[] = "$key=$value";
	}
	public function updateBool($key,$value){
		$this->updates[] = "$key=".mysql_real_escape_string($value)."";
	}
	public function commitUpdates(){
		$query = "UPDATE Properties SET ";
		foreach($this->updates as $up) {
			$query .= "$up,";
		}
		$query = rtrim($query, ",");
		$query .= " WHERE property_id=".$this->id;
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyProperty MySQL query : $query",0);
		return mysql_query($query, $this->handle);
	}
}
?>