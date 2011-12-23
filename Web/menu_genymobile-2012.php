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
		<a href="profile_display.php">
			<img src="images/test.jpg" alt=""/>
			<span class="sdt_active"></span>
			<span class="sdt_wrap">
				<span class="sdt_link"><?php echo $profile->login ?></span>
				<span class="sdt_descr"><?php echo $profile->firstname." ".$profile->lastname ; ?><br/><br/><span class="sdt_descr_more">CRA remplies: 80%<br/>Congés dispo : 12 j<br/>Notif. non lues : 7</span></span>
			</span>
		</a>
	</li>
	<li>
		<a href="home.php">
			<img src="images/test.jpg" alt=""/>
			<span class="sdt_active"></span>
			<span class="sdt_wrap">
				<span class="sdt_link">Home</span>
				<span class="sdt_descr">There's no place like 127.0.0.1</span>
			</span>
		</a>
	</li>
	<li>
		<a href="home_cra.php">
			<img src="images/6.jpg" alt=""/>
			<span class="sdt_active"></span>
			<span class="sdt_wrap">
				<span class="sdt_link">CRA</span>
				<span class="sdt_descr">Compte-rendus d'activité</span>
			</span>
		</a>
		<div class="sdt_box">
			<a href="cra_add.php">Ajouter un CRA</a>
			<a href="cra_validation.php">Valider vos CRA</a>
			<a href="cra_list.php">Lister vos CRA</a>
			<a href="cra_deletion.php">Supprimer des CRA</a>
		</div>
	</li>
	<li>
		<a href="#">
			<img src="images/1.jpg" alt=""/>
			<span class="sdt_active"></span>
			<span class="sdt_wrap">
				<span class="sdt_link">Congés</span>
				<span class="sdt_descr">Congés payés (ou pas) / R.T.T</span>
			</span>
		</a>
		<div class="sdt_box">
			<a href="conges_add.php">Poser des congés</a>
			<a href="conges_validation.php">Valider vos congés</a>
			<a href="conges_list.php">Lister vos congés</a>
			<a href="conges_deletion.php">Supprimer des congés</a>
		</div>
	</li>
	<li>
		<a href="home_reporting.php">
			<img src="images/3.jpg" alt=""/>
			<span class="sdt_active"></span>
			<span class="sdt_wrap">
				<span class="sdt_link">Reporting</span>
				<span class="sdt_descr">Reporting d'activité</span>
			</span>
		</a>
		<div class="sdt_box">
			<a href="reporting_personal_load.php">Rapport de charge</a>
			<a href="reporting_load.php">Rapport charge mensuel</a>
			<a href="reporting_cra_completion.php">Remplissage des CRA</a>
			<a href="home_reporting.php">Plus...</a>
		</div>
	</li>
	<li>
		<a href="home_admin.php">
			<img src="images/4.jpg" alt=""/>
			<span class="sdt_active"></span>
			<span class="sdt_wrap">
				<span class="sdt_link">Admin</span>
				<span class="sdt_descr">Administration de GYMActivity</span>
			</span>
		</a>
	</li>
	<li>
		<a href="logout.php">
			<img src="images/5.jpg" alt=""/>
			<span class="sdt_active"></span>
			<span class="sdt_wrap">
				<span class="sdt_link">Logout</span>
				<span class="sdt_descr">Se déconnecter</span>
			</span>
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
