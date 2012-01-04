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

class GenyHolydaySummary extends GenyDatabaseTools  {
	public function __construct( $id = -1 ) {
		parent::__construct("HolydaySummaries",  "holiday_summary_id");
		$this->id = -1;
		$this->profile_id = -1;
		$this->type = '';
		$this->period_start = '';
		$this->period_end = '';
		$this->count_acquired = -1;
		$this->count_taken = -1;
		$this->count_remaining = -1;
		if( $id > -1 ) {
 			$this->loadHolydaySummaryById( $id );
		}
	}
	
	public function insertNewHolydaySummary( $id, $profile_id, $holiday_summary_type, $holiday_summary_period_start, $holiday_summary_period_end, $holiday_summary_count_acquired, $holiday_summary_count_taken, $holiday_summary_count_remaining ) {
		if( $this->config->debug ) {
			echo "<!-- DEBUG: GenyHolydaySummary new holiday_summary insertion - id: $id - profile_id: $profile_id - holiday_summary_type: $holiday_summary_type - holiday_summary_period_start: $holiday_summary_period_start - holiday_summary_period_end: $holiday_summary_period_end - holiday_summary_count_acquired: $holiday_summary_count_acquired - holiday_summary_count_taken: $holiday_summary_count_taken - holiday_summary_count_remaining: $holiday_summary_count_remaining -->\n";
		}
		if( !is_numeric( $id ) && $id != 'NULL' ) {
			return -1;
		}
		if( !is_numeric( $profile_id ) ) {
			return -1;
		}
		if( !is_numeric( $holiday_summary_count_acquired ) ) {
			return -1;
		}
		if( !is_numeric( $holiday_summary_count_taken ) ) {
			return -1;
		}
		if( !is_numeric( $holiday_summary_count_remaining ) ) {
			return -1;
		}
		$query = "INSERT INTO HolydaySummaries VALUES($id,'".$profile_id."','".mysql_real_escape_string( $holiday_summary_type )."','".$holiday_summary_period_start."','".$holiday_summary_period_end."','".$holiday_summary_count_acquired."','".$holiday_summary_count_taken."','".$holiday_summary_count_remaining."')";
		if( $this->config->debug ) {
			error_log("[GYMActivity::DEBUG] GenyHolydaySummary MySQL query : $query",0);
		}
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return -1;
		}
	}

	public function removeHolydaySummary( $id ) {
		if( !is_numeric( $id ) ) {
			return false;
		}
		$query = "DELETE FROM HolydaySummaries WHERE holyday_summary_id=$id";
		return mysql_query( $query, $this->handle );
	}

	public function getHolydaySummariesListWithRestrictions( $restrictions, $restriction_type = "AND" ) {
		// $restrictions is in the form of array("holyday_summary_id=1","holiday_summary_type=CP")
		$last_index = count( $restrictions ) - 1;
		$query = "SELECT holyday_summary_id,profile_id,holyday_summary_type,holyday_summary_period_start,holyday_summary_period_end,holyday_summary_count_acquired,holyday_summary_count_taken,holyday_summary_count_remaining FROM HolydaySummaries";
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
			error_log("[GYMActivity::DEBUG] GenyHolydaySummaries MySQL query : $query",0);
		}
		$result = mysql_query( $query, $this->handle );
		$holyday_summary_list = array();
		if( mysql_num_rows( $result ) != 0 ) {
			while( $row = mysql_fetch_row( $result ) ) {
				$tmp_holyday_summary = new GenyHolydaySummary();
				$tmp_holyday_summary->id = $row[0];
				$tmp_holyday_summary->profile_id = $row[1];
				$tmp_holyday_summary->type = $row[2];
				$tmp_holyday_summary->period_start = $row[3];
				$tmp_holyday_summary->period_end = $row[4];
				$tmp_holyday_summary->count_acquired = $row[5];
				$tmp_holyday_summary->count_taken = $row[6];
				$tmp_holyday_summary->count_remaining = $row[7];
				$holyday_summary_list[] = $tmp_holyday_summary;
			}
		}
// 		mysql_close();
		return $holyday_summary_list;
	}

	public function getAllHolydaySummaries() {
		return $this->getHolydaySummariesListWithRestrictions( array() );
	}

	public function getHolydaySummariesListByType( $type ) {
		return $this->getHolydaySummariesListWithRestrictions( array("holyday_summary_type='".mysql_real_escape_string( $type )."'") );
	}

	public function loadHolydaySummaryById( $id ) {
		if( !is_numeric( $id ) ) {
			return false;
		}
		$holyday_summaries = $this->getHolydaySummariesListWithRestrictions( array("holyday_summary_id=".$id) );
		$holyday_summary = $holyday_summaries[0];
		if( isset( $holyday_summary ) && $holyday_summary->id > -1 ) {
			$this->id = $holyday_summary->id;
			$this->profile_id = $holyday_summary->profile_id;
			$this->type = $holyday_summary->type;
			$this->period_start = $holyday_summary->period_start;
			$this->period_end = $holyday_summary->period_end;
			$this->count_acquired = $holyday_summary->count_acquired;
			$this->count_taken = $holyday_summary->count_taken;
			$this->count_remaining = $holyday_summary->count_remaining;
		}
	}
}

?>