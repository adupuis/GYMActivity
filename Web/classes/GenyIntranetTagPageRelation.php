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

class GenyIntranetTagPageRelation extends GenyDatabaseTools {
	
	public function __construct( $id = -1 ) {
		parent::__construct( "IntranetTagPageRelations",  
				     "intranet_tag_page_relation_id" );
		$this->id = -1;
		$this->intranet_tag_id = -1;
		$this->intranet_page_id = -1;
		if( $id > -1 ) {
			$this->loadIntranetTagPageRelationById( $id );
		}
	}
	
	public function deleteIntranetTagPageRelation( $id = 0 ) {
		if( is_numeric( $id ) ) {
			if( $id == 0 && $this->id > 0 ) {
				$id = $this->id;
			}
			if($id <= 0) {
				return -1;
			}
			
			$query = "DELETE FROM IntranetTagPageRelations WHERE intranet_tag_page_relation_id=$id";
			if( $this->config->debug ) {
				error_log("[GYMActivity::DEBUG] GenyIntranetTagPageRelation MySQL DELETE query : $query",0);
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
	
	public function insertNewIntranetTagPageRelation( $intranet_tag_id, $intranet_page_id ) {
		if( !is_numeric( $intranet_tag_id ) ) {
			return -1;
		}
		if( !is_numeric( $intranet_page_id ) ) {
			return -1;
		}
		$query = "INSERT INTO IntranetTagPageRelations VALUES(0,$intranet_tag_id,$intranet_page_id)";
		if( $this->config->debug ) {
			error_log( "[GYMActivity::DEBUG] GenyIntranetTagPageRelation MySQL query : $query", 0 );
		}
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return -1;
		}
	}
	
	public function getIntranetTagPageRelationsListWithRestrictions( $restrictions ) {
		// $restrictions is in the form of array("intranet_tag_id=1","intranet_page_id=2" )
		$last_index = count( $restrictions ) - 1;
		$query = "SELECT intranet_tag_page_relation_id,intranet_tag_id,intranet_page_id FROM IntranetTagPageRelations";
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
			error_log( "[GYMActivity::DEBUG] GenyIntranetTagPageRelation MySQL query : $query", 0 );
		}
		$result = mysql_query( $query, $this->handle );
		$object_list = array();
		if( mysql_num_rows( $result ) != 0 ) {
			while( $row = mysql_fetch_row( $result ) ) {
				$tmp_object = new GenyIntranetTagPageRelation();
				$tmp_object->id = $row[0];
				$tmp_object->intranet_tag_id = $row[1];
				$tmp_object->intranet_page_id = $row[2];
				$object_list[] = $tmp_object;
			}
		}
// 		mysql_close();
		return $object_list;
	}
	
	public function getAllIntranetTagPageRelations() {
		return $this->getIntranetTagPageRelationsListWithRestrictions( array() );
	}
	
	public function getIntranetTagPageRelationsListByTagId( $id ) {
		if( !is_numeric( $id ) ) {
			return -1;
		}
		return $this->getIntranetTagPageRelationsListWithRestrictions( array( "intranet_tag_id=$id" ) );
	}
	
	public function getIntranetTagPageRelationsListByPageId( $id ) {
		if( !is_numeric( $id ) ) {
			return -1;
		}
		return $this->getIntranetTagPageRelationsListWithRestrictions( array( "intranet_page_id=$id" ) );
	}
	
	public function loadIntranetTagPageRelationById( $id ) {
		if( !is_numeric( $id ) ) {
			return -1;
		}
		$objects = $this->getIntranetTagPageRelationListWithRestrictions( array( "intranet_tag_page_relation_id=$id" ) );
		$object = $objects[0];
		if( isset( $object ) && $object->id > -1 ) {
			$this->id = $object->id;
			$this->intranet_tag_id = $object->intranet_tag_id;
			$this->intranet_page_id = $object->intranet_page_id;
		}
	}
	
	public function deleteAllIntranetTagPageRelationsByTagId( $intranet_tag_id ){
		if( !is_numeric( $intranet_tag_id ) ) {
			return false;
		}
		$query = "DELETE FROM IntranetTagPageRelations WHERE intranet_tag_id=$intranet_tag_id";
		return mysql_query( $query, $this->handle );
	}
	
	public function deleteAllIntranetTagPageRelationsByPageId( $intranet_page_id ){
		if( !is_numeric( $intranet_page_id ) ) {
			return false;
		}
		$query = "DELETE FROM IntranetTagPageRelations WHERE intranet_page_id=$intranet_page_id";
		return mysql_query( $query, $this->handle );
	}
}
?>