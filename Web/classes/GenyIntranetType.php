<?php
//  Copyright (C) 2011 by GENYMOBILE & Quentin Désert
//  qdesert@genymobile.com
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

class GenyIntranetType extends GenyDatabaseTools {

	public function __construct( $id = -1 ) {
		parent::__construct("IntranetTypes",  "intranet_type_id");
		$this->id = -1;
		$this->name = '';
		$this->description = '';
		$this->category_id = -1;
		if( $id > -1 ) {
 			$this->loadIntranetTypeById( $id );
		}
	}
	
	public function insertNewIntranetType( $id, $intranet_type_name, $intranet_type_description, $category_id ) {
		if( $this->config->debug ) {
			error_log( "[GYMActivity::DEBUG] GenyIntranetType new intranet_type insertion - id: $id - intranet_type_name: $intranet_type_name - intranet_type_description: $intranet_type_description - category_id: $category_id", 0 );
		}
		if( !is_numeric( $id ) && $id != 'NULL' ) {
			return -1;
		}
		if( !is_numeric( $category_id ) ) {
			return -1;
		}
		$query = "INSERT INTO IntranetTypes VALUES($id,'".mysql_real_escape_string( $intranet_type_name )."','".mysql_real_escape_string( $intranet_type_description )."','".$category_id."')";
		if( $this->config->debug ) {
			error_log( "[GYMActivity::DEBUG] GenyIntranetType MySQL query : $query", 0 );
		}
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return -1;
		}
	}

	public function removeIntranetType( $id ) {
		if( !is_numeric( $id ) ) {
			return false;
		}
		$query = "DELETE FROM IntranetTypes WHERE intranet_type_id=$id";
		return mysql_query( $query, $this->handle );
	}

	public function getIntranetTypesListWithRestrictions( $restrictions, $restriction_type = "AND" ) {
		// $restrictions is in the form of array("intranet_type_id=1","category_id=1")
		$last_index = count( $restrictions ) - 1;
		$query = "SELECT intranet_type_id,intranet_type_name,intranet_type_description,intranet_category_id FROM IntranetTypes";
		if( count( $restrictions ) > 0 ) {
			$query .= " WHERE ";
			$op = mysql_real_escape_string( $restriction_type );
			foreach( $restrictions as $key => $value ) {
				$query .= $value;
				if( $key != $last_index ) {
					$query .= " $op ";
				}
			}
		}
		if( $this->config->debug ) {
			error_log("[GYMActivity::DEBUG] IntranetTypes MySQL query : $query",0);
		}
		$result = mysql_query( $query, $this->handle );
		$intranet_type_list = array();
		if( mysql_num_rows( $result ) != 0 ) {
			while( $row = mysql_fetch_row( $result ) ) {
				$tmp_intranet_type = new GenyIntranetType();
				$tmp_intranet_type->id = $row[0];
				$tmp_intranet_type->name = $row[1];
				$tmp_intranet_type->description = $row[2];
				$tmp_intranet_type->category_id = $row[3];
				$intranet_type_list[] = $tmp_intranet_type;
			}
		}
// 		mysql_close();
		return $intranet_type_list;
	}

	public function getAllIntranetTypes() {
		return $this->getIntranetTypesListWithRestrictions( array() );
	}

	public function getIntranetTypesByCategoryId( $category_id ) {
		return $this->getIntranetTypesListWithRestrictions( array( "intranet_category_id=".$category_id ) );
	}

	public function loadIntranetTypeById( $id ) {
		if( !is_numeric( $id ) ) {
			return false;
		}
		$intranet_types = $this->getIntranetTypesListWithRestrictions( array("intranet_type_id=".$id) );
		$intranet_type = $intranet_types[0];
		if( isset( $intranet_type ) && $intranet_type->id > -1 ) {
			$this->id = $intranet_type->id;
			$this->name = $intranet_type->name;
			$this->description = $intranet_type->description;
			$this->category_id = $intranet_type->category_id;
		}
	}
}

?>