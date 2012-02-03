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

class GenyIntranetPageStatus extends GenyDatabaseTools {
	
	public function __construct( $id = -1 ) {
		parent::__construct( "IntranetPageStatus", "intranet_page_status_id" );
		$this->id = -1;
		$this->name = '';
		$this->description = '';
		if( $id > -1 ) {
			$this->loadIntranetPageStatusById( $id );
		}
	}
	
	public function insertNewIntranetPageStatus( $id, $name, $description ) {
		$query = "INSERT INTO IntranetPageStatus VALUES($id,'".mysql_real_escape_string( $name )."','".mysql_real_escape_string( $description )."')";
		if( $this->config->debug ) {
			error_log( "[GYMActivity::DEBUG] GenyIntranetPageStatus MySQL query : $query", 0 );
		}
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return -1;
		}
	}
	
	public function removeIntranetPageStatus( $id = 0 ) {
		if( is_numeric( $id ) ) {
			if( $id == 0 && $this->id > 0 ) {
				$id = $this->id;
			}
			if( $id <= 0 ) {
				return -1;
			}
			$query = "DELETE FROM IntranetPageStatus WHERE intranet_page_status_id=$id";
			if( $this->config->debug ) {
				error_log( "[GYMActivity::DEBUG] GenyIntranetPageStatus MySQL DELETE query : $query", 0 );
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
	
	public function getIntranetPageStatusListWithRestrictions( $restrictions ) {
		// $restrictions is in the form of array("intranet_page_status_id=1")
		$last_index = count( $restrictions ) - 1;
		$query = "SELECT intranet_page_status_id,intranet_page_status_name,intranet_page_status_description FROM IntranetPageStatus";
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
			error_log( "[GYMActivity::DEBUG] GenyIntranetPageStatus MySQL query : $query", 0 );
		}
		$result = mysql_query( $query, $this->handle );
		$intranet_page_status_list = array();
		if( mysql_num_rows( $result ) != 0 ) {
			while( $row = mysql_fetch_row( $result ) ) {
				$tmp_intranet_page_status = new GenyIntranetPageStatus();
				$tmp_intranet_page_status->id = $row[0];
				$tmp_intranet_page_status->name = $row[1];
				$tmp_intranet_page_status->description = $row[2];
				$intranet_page_status_list[] = $tmp_intranet_page_status;
			}
		}
// 		mysql_close();
		return $intranet_page_status_list;
	}
	
	public function getAllIntranetPageStatus() {
		return $this->getIntranetPageStatusListWithRestrictions( array() );
	}
	
	public function searchIntranetPageStatus( $term ) {
		$q = mysql_real_escape_string( $term );
		return $this->getIntranetPageStatusListWithRestrictions( array("intranet_page_status_name LIKE '%$q%' or intranet_page_status_description LIKE '%$q%'") );
	}
	
	public function loadIntranetPageStatusById( $id ) {
		$intranet_page_statuses = $this->getIntranetPageStatusListWithRestrictions( array( "intranet_page_status_id=".mysql_real_escape_string( $id ) ) );
		$intranet_page_status = $intranet_page_statuses[0];
		if( isset( $intranet_page_status ) && $intranet_page_status->id > -1 ) {
			$this->id = $intranet_page_status->id;
			$this->name = $intranet_page_status->name;
			$this->description = $intranet_page_status->description;
		}
	}
	
	public function loadIntranetPageStatusByName( $name ) {
		$intranet_page_statuses = $this->getIntranetPageStatusListWithRestrictions( array( "intranet_page_status_name='".mysql_real_escape_string( $name )."'" ) );
		if( count( $intranet_page_statuses ) == 0 ) {
			return;
		}
		$intranet_page_status = $intranet_page_statuses[0];
		if( isset( $intranet_page_status ) && $intranet_page_status->id > -1 ) {
			$this->id = $intranet_page_status->id;
			$this->name = $intranet_page_status->name;
			$this->description = $intranet_page_status->description;
		}
	}
	
}
?>
