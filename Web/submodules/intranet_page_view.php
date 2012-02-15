<?php
//  Copyright (C) 2011 by GENYMOBILE & Quentin Désert
//  qdesert@genymobile.com
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

$gritter_notifications = array();
$geny_intranet_page = new GenyIntranetPage();

$load_intranet_page = GenyTools::getParam( 'load_intranet_page', 'NULL' );

if( $load_intranet_page == 'true' ) {
	$intranet_page_id = GenyTools::getParam( 'intranet_page_id', 'NULL' );
	if( $intranet_page_id != 'NULL' ) {

		$tmp_geny_intranet_page = new GenyIntranetPage();
		$tmp_geny_intranet_page->loadIntranetPageById( $intranet_page_id );
		
		$profile_authorized = false;
		$intranet_page_status_id = $tmp_geny_intranet_page->status_id;
		$intranet_page_profile = new GenyProfile( $tmp_geny_intranet_page->profile_id );
		if( $intranet_page_status_id == 1 ) {
			if( $profile->rights_group_id == 1  || /* admin */
			    $profile->rights_group_id == 2  ||   /* superuser */
			    $profile->id == $tmp_geny_intranet_page->profile_id ) {
				$profile_authorized = true;
			}
		}
		else if( $intranet_page_status_id == 2 ) {
			if( $profile->rights_group_id == 1  || /* admin */
			    $profile->rights_group_id == 2  ||   /* superuser */
			    $profile->rights_group_id == $intranet_page_profile->rights_group_id ) {
				$profile_authorized = true;
			}
		}
		else {
			$profile_authorized = true;
		}
		if( !$profile_authorized ) {
			$gritter_notifications[] = array('status'=>'error', 'title' => "Impossible de charger la page Intranet",'msg'=>"Vous n'êtes pas autorisé.");
			include_once( 'bork.php' );
		}
			
		$geny_intranet_page->loadIntranetPageById( $intranet_page_id );
		$intranet_category_id = $geny_intranet_page->intranet_category_id;
		switch( $intranet_category_id ) {
			case 1:
				$intranet_page_image = "intranet_category_administratif.png";
				break;
			case 2:
				$intranet_page_image = "intranet_category_commerce.png";
				break;
			case 3:
				$intranet_page_image = "intranet_category_marketing.png";
				break;
			case 4:
				$intranet_page_image = "intranet_category_informatique.png";
				break;
			case 5:
				$intranet_page_image = "intranet_category_projet_r&d.png";
				break;
			case 6:
				$intranet_page_image = "intranet_category_partage.png";
				break;
			default:
				$intranet_page_image = "intranet_category_generic.png";
				break;	
		}
	}
	else {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Impossible de charger la page Intranet','msg'=>"id non spécifié." );
	}
}

if( $profile_authorized ) {
?>

<style>
	@import "styles/genymobile-2012/chosen_override.css";
</style>

<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme."/".$intranet_page_image; ?>"></img>
		<span class="intranet_page_view">
			<?php echo $geny_intranet_page->title ?>
		</span>
	</p>
	<div class="mainarea_content">
		<div class="intranet_page_content">
			<?php echo html_entity_decode( $geny_intranet_page->content ) ?>
		</div>
	</div>
</div>
<?php
} // endif $profile_authorized

	$bottomdock_items = array('backend/widgets/intranet_page_list.dock.widget.php','backend/widgets/intranet_page_add.dock.widget.php');
?>
