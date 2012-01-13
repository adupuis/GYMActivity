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

class GenyIntranetPage extends GenyDatabaseTools {
	public function __construct($id = -1){
		parent::__construct("IntranetPages",  "intranet_page_id");
		$this->id = -1;
		$this->title = '';
		$this->categorie_id = -1;
		$this->type_id = -1;
		$this->page_content = '';
		if($id > -1)
			$this->loadIntranetPageById($id);
	}
	public function deleteIntranetPage($id=0){
		if(is_numeric($id)){
			if( $id == 0 && $this->id > 0 )
				$id = $this->id;
			if($id <= 0)
				return -1;
			$query = "DELETE FROM IntranetPages WHERE intranet_page_id=$id";
			if( $this->config->debug )
				error_log("[GYMActivity::DEBUG] GenyClient MySQL DELETE query : $query",0);
			if(mysql_query($query,$this->handle))
				return 1;
			else
				return -1;
		}
		return -1;
	}
public function insertNewIntranetPage($id,$title,$categorie_id,$type_id,
				      $page_content){
	$query = "INSERT INTO IntranetPage VALUES($id,'".mysql_real_escape_string($title)."','".$categorie_id."','".$type_id."','".mysql_real_escape_string($page_content)."')";
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyClient MySQL query : $query",0);
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return -1;
		}
	}
	public function getIntranetPagesListWithRestrictions($restrictions){
		// $restrictions is in the form of array("categorie_id=1","type_id=2")
		$last_index = count($restrictions)-1;
		$query = "SELECT intranet_page_id,intranet_page_title,intranet_category_id,intranet_type_id,intranet_page_content FROM IntranetPages";
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
		$page_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_page = new GenyIntranetPage();
				$tmp_page->id = $row[0];
				$tmp_page->title = $row[1];
				$tmp_page->category_id = $row[2];
				$tmp_page->type_id = $row[3];
				$tmp_page->page_content = $row[4];
				$page_list[] = $tmp_page;
			}
		}
// 		mysql_close();
		return $page_list;
	}
	public function getAllIntranetPages(){
		return $this->getIntranetPagesListWithRestrictions( array() );
	}
	public function searchIntranetPages($term){
		$q = mysql_real_escape_string($term);
		return $this->getIntranetPagesListWithRestrictions( array("intranet_page_title LIKE '%$q%' or intranet_page_content '%$q%'") );
	}
	public function loadIntranetPageByTitle($name){
		$pages = $this->getIntranetPagesListWithRestrictions(array("intranet_page_title='".mysql_real_escape_string($name)."'"));
		if(count($pages) == 0)
			return;
		$page = $pages[0];
		if(isset($page) && $page->id > -1){
			$this->id = $page->id;
			$this->title = $page->title;
			$this->category_id = $page->category_id;
			$this->type_id = $page->type_id;
			$this->page_content = $page->page_content;
		}
	}
	public function loadIntranetPagesByCategoryAndType($cat_id, $type_id){
		$pages = $this->getIntranetPagesListWithRestrictions(array("intranet_category_id='".mysql_real_escape_string($cat_id)."'","intranet_type_id='".mysql_real_escape_string($type_id)."'"));
		$page_list = array();
		foreach($pages as $page) {
			$tmp_page = new GenyIntranetPage();
			$tmp_page->id = $page->id;
			$tmp_page->title = $page->title;
			$tmp_page->category_id = $page->category_id;
			$tmp_page->type_id = $page->type_id;
			$tmp_page->page_content = $page->page_content;
			$page_list[] = $tmp_page;
		}
		return $page_list;
	}
	public function loadIntranetPagesByCategorie($cat_id){
		$pages = $this->getIntranetPagesListWithRestrictions(array("intranet_category_id='".mysql_real_escape_string($cat_id)."'"));
		$page_list = array();
		foreach($pages as $page) {
			$tmp_page = new GenyIntranetPage();
			$tmp_page->id = $page->id;
			$tmp_page->title = $page->title;
			$tmp_page->category_id = $page->category_id;
			$tmp_page->type_id = $page->type_id;
			$tmp_page->page_content = $page->page_content;
			$page_list[] = $tmp_page;
		}
		return $page_list;
	}

	public function loadIntranetPagesById($id){
		$pages = $this->getIntranetPagesListWithRestrictions(array("intranet_page_id=".mysql_real_escape_string($id)));
		$page = $pages[0];
		if(isset($page) && $page->id > -1){
			$this->id = $page->id;
			$this->title = $page->title;
			$this->category_id = $page->category_id;
			$this->type_id = $page->type_id;
			$this->page_content = $page->page_content;
		}
	}
}
?>