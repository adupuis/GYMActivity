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

class GenyIntranetTag extends GenyDatabaseTools {
	public $id = -1;
	public $name = '';
	public function __construct( $id = -1 ) {
		parent::__construct( "IntranetTags", "intranet_tag_id" );
		$this->id = -1;
		$this->name = '';
		if($id > -1)
			$this->loadIntranetTagById( $id );
	}

	public function insertNewIntranetTag( $id, $intranet_tag_name ) {
		$query = "INSERT INTO IntranetTags VALUES($id,'".mysql_real_escape_string($intranet_tag_name)."')";
			if( $this->config->debug ) {
				error_log( "[GYMActivity::DEBUG] GenyIntranetTag MySQL query : $query", 0 );
			}
			if( mysql_query( $query, $this->handle ) ) {
				return mysql_insert_id( $this->handle );
			}
			else {
				return -1;
			}
	}

	public function removeIntranetTag( $id = 0 ) {
		if( is_numeric( $id ) ) {
			if( $id == 0 && $this->id > 0 ) {
				$id = $this->id;
			}
			if( $id <= 0 ) {
				return -1;
			}
			$query = "DELETE FROM IntranetTags WHERE intranet_tag_id=$id";
			if( $this->config->debug ) {
				error_log( "[GYMActivity::DEBUG] GenyIntranetTag MySQL DELETE query : $query", 0 );
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

	public function getIntranetTagsListWithRestrictions( $restrictions ) {
		// $restrictions is in the form of array("intranet_tag_id=1")
		$last_index = count( $restrictions ) - 1;
		$query = "SELECT intranet_tag_id,intranet_tag_name FROM IntranetTags";
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
			error_log( "[GYMActivity::DEBUG] GenyIntranetTag MySQL query : $query", 0 );
		}
		$result = mysql_query( $query, $this->handle );
		$intranet_tags_list = array();
		if( mysql_num_rows( $result ) != 0 ) {
			while( $row = mysql_fetch_row( $result ) ) {
				$tmp_intranet_tag = new GenyIntranetTag();
				$tmp_intranet_tag->id = $row[0];
				$tmp_intranet_tag->name = $row[1];
				$intranet_tags_list[] = $tmp_intranet_tag;
			}
		}
// 		mysql_close();
		return $intranet_tags_list;
	}

	public function getAllIntranetTags(){
		return $this->getIntranetTagsListWithRestrictions( array() );
	}
	
	public function getIntranetTagsByName( $intranet_tag_name ) {
		$intranet_tags = $this->getIntranetTagsListWithRestrictions( array( "intranet_tag_name='".mysql_real_escape_string( $intranet_tag_name )."'" ) );
		$intranet_tags_list = array();
		foreach( $intranet_tags as $intranet_tag ) {
			$tmp_intranet_tag = new GenyIntranetTag();
			$tmp_intranet_tag->id = $intranet_tag->id;
			$tmp_intranet_tag->name = $intranet_tag->name;
			$intranet_tags_list[] = $tmp_intranet_tag;
		}
		return $intranet_tags_list;
	}
	
	public function getIntranetTagsByType( $intranet_type_id ) {
		$query = "SELECT IntranetTags.intranet_tag_id, intranet_tag_name FROM IntranetTags, IntranetTagPageRelations, IntranetPages WHERE IntranetTags.intranet_tag_id = IntranetTagPageRelations.intranet_tag_id AND IntranetTagPageRelations.intranet_page_id = IntranetPages.intranet_page_id AND IntranetPages.intranet_type_id = ".$intranet_type_id;
		
		$result = mysql_query( $query, $this->handle );
		if( $this->config->debug ) {
			error_log( "[GYMActivity::DEBUG] GenyIntranetTag MySQL query : $query", 0 );
		}
		
		$intranet_tags_list = array();
		if( mysql_num_rows( $result ) != 0 ) {
			while( $row = mysql_fetch_row( $result ) ) {
				$tmp_intranet_tag = new GenyIntranetTag();
				$tmp_intranet_tag->id = $row[0];
				$tmp_intranet_tag->name = $row[1];
				$intranet_tags_list[] = $tmp_intranet_tag;
			}
		}
		return $intranet_tags_list;
	}

	public function getIntranetTagsByCategory( $intranet_category_id ) {
		$query = "SELECT IntranetTags.intranet_tag_id, intranet_tag_name FROM IntranetTags, IntranetTagPageRelations, IntranetPages WHERE IntranetTags.intranet_tag_id = IntranetTagPageRelations.intranet_tag_id AND IntranetTagPageRelations.intranet_page_id = IntranetPages.intranet_page_id AND IntranetPages.intranet_category_id = ".$intranet_category_id;
		
		$result = mysql_query( $query, $this->handle );
		if( $this->config->debug ) {
			error_log( "[GYMActivity::DEBUG] GenyIntranetTag MySQL query : $query", 0 );
		}
		
		$intranet_tags_list = array();
		if( mysql_num_rows( $result ) != 0 ) {
			while( $row = mysql_fetch_row( $result ) ) {
				$tmp_intranet_tag = new GenyIntranetTag();
				$tmp_intranet_tag->id = $row[0];
				$tmp_intranet_tag->name = $row[1];
				$intranet_tags_list[] = $tmp_intranet_tag;
			}
		}
		return $intranet_tags_list;
	}
	
	public function getIntranetTagsByPage( $intranet_page_id ) {
		
		$query = "SELECT IntranetTags.intranet_tag_id, intranet_tag_name FROM IntranetTags, IntranetTagPageRelations WHERE IntranetTags.intranet_tag_id = IntranetTagPageRelations.intranet_tag_id AND IntranetTagPageRelations.intranet_page_id=".$intranet_page_id;
		
		$result = mysql_query( $query, $this->handle );
		if( $this->config->debug ) {
			error_log( "[GYMActivity::DEBUG] GenyIntranetTag MySQL query : $query", 0 );
		}
		
		$intranet_tags_list = array();
		if( mysql_num_rows( $result ) != 0 ) {
			while( $row = mysql_fetch_row( $result ) ) {
				$tmp_intranet_tag = new GenyIntranetTag();
				$tmp_intranet_tag->id = $row[0];
				$tmp_intranet_tag->name = $row[1];
				$intranet_tags_list[] = $tmp_intranet_tag;
			}
		}
		return $intranet_tags_list;
	}

	public function searchIntranetTags( $term ) {
		$q = mysql_real_escape_string( $term );
		return $this->getIntranetTagsListWithRestrictions( array( "intranet_tag_name LIKE '%$q%'" ) );
	}

	public function loadIntranetTagById( $id ) {
		$intranet_tags = $this->getIntranetTagsListWithRestrictions( array( "intranet_tag_id=".$id ) );
		$intranet_tag = $intranet_tags[0];
		if( isset( $intranet_tag ) && $intranet_tag->id > -1 ) {
			$this->id = $intranet_tag->id;
			$this->name = $intranet_tag->name;
		}
	}

	public function loadIntranetTagByName( $name ) {
		$intranet_tags = $this->getIntranetTagsListWithRestrictions( array( "intranet_tag_name='".mysql_real_escape_string( $name )."'" ) );
		if( count( $intranet_tags ) == 0 ) {
			return;
		}
		$intranet_tag = $intranet_tags[0];
		if( isset( $intranet_tag ) && $intranet_tag->id > -1 ) {
			$this->id = $intranet_tag->id;
			$this->name = $intranet_tag->name;
		}
	}
}
?>