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


date_default_timezone_set('Europe/Paris');

?>


<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/conges_list.png"></img>
		<span class="conges_list">
			Tableau des congés collaborateurs
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Overview des congés collaborateurs -15/+15 jours.<br />
		</p>
		<script>
			
		
			(function($) {
			/*
			 * Function: fnGetColumnData
			 * Purpose:  Return an array of table values from a particular column.
			 * Returns:  array string: 1d data array 
			 * Inputs:   object:oSettings - dataTable settings object. This is always the last argument past to the function
			 *           int:iColumn - the id of the column to extract the data from
			 *           bool:bUnique - optional - if set to false duplicated values are not filtered out
			 *           bool:bFiltered - optional - if set to false all the table data is used (not only the filtered)
			 *           bool:bIgnoreEmpty - optional - if set to false empty values are not filtered from the result array
			 * Author:   Benedikt Forchhammer <b.forchhammer /AT\ mind2.de>
			 */
			$.fn.dataTableExt.oApi.fnGetColumnData = function ( oSettings, iColumn, bUnique, bFiltered, bIgnoreEmpty ) {
				// check that we have a column id
				if ( typeof iColumn == "undefined" ) return new Array();
				
				// by default we only wany unique data
				if ( typeof bUnique == "undefined" ) bUnique = true;
				
				// by default we do want to only look at filtered data
				if ( typeof bFiltered == "undefined" ) bFiltered = true;
				
				// by default we do not wany to include empty values
				if ( typeof bIgnoreEmpty == "undefined" ) bIgnoreEmpty = true;
				
				// list of rows which we're going to loop through
				var aiRows;
				
				// use only filtered rows
				if (bFiltered == true) aiRows = oSettings.aiDisplay; 
				// use all rows
				else aiRows = oSettings.aiDisplayMaster; // all row numbers
			
				// set up data array	
				var asResultData = new Array();
				
				for (var i=0,c=aiRows.length; i<c; i++) {
					iRow = aiRows[i];
					var aData = this.fnGetData(iRow);
					var sValue = aData[iColumn];
					
					// ignore empty values?
					if (bIgnoreEmpty == true && sValue.length == 0) continue;
			
					// ignore unique values?
					else if (bUnique == true && jQuery.inArray(sValue, asResultData) > -1) continue;
					
					// else push the value onto the result data array
					else asResultData.push(sValue);
				}
				
				return asResultData;
			}}(jQuery));


			function fnCreateSelect( aData )
			{
				var r='<select><option value=""></option>', i, iLen=aData.length;
				for ( i=0 ; i<iLen ; i++ )
				{
					r += '<option value="'+aData[i]+'">'+aData[i]+'</option>';
				}
				return r+'</select>';
			}

		
			jQuery(document).ready(function(){
				
				var oTable = $('#conges_view_table').dataTable( {
					"bDeferRender": true,
					"bJQueryUI": true,
					"bStateSave": true,
					"bAutoWidth": false,
					"sCookiePrefix": "GYMActivity_",
					"iCookieDuration": 60*60*24*365, // 1 year
					"sPaginationType": "full_numbers",
					"oLanguage": {
						"sSearch": "Recherche :",
						"sLengthMenu": "Rapport par page _MENU_",
						"sZeroRecords": "Aucun résultat",
						"sInfo": "Aff. _START_ à _END_ de _TOTAL_ enregistrements",
						"sInfoEmpty": "Aff. 0 à 0 de 0 enregistrements",
						"sInfoFiltered": "(filtré de _MAX_ enregistrements)",
						"oPaginate":{ 
							"sFirst":"Début",
							"sLast": "Fin",
							"sNext": "Suivant",
							"sPrevious": "Précédent"
						}
					}
				} );
				/* Add a select menu for each TH element in the table footer */
				/* i+1 is to avoid the first row wich contains a <input> tag without any informations */
				$("tfoot th").each( function ( i ) {
					if( i==0){
						this.innerHTML = fnCreateSelect( oTable.fnGetColumnData(i) );
						$('select', this).change( function () {
							oTable.fnFilter( $(this).val(), i );
						} );
					}
				} );
			
			});
		</script>
		
		
		<?php
		// create the filters
		$dateStart = date('Y.m.d',strtotime("-15 days"));
		$dateEnd = date('Y.m.d',strtotime("+15 days"));
		
		// retrieve all days off with good filter
		$filter = array();
		array_push($filter, "activity_date >= \"$dateStart\"");
		array_push($filter, "activity_date <= \"$dateEnd\"");
		array_push($filter, "project_name = \"Congés\"");
		$activity_report_workflow = new GenyActivityReportWorkflow();
		$activities = $activity_report_workflow->getActivityReportsWorkflowWithRestrictions($filter);
		
		// put it on a table by profile_id
		$holidays = array();
		foreach( $activities as $activity ){
			$holidays[$activity->profile_id][$activity->activity_date] = true;
		}
		?>
		
		<form id="formID" action="#" method="post" class="table_container">
			<p>
				<table id="conges_view_table" style="color: black; width: 100%;">
					<thead>
						<th>Login</th>
						<th>Prénom</th>
						<th>Nom</th>
						<?php
						for ($cpt=-15; $cpt <=15; $cpt++) {
							print "<th style='font-size:10px;'>" . date('d.m',strtotime($cpt . " days")) . "</th>";
						}
						?>
					</thead>
					<tbody>
					<?php
					
					$geny_rg = new GenyRightsGroup();
					foreach( $geny_rg->getAllRightsGroups() as $group ){
						$groups[$group->id] = $group;
					}
					foreach( $profile->getAllProfiles() as $tmp ){
						if ($groups[$tmp->rights_group_id]->name != "Externes" && $tmp->is_active) {
							echo "<tr>";
							//echo "<td>$tmp->id</td>";
							echo "<td>$tmp->login</td>";
							echo "<td>$tmp->firstname</td>";
							echo "<td>$tmp->lastname</td>";
							for ($cpt=-15; $cpt <=15; $cpt++) {
								$currentdate = date('Y.m.d',strtotime($cpt . " days"));
								if (isset($holidays[$tmp->id]) && isset($holidays[$tmp->id][$currentdate]) && $holidays[$tmp->id][$currentdate] == true) {
									echo "<td style='background-color:#A376E4; border:1px solid #B7B7B7'></td>"; //gris = en conges
								}
								else {
									echo "<td style='background-color:#A3E476; border:1px solid #eeeeee'></td>"; //vert = dispo
								}
							}
							echo "</tr>";
						}
				
				}
					?>
					</tbody>
					<tfoot>
						<th>Login</th>
						<th>Prénom</th>
						<th>Nom</th>
						<?php
						for ($cpt=-15; $cpt <=15; $cpt++) {
							print "<th style='font-size:10px;'>" . date('d.m',strtotime($cpt . " days")) . "</th>";
						}
						?>
					</tfoot>
				</table>
			</p>
		</form>
	</p>
</div>

<?php
	$bottomdock_items = array('backend/widgets/conges_add.dock.widget.php','backend/widgets/conges_validation.dock.widget.php');
?>
