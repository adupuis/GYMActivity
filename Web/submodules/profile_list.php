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


$geny_rg = new GenyRightsGroup();
foreach( $geny_rg->getAllRightsGroups() as $group ){
	$groups[$group->id] = $group;
}

?>
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
				
				// Ignore html
				if( sValue.indexOf("<a") >= 0 ) continue;
				
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
			
				var oTable = $('#profile_list').dataTable( {
					"bJQueryUI": true,
					"bStateSave": true,
					"bAutoWidth": false,
					"sCookiePrefix": "GYMActivity_",
					"sPaginationType": "full_numbers",
					"oLanguage": {
						"sSearch": "Recherche :",
						"sLengthMenu": "Profiles par page _MENU_",
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
				$("tfoot th").each( function ( i ) {
					if( i == 6){
						this.innerHTML = fnCreateSelect( oTable.fnGetColumnData(i) );
						$('select', this).change( function () {
							oTable.fnFilter( $(this).val(), i );
						} );
					}
				} );
			});
</script>
<div id="mainarea">
	<p class="mainarea_title">
		<span class="profile_list">
			Liste des profils
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Voici la liste des profils dans la base des utilisateurs.
		</p>
		<style>
			@import 'styles/<?php echo $web_config->theme ?>/profile_list.css';
		</style>
		<div class="table_container">
		<p>
			<table id="profile_list" class='table_hover_effect'>
			<thead>
				<tr>
					<th>Login</th>
					<th>Prénom</th>
					<th>Nom</th>
					<th>Email</th>
					<th>Actif</th>
					<th>R-à-Z Password requis</th>
					<th>Groupe</th>
					<th>Éditer</th>
					<th>Supprimer</th>
				</tr>
			</thead>
			<tbody>
			<?php
				function getImage($bool){
					if($bool == 1)
						return 'button_success_small.png';
					else
						return 'button_error_small.png';
				}
				foreach( $profile->getAllProfiles() as $tmp ){
					echo "<tr><td>$tmp->login</td><td>$tmp->firstname</td><td>$tmp->lastname</td><td>$tmp->email</td><td><img src='images/$web_config->theme/".getImage($tmp->is_active)."' /></td><td><img src='images/$web_config->theme/".getImage($tmp->needs_password_reset)."' /></td><td>".$groups["$tmp->rights_group_id"]->name."</td><td><a href='loader.php?module=profile_edit&load_profile=true&profile_id=$tmp->id' title='Editer le profile'><img src='images/".$web_config->theme."/profile_edit_small.png' alt='Editer le profile'></a></td><td><a href='loader.php?module=profile_remove&profile_id=$tmp->id' title='Supprimer définitivement le profile'><img src='images/".$web_config->theme."/profile_remove_small.png' alt='Supprimer définitivement le profile'></a></td></tr>";
				}
			?>
			</tbody>
			<tfoot>
				<tr>
					<th>Login</th>
					<th>Prénom</th>
					<th>Nom</th>
					<th>Email</th>
					<th>Actif</th>
					<th>R-à-Z Password requis</th>
					<th>Groupe</th>
					<th>Éditer</th>
					<th>Supprimer</th>
				</tr>
			</tfoot>
			</table>
		</p>
		</div>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php','backend/widgets/profile_add.dock.widget.php');
?>
