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

$param_profile_id = GenyTools::getParam("profile_id",$profile->id);

date_default_timezone_set('Europe/Paris');
$geny_profile = new GenyProfile( $param_profile_id );
$geny_rights_group = new GenyRightsGroup( $geny_profile->rights_group_id );
$geny_pmd = new GenyProfileManagementData();
$geny_pmd->loadProfileManagementDataByProfileId($geny_profile->id);
$geny_hs = new GenyHolidaySummary();
$geny_ce = new GenyCareerEvent();
$geny_profile_category_option = new GenyPropertyOption( $geny_pmd->category );

$data_array = array();
$data_array_filters = array( 0 => array(), 2 => array('Über positif','Positif','Neutre','Négatif','Faute') );

// Nous ne pouvons avoir qu'un seul solde de congés valide pour une période annuelle
$geny_hs->setDebug(true);
$hs_cp = $geny_hs->getCurrentCPSummaryByProfileId($geny_profile->id);
$geny_hs->setDebug(false);

// Idem pour les RTT
$hs_rtt = $geny_hs->getCurrentRTTSummaryByProfileId($geny_profile->id);

// Nous ne pouvons avoir qu'un seul solde de congés valide pour une période annuelle
$prev_hs_cp = $geny_hs->getPreviousCPSummaryByProfileId($geny_profile->id);

// Idem pour les RTT
$prev_hs_rtt = $geny_hs->getPreviousRTTSummaryByProfileId($geny_profile->id);


foreach( $geny_ce->getCareerEventListByProfileId($geny_profile->id) as $ce ){
	$ce_date = date('Y-m-d',$ce->timestamp);
	if( ! in_array($ce_date,$data_array_filters[0]) )
		$data_array_filters[0][] = $ce_date;
}

function ceTypeToHtml($type="neutral"){
	if ( $type == "positive" ) {
		return "<span style='color: #4169E1;'>Positif</span>";
	}
	elseif ( $type == "negative" ) {
		return "<span style='color: orange;'>Négatif</span>";
	}
	elseif ( $type == "fault" ) {
		return "<span style='color: red;font-weight: bold;'>Faute</span>";
	}
	elseif ( $type == "uber" ) {
		return "<span style='color: green;font-weight: bold;'>Über positif</span>";
	}
	return "Neutre";
}

function ceAgreementToHtml($type,$ce_id,$agreement,$theme,$current_profile,$consulted_profile) {
	$cp_pmd = new GenyProfileManagementData();
	$cp_pmd->loadProfileManagementDataByProfileId($consulted_profile->id);
	$grg = new GenyRightsGroup($current_profile->rights_group_id);
	if( $agreement == 0){
		if($type == 'employee_agreement' && $current_profile->id == $consulted_profile->id){
			return "<a href='#ce_employee_agreement_$ce_id' class='ceVoteLink' ceId='$ce_id' ceAgreementType='employee_agreement' ceVote='1'><img src='images/$theme/idea_vote_up_small.png' /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a  href='#ce_employee_agreement_$ce_id' class='ceVoteLink' ceId='$ce_id' ceAgreementType='employee_agreement' ceVote='-1'><img src='images/$theme/idea_vote_down_small.png' /></a>";
			
		}
		elseif ($type == 'manager_agreement' && ($current_profile->rights_group_id == $grg->getIdByShortname('ADM') || $current_profile->rights_group_id == $grg->getIdByShortname('TM')) && $current_profile->id == $cp_pmd->group_leader_id ) {
			return "<a href='#ce_manager_agreement_$ce_id' class='ceVoteLink' ceId='$ce_id' ceAgreementType='manager_agreement' ceVote='1'><img src='images/$theme/idea_vote_up_small.png' /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a  href='#ce_manager_agreement_$ce_id' class='ceVoteLink' ceId='$ce_id' ceAgreementType='manager_agreement' ceVote='-1'><img src='images/$theme/idea_vote_down_small.png' /></a>";
		}
		else {
			return "<img src='images/$theme/edit_add_small.png'>";
		}
	}
	elseif ($agreement == 1) {
		return "<img src='images/$theme/idea_vote_up_small.png' />";
	}
	elseif ($agreement == -1) {
		return "<img src='images/$theme/idea_vote_down_small.png' />";
	}
	
	return "<img src='images/$theme/edit_add_small.png'>";
}


