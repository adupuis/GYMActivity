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

class GenyPropertyOption extends GenyDatabaseTools {
	public $id = -1;
	public $content = '';
	public $property_id = -1;
	public function __construct($id = -1){
		parent::__construct("PropertyOptions",
				    "property_option_id");
		$this->id = -1;
		$this->content = '';
		$this->property_id = -1;
		if($id > -1)
			$this->loadPropertyOptionById($id);
	}
	public function deletePropertyOption($id=0){
		if(is_numeric($id)){
			if( $id == 0 && $this->id > 0 )
				$id = $this->id;
			if($id <= 0)
				return -1;
				
			$query = "DELETE FROM PropertyOptions WHERE property_option_id=$id";
			if( $this->config->debug )
				error_log("[GYMActivity::DEBUG] GenyPropertyOption MySQL DELETE query : $query",0);
			if(mysql_query($query,$this->handle))
				return 1;
			else
				return -1;
		}
		return -1;
	}
	public function insertNewPropertyOption($content,$property_id){
		if( ! is_numeric($property_id) )
			return GENYMOBILE_FALSE;
		$query = "INSERT INTO PropertyOptions VALUES(0,'".mysql_real_escape_string($content)."',$property_id)";
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyPropertyOption MySQL query : $query",0);
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return GENYMOBILE_FALSE;
		}
	}
	public function getPropertyOptionsListWithRestrictions($restrictions,$restriction_type = "AND"){
		$last_index = count($restrictions)-1;
		$query = "SELECT property_option_id,property_option_content,property_id FROM PropertyOptions";
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
			error_log("[GYMActivity::DEBUG] GenyPropertyOption MySQL query : $query",0);
		$result = mysql_query($query, $this->handle);
		$p_o_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_p_o = new GenyPropertyOption();
				$tmp_p_o->id = $row[0];
				$tmp_p_o->content = $row[1];
				$tmp_p_o->property_id = $row[2];
				$p_o_list[] = $tmp_p_o;
			}
		}
// 		mysql_close();
		return $p_o_list;
	}
	public function searchPropertyOptions($term){
		$q = mysql_real_escape_string($term);
		return $this->getPropertyOptionsListWithRestrictions( array("property_option_content LIKE '%$q%'") );
	}
	public function getPropertyOptionsByPropertyId($id){
		if( ! is_numeric($id) )
			return GENYMOBILE_FALSE;
		return $this->getPropertyOptionsListWithRestrictions(array("property_id=$id"));
	}
	public function loadPropertyOptionById($id){
		if( ! is_numeric($id) )
			return GENYMOBILE_FALSE;
		$p_os = $this->getPropertyOptionsListWithRestrictions(array("property_option_id=$id"));
		if(isset($p_os[0])) $p_o = $p_os[0];
		if(isset($p_o) && $p_o->id > -1){
			$this->id = $p_o->id;
			$this->content = $p_o->content;
			$this->property_id = $p_o->property_id;
		}
	}
}
?>