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
}

?>