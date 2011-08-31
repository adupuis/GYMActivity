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

$month = date('m', time());
$year=date('Y', time());
$lastday = date('t',mktime(0,0,0,$month,28,$year));
$start_date="$year-$month-01";
$end_date="$year-$month-$lastday";

?>

<li class="reporting_monthly_view">
	<a href="reporting_load.php?reporting_start_date=<?php echo $start_date; ?>&reporting_end_date=<?php echo $end_date; ?>">
		<span class="dock_item_title">Reporting Mensuel</span><br/>
		<span class="dock_item_content">Reporting sur l'activité mensuelle.</span>
	</a>
</li>