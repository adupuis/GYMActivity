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
include_once 'GenyDatabaseTools.php';

class GenyBankHoliday extends GenyDatabaseTools {
	public $id = -1;
	public $name = '';
    public $project_id = -1;
    public $task_id = -1;
    public $start_date = '1979-01-01';
    public $stop_date = '1979-01-01';
    public $country_id = -1;
	public function __construct($id = -1){
		parent::__construct("BankHolidays",  "bank_holiday_id");
		$this->id = -1;
		$this->name = '';
		$this->project_id = -1;
        $this->task_id = -1;
        $this->start_date = '1979-01-01';
        $this->stop_date = '1979-01-01';
        $this->country_id = -1;
		if($id > -1)
			$this->loadBankHolidayById($id);
	}
	public function deleteBankHoliday($id=0){
		if(is_numeric($id)){
			if( $id == 0 && $this->id > 0 )
				$id = $this->id;
			if($id <= 0)
				return -1;
			$query = "DELETE FROM BankHolidays WHERE bank_holiday_id=$id";
			if( $this->config->debug )
				error_log("[GYMActivity::DEBUG] GenyBankHoliday MySQL DELETE query : $query",0);
			if(mysql_query($query,$this->handle))
				return 1;
			else
				return -1;
		}
		return -1;
	}
	public function insertNewBankHoliday($id,$name,$project_id,$task_id,$start_date,$stop_date,$country_id){
		$query = "INSERT INTO BankHolidays VALUES($id,'".mysql_real_escape_string($name)."',$project_id,$task_id,'".mysql_real_escape_string($start_date)."','".mysql_real_escape_string($stop_date)."','".$country_id."')";
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyBankHoliday MySQL query : $query",0);
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return -1;
		}
	}
	public function getBankHolidaysListWithRestrictions($restrictions){
		// $restrictions is in the form of array("project_id=1","project_status_id=2")
		$last_index = count($restrictions)-1;
		$query = "SELECT bank_holiday_id,bank_holiday_name,bank_holiday_project_id,bank_holiday_task_id,bank_holiday_start_date,bank_holiday_stop_date,bank_holiday_country_id FROM BankHolidays";
		if(count($restrictions) > 0){
			$query .= " WHERE ";
			foreach($restrictions as $key => $value) {
				$query .= $value;
				if($key != $last_index){
					$query .= " AND ";
				}
			}
		}
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyBankHoliday MySQL query : $query",0);
		$result = mysql_query($query, $this->handle);
		$bh_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_bh = new GenyBankHoliday();
				$tmp_bh->id = $row[0];
				$tmp_bh->name = $row[1];
				$tmp_bh->project_id = $row[2];
				$tmp_bh->task_id = $row[3];
				$tmp_bh->start_date = $row[4];
				$tmp_bh->stop_date = $row[5];
				$tmp_bh->country_id = $row[6];
				$bh_list[] = $tmp_bh;
			}
		}
// 		mysql_close();
		return $bh_list;
	}
	public function getAllBankHolidays(){
		return $this->getBankHolidaysListWithRestrictions( array() );
	}
	public function getBankHolidaysByCountryId($id){
		return $this->getBankHolidaysListWithRestrictions( array("bank_holiday_country_id=".mysql_real_escape_string($id)) );
	}
	public function searchBankHolidays($term){
		$q = mysql_real_escape_string($term);
		return $this->getBankHolidaysListWithRestrictions( array("bank_holiday_name LIKE '%$q%'") );
	}
	public function loadBankHolidayByName($name){
		$bhs = $this->getBankHolidaysListWithRestrictions(array("bank_holiday_name='".mysql_real_escape_string($name)."'"));
		$bh = $bhs[0];
		if(isset($bh) && $bh->id > -1){
			$this->id = $bh->id;
			$this->name = $bh->name;
			$this->project_id = $bh->project_id;
			$this->task_id = $bh->task_id;
			$this->start_date = $bh->start_date;
			$this->stop_date = $bh->stop_date;
			$this->country_id = $bh->country_id;
		}
	}
	public function loadBankHolidayById($id){
		$bhs = $this->getBankHolidaysListWithRestrictions(array("bank_holiday_id=".mysql_real_escape_string($id)));
		$bh = $bhs[0];
		if(isset($bh) && $bh->id > -1){
			$this->id = $bh->id;
			$this->name = $bh->name;
			$this->project_id = $bh->project_id;
			$this->task_id = $bh->task_id;
			$this->start_date = $bh->start_date;
			$this->stop_date = $bh->stop_date;
			$this->country_id = $bh->country_id;
		}
	}
}
?>
