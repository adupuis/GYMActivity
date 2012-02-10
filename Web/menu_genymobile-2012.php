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
$start_date="$year-$month-1";
$end_date="$year-$month-31";

$estimated_load = 0;
$user_load = 0;

$geny_ar = new GenyActivityReport();
$geny_hs = new GenyHolidaySummary();

// Nous ne pouvons avoir qu'un seul solde de congés valide pour une période annuelle
$hs_cp = $geny_hs->getCurrentCPSummaryByProfileId($profile->id);

// Idem pour les RTT
$hs_rtt = $geny_hs->getCurrentRTTSummaryByProfileId($profile->id);

$hs_remaining = $hs_cp->count_remaining + $hs_rtt->count_remaining;

foreach( GenyTools::getWorkedDaysList(strtotime($start_date),strtotime($end_date)) as $day ){
	$estimated_load += 8;
	$user_load += $geny_ar->getDayLoad($profile->id,$day);
}

$completion = round(($user_load*100)/$estimated_load,1);

$geny_ic = new GenyIntranetCategory();

?>
<!-- Here is the genymobile-2012 menu -->

<link rel="stylesheet" href="styles/genymobile-2012/style.css" type="text/css" media="screen"/>
<style>
	ul.sdt_menu{
		position: relative;
		padding-top:10px;
		padding-right: 10px;
		margin-left: auto;
	}
</style>

<ul id="sdt_menu" class="sdt_menu">
	<li>
<!-- 		<a href="profile_display.php"> -->
		<a href="loader.php?module=profile_summary">
			<img src="images/genymobile-2012/profile_banner.png" alt=""/>
			<span class="sdt_active"></span>
			<span class="sdt_wrap">
				<span class="sdt_link"><?php echo $profile->login ?></span>
				<span class="sdt_descr"><?php echo $profile->firstname." ".$profile->lastname ; ?><br/><br/><span class="sdt_descr_more">CRA remplies: <?php echo $completion; ?>%<br/>Congés dispo : <?php echo $hs_remaining;?> j<br/>Notif. non lues : <span id='menu_notification_count'>7</span></span></span>
			</span>
		</a>
	</li>
	<li>
		<a href="loader.php?module=home_cra">
			<img src="images/genymobile-2012/cra_banner.png" alt=""/>
			<span class="sdt_active"></span>
			<span class="sdt_wrap">
				<span class="sdt_link">CRA</span>
				<span class="sdt_descr">Compte-rendus d'activité</span>
			</span>
		</a>
		<div class="sdt_box">
			<a href="loader.php?module=cra_add">Ajouter un CRA</a>
			<a href="loader.php?module=cra_validation">Valider vos CRA</a>
			<a href="loader.php?module=cra_list">Lister vos CRA</a>
			<a href="loader.php?module=cra_deletion">Supprimer des CRA</a>
		</div>
	</li>
	<li>
		<a href="loader.php?module=home_conges">
			<img src="images/genymobile-2012/conges_banner.png" alt=""/>
			<span class="sdt_active"></span>
			<span class="sdt_wrap">
				<span class="sdt_link">Congés</span>
				<span class="sdt_descr">Congés payés (ou pas) / R.T.T</span>
			</span>
		</a>
		<div class="sdt_box">
			<a href="loader.php?module=conges_add">Poser des congés</a>
			<a href="loader.php?module=conges_validation">Valider vos congés</a>
			<a href="loader.php?module=conges_list">Lister vos congés</a>
			<a href="loader.php?module=conges_deletion">Supprimer des congés</a>
		</div>
	</li>
	<li>
		<a href="loader.php?module=home_project">
			<img src="images/genymobile-2012/project_banner.png" alt=""/>
			<span class="sdt_active"></span>
			<span class="sdt_wrap">
				<span class="sdt_link">Projets</span>
				<span class="sdt_descr">Consultation / Administration de projets</span>
			</span>
		</a>
		<div class="sdt_box">
			<a href="loader.php?module=project_assignemts_list">Liste de vos affectations</a>
			<a href="loader.php?module=conges_validation">Valider vos congés</a>
			<a href="loader.php?module=conges_list">Lister vos congés</a>
			<a href="loader.php?module=conges_deletion">Supprimer des congés</a>
		</div>
	</li>
	<li>
		<a href="loader.php?module=home_reporting">
			<img src="images/genymobile-2012/reporting_banner.png" alt=""/>
			<span class="sdt_active"></span>
			<span class="sdt_wrap">
				<span class="sdt_link">Reporting</span>
				<span class="sdt_descr">Reporting d'activité</span>
			</span>
		</a>
		<div class="sdt_box">
			<a href="loader.php?module=reporting_personal_load">Rapport de charge</a>
			<a href="loader.php?module=reporting_load">Rapport charge mensuel</a>
			<a href="loader.php?module=reporting_cra_completion">Remplissage des CRA</a>
			<a href="loader.php?module=home_reporting">Plus...</a>
		</div>
	</li>
	<li>
		<a href="loader.php?module=home_admin">
			<img src="images/genymobile-2012/admin_banner.png" alt=""/>
			<span class="sdt_active"></span>
			<span class="sdt_wrap">
				<span class="sdt_link">Admin</span>
				<span class="sdt_descr">Administration de GYMActivity</span>
			</span>
		</a>
	</li>
	<li>
		<a href="loader.php?module=home_intranet">
			<img src="images/genymobile-2012/intranet_banner.png" alt=""/>
			<span class="sdt_active"></span>
			<span class="sdt_wrap">
				<span class="sdt_link">Intranet</span>
				<span class="sdt_descr">((inter)^-1)net</span>
			</span>
			<div class="sdt_box">
				<?php
					foreach( $geny_ic->getAllIntranetCategories() as $cat ){
						echo "<a href='loader.php?module=home_intranet&category_id=$cat->id'>$cat->name</a>";
					}
					if($profile->rights_group_id == 1 || $profile->rights_group_id == 2 ){
						echo "<a href='loader.php?module=home_intranet_admin'>Admin. Intranet</a>";
					}
				?>
			</div>
		</a>
	</li>
