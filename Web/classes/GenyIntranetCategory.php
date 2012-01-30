<?php
//  Copyright (C) 2011 by GENYMOBILE

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

class GenyIntranetCategory extends GenyDatabaseTools {
	
	public function __construct($id = -1){
		parent::__construct("IntranetCategories",  "intranet_category_id");
		$this->id = -1;
		$this->name = '';
		$this->description = '';
		if($id > -1)
			$this->loadIntranetCategoryById($id);
	}
	
	public function insertNewIntranetCategory($id,$name,$desc){
		$query = "INSERT INTO IntranetCategories VALUES($id,'".mysql_real_escape_string($name)."','".mysql_real_escape_string($desc)."')";
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyClient MySQL query : $query",0);
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return -1;
		}
	}
	
	public function removeIntranetCategory($id=0){
		if(is_numeric($id)){
			if( $id == 0 && $this->id > 0 )
				$id = $this->id;
			if($id <= 0)
				return -1;
			//delete type
			$p_object = new GenyIntranetType();
			foreach( $p_object->getIntranetTypesByCategory($id) as $p ){
				if( $p->removeIntranetType() <= 0 )
					return -1;
			}
			//delete page
			$p_object = new GenyIntranetPage();
			foreach( $p_object->getIntranetPagesByCategory($id) as $p ){
				if( $p->removeIntranetPage() <= 0 )
					return -1;
			}

			$query = "DELETE FROM IntranetCategories WHERE intranet_category_id=$id";
			if( $this->config->debug )
				error_log("[GYMActivity::DEBUG] GenyClient MySQL DELETE query : $query",0);
			if(mysql_query($query,$this->handle))
				return 1;
			else
				return -1;
		}
		return -1;
	}
	
	public function getIntranetCategoryListWithRestrictions($restrictions){
		// $restrictions is in the form of array("project_id=1","project_status_id=2")
		$last_index = count($restrictions)-1;
		$query = "SELECT intranet_category_id,intranet_category_name,intranet_category_description FROM IntranetCategories";
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
			error_log("[GYMActivity::DEBUG] GenyClient MySQL query : $query",0);
		$result = mysql_query($query, $this->handle);
		$cat_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_cat = new GenyIntranetCategory();
				$tmp_cat->id = $row[0];
				$tmp_cat->name = $row[1];
				$tmp_cat->description = $row[2];
				$cat_list[] = $tmp_cat;
			}
		}
// 		mysql_close();
		return $cat_list;
	}
	
	public function getAllIntranetCategories(){
		return $this->getIntranetCategoryListWithRestrictions( array() );
	}
	
	public function searchIntranetCategory($term){
		$q = mysql_real_escape_string($term);
		return $this->getIntranetCategoryListWithRestrictions( array("intranet_category_name LIKE '%$q%' or intranet_category_description LIKE '%$q%'") );
	}
	
	public function loadIntranetCategoryById($id){
		$cats = $this->getIntranetCategoryListWithRestrictions(array("intranet_category_id=".mysql_real_escape_string($id)));
		$cat = $cats[0];
		if(isset($cat) && $cat->id > -1){
			$this->id = $cat->id;
			$this->name = $cat->name;
			$this->description = $cat->description;
		}
	}
	
	public function loadIntranetCategoryByName($name){
		$cats = $this->getIntranetCategoryListWithRestrictions(array("intranet_category_name='".mysql_real_escape_string($name)."'"));

		if(count($cats) == 0)
			return;
		$cat = $cats[0];
		if(isset($cat) && $cat->id > -1){
			$this->id = $cat->id;
			$this->name = $cat->name;
			$this->description = $cat->description;
		}
	}
}
?>
