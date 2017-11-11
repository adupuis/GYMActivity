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

class GenyCountry extends GenyDatabaseTools {
	public $id = -1;
	public $name = '';
	public function __construct($id = -1){
		parent::__construct("Countries",  "country_id");
		$this->id = -1;
		$this->name = '';
		if($id > -1)
			$this->loadCountryById($id);
	}
	public function deleteCountry($id=0){
		if(is_numeric($id)){
			if( $id == 0 && $this->id > 0 )
				$id = $this->id;
			if($id <= 0)
				return -1;
			$query = "DELETE FROM Countries WHERE country_id=$id";
			if( $this->config->debug )
				error_log("[GYMActivity::DEBUG] GenyCountry MySQL DELETE query : $query",0);
			if(mysql_query($query,$this->handle))
				return 1;
			else
				return -1;
		}
		return -1;
	}
	public function insertNewCountry($id,$name){
		$query = "INSERT INTO Countries VALUES($id,'".mysql_real_escape_string($name)."')";
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyCountry MySQL query : $query",0);
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return -1;
		}
	}
	public function getCountriesListWithRestrictions($restrictions){
		// $restrictions is in the form of array("project_id=1","project_status_id=2")
		$last_index = count($restrictions)-1;
		$query = "SELECT country_id,country_name FROM Countries";
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
			error_log("[GYMActivity::DEBUG] GenyCountry MySQL query : $query",0);
		$result = mysql_query($query, $this->handle);
		$country_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_country = new GenyCountry();
				$tmp_country->id = $row[0];
				$tmp_country->name = $row[1];
				$country_list[] = $tmp_country;
			}
		}
// 		mysql_close();
		return $country_list;
	}
	public function getAllCountries(){
		return $this->getCountriesListWithRestrictions( array() );
	}
	public function searchCountries($term){
		$q = mysql_real_escape_string($term);
		return $this->getCountriesListWithRestrictions( array("country_name LIKE '%$q%'") );
	}
	public function loadCountryByName($name){
		$countries = $this->getCountriesListWithRestrictions(array("country_name='".mysql_real_escape_string($name)."'"));
		$country = $countries[0];
		if(isset($country) && $country->id > -1){
			$this->id = $country->id;
			$this->name = $country->name;
		}
	}
	public function loadCountryById($id){
		$countries = $this->getCountriesListWithRestrictions(array("country_id=".mysql_real_escape_string($id)));
		$country = $countries[0];
		if(isset($country) && $country->id > -1){
			$this->id = $country->id;
			$this->name = $country->name;
		}
	}
}
?>