</ul>

 <script type="text/javascript">
    $(function() {
			/**
			* for each menu element, on mouseenter, 
			* we enlarge the image, and show both sdt_active span and 
			* sdt_wrap span. If the element has a sub menu (sdt_box),
			* then we slide it - if the element is the last one in the menu
			* we slide it to the left, otherwise to the right
			*/
        $('#sdt_menu > li').bind('mouseenter',function(){
				var $elem = $(this);
				$elem.find('img')
					 .stop(true)
					 .animate({
						'width':'170px',
						'height':'85px',
						'left':'0px'
					 },400,'easeOutBack')
					 .andSelf()
					 .find('.sdt_wrap')
				     .stop(true)
					 .animate({'top':'140px'},500,'easeOutBack')
					 .andSelf()
					 .find('.sdt_active')
				     .stop(true)
					 .animate({'height':'170px'},300,function(){
					var $sub_menu = $elem.find('.sdt_box');
					if($sub_menu.length){
						var left = '170px';
						if($elem.parent().children().length == $elem.index()+1)
							left = '-170px';
						$sub_menu.show().animate({'left':left},200);
					}	
				});
				$elem.find('.sdt_descr_more').show('slow'); 
			}).bind('mouseleave',function(){
				var $elem = $(this);
				var $sub_menu = $elem.find('.sdt_box');
				if($sub_menu.length)
					$sub_menu.hide().css('left','0px');
				
				$elem.find('.sdt_active')
					 .stop(true)
					 .animate({'height':'0px'},300)
					 .andSelf().find('img')
					 .stop(true)
					 .animate({
						'width':'0px',
						'height':'0px',
						'left':'85px'},400)
					 .andSelf()
					 .find('.sdt_wrap')
					 .stop(true)
					 .animate({'top':'25px'},500);
				
				$elem.find('.sdt_descr_more').hide('fast');
			});
    });
</script>
<!-- End of menu -->
