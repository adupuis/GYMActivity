<?php

// Pour le reporting des CRA (2012) il faut générer un fichier Excel.
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
$year = '2012';
$filename = $web_config->company_name."-Recapitulatif-CRA-$year.xls";
$worksheets = array();
$worksheets_indexes = array();

// Création d'un manuel de travail
$workbook = new Spreadsheet_Excel_Writer();
// $workbook->setVersion(8);
$worksheets = array();

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
$workflow = $activity_report_workflow->getActivityReportsWorkflowWithRestrictions( array( "activity_date >= '2012-01-01'", "activity_date <= '2012-12-31'" ) );
$geny_ars_approved = new GenyActivityReportStatus();
$geny_ars_approved->loadActivityReportStatusByShortName('APPROVED');
$geny_ars_billed = new GenyActivityReportStatus();
$geny_ars_billed->loadActivityReportStatusByShortName('BILLED');
$geny_ars_paid = new GenyActivityReportStatus();
$geny_ars_paid->loadActivityReportStatusByShortName('PAID');
$geny_ars_close = new GenyActivityReportStatus();
$geny_ars_close->loadActivityReportStatusByShortName('CLOSE');


$worksheet =& $workbook->addWorksheet('2012');
$worksheet->setInputEncoding('utf-8');
$worksheet->setColumn(0,4,30);
$worksheet->write(0, 0, 'Collaborateur',$format_title);
$worksheet->write(0, 1, 'Date',$format_title);
$worksheet->write(0, 2, "Projet",$format_title);
$worksheet->write(0, 3, "Soci\xE9t\xE9",$format_title);
$worksheet->write(0, 4, 'Charge en jour',$format_title);
$worksheets_indexes=1;

foreach($workflow as $row) {
	GenyTools::debug("[generate_cra_report] $row->project_name.");
	if( $row->activity_report_status_id == $geny_ars_approved->id || $row->activity_report_status_id == $geny_ars_billed->id || $row->activity_report_status_id == $geny_ars_paid->id || $row->activity_report_status_id == $geny_ars_close->id ){
		$worksheet->write($worksheets_indexes, 0, convertToUtf8("$row->profile_lastname $row->profile_firstname"),$format_center);
		$worksheet->write($worksheets_indexes, 1, $row->activity_date,$format_center);
		$worksheet->write($worksheets_indexes, 2, convertToUtf8($row->task_name),$format_center);
		$worksheet->write($worksheets_indexes, 3, convertToUtf8($row->client_name),$format_center);
		$worksheet->write($worksheets_indexes, 4, $row->activity_load/8,$format_center);
		$worksheets_indexes++;
	}
}

// Envoi du fichier
$workbook->send($filename);
$workbook->close();

?>