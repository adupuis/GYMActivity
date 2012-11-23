<?php

// Pour le reporting des congés il faut générer le fichier Excel pour Marie.
// Utilisation de Spreadsheet_Excel_Writer :
// http://pear.php.net/manual/fr/package.fileformats.spreadsheet-excel-writer.intro.php
// pear install Spreadsheet_Excel_Writer

require_once 'Spreadsheet/Excel/Writer.php';
include_once '../classes/GenyWebConfig.php';
include_once '../classes/GenyActivityReportWorkflow.php';
include_once '../classes/GenyActivityReportStatus.php';

$web_config = new GenyWebConfig();
$month = date('m', time());
$year=date('Y', time());
$filename = $web_config->company_name."-Recapitulatif-conges-$month-$year.xls";

// Création d'un manuel de travail
$workbook = new Spreadsheet_Excel_Writer();
// $workbook->setVersion(8);

// Création d'une feuille de travail
$worksheet =& $workbook->addWorksheet('Congés');
$worksheet->setInputEncoding('utf-8');
$worksheet->setColumn(0,4,30);

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

// On affiche les headers
$worksheet->write(0, 0, 'Collaborateur',$format_title);
$worksheet->write(0, 1, 'Date',$format_title);
$worksheet->write(0, 2, "Type de cong\xE9s",$format_title);
$worksheet->write(0, 3, "Soci\xE9t\xE9",$format_title);
$worksheet->write(0, 4, 'Charge en jour',$format_title);

$activity_report_workflow = new GenyActivityReportWorkflow();
$workflow = $activity_report_workflow->getActivityReportsWorkflow();
$geny_ars_approved = new GenyActivityReportStatus();
$geny_ars_approved->loadActivityReportStatusByShortName('APPROVED');
$geny_ars_billed = new GenyActivityReportStatus();
$geny_ars_billed->loadActivityReportStatusByShortName('BILLED');
$geny_ars_paid = new GenyActivityReportStatus();
$geny_ars_paid->loadActivityReportStatusByShortName('PAID');
$geny_ars_close = new GenyActivityReportStatus();
$geny_ars_close->loadActivityReportStatusByShortName('CLOSE');

foreach($workflow as $row) {
	// Oh mon dieu et dire que j'ose écrire ça et que ça me fait même sourrire !
	// TODO: Il faudra quand même mettre 'Congés' dans une property
	if( $row->project_name == 'Congés' && ($row->activity_report_status_id == $geny_ars_approved->id || $row->activity_report_status_id == $geny_ars_billed->id || $row->activity_report_status_id == $geny_ars_paid->id || $row->activity_report_status_id == $geny_ars_close->id) ){
		$worksheet->write(1, 0, "$row->profile_lastname $row->profile_firstname",$format_center);
		$worksheet->write(1, 1, $row->activity_date,$format_center);
		$worksheet->write(1, 2, $row->task_name,$format_center);
		$worksheet->write(1, 3, $row->client_name,$format_center);
		$worksheet->write(1, 4, $row->activity_load/8,$format_center);
		
	}
}

// Envoi du fichier
$workbook->send($filename);
$workbook->close();

?>