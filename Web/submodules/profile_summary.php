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
$geny_rights_group = new GenyRightsGroup( $profile->rights_group_id );
$geny_pmd = new GenyProfileManagementData();
$geny_pmd->loadProfileManagementDataByProfileId($profile->id);
$geny_hs = new GenyHolidaySummary();
$geny_ce = new GenyCareerEvent();

$data_array = array();
$data_array_filters = array( 0 => array(), 2 => array(), 4 => array(), 5 => array() );

// Nous ne pouvons avoir qu'un seul solde de congés valide pour une période annuelle
$hs_cp = $geny_hs->getCurrentCPSummaryByProfileId($profile->id);

// Idem pour les RTT
$hs_rtt = $geny_hs->getCurrentRTTSummaryByProfileId($profile->id);

// TODO : faire la construction des data_array* (c'est chiant...)


function ceTypeToHtml($type="neutral"){
	if ( $type == "positive" ) {
		return "<span style='color: green;'>Positif</span>";
	}
	elseif ( $type == "negative" ) {
		return "<span style='color: red;'>Négatif</span>";
	}
	return "Neutre";
}

function ceAgreementToHtml($ce_id,$agreement,$theme) {
	if( $agreement == 0){
		return "<a href='#ce_agreement_validation_$ce_id' rel='prettyPhoto[ce_$ce_id]'><img src='images/$theme/edit_add_small.png'></a>";
	}
}


