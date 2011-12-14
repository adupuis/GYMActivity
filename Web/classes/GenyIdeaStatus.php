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

class GenyIdeaStatus {
	
	private $updates = array();

	public function __construct( $id = -1 ) {
		$this->config = new GenyWebConfig();
		$this->handle = mysql_connect( $this->config->db_host, $this->config->db_user, $this->config->db_password );
		mysql_select_db($this->config->db_name);
		mysql_query( "SET NAMES 'utf8'" );
		$this->id = -1;
		$this->name = '';
		$this->description = '';
		if( $id > -1 ) {
			$this->loadIdeaStatusById( $id );
		}
	}

	public function insertNewIdeaStatus( $id, $idea_status_name, $idea_status_description ) {
		if( !is_numeric( $id ) && $id != 'NULL' ) {
			return -1;
		}
		$query = "INSERT INTO IdeaStatus VALUES($id,'".mysql_real_escape_string($idea_status_name)."','".mysql_real_escape_string($idea_status_description).")";
		if( $this->config->debug ) {
			error_log("[GYMActivity::DEBUG] GenyIdeaStatus MySQL query : $query",0);
		}
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return -1;
		}
	}

	public function removeIdeaStatus( $id ) {
		if( !is_numeric( $id ) ) {
			return false;
		}
		$query = "DELETE FROM IdeaStatus WHERE idea_status_id=$id";
		return mysql_query( $query, $this->handle );
	}

	public function getIdeaStatusListWithRestrictions( $restrictions ) {
		// $restrictions is in the form of array("idea_id=1","idea_status_id=2")
		$last_index = count( $restrictions ) - 1;
		$query = "SELECT idea_status_id,idea_status_name,idea_status_description FROM IdeaStatus";
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
			error_log("[GYMActivity::DEBUG] GenyIdeaStatus MySQL query : $query",0);
		}
		$result = mysql_query( $query, $this->handle );
		$idea_status_list = array();
		if( mysql_num_rows( $result ) != 0 ) {
			while( $row = mysql_fetch_row( $result ) ) {
				$tmp_idea_status = new GenyIdeaStatus();
				$tmp_idea_status->id = $row[0];
				$tmp_idea_status->name = $row[1];
				$tmp_idea_status->description = $row[2];
				$idea_status_list[] = $tmp_idea_status;
			}
		}
// 		mysql_close();
		return $idea_status_list;
	}

	public function getAllIdeaStatus() {
		return $this->getIdeaStatusListWithRestrictions( array() );
	}

	public function getIdeaStatusListByName( $name ) {
		return $this->getIdeaStatusListWithRestrictions( array("idea_status_name='".mysql_real_escape_string( $name )."'") );
	}

	public function loadIdeaStatusByName( $name ) {
		$idea_statuses = $this->getIdeaStatusListWithRestrictions( array("idea_status_name='".mysql_real_escape_string( $name )."'") );
		$idea_status = $idea_statuses[0];
		if( isset( $idea_status ) && $idea_status->id > -1 ) {
			$this->id = $idea_status->id;
			$this->name = $idea_status->name;
			$this->description = $profile->description;
		}
	}

	public function loadIdeaStatusById( $id ) {
		if( !is_numeric( $id ) ) {
			return false;
		}
		$idea_statuses = $this->getIdeaStatusListWithRestrictions( array("idea_status_id=$id") );
		$idea_status = $idea_statuses[0];
		if( isset( $idea_status ) && $idea_status->id > -1 ) {
			$this->id = $idea_status->id;
			$this->name = $idea_status->name;
			$this->description = $idea_status->description;
		}
	}

	public function updateString( $key, $value ) {
		$this->updates[] = "$key='".mysql_real_escape_string( $value )."'";
		return true;
	}

	public function updateInt( $key, $value ) {
		if( is_numeric( $value ) ) {
			$this->updates[] = "$key=$value";
			return true;
		}
		else {
			return false;
		}
	}

	public function updateBool( $key, $value ) {
		if( is_bool( $value ) ) {
			$this->updates[] = "$key=$value";
			return true;
		}
		else {
			return false;
		}
	}

	public function commitUpdates() {
		$query = "UPDATE IdeaStatus SET ";
		foreach( $this->updates as $up ) {
			$query .= "$up,";
		}
		$query = rtrim( $query, "," );
		$query .= " WHERE idea_status_id=".$this->id;
		if( $this->config->debug ) {
			error_log("[GYMActivity::DEBUG] GenyIdeaStatus MySQL query : $query",0);
		}
		return mysql_query( $query, $this->handle );
	}

}

?>