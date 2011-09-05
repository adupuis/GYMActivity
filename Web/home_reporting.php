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

// Variable to configure global behaviour
$header_title = '%COMPANY_NAME% - Reporting';
$required_group_rights = 6;

include_once 'header.php';
include_once 'menu.php';


?>

<div class="page_title">
	<img src="images/default/reporting.png"/><p>Reporting</p>
</div>

<div id="maindock">
	<ul>
		<?php
			include 'backend/widgets/reporting_cra_fulfilment.dock.widget.php';
			if( in_array($profile->rights_group_id, array(1,2,4,5)) ){
				include 'backend/widgets/reporting_monthly_view.dock.widget.php';
				include 'backend/widgets/reporting_previous_month_view.dock.widget.php';
				include 'backend/widgets/reporting_load.dock.widget.php';
				include 'backend/widgets/reporting_cra_completion.dock.widget.php';
				include 'backend/widgets/reporting_cra_status.dock.widget.php';
			}
		?>
	</ul>
</div>

<?php
include_once 'footer.php';
?>
