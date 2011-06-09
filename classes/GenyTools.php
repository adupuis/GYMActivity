<?php

class GenyTools {
	static function getYearHolidaysNG(){
		date_default_timezone_set('Europe/Paris');
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
}

?>