?>
<script>
	var indexData = new Array();
	<?php
		if(array_key_exists("GYMActivity_ce_list_table_profile_summary_php", $_COOKIE)) {
			$cookie = json_decode($_COOKIE["GYMActivity_ce_list_table_profile_summary_php"]);
		}

		$data_array_filters_html = array();
		foreach( $data_array_filters as $idx => $data ){
			$data_array_filters_html[$idx] = '<select><option value=""></option>';
			foreach( $data as $d ){
				if( isset($cookie) && htmlspecialchars_decode(urldecode($cookie->aaSearchCols[$idx][0]),ENT_QUOTES) == htmlspecialchars_decode($d,ENT_QUOTES) )
					$data_array_filters_html[$idx] .= '<option selected="selected" value="'.htmlentities($d,ENT_QUOTES,'UTF-8').'">'.htmlentities($d,ENT_QUOTES,'UTF-8').'</option>';
				else
					$data_array_filters_html[$idx] .= '<option value="'.htmlentities($d,ENT_QUOTES,'UTF-8').'">'.htmlentities($d,ENT_QUOTES,'UTF-8').'</option>';
			}
			$data_array_filters_html[$idx] .= '</select>';
		}
		foreach( $data_array_filters_html as $idx => $html ){
			echo "indexData[$idx] = '$html';\n";
		}
	?>
	jQuery(document).ready(function(){
		var oTable = $('#ce_list_table').dataTable( {
			"bJQueryUI": true,
			"bStateSave": true,
			"bAutoWidth": false,
			"sCookiePrefix": "GYMActivity_",
			"sPaginationType": "full_numbers",
			"oLanguage": {
				"sSearch": "Recherche :",
				"sLengthMenu": "Évènements par page _MENU_",
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
		if( i==0 || i == 2 || i == 4 || i == 5){
			this.innerHTML = indexData[i];
			$('select', this).change( function () {
				oTable.fnFilter( $(this).val(), i );
			} );
		}
	} );
	
	});
	
	function onCheckBoxSelectAll(){
		$("#ce_list_table").find(':checkbox').attr('checked', $('#chkBoxSelectAll').attr('checked'));
	}
	
	function submit_ce_agreement(agreement_type,ce_id,ce_vote){
		
	}
	
</script>
<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/profile_generic.png"></img>
		<span class="profile_add">
			Résumé du profil
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Dans cette page vous trouverez toutes les informations relatives à votre profil chez <?php echo $web_config->company_name ?>. Vous trouverez aussi la liste complètes des évènements de carrière au sein de l'entreprise.
		</p>
		<style>
		@import 'styles/<?php echo $web_config->theme ?>/profile_summary.css';
		</style>
		<ul class="ps_float">
			<li>
				<strong>Nom : </strong> <?php echo $profile->lastname ; ?><br/>
				<strong>Prénom : </strong> <?php echo $profile->firstname ; ?><br/>
				<strong>Login : </strong> <?php echo $profile->login ; ?><br/>
				<strong>Email : </strong> <?php echo $profile->email ; ?><br/>
				<strong>Groupe : </strong> <?php echo $geny_rights_group->name ; ?>
			</li>
			<li>
				<strong>Facturable : </strong> <?php if($geny_pmd->is_billable){ echo 'Oui' ;}else{echo 'Non';} ?><br/>
				<strong>Date de recrutement : </strong> <?php echo $geny_pmd->recruitement_date ;?><br/>
				<strong>Salaire (brut annuel) : </strong> <?php echo $geny_pmd->salary ;?> &euro;<br/>
				<strong>Date de disponibilité : </strong> <?php echo $geny_pmd->availability_date ;?>
			</li>
			<li>
				<strong><u>Congés Payés pour la période du <?php echo $hs_cp->period_start." au ".$hs_cp->period_end; ?></u></strong><br/>
				<strong>Congés acquis : </strong><?php echo $hs_cp->count_acquired; ?><br/>
				<strong>Congés pris : </strong><?php echo $hs_cp->count_taken; ?><br/>
				<strong>Congés restant : </strong><?php echo $hs_cp->count_remaining; ?>
			</li>
			<li>
				<strong><u>RTT pour la période du <?php echo $hs_rtt->period_start." au ".$hs_rtt->period_end; ?></u></strong><br/>
				<strong>Congés acquis : </strong><?php echo $hs_rtt->count_acquired; ?><br/>
				<strong>Congés pris : </strong><?php echo $hs_rtt->count_taken; ?><br/>
				<strong>Congés restant : </strong><?php echo $hs_rtt->count_remaining; ?>
			</li>
			<?php
				if($profile->rights_group_id == $geny_rights_group->getIdByShortname('ADM') || $profile->rights_group_id == $geny_rights_group->getIdByShortname('TM')){
			?>
			<li>
				<a href='#create_career_event' rel='prettyPhoto[create_career_event]' class="submit">Ajouter un évènement de carrière</a>
			</li>
			<?php
				}
			?>
		</ul>
	</p>
	<div class="table_container">
		<p>
			<table id="ce_list_table" style="color: black; width: 100%;">
				<thead>
					<th>Date</th>
					<th>Titre</th>
					<th>Type</th>
					<th>Texte</th>
					<th>Accord employé</th>
					<th>Accord manager</th>
				</thead>
				<tbody>
				<?php
				foreach( $geny_ce->getCareerEventListByProfileId($profile->id) as $ce ){
					echo "<tr class='centered'><td>".date('Y-m-d',$ce->timestamp)."</td><td>$ce->title</td><td>".ceTypeToHtml($ce->type)."</td><td>$ce->text</td><td id='ce_employee_agreement_$ce->id'><a href='#ce_employee_agreement_$ce->id' onclick=\"submit_ce_agreement('employee_agreement',$ce->id,1)\"><img src='images/$web_config->theme/idea_vote_up_small.png' /></a>&nbsp;&nbsp;<a  href='#' onclick=\"submit_ce_agreement('employee_agreement',$ce->id,-1)\"><img src='images/$web_config->theme/idea_vote_down_small.png' /></a></td><td>".ceAgreementToHtml($ce->id,$ce->manager_agreement,$web_config->theme)."</td></tr>";
					
				}
				?>
				</tbody>
				<tfoot>
					<th>Date</th>
					<th>Titre</th>
					<th>Type</th>
					<th>Texte</th>
					<th>Accord employé</th>
					<th>Accord manager</th>
				</tfoot>
			</table>
		</p>
	</div>
</div>

<!-- Cette div permet contient le formulaire de création d'un career event -->
<div id='create_career_event' style="display: none;">
	<form id="form_career_event_add" class="popup">
		<p>
			<label for="ce_type">Type</label>
			<select name="ce_type" id="ce_type">
				<option value="negative">Négatif</option>
				<option value="neutral" selected="selected">Neutre</option>
				<option value="positive">Positif</option>
			</select>
		</p>
		<p>
			<label for="ce_title" onchange="ce_title=$(this).val();">Titre</label>
			<input name="ce_title" id="ce_title" type="text" class="validate[required] text-input" />
		</p>
		<p>
			<label for="ce_description">Description</label>
			<textarea name="ce_description" id="ce_description" class="validate[required] text-input"></textarea>
		</p>
		<p>
			<a href="#" id="submit_ce" class="submit">Ajouter</a> <a href="#" id="close_popup" onclick="$.prettyPhoto.close()" class="submit" >Annuler</a>
		</p>
	</form>
</div>

<script>
$("a[rel='prettyPhoto[create_career_event]']").prettyPhoto({modal: 'true',animation_speed:'fast',slideshow:false, hideflash: true, social_tools: '<div class="pp_social" id="status_message_display"></div>', theme: 'pp_default', default_width: 700, keyboard_shortcuts: false});
// Les éléments dans TR sont centrés
$.fn.dataTableExt.oJUIClasses.sStripOdd = "centered odd";
$.fn.dataTableExt.oJUIClasses.sStripEven = "centered even";

$(document).on("click", "div#pp_full_res #submit_ce", function(){
	var ce_type = $("div#pp_full_res #ce_type").val();
	var ce_title = $('div#pp_full_res #ce_title').val();
	var ce_description = $('div#pp_full_res #ce_description').val();
	var error_string = "Les champs suivants ne peuvent être vide:\n";
	if( ce_title == "" || ce_description == "" ){
		if( ce_title == "" ){
			error_string += "Titre\n";
		}
		if( ce_description == "" ){
			error_string += "Description\n";
		}
		alert(error_string);
	}
	else{
		console.log("About to send AJAX request");
		jQuery.get("backend/api/create_career_event.php?type="+encodeURIComponent(ce_type)+"&title="+encodeURIComponent(ce_title)+"&text="+encodeURIComponent(ce_description), function(data){
			console.log("Back from AJAX, processing");
			console.log("status="+data.status);
			console.log("status_message="+data.status_message);
			$(".pp_social #status_message_display").empty();
			if( data.status == "success" ){
				$('#ce_list_table').dataTable().fnAddData( [
				"<?php echo date('Y-m-d',time()); ?>",
				ce_title,
				ce_type,
				ce_description,
				0,
				0
				] );
				$(".pp_social #status_message_display").append("<strong style='color: green;'>"+data.status_message+"</strong>");
				$("div#pp_full_res #close_popup").empty();
				$("div#pp_full_res #close_popup").append("Fermer");
			}
			else{
				$(".pp_social #status_message_display").append("<strong style='color: red;'>"+data.status_message+"</strong>");
			}
		},"json");
	}
} );

$("#pp_full_res #form_career_event_add").validationEngine('init');
// binds form submission and fields to the validation engine
$("##pp_full_res #form_career_event_add").validationEngine('attach');

</script>
<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php');
?>
