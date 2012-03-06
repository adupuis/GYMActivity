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

$handle = mysql_connect( $web_config->db_host, $web_config->db_user, $web_config->db_password );
mysql_select_db( $web_config->db_name );
mysql_query( "SET NAMES 'utf8'" );

$remove_intranet_page = GenyTools::getParam( 'remove_intranet_page', 'NULL' );
if( $remove_intranet_page == "true" ) {
	$intranet_page_id = GenyTools::getParam( 'intranet_page_id', 'NULL' );
	if( $intranet_page_id != 'NULL' ) {
		$force_remove_intranet_page = GenyTools::getParam( 'force_remove', 'NULL' );
		if( $force_remove_intranet_page == "true" ) {
			$id = GenyTools::getParam( 'intranet_page_id', 'NULL' );
			$geny_intranet_page->loadIntranetPageById( $id );
			if( $profile->rights_group_id == 1 /* admin */       ||
			    $profile->rights_group_id == 2 /* superuser */)  {
				$query = "DELETE FROM IntranetPages WHERE intranet_page_id=$id";
				if( !mysql_query( $query ) ) {
					$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la suppression de la page Intranet de la table IntranetPages." );
				}
				else
					$gritter_notifications[] = array( 'status'=>'success', 'title' => 'Succès','msg'=>"Page Intranet supprimée avec succès." );
			}
		}
		else {
			$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Veuillez cochez la case acquittant votre compréhension de la portée de l'opération en cours." );
		}
	}
	else {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => "Impossible de supprimer la page Intranet",'msg'=>"id non spécifié." );
	}
}
else {
// 	$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Aucune action spécifiée.");
}


?>

<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/intranet_page_remove.png"></img>
		<span class="intranet_page_remove">
			Supprimer une page Intranet
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de <strong>supprimer définitivement</strong> une page Intranet.
		</p>
		<script>
			<?php
				// Cette fonction est définie dans header.php
				displayStatusNotifications( $gritter_notifications, $web_config->theme );
			?>
		</script>
		<script>
			jQuery(document).ready(function(){
				$("#select_login_form").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#select_login_form").validationEngine('attach');
			});
			
		</script>
		<form id="select_login_form" action="loader.php?module=intranet_page_remove" method="post">
			<input type="hidden" name="remove_intranet_page" value="true" />
			<p>
				<label for="intranet_page_id">Sélection page Intranet</label>

				<select name="intranet_page_id" id="intranet_page_id" class="chzn-select">
					<?php
					$intranet_pages = $geny_intranet_page->getAllIntranetPages();
					$intranet_page_id = GenyTools::getParam( 'intranet_page_id', 'NULL' );

					foreach( $intranet_pages as $intranet_page ) {
						
						if( $intranet_page_id == $intranet_page->id ) {
							echo "<option value=\"".$intranet_page->id."\" selected>".$intranet_page->title."</option>\n";
						}
						else {
							echo "<option value=\"".$intranet_page->id."\">".$intranet_page->title."</option>\n";
						}
					}
					if( $geny_intranet_page->id < 0 ) {
						$geny_intranet_page->loadIntranetPageById( $intranet_pages[0]->id );
					}
					?>
				</select>
			</p>
			<p>
			<input type="checkbox" name="force_remove" value="true" class="validate[required] checkbox" /> Veuillez cocher cette case pour confirmer la suppression de la page Intranet. <strong>La suppression est définitive et ne pourra pas être annulée.</strong>
			</p>
			<p>
				<input type="submit" value="Supprimer" /> ou <a href="loader.php?module=intranet_page_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/intranet_page_list.dock.widget.php');
?>
