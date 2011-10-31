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

class GenyPropertyType {
	private $updates = array();
	public function __construct($id = -1){
		$this->config = new GenyWebConfig();
		$this->handle = mysql_connect($this->config->db_host,$this->config->db_user,$this->config->db_password);
		mysql_select_db($this->config->db_name);
		mysql_query("SET NAMES 'utf8'");
		$this->id = -1;
		$this->shortname = '';
		$this->name = '';
		if($id > -1)
			$this->loadPropertyTypeById($id);
	}
	public function deletePropertyType($id=0){
		if(is_numeric($id)){
			if( $id == 0 && $this->id > 0 )
				$id = $this->id;
			if($id <= 0)
				return -1;
				
			$query = "DELETE FROM PropertyTypes WHERE property_type_id=$id";
			if( $this->config->debug )
				error_log("[GYMActivity::DEBUG] GenyPropertyType MySQL DELETE query : $query",0);
			if(mysql_query($query,$this->handle))
				return 1;
			else
				return -1;
		}
		return -1;
	}
	public function insertNewPropertyType($shortname,$name){
		$query = "INSERT INTO PropertyTypes VALUES(0,'".mysql_real_escape_string($shortname)."','".mysql_real_escape_string($name)."')";
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyPropertyType MySQL query : $query",0);
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return GENYMOBILE_FALSE;
		}
	}
	public function getPropertyTypesListWithRestrictions($restrictions){
		$last_index = count($restrictions)-1;
		$query = "SELECT property_type_id,property_type_shortname,property_type_name FROM PropertyTypes";
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
			error_log("[GYMActivity::DEBUG] GenyPropertyType MySQL query : $query",0);
		$result = mysql_query($query, $this->handle);
		$p_t_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_p_t = new GenyPropertyType();
				$tmp_p_t->id = $row[0];
				$tmp_p_t->shortname = $row[1];
				$tmp_p_t->name = $row[2];
				$p_t_list[] = $tmp_p_t;
			}
		}
// 		mysql_close();
		return $p_t_list;
	}
	public function searchPropertyTypes($term){
		$q = mysql_real_escape_string($term);
		return $this->getPropertyTypesListWithRestrictions array("property_type_name LIKE '%$q%'") );
	}
	public function loadPropertyTypesById($id){
		if( ! is_numeric($id) )
			return GENYMOBILE_FALSE;
		$p_ts = $this->getPropertyTypesListWithRestrictions(array("property_type_id=$id"));
		$p_t = $p_ts[0];
		if(isset($p_t) && $p_t->id > -1){
			$this->id = $p_t->id;
			$this->shortname = $p_t->shortname;
			$this->name = $p_t->name;
		}
	}
	public function loadPropertyTypeByShortName($shortname){
		$p_ts = $this->getPropertyTypesListWithRestrictions(array("property_type_shortname=".mysql_real_escape_string($shortname)));
		$p_t = $p_ts[0];
		if(isset($p_t) && $p_t->id > -1){
			$this->id = $p_t->id;
			$this->shortname = $p_t->shortname;
			$this->name = $p_t->name;
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
		$query = "UPDATE PropertyTypes SET ";
		foreach($this->updates as $up) {
			$query .= "$up,";
		}
		$query = rtrim($query, ",");
		$query .= " WHERE property_value_id=".$this->id;
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyPropertyType MySQL query : $query",0);
		return mysql_query($query, $this->handle);
	}
}
?>