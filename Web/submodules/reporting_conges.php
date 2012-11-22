<?php

// Pour le reporting des congés il faut générer le fichier Excel pour Marie.
// Utilisation de Spreadsheet_Excel_Writer :
// http://pear.php.net/manual/fr/package.fileformats.spreadsheet-excel-writer.intro.php
// pear install Spreadsheet_Excel_Writer

require_once 'Spreadsheet/Excel/Writer.php';
include_once '../classes/GenyWebConfig.php';

$web_config = new GenyWebConfig();
$month = date('m', time());
$year=date('Y', time());
$filename = $web_config->company_name."-Recapitulatif-conges-$month-$year.xls";

// Création d'un manuel de travail
$workbook = new Spreadsheet_Excel_Writer();
// $workbook->setVersion(8);

// Création d'une feuille de travail
$worksheet =& $workbook->addWorksheet('My first worksheet');
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

// Les données actuelles
$worksheet->write(0, 0, 'Collaborateur',$format_title);
$worksheet->write(0, 1, 'Date',$format_title);
$worksheet->write(0, 2, 'Type de conges',$format_title);
$worksheet->write(0, 3, 'Societe',$format_title);
$worksheet->write(0, 4, 'Charge en jour',$format_title);

$worksheet->write(1, 0, 'John Smith',$format_center);
$worksheet->write(1, 1, '2012-06-23',$format_center);
$worksheet->write(1, 2, 'R.T.T',$format_center);
$worksheet->write(1, 3, 'Genymobile',$format_center);
$worksheet->write(1, 4, 1,$format_center);

// Envoi du fichier
$workbook->send($filename);
$workbook->close();

?>