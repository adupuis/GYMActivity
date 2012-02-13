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
	public $id = -1;
	public $name = '';
	public $description = '';
	public $image_name = '';
	
	public function __construct( $id = -1 ) {
		parent::__construct( "IntranetCategories", "intranet_category_id" );
		$this->id = -1;
		$this->name = '';
		$this->description = '';
		$this->image_name = '';
		if( $id > -1 ) {
			$this->loadIntranetCategoryById( $id );
		}
	}
	
	public function insertNewIntranetCategory( $id, $name, $description ) {
		$query = "INSERT INTO IntranetCategories VALUES($id,'".mysql_real_escape_string( $name )."','".mysql_real_escape_string( $description )."','".mysql_real_escape_string( $image_name )."')";
		if( $this->config->debug ) {
			error_log( "[GYMActivity::DEBUG] GenyIntranetCategory MySQL query : $query", 0 );
		}
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return -1;
		}
	}
	
	public function removeIntranetCategory( $id = 0 ) {
		if( is_numeric( $id ) ) {
			if( $id == 0 && $this->id > 0 ) {
				$id = $this->id;
			}
			if( $id <= 0 ) {
				return -1;
			}
			// Delete intranet_type
			$p_object = new GenyIntranetType();
			foreach( $p_object->getIntranetTypesByCategory( $id ) as $p ) {
				if( $p->removeIntranetType() <= 0 ) {
					return -1;
				}
			}
			// Delete intranet_page
			$p_object = new GenyIntranetPage();
			foreach( $p_object->getIntranetPagesByCategory( $id ) as $p ) {
				if( $p->removeIntranetPage() <= 0 ) {
					return -1;
				}
			}

			$query = "DELETE FROM IntranetCategories WHERE intranet_category_id=$id";
			if( $this->config->debug ) {
				error_log( "[GYMActivity::DEBUG] GenyIntranetCategory MySQL DELETE query : $query", 0 );
			}
			if( mysql_query( $query, $this->handle ) ) {
				return 1;
			}
			else {
				return -1;
			}
		}
		return -1;
	}
	
	public function getIntranetCategoryListWithRestrictions( $restrictions ) {
		// $restrictions is in the form of array("intranet_category_id=1")
		$last_index = count( $restrictions ) - 1;
		$query = "SELECT intranet_category_id,intranet_category_name,intranet_category_description,intranet_category_image_name FROM IntranetCategories";
		if( count( $restrictions ) > 0 ) {
			$query .= " WHERE ";
			foreach( $restrictions as $key => $value ) {
				$query .= $value;
				if( $key != $last_index ) {
					$query .= " AND ";
				}
			}
		}
		if( $this->config->debug ) {
			error_log( "[GYMActivity::DEBUG] GenyIntranetCategory MySQL query : $query", 0 );
		}
		$result = mysql_query( $query, $this->handle );
		$intranet_category_list = array();
		if( mysql_num_rows( $result ) != 0 ) {
			while( $row = mysql_fetch_row( $result ) ) {
				$tmp_intranet_category = new GenyIntranetCategory();
				$tmp_intranet_category->id = $row[0];
				$tmp_intranet_category->name = $row[1];
				$tmp_intranet_category->description = $row[2];
				$tmp_intranet_category->image_name = $row[3];
				$intranet_category_list[] = $tmp_intranet_category;
			}
		}
// 		mysql_close();
		return $intranet_category_list;
	}
	
	public function getAllIntranetCategories() {
		return $this->getIntranetCategoryListWithRestrictions( array() );
	}
	
	public function searchIntranetCategory( $term ) {
		$q = mysql_real_escape_string( $term );
		return $this->getIntranetCategoryListWithRestrictions( array("intranet_category_name LIKE '%$q%' or intranet_category_description LIKE '%$q%'") );
	}
	
	public function loadIntranetCategoryById( $id ) {
		$intranet_categories = $this->getIntranetCategoryListWithRestrictions( array( "intranet_category_id=".mysql_real_escape_string( $id ) ) );
		$intranet_category = $intranet_categories[0];
		if( isset( $intranet_category ) && $intranet_category->id > -1 ) {
			$this->id = $intranet_category->id;
			$this->name = $intranet_category->name;
			$this->description = $intranet_category->description;
			$this->image_name = $intranet_category->image_name;
		}
	}
	
	public function loadIntranetCategoryByName( $name ) {
		$intranet_categories = $this->getIntranetCategoryListWithRestrictions( array( "intranet_category_name='".mysql_real_escape_string( $name )."'" ) );
		if( count( $intranet_categories ) == 0 ) {
			return;
		}
		$intranet_category = $intranet_categories[0];
		if( isset( $intranet_category ) && $intranet_category->id > -1 ) {
			$this->id = $intranet_category->id;
			$this->name = $intranet_category->name;
			$this->description = $intranet_category->description;
			$this->image_name = $intranet_category->image_name;
		}
	}
	
}
?>
