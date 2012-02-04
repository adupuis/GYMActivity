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


$geny_rights_group = new GenyRightsGroup( $profile->rights_group_id );
$geny_pmd = new GenyProfileManagementData();
$geny_pmd->loadProfileManagementDataByProfileId($profile->id);
$geny_hs = new GenyHolidaySummary();

$month = date('m', time());
$year=date('Y', time());
$start_hs_cp_date = '1979-06-01';
$end_hs_cp_date = '1980-05-31';

if( $month < 6 ){
	$start_year = $year-1;
	$start_hs_cp_date = "$start_year-06-01";
	$end_hs_cp_date = "$year-05-31";
}
else {
	$next_year = $year+1;
	$start_hs_cp_date = "$year-06-01";
	$end_hs_cp_date = "$next_year-05-31";
}

// Nous ne pouvons avoir qu'un seul solde de congés valide pour une période annuelle
$hs_cp = $geny_hs->getCurrentCPSummaryByProfileId($profile->id);

// Idem pour les RTT
$geny_hs->setDebug(true);
$hs_rtt = $geny_hs->getCurrentRTTSummaryByProfileId($profile->id);
$geny_hs->setDebug(false);
?>
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
<!-- 		<span> -->
		<ul id="profile_general_info" class="ps_float">
			<li><strong>Nom : </strong> <?php echo $profile->lastname ; ?></li>
			<li><strong>Prénom : </strong> <?php echo $profile->firstname ; ?></li>
			<li><strong>Login : </strong> <?php echo $profile->login ; ?></li>
			<li><strong>Email : </strong> <?php echo $profile->email ; ?></li>
			<li><strong>Groupe : </strong> <?php echo $geny_rights_group->name ; ?></li>
		</ul>
		<ul id="profile_management_info" class="ps_float">
			<li><strong>Facturable : </strong> <?php if($geny_pmd->is_billable){ echo 'Oui' ;}else{echo 'Non';} ?></li>
			<li><strong>Date de recrutement : </strong> <?php echo $geny_pmd->recruitement_date ;?></li>
			<li><strong>Salaire (brut annuel) : </strong> <?php echo $geny_pmd->salary ;?> &euro;</li>
			<li><strong>Date de disponibilité : </strong> <?php echo $geny_pmd->availability_date ;?></li>
		</ul>
		<div style="clear:both"></div>
		<!--</span>
		<span>-->
		<ul id="profile_holiday_info_cp" class="ps_float">
		<strong><u>Congés Payés pour la période du <?php echo $hs_cp->period_start." au ".$hs_cp->period_end; ?></u></strong>
			<li><strong>Congés acquis : </strong><?php echo $hs_cp->count_acquired; ?></li>
			<li><strong>Congés pris : </strong><?php echo $hs_cp->count_taken; ?></li>
			<li><strong>Congés restant : </strong><?php echo $hs_cp->count_remaining; ?></li>
		</ul>
		<ul id="profile_holiday_info_rtt" class="ps_float">
		<strong><u>RTT pour la période du <?php echo $hs_rtt->period_start." au ".$hs_rtt->period_end; ?></u></strong>
		<li><strong>Congés acquis : </strong><?php echo $hs_rtt->count_acquired; ?></li>
		<li><strong>Congés pris : </strong><?php echo $hs_rtt->count_taken; ?></li>
		<li><strong>Congés restant : </strong><?php echo $hs_rtt->count_remaining; ?></li>
		</ul>
<!-- 		</span> -->
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php');
?>
