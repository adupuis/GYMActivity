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

$geny_config = new GenyWebConfig();
$tool = new GenyTools();
$month = date('m', time());
$year=date('Y', time());
$start_date="$year-$month-1";
$end_date="$year-$month-31";

$estimated_load = 0;
$user_load = 0;

$geny_ar = new GenyActivityReport();

foreach( GenyTools::getWorkedDaysList(strtotime($start_date),strtotime($end_date)) as $day ){
	$estimated_load += 8;
	$user_load += $geny_ar->getDayLoad($profile->id,$day);
}

$completion = ($user_load*100)/$estimated_load;
if($geny_config->debug) error_log("reporting_cra_fulfilment: completion=$completion",0);

?>

<script type='text/javascript'>
	$(function() {
		$( "#cra_fulfilment" ).progressbar({
			value: <?php echo $completion ; ?>
		});
	});
</script>

<style>
	#cra_fulfilment {
		display: block;
		position: relative;
		top: 60px;
		left: 20px;
		width: 88%;
		height: 20px;
	}
</style>

<li class="reporting_cra">
	<a href="#" onClick=''>
		<span class="dock_item_title">Remplissage CRA</span><br/>
		<span class="dock_item_content">Ce widget vous montre l'état de remplissage de vos CRA pour le mois. Si la barre de progression est incomplète c'est qu'il manque des saisis.<br/>
		</span>
		<div id="cra_fulfilment"></div>
	</a>
</li>
