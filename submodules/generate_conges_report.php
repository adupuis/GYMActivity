<?php

// Pour le reporting des congés il faut générer le fichier Excel pour Marie.
// Utilisation de Spreadsheet_Excel_Writer :
// http://pear.php.net/manual/fr/package.fileformats.spreadsheet-excel-writer.intro.php
// pear install Spreadsheet_Excel_Writer
date_default_timezone_set('Europe/Paris');
require_once 'Spreadsheet/Excel/Writer.php';
include_once '../classes/GenyWebConfig.php';
include_once '../classes/GenyActivityReportWorkflow.php';
include_once '../classes/GenyActivityReportStatus.php';
include_once '../classes/GenyTools.php';
include_once '../classes/GenyProperty.php';
include_once '../classes/GenyPropertyValue.php';
include_once '../classes/GenyProject.php';

function convertToUtf8($string) {
	$new_string = str_replace("/","\x2F",$string);
	$new_string = str_replace("é","\xE9",$new_string);
	$new_string = str_replace("è","\xE8",$new_string);
	$new_string = str_replace("ê","\xEA",$new_string);
	$new_string = str_replace("ë","\xEA",$new_string);
	$new_string = str_replace("<","\x3C",$new_string);
	$new_string = str_replace(">","\x3E",$new_string);
	$new_string = str_replace("-","\x2D",$new_string);
	
	return $new_string;
}


$web_config = new GenyWebConfig();
$month = date('m', time());
$year=date('Y', time());
$filename = $web_config->company_name."-Recapitulatif-conges-$month-$year.xls";
$worksheets = array();
$worksheets_indexes = array();

// Création d'un manuel de travail
$workbook = new Spreadsheet_Excel_Writer();
// $workbook->setVersion(8);

// Format 
// Couleurs Geny
$workbook->setCustomColor(42, 106, 106, 106); // Couleur police
$workbook->setCustomColor(43, 132, 190, 93); // Couleur background
$format_title =& $workbook->addFormat();
$format_title->setBold();
$format_title->setColor(42);
$format_title->setPattern(1);
$format_title->setFgColor(43);
$format_title->setHAlign('center');
$format_title->setVAlign('vcenter');

$format_center =& $workbook->addFormat();
$format_center->setHAlign('center');

// Récupération des status intéressant
$activity_report_workflow = new GenyActivityReportWorkflow();
$workflow = $activity_report_workflow->getActivityReportsWorkflowWithRestrictions( array( "activity_date >= '".GenyTools::getCurrentMonthFirstDayDate()."'", "activity_date <= '".GenyTools::getCurrentMonthLastDayDate()."'" ) );
$geny_ars_approved = new GenyActivityReportStatus();
$geny_ars_approved->loadActivityReportStatusByShortName('APPROVED');
$geny_ars_billed = new GenyActivityReportStatus();
$geny_ars_billed->loadActivityReportStatusByShortName('BILLED');
$geny_ars_paid = new GenyActivityReportStatus();
$geny_ars_paid->loadActivityReportStatusByShortName('PAID');
$geny_ars_close = new GenyActivityReportStatus();
$geny_ars_close->loadActivityReportStatusByShortName('CLOSE');

// TODO: Il faut que cette propriété soit une multi... Mais là franchement j'en peut plus du PHP !
$prop = new GenyProperty();
$prop->loadPropertyByName('CONGES_REPORT_PROJECT_NAMES');
$prop_values = $prop->getPropertyValues();
if( sizeof($prop_values) > 1 ){
	GenyTools::debug("[generate_conges_report] there is more than 1 value in CONGES_REPORT_PROJECT_NAMES, only the first one is used.");
}

$projects = array();
foreach (explode(',',html_entity_decode($prop_values[0]->content)) as $project_id){
	$tmp_proj = new GenyProject($project_id);
	$projects[] = $tmp_proj->name;
	GenyTools::debug("[generate_conges_report] new project for id: $project_id. Object id:".$tmp_proj->id." name : ".$tmp_proj->name);
}

foreach($workflow as $row) {
	GenyTools::debug("[generate_conges_report] $row->project_name.");
	if( in_array($row->project_name, $projects ) && ($row->activity_report_status_id == $geny_ars_approved->id || $row->activity_report_status_id == $geny_ars_billed->id || $row->activity_report_status_id == $geny_ars_paid->id || $row->activity_report_status_id == $geny_ars_close->id) ){
		if( ! array_key_exists($row->task_name, $worksheets) ) {
			// Création d'une feuille de travail
			$worksheet =& $workbook->addWorksheet(convertToUtf8($row->task_name));
			$worksheet->setInputEncoding('utf-8');
			$worksheet->setColumn(0,4,30);
			// On affiche les headers
			$worksheet->write(0, 0, 'Collaborateur',$format_title);
			$worksheet->write(0, 1, 'Date',$format_title);
			$worksheet->write(0, 2, "Type de cong\xE9s",$format_title);
			$worksheet->write(0, 3, "Soci\xE9t\xE9",$format_title);
			$worksheet->write(0, 4, 'Charge en jour',$format_title);
			$worksheets[$row->task_name] = $worksheet;
			$worksheets_indexes[$row->task_name] = 1;
		}
		$worksheets[$row->task_name]->write($worksheets_indexes[$row->task_name], 0, convertToUtf8("$row->profile_lastname $row->profile_firstname"),$format_center);
		$worksheets[$row->task_name]->write($worksheets_indexes[$row->task_name], 1, $row->activity_date,$format_center);
		$worksheets[$row->task_name]->write($worksheets_indexes[$row->task_name], 2, convertToUtf8($row->task_name),$format_center);
		$worksheets[$row->task_name]->write($worksheets_indexes[$row->task_name], 3, convertToUtf8($row->client_name),$format_center);
		$worksheets[$row->task_name]->write($worksheets_indexes[$row->task_name], 4, $row->activity_load/8,$format_center);
		$worksheets_indexes[$row->task_name]++;
	}
}

// Envoi du fichier
$workbook->send($filename);
$workbook->close();

?>
