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

class GenyHolidaySummary extends GenyDatabaseTools  {
	public $id = -1;
	public $profile_id = -1;
	public $project_id = -1;
	public $task_id = -1;
	public $period_start = '';
	public $period_end = '';
	public $count_acquired = -1;
	public $count_taken = -1;
	public $count_remaining = -1;
	public function __construct( $id = -1 ) {
		parent::__construct("HolidaySummaries_NG",  "holiday_summary_id");
		$this->id = -1;
		$this->profile_id = -1;
		$this->project_id = -1;
		$this->task_id = -1;
		$this->period_start = '';
		$this->period_end = '';
		$this->count_acquired = -1;
		$this->count_taken = -1;
		$this->count_remaining = -1;
		if( $id > -1 ) {
 			$this->loadHolidaySummaryById( $id );
		}
	}
	
	public function insertNewHolidaySummary( $id, $profile_id, $project_id,$task_id, $holiday_summary_period_start, $holiday_summary_period_end, $holiday_summary_count_acquired, $holiday_summary_count_taken, $holiday_summary_count_remaining ) {
		if( $this->config->debug ) {
			error_log( "[GYMActivity::DEBUG] GenyHolidaySummary new holiday_summary insertion - id: $id - profile_id: $profile_id - project_id: $project_id - task_id: $task_id - holiday_summary_period_start: $holiday_summary_period_start - holiday_summary_period_end: $holiday_summary_period_end - holiday_summary_count_acquired: $holiday_summary_count_acquired - holiday_summary_count_taken: $holiday_summary_count_taken - holiday_summary_count_remaining: $holiday_summary_count_remaining", 0 );
		}
		if( !is_numeric( $id ) && $id != 'NULL' ) {
			return -1;
		}
		if( !is_numeric( $profile_id ) ) {
			return -1;
		}
		if( !is_numeric( $project_id ) ) {
			return -1;
		}
		if( !is_numeric( $task_id ) ) {
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
		$query = "INSERT INTO HolidaySummaries_NG VALUES($id,'".$profile_id."','".$project_id."','".$task_id."','".$holiday_summary_period_start."','".$holiday_summary_period_end."','".$holiday_summary_count_acquired."','".$holiday_summary_count_taken."','".$holiday_summary_count_remaining."')";
		if( $this->config->debug ) {
			error_log("[GYMActivity::DEBUG] GenyHolidaySummary MySQL query : $query",0);
		}
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return -1;
		}
	}

	public function removeHolidaySummary( $id ) {
		if( !is_numeric( $id ) ) {
			return false;
		}
		$query = "DELETE FROM HolidaySummaries_NG WHERE holiday_summary_id=$id";
		return mysql_query( $query, $this->handle );
	}

