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

class GenyDailyRate extends GenyDatabaseTools {
	public $id = -1;
	public $project_id = -1;
	public $task_id = -1;
	public $profile_id = -1;
	public $start_date = '';
	public $end_date = '';
	public $value = -1;
	public $po_number = -1;
	public $po_days = -1;
	public function __construct( $id = -1 ) {
		parent::__construct("DailyRates",  "daily_rate_id");
		$this->id = -1;
		$this->project_id = -1;
		$this->task_id = -1;
		$this->profile_id = -1;
		$this->start_date = '';
		$this->end_date = '';
		$this->value = -1;
		$this->po_number = -1;
		$this->po_days = -1;
		if( $id > -1 ) {
 			$this->loadDailyRateById( $id );
		}
	}
	
	public function insertNewDailyRate( $id, $project_id, $task_id, $profile_id, $daily_rate_start_date, $daily_rate_end_date, $daily_rate_value, $daily_rate_po_number=0, $daily_rate_po_days=0 ) {
		if( $this->config->debug ) {
			error_log( "[GYMActivity::DEBUG] GenyDailyRate new daily_rate insertion - id: $id - project_id: $project_id - task_id: $task_id - profile_id: $profile_id - daily_rate_start_date: $daily_rate_start_date - daily_rate_end_date: $daily_rate_end_date - daily_rate_value: $daily_rate_value - daily_rate_po_number: $daily_rate_po_number - daily_rate_po_days: $daily_rate_po_days",   0 );
		}
		if( $id != 'NULL' && !is_numeric( $id ) ) {
			return -1;
		}
		if( !is_numeric( $project_id ) ) {
			return -1;
		}
		if( !is_numeric( $task_id ) ) {
			return -1;
		}
		if( $profile_id != 'NULL' ) {
			if( !is_numeric( $profile_id ) ) {
				return -1;
			}
			$profile_id = "'".$profile_id."'";
		}
		if( !is_numeric( $daily_rate_value ) ) {
			return -1;
		}
		if( !is_numeric( $daily_rate_po_days ) ) {
			return GENYMOBILE_ERROR;
		}
		$query = "INSERT INTO DailyRates VALUES($id,'".$project_id."','".$task_id."',".$profile_id.",'".$daily_rate_start_date."','".$daily_rate_end_date."','".$daily_rate_value."','".mysql_real_escape_string($daily_rate_po_number)."',$daily_rate_po_days)";
		if( $this->config->debug ) {
			error_log( "[GYMActivity::DEBUG] GenyDailyRate MySQL query : $query", 0 );
		}
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return -1;
		}
	}

	public function removeDailyRate( $id ) {
		if( !is_numeric( $id ) ) {
			return false;
		}
		$query = "DELETE FROM DailyRates WHERE daily_rate_id=$id";
		return mysql_query( $query, $this->handle );
	}

	public function getDailyRatesListWithRestrictions( $restrictions, $restriction_type = "AND" ) {
		// $restrictions is in the form of array("daily_rate_id=1","profile_id=1")
		$last_index = count( $restrictions ) - 1;
		$query = "SELECT daily_rate_id,project_id,task_id,profile_id,daily_rate_start_date,daily_rate_end_date,daily_rate_value,daily_rate_po_number,daily_rate_po_days FROM DailyRates";
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
			error_log("[GYMActivity::DEBUG] DailyRates MySQL query : $query",0);
		}
		$result = mysql_query( $query, $this->handle );
		$daily_rate_list = array();
		if( mysql_num_rows( $result ) != 0 ) {
			while( $row = mysql_fetch_row( $result ) ) {
				$tmp_daily_rate = new GenyDailyRate();
				$tmp_daily_rate->id = $row[0];
				$tmp_daily_rate->project_id = $row[1];
				$tmp_daily_rate->task_id = $row[2];
				$tmp_daily_rate->profile_id = $row[3];
				$tmp_daily_rate->start_date = $row[4];
				$tmp_daily_rate->end_date = $row[5];
				$tmp_daily_rate->value = $row[6];
				$tmp_daily_rate->po_number = $row[7];
				$tmp_daily_rate->po_days = $row[8];
				$daily_rate_list[] = $tmp_daily_rate;
			}
		}
// 		mysql_close();
		return $daily_rate_list;
	}

	public function getAllDailyRates() {
		return $this->getDailyRatesListWithRestrictions( array() );
	}

	public function getValuesList() {
		$query = "SELECT DISTINCT daily_rate_value FROM DailyRates";
		if( $this->config->debug ) {
			error_log( "[GYMActivity::DEBUG] DailyRates MySQL query : $query", 0 );
		}
		$result = mysql_query( $query, $this->handle );
		$values_list = array();
		if( mysql_num_rows( $result ) != 0 ) {
			while( $row = mysql_fetch_row( $result ) ) {
				$values_list[] = $row[0];
			}
		}
// 		mysql_close();
		return $values_list;
	}

	// This function is used for checking if for daily rates with same project, same task and same profile, there is no time overlap
	function checkDailyRateOverlap( $daily_rate_id, $project_id, $task_id, $profile_id, $daily_rate_start_date, $daily_rate_end_date ) {
		$query = "SELECT * FROM DailyRates WHERE daily_rate_id!=".$daily_rate_id." AND project_id=".$project_id." AND task_id=".$task_id." AND profile_id=".$profile_id.
			" AND ( daily_rate_start_date <= '".$daily_rate_start_date."' AND daily_rate_end_date >= '".$daily_rate_start_date."' OR daily_rate_start_date > '".$daily_rate_start_date."' AND daily_rate_start_date <= '".$daily_rate_end_date."' )";
		if( $this->config->debug ) {
			error_log( "[GYMActivity::DEBUG] DailyRates MySQL query : $query", 0 );
		}
		$result = mysql_query( $query, $this->handle );
		$nb_rows = mysql_num_rows( $result );
		if( $nb_rows != 0 ) {
			return false;
		}
		return true;
	}

	public function loadDailyRateById( $id ) {
		if( !is_numeric( $id ) ) {
			return false;
		}
		$daily_rates = $this->getDailyRatesListWithRestrictions( array("daily_rate_id=".$id) );
		$daily_rate = $daily_rates[0];
		if( isset( $daily_rate ) && $daily_rate->id > -1 ) {
			$this->id = $daily_rate->id;
			$this->project_id = $daily_rate->project_id;
			$this->task_id = $daily_rate->task_id;
			$this->profile_id = $daily_rate->profile_id;
			$this->start_date = $daily_rate->start_date;
			$this->end_date = $daily_rate->end_date;
			$this->value = $daily_rate->value;
			$this->po_number = $daily_rate->po_number;
			$this->po_days = $daily_rate->po_days;
		}
	}
	public function getDailyRatesByPONumber( $po_number ){
		return $this->getDailyRatesListWithRestrictions( array("daily_rate_po_number='".mysql_real_escape_string($po_number)."'") );
	}
	public function getDailyRatesByProjectId( $id ){
		if( ! is_numeric($id) )
			return GENYMOBILE_FALSE;
		return $this->getDailyRatesListWithRestrictions( array("project_id=$id") );
	}
	public function getDailyRatesByProfileId( $id ){
		if( ! is_numeric($id) )
			return GENYMOBILE_FALSE;
		return $this->getDailyRatesListWithRestrictions( array("profile_id=$id") );
	}
	public function getDailyRatesByTaskId( $id ){
		if( ! is_numeric($id) )
			return GENYMOBILE_FALSE;
		return $this->getDailyRatesListWithRestrictions( array("task_id=$id") );
	}
}

?>