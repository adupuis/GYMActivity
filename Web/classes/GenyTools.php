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

class GenyTools {
	static function getYearHolidays(){
		$year = (int)date('Y');
		// Liste des jours feriés
		$arr_bank_holidays[] = $year.'-1-1'; // Jour de l'an
		$arr_bank_holidays[] = $year.'-5-1'; // Fete du travail
		$arr_bank_holidays[] = $year.'-5-8'; // Victoire 1945
		$arr_bank_holidays[] = $year.'-7-14'; // Fete nationale
		$arr_bank_holidays[] = $year.'-8-15'; // Assomption
		$arr_bank_holidays[] = $year.'-11-1'; // Toussaint
		$arr_bank_holidays[] = $year.'-11-11'; // Armistice 1918
		$arr_bank_holidays[] = $year.'-12-25'; // Noel
		// Récupération de paques. Permet ensuite d'obtenir le jour de l'ascension et celui de la pentecote
		$easter = easter_date($year);
		$arr_bank_holidays[] = date($year.'-n-j', $easter + 86400); // Paques
		$arr_bank_holidays[] = date($year.'-n-j', $easter + (86400*39)); // Ascension
		$arr_bank_holidays[] = date($year.'-n-j', $easter + (86400*50)); // Pentecote
		return $arr_bank_holidays;
	}
	// Cette fonction prend deux dates en arguments sous forme de timestamp UNIX.
	static function getWorkedDaysList($start_date,$end_date){
		$worked_days = array();
		$arr_bank_holidays = GenyTools::getYearHolidays();
		while ($start_date <= $end_date) {
			// Si le jour suivant n'est ni un dimanche (0) ou un samedi (6), ni un jour férié, on incrémente les jours ouvrés
			if (!in_array(date('w', $start_date), array(0, 6))
			&& !in_array(date(date('Y', $start_date).'-n-j', $start_date), $arr_bank_holidays)) {
				$worked_days[] = date('Y-m-j',$start_date);
			}
			$start_date = mktime(date('H', $start_date), date('i', $start_date), date('s', $start_date), date('m', $start_date), date('d', $start_date) + 1, date('Y', $start_date));
		} 
		return $worked_days;
	}
	
	static function getProfileDisplayName($profile_object){
		$name = $profile_object->login;
		if( $profile_object->firstname && $profile_object->lastname){
			$name = $profile_object->firstname." ".$profile_object->lastname;
		}
		return $name;
	}
	static function getActivityReportStatusAsColoredHtml($s){
		if( $s->shortname == "P_USER_VALIDATION" )
			return "<span style='color: crimson;'>".$s->name."</span>";
		if( $s->shortname == "P_APPROVAL" || $s->shortname == "P_REMOVAL")
			return "<span style='color: orange;'>".$s->name."</span>";
		if( $s->shortname == "APPROVED" )
			return "<span style='color: RoyalBlue;'>".$s->name."</span>";
		if( $s->shortname == "CLOSE" || $s->shortname == "REMOVED" )
			return "<span style='color: SlateGrey;'>".$s->name."</span>";
		if( $s->shortname == "BILLED" || $s->shortname == "PAID" )
			return "<span style='color: green;'>".$s->name."</span>";
		if( $s->shortname == "REFUSED" )
			return "<span style='color: red;'>".$s->name."</span>";
		
	}
	static function getCurrentMonthFirstDayDate(){
		$month = date('m', time());
		$year=date('Y', time());
		$lastday = date('t',mktime(0,0,0,$month,28,$year));
		$start_date="$year-$month-01";
		return $start_date;
	}
	static function getCurrentMonthLastDayDate(){
		$month = date('m', time());
		$year=date('Y', time());
		$lastday = date('t',mktime(0,0,0,$month,28,$year));
		$end_date="$year-$month-$lastday";
		return $end_date;
	}
	static function debug($str){
		error_log("[GYMActivity::DEBUG] $str",0);
	}
	static function getParam($param,$default=""){
		$ret = $default;
		if(isset($_POST[$param]))
			$ret = $_POST[$param];
		else if( isset($_GET[$param]))
			$ret = $_GET[$param];
		if(!is_array($ret))
			return htmlentities($ret,ENT_QUOTES,'UTF-8');
		else {
			foreach($ret as $key => $val) {
				$ret["$key"] = htmlentities($val,ENT_QUOTES,'UTF-8');
			}
			return $ret;
		}
	}
	static function sortMultiArrayCaseInsensitive( $array, $key ) {
		// example: $array = sortMultiArrayCaseInsensitive( $array, "key" );
		for( $i = 0; $i < sizeof( $array ); $i++ ) {
			$sort_values[$i] = $array[$i][$key];
		}
		natcasesort( $sort_values );
		reset( $sort_values );

		while( list( $arr_key, $arr_val ) = each( $sort_values ) ) {
			$sorted_arr[] = $array[$arr_key];
		}
		return $sorted_arr;
	}
}

?>