?>
<script>
	var indexData = new Array();
	<?php
		if(array_key_exists("GYMActivity_profile_summary_table_loader_php", $_COOKIE)) {
			$cookie = json_decode($_COOKIE["GYMActivity_profile_summary_table_loader_php"]);
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
			"bDeferRender": true,
			"bJQueryUI": true,
			"bStateSave": true,
			"bAutoWidth": false,
			"sCookiePrefix": "GYMActivity_",
			"iCookieDuration": 60*60*24*365, // 1 year
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
		if( i==0 || i == 2){
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
				<strong>Nom : </strong> <?php echo $geny_profile->lastname ; ?><br/>
				<strong>Prénom : </strong> <?php echo $geny_profile->firstname ; ?><br/>
				<strong>Login : </strong> <?php echo $geny_profile->login ; ?><br/>
				<strong>Email : </strong> <?php echo $geny_profile->email ; ?><br/>
				<strong>Groupe : </strong> <?php echo $geny_rights_group->name ; ?>
			</li>
			<li>
				<strong>Catégorie : </strong> <?php echo $geny_profile_category_option->content ; ?><br/>
				<strong>Facturable : </strong> <?php if($geny_pmd->is_billable){ echo 'Oui' ;}else{echo 'Non';} ?><br/>
				<strong>Date de recrutement : </strong> <?php echo $geny_pmd->recruitement_date ;?><br/>
				<strong>Salaire (brut annuel) : </strong> <?php echo $geny_pmd->salary ;?> &euro;<br/>
				<strong>Salaire Variable (brut annuel) : </strong> <?php echo $geny_pmd->variable_salary ;?> &euro;<br/>
				<strong>Prime sur objectif (brut annuel) : </strong> <?php echo $geny_pmd->objectived_salary ;?> &euro;<br/>
				<strong>Date de disponibilité : </strong> <?php echo $geny_pmd->availability_date ;?>
			</li>
			<li>
				<strong>Group Leader : </strong> <?php $gl = new GenyProfile( $geny_pmd->group_leader_id ); echo GenyTools::getProfileDisplayName( $gl ); ?><br/>
				<strong>Technology Leader : </strong> <?php $gl = new GenyProfile( $geny_pmd->technology_leader_id ); echo GenyTools::getProfileDisplayName( $gl ); ?>
			</li>
			<?php
				if( ($prev_hs_cp->count_acquired - $prev_hs_cp->count_taken) > 0 && $prev_hs_cp->count_remaining > 0 ){
			?>
			<li>
				<strong><u>Congés Payés pour la période du <?php echo $prev_hs_cp->period_start." au ".$prev_hs_cp->period_end; ?></u></strong><br/>
				<strong>Congés acquis : </strong><?php echo $prev_hs_cp->count_acquired; ?><br/>
				<strong>Congés pris : </strong><?php echo $prev_hs_cp->count_taken; ?><br/>
				<strong>Congés restant : </strong><?php echo $prev_hs_cp->count_remaining; ?>
			</li>
			<?php
				}
			?>
			<li>
				<strong><u>Congés Payés pour la période du <?php echo $hs_cp->period_start." au ".$hs_cp->period_end; ?></u></strong><br/>
				<strong>Congés acquis : </strong><?php echo $hs_cp->count_acquired; ?><br/>
				<strong>Congés pris : </strong><?php echo $hs_cp->count_taken; ?><br/>
				<strong>Congés restant : </strong><?php echo $hs_cp->count_remaining; ?>
			</li>
			<?php
				if( ($prev_hs_rtt->count_acquired - $prev_hs_rtt->count_taken) > 0 && $prev_hs_rtt->count_remaining > 0 ){
			?>
			<li>
				<strong><u>RTT pour la période du <?php echo $prev_hs_rtt->period_start." au ".$prev_hs_rtt->period_end; ?></u></strong><br/>
				<strong>Congés acquis : </strong><?php echo $prev_hs_rtt->count_acquired; ?><br/>
				<strong>Congés pris : </strong><?php echo $prev_hs_rtt->count_taken; ?><br/>
				<strong>Congés restant : </strong><?php echo $prev_hs_rtt->count_remaining; ?>
			</li>
			<?php
				}
			?>
			<li>
				<strong><u>RTT pour la période du <?php echo $hs_rtt->period_start." au ".$hs_rtt->period_end; ?></u></strong><br/>
				<strong>Congés acquis : </strong><?php echo $hs_rtt->count_acquired; ?><br/>
				<strong>Congés pris : </strong><?php echo $hs_rtt->count_taken; ?><br/>
				<strong>Congés restant : </strong><?php echo $hs_rtt->count_remaining; ?>
			</li>
			<?php
				$cp_pmd = new GenyProfileManagementData();
				$cp_pmd->loadProfileManagementDataByProfileId($geny_profile->id);
				if(($profile->rights_group_id == $geny_rights_group->getIdByShortname('ADM') || ($profile->rights_group_id == $geny_rights_group->getIdByShortname('TM')) && $profile->id == $cp_pmd->group_leader_id) ){
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
					<th>Pièce jointe</th>
					<th>Accord employé</th>
					<th>Accord manager</th>
				</thead>
				<tbody>
				<?php
				foreach( $geny_ce->getCareerEventListByProfileId($geny_profile->id) as $ce ){
					$attch = "";
					if( $ce->attachement != "" ){
						$attch = "<a href='$ce->attachement' target='_blank'>Télécharger</a>";
					}
					echo "<tr class='centered'><td>".date('Y-m-d',$ce->timestamp)."</td><td>$ce->title</td><td>".ceTypeToHtml($ce->type)."</td><td>$ce->text</td><td>$attch</td><td id='ce_employee_agreement_$ce->id'>".ceAgreementToHtml('employee_agreement',$ce->id,$ce->employee_agreement,$web_config->theme,$profile,$geny_profile)."</td><td id='ce_manager_agreement_$ce->id'>".ceAgreementToHtml('manager_agreement',$ce->id,$ce->manager_agreement,$web_config->theme,$profile,$geny_profile)."</td></tr>";
					
				}
				?>
				</tbody>
				<tfoot>
					<th>Date</th>
					<th>Titre</th>
					<th>Type</th>
					<th>Texte</th>
					<th>Pièce jointe</th>
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
				<option value="fault">Faute</option>
				<option value="negative">Négatif</option>
				<option value="neutral" selected="selected">Neutre</option>
				<option value="positive">Positif</option>
				<option value="uber">Über positif</option>
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
			<label for="ce_attachement" >Pièce jointe (lien)</label>
			<input name="ce_attachement" id="ce_attachement" type="text" class="validate[required] text-input" />
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
	var ce_attachement = $('div#pp_full_res #ce_attachement').val();
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
		var attachement_params = "";
		var attachement_html = "";
		if( ce_attachement != "" ){
			attachement_params = "&attachement="+ce_attachement;
			attachement_html = "<a href='"+ce_attachement+"' target='_blank'>Télécharger</a>";
		}
		jQuery.get("backend/api/create_career_event.php?type="+encodeURIComponent(ce_type)+"&title="+encodeURIComponent(ce_title)+"&text="+encodeURIComponent(ce_description)+"&profile_id="+<?php echo $geny_profile->id;?>+attachement_params, function(data){
			console.log("status="+data.status);
			console.log("status_message="+data.status_message);
			$(".pp_social #status_message_display").empty();
			if( data.status == "success" ){
				$('#ce_list_table').dataTable().fnAddData( [
				"<?php echo date('Y-m-d',time()); ?>",
				ce_title,
				ce_type,
				ce_description,
				attachement_html,
				'Recharger la page pour valider',
				'Recharger la page pour valider'
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

$('a[class="ceVoteLink"]').click(function(e) {
	e.preventDefault();
	var $item = $(this);
	var initial_html = $item.closest('td').html();
	var ce_id = $item.attr('ceId');
	var agreement_type = $item.attr('ceAgreementType');
	var ce_vote = $item.attr('ceVote');
	var api_call_url = "backend/api/update_career_event.php?career_event_id="+ce_id+"&profile_id="+<?php echo $geny_profile->id;?>+"&"+agreement_type+"="+ce_vote;
	
	$item.closest('td').html("<img src='images/<?php echo $web_config->theme;?>/ajax-loader-indicator.gif' />");

	$.get(api_call_url, function(data){
		console.log("status="+data.status);
		console.log("status_message="+data.status_message);
		if( data.status == "success" ){
			if( $item.attr('ceVote') == 1 ){
				$('#ce_'+$item.attr('ceAgreementType')+'_'+$item.attr('ceId')).empty();
				$('#ce_'+$item.attr('ceAgreementType')+'_'+$item.attr('ceId')).append( "<img src='images/<?php echo $web_config->theme;?>/idea_vote_up_small.png' />" );
			}
			else if($item.attr('ceVote') == -1 ){
				$('#ce_'+$item.attr('ceAgreementType')+'_'+$item.attr('ceId')).empty();
				$('#ce_'+$item.attr('ceAgreementType')+'_'+$item.attr('ceId')).append( "<img src='images/<?php echo $web_config->theme;?>/idea_vote_down_small.png' />" );
			}
		}
		else{
			$('#ce_'+$item.attr('ceAgreementType')+'_'+$item.attr('ceId')).empty();
			$('#ce_'+$item.attr('ceAgreementType')+'_'+$item.attr('ceId')).append( " "+initial_html );
		}
	});
});

</script>
<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php');
?>