	public function getHolidaySummariesListWithRestrictions( $restrictions, $restriction_type = "AND" ) {
		// $restrictions is in the form of array("holiday_summary_id=1","holiday_summary_type=CP")
		$last_index = count( $restrictions ) - 1;
		$query = "SELECT holiday_summary_id,profile_id,project_id,task_id,holiday_summary_period_start,holiday_summary_period_end,holiday_summary_count_acquired,holiday_summary_count_taken,holiday_summary_count_remaining FROM HolidaySummaries_NG";
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
			error_log("[GYMActivity::DEBUG] GenyHolidaySummaries MySQL query : $query",0);
		}
		$result = mysql_query( $query, $this->handle );
		$holiday_summary_list = array();
		if( mysql_num_rows( $result ) != 0 ) {
			while( $row = mysql_fetch_row( $result ) ) {
				$tmp_holiday_summary = new GenyHolidaySummary();
				$tmp_holiday_summary->id = $row[0];
				$tmp_holiday_summary->profile_id = $row[1];
				$tmp_holiday_summary->project_id = $row[2];
				$tmp_holiday_summary->task_id = $row[3];
				$tmp_holiday_summary->period_start = $row[4];
				$tmp_holiday_summary->period_end = $row[5];
				$tmp_holiday_summary->count_acquired = $row[6];
				$tmp_holiday_summary->count_taken = $row[7];
				$tmp_holiday_summary->count_remaining = $row[8];
				$holiday_summary_list[] = $tmp_holiday_summary;
			}
		}
// 		mysql_close();
		return $holiday_summary_list;
	}

	public function getAllHolidaySummaries() {
		return $this->getHolidaySummariesListWithRestrictions( array() );
	}
    
    public function getHolidaySummariesListByProfileId( $id ) {
		return $this->getHolidaySummariesListWithRestrictions( array("profile_id='".mysql_real_escape_string( $id )."'") );
	}
    
	public function getHolidaySummariesListByProjectId( $id ) {
		return $this->getHolidaySummariesListWithRestrictions( array("project_id='".mysql_real_escape_string( $id )."'") );
	}
	
	public function getHolidaySummariesListByTaskId( $id ) {
		return $this->getHolidaySummariesListWithRestrictions( array("task_id='".mysql_real_escape_string( $id )."'") );
	}
	
	public function getAvailableHolidaySummariesList() {
        $today = date('Y-m-d', time());
		return $this->getHolidaySummariesListWithRestrictions( array("holiday_summary_period_start>='".mysql_real_escape_string( $today )."'","holiday_summary_period_end <= '".mysql_real_escape_string( $today )."'") );
	}

	public function loadHolidaySummaryById( $id ) {
		if( !is_numeric( $id ) ) {
			return false;
		}
		$holiday_summaries = $this->getHolidaySummariesListWithRestrictions( array("holiday_summary_id=".$id) );
		$holiday_summary = $holiday_summaries[0];
		if( isset( $holiday_summary ) && $holiday_summary->id > -1 ) {
			$this->id = $holiday_summary->id;
			$this->profile_id = $holiday_summary->profile_id;
			$this->project_id = $holiday_summary->project_id;
			$this->task_id = $holiday_summary->task_id;
			$this->period_start = $holiday_summary->period_start;
			$this->period_end = $holiday_summary->period_end;
			$this->count_acquired = $holiday_summary->count_acquired;
			$this->count_taken = $holiday_summary->count_taken;
			$this->count_remaining = $holiday_summary->count_remaining;
		}
	}
	
	// WARNING: the getCurrentCPSummaryByProfileId() function and the likes are used in menu_genymobile-2012.php and submodules/profile_summary.php
	/*
	public function getCurrentCPSummaryByProfileId( $id ){
		$month = date('m', time());
		$year=date('Y', time());
		$start_hs_cp_date = '1979-06-01';
		$end_hs_cp_date = '1980-05-31';
		
		if( $month < 6 ){
			$start_year = $year-1;
			$start_hs_cp_date = "$start_year-06-01";
			$end_hs_cp_date = "$year-05-31";
		}
		else {
			$next_year = $year+1;
			$start_hs_cp_date = "$year-06-01";
			$end_hs_cp_date = "$next_year-05-31";
		}
		
		$hs_cp_list = $this->getHolidaySummariesListWithRestrictions(array("profile_id=".$id,"holiday_summary_period_start >= '$start_hs_cp_date'","holiday_summary_period_end <= '$end_hs_cp_date'","holiday_summary_type='CP'"));
		if( count($hs_cp_list) == 1 ){
			return $hs_cp_list[0];
		}
		elseif ( count($hs_cp_list) > 1 ) {
			error_log("[GYMActivity::WARNING] GenyHolidaySummary::getCurrentCPSummaryByProfileId() : il y a plus d'un HolidaySummary pour les CP de la période du $start_hs_cp_date au $end_hs_cp_date pour le profil $id ! Le premier résultat a été retourné mais cela peut être une erreur.",0);
			return $hs_cp_list[0];
		}
		return new GenyHolidaySummary();
	}
	public function getCurrentRTTSummaryByProfileId( $id ){
		$year=date('Y', time());
		
		$hs_rtt_list = $this->getHolidaySummariesListWithRestrictions(array("profile_id=".$id,"holiday_summary_period_start >= '$year-01-01'","holiday_summary_period_end <= '$year-12-31'","holiday_summary_type='RTT'"));
		if( count($hs_rtt_list) == 1 ){
			return $hs_rtt_list[0];
		}
		elseif ( count($hs_rtt_list) > 1 ) {
			error_log("[GYMActivity::WARNING] GenyHolidaySummary::getCurrentRTTSummaryByProfileId() : il y a plus d'un HolidaySummary pour les RTT de la période du $year-01-01 au $year-12-31 pour le profil $id ! Le premier résultat a été retourné mais cela peut être une erreur.",0);
			return $hs_rtt_list[0];
		}
		return new GenyHolidaySummary();
	}
	public function getPreviousCPSummaryByProfileId( $id ){
		$month = date('m', time());
		$year=date('Y', time());
		$start_hs_cp_date = '1979-06-01';
		$end_hs_cp_date = '1980-05-31';
		
		if( $month < 6 ){
			$start_year = $year-2;
			$end_year = $year-1;
			$start_hs_cp_date = "$start_year-06-01";
			$end_hs_cp_date = "$end_year-05-31";
		}
		else {
			$prev_year = $year-1;
			$next_year = $year;
			$start_hs_cp_date = "$prev_year-06-01";
			$end_hs_cp_date = "$next_year-05-31";
		}
		
		$hs_cp_list = $this->getHolidaySummariesListWithRestrictions(array("profile_id=".$id,"holiday_summary_period_start >= '$start_hs_cp_date'","holiday_summary_period_end <= '$end_hs_cp_date'","holiday_summary_type='CP'"));
		if( count($hs_cp_list) == 1 ){
			return $hs_cp_list[0];
		}
		elseif ( count($hs_cp_list) > 1 ) {
			error_log("[GYMActivity::WARNING] GenyHolidaySummary::getCurrentCPSummaryByProfileId() : il y a plus d'un HolidaySummary pour les CP de la période du $start_hs_cp_date au $end_hs_cp_date pour le profil $id ! Le premier résultat a été retourné mais cela peut être une erreur.",0);
			return $hs_cp_list[0];
		}
		return new GenyHolidaySummary();
	}
	public function getPreviousRTTSummaryByProfileId( $id ){
		$year=date('Y', time())-1;
		
		$hs_rtt_list = $this->getHolidaySummariesListWithRestrictions(array("profile_id=".$id,"holiday_summary_period_start >= '$year-01-01'","holiday_summary_period_end <= '$year-12-31'","holiday_summary_type='RTT'"));
		if( count($hs_rtt_list) == 1 ){
			return $hs_rtt_list[0];
		}
		elseif ( count($hs_rtt_list) > 1 ) {
			error_log("[GYMActivity::WARNING] GenyHolidaySummary::getCurrentRTTSummaryByProfileId() : il y a plus d'un HolidaySummary pour les RTT de la période du $year-01-01 au $year-12-31 pour le profil $id ! Le premier résultat a été retourné mais cela peut être une erreur.",0);
			return $hs_rtt_list[0];
		}
		return new GenyHolidaySummary();
	}
	*/

	}

?>
