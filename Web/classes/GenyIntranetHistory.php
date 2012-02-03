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

class GenyIntranetHistory extends GenyDatabaseTools {
	public $id = -1;
	public $page_id = '';
	public $profile_id = '';
	public $history_date = '';
	public $history_content = '';
	
	public function __construct( $id = -1 ) {
		parent::__construct( "InternetHistories", "intranet_history_id" );
		$this->id = -1;
		$this->page_id = '';
		$this->page_status_id = '';
		$this->profile_id = '';
		$this->history_date = '';
		$this->history_content = '';
		if( $id > -1 ) {
			$this->loadIntranetHistoryById( $id );
		}
	}
	
	public function insertNewIntranetHistory( $id, $page_id, $page_status_id, $profile_id, $date, $content ) {
		$query = "INSERT INTO IntranetHistories VALUES($id,$page_id,$page_status_id,$profile_id,$date'".mysql_real_escape_string( $content )."')";
		if( $this->config->debug ) {
			error_log( "[GYMActivity::DEBUG] GenyIntranetHistory MySQL query : $query", 0 );
		}
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return -1;
		}
	}
	
	public function removeIntranetHistory( $id = 0 ) {
		if( is_numeric( $id ) ) {
			if( $id == 0 && $this->id > 0 ) {
				$id = $this->id;
			}
			if( $id <= 0 ) {
				return -1;
			}

			$query = "DELETE FROM IntranetHistories WHERE intranet_history_id=$id";
			if( $this->config->debug ) {
				error_log( "[GYMActivity::DEBUG] GenyIntranetHistory MySQL DELETE query : $query", 0 );
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
	
	public function getIntranetHistoriesListWithRestrictions( $restrictions ) {
		$last_index = count( $restrictions ) - 1;
		$query = "SELECT intranet_history_id,intranet_page_id,intranet_page_status_id,profile_id,intranet_history_date,intranet_history_content FROM IntranetHistories";
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
			error_log( "[GYMActivity::DEBUG] GenyIntranetHistory MySQL query : $query", 0 );
		}
		$result = mysql_query( $query, $this->handle );
		$intranet_history_list = array();
		if( mysql_num_rows( $result ) != 0 ) {
			while( $row = mysql_fetch_row( $result ) ) {
				$tmp_intranet_history = new GenyIntranetHistory();
				$tmp_intranet_history->id = $row[0];
				$tmp_intranet_history->page_id = $row[1];
				$tmp_intranet_history->page_status_id = $row[2];
				$tmp_intranet_history->profile_id = $row[3];
				$tmp_intranet_history->history_date = $row[4];
				$tmp_intranet_history->history_content = $row[5];
				$intranet_history_list[] = $tmp_intranet_history;
			}
		}
// 		mysql_close();
		return $intranet_history_list;
	}
	
	public function getAllIntranetHistories() {
		return $this->getIntranetHistoriesListWithRestrictions( array() );
	}
	
	public function getIntranetHistoriesByProfileId( $id ) {
		return $this->getIntranetHistoriesListWithRestrictions( array( "profile_id=".$id ) );
	}
	
	public function loadHistoryById( $id ) {
		$intranet_histories = $this->getIntranetHistoriesListWithRestrictions( array( "intranet_history_id=".$id ) );
		if( count( $intranet_histories ) == 0 ) {
			return;
		}
		$intranet_history = $intranet_histories[0];
		if( isset( $intranet_history ) && $intranet_history->id > -1 ) {
			$this->id = $intranet_history->id;
			$this->page_id = $intranet_history->page_id;
			$this->page_status_id = $intranet_history->page_status_id;
			$this->profile_id = $intranet_history->profile_id;
			$this->history_date = $intranet_history->history_date;
			$this->history_content = $intranet_history->history_content;
		}
	}
}
?>
