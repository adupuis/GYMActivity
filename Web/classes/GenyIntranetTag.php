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
		$tags_list = array();
		if( mysql_num_rows( $result ) != 0 ) {
			while( $row = mysql_fetch_row( $result ) ) {
				$tmp_tag = new GenyIntranetTag();
				$tmp_tag->id = $row[0];
				$tmp_tag->name = $row[1];
				$tags_list[] = $tmp_tag;
			}
		}
// 		mysql_close();
		return $tags_list;
	}

	public function getAllIntranetTags(){
		return $this->getIntranetTagsListWithRestrictions( array() );
	}

	public function searchIntranetTags( $term ) {
		$q = mysql_real_escape_string( $term );
		return $this->getIntranetTagsListWithRestrictions( array( "intranet_tag_name LIKE '%$q%'" ) );
	}

	public function getIntranetTagsByCategoryAndType( $category_id, $type_id ) {
		$tags = $this->getIntranetTagsListWithRestrictions( array( "intranet_category_id='".$category_id."'","intranet_type_id='".$type_id."'" ) );
		$tags_list = array();
		foreach( $tags as $tag ) {
			$tmp_tag = new GenyIntranetTag();
			$tmp_tag->id = $tag->id;
			$tmp_tag->name = $tag->name;
			$tags_list[] = $tmp_tag;
		}
		return $tags_list;
	}

	public function getIntranetTagsByCategory( $category_id ) {
		$tags = $this->getIntranetTagsListWithRestrictions( array( "intranet_category_id='".$category_id."'" ) );
		$tags_list = array();
		foreach(  $tags as $tag ) {
			$tmp_tag = new GenyIntranetTag();
			$tmp_tag->id = $tag->id;
			$tmp_tag->name = $tag->name;
			$tags_list[] = $tmp_tag;
		}
		return $tags_list;
	}

	public function loadIntranetTagById( $id ) {
		$tags = $this->getIntranetTagsListWithRestrictions( array( "intranet_tag_id=".$id ) );
		$tag = $tags[0];
		if( isset( $tag ) && $tag->id > -1 ) {
			$this->id = $tag->id;
			$this->name = $tag->name;
		}
	}

	public function loadIntranetTagByName( $name ) {
		$tags = $this->getIntranetTagsListWithRestrictions( array( "intranet_tag_name='".mysql_real_escape_string( $name )."'" ) );
		if( count( $tags ) == 0 ) {
			return;
		}
		$tag = $tags[0];
		if( isset( $tag ) && $tag->id > -1 ) {
			$this->id = $tag->id;
			$this->name = $tag->name;
		}
	}
}
?>