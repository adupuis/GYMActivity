<?php

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
	function getWorkedDaysList($start_date,$end_date){
		$worked_days = array();
		while ($date_start < $date_stop) {
			// Si le jour suivant n'est ni un dimanche (0) ou un samedi (6), ni un jour férié, on incrémente les jours ouvrés
			if (!in_array(date('w', $date_start), array(0, 6))
			&& !in_array(date(date('Y', $date_start).'-n-j', $date_start), $arr_bank_holidays)) {
				$worked_days[] = date('Y-m-j',$start_date);
			}
			$date_start = mktime(date('H', $date_start), date('i', $date_start), date('s', $date_start), date('m', $date_start), date('d', $date_start) + 1, date('Y', $date_start));
		} 
		return $worked_days;
	}
}

?>