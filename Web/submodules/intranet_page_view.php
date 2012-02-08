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
//TODO: rights_group check or not on this page
// 		if( $profile->rights_group_id == 1  || /* admin */
// 		    $profile->rights_group_id == 2     /* superuser */ ) {
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
// 		}
// 		else {
// 			$gritter_notifications[] = array('status'=>'error', 'title' => "Impossible de charger la page Intranet",'msg'=>"Vous n'êtes pas autorisé.");
// 			header( 'Location: error.php?category=intranet_page' );
// 		}
	}
	else {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Impossible de charger la page Intranet','msg'=>"id non spécifié." );
	}
}

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
	$bottomdock_items = array();
	if( $profile->rights_group_id == 1  || /* admin */
	    $profile->rights_group_id == 2     /* superuser */ ) {
		array_push( $bottomdock_items, 'backend/widgets/intranet_page_list.dock.widget.php' );
	}
	array_push( $bottomdock_items, 'backend/widgets/intranet_page_add.dock.widget.php' );
?>
