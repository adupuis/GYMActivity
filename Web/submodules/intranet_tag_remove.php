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

$geny_intranet_tag = new GenyIntranetTag();

$handle = mysql_connect( $web_config->db_host, $web_config->db_user, $web_config->db_password );
mysql_select_db( $web_config->db_name );
mysql_query( "SET NAMES 'utf8'" );

$remove_intranet_tag = GenyTools::getParam( 'remove_intranet_tag', 'NULL' );
if( $remove_intranet_tag == "true" ) {
	$intranet_tag_id = GenyTools::getParam( 'intranet_tag_id', 'NULL' );
	if( $intranet_tag_id != 'NULL' ) {
		$force_remove_intranet_tag = GenyTools::getParam( 'force_remove', 'NULL' );
		if( $force_remove_intranet_tag == "true" ) {
			$id = GenyTools::getParam( 'intranet_tag_id', 'NULL' );
			$geny_intranet_tag->loadIntranetTagById( $id );
			if( $profile->rights_group_id == 1 /* admin */       ||
			    $profile->rights_group_id == 2 /* superuser */)  {
				$query = "DELETE FROM IntranetTags WHERE intranet_tag_id=$id";
				if( !mysql_query( $query ) ) {
					$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la suppression du tag Intranet de la table IntranetTags." );
				}
				else
					$gritter_notifications[] = array( 'status'=>'success', 'title' => 'Succès','msg'=>"Tag Intranet supprimé avec succès." );
			}
		}
		else {
			$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Veuillez cochez la case acquittant votre compréhension de la portée de l'opération en cours." );
		}
	}
	else {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => "Impossible de supprimer le tag Intranet",'msg'=>"id non spécifié." );
	}
}
else {
// 	$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Aucune action spécifiée.");
}


?>

<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/intranet_tag_remove.png"></img>
		<span class="intranet_tag_remove">
			Supprimer un tag Intranet
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de <strong>supprimer définitivement</strong> un tag Intranet.
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
		<form id="select_login_form" action="loader.php?module=intranet_tag_remove" method="post">
			<input type="hidden" name="remove_intranet_tag" value="true" />
			<p>
				<label for="intranet_tag_id">Sélection tag Intranet</label>

				<select name="intranet_tag_id" id="intranet_tag_id" class="chzn-select">
					<?php
					$intranet_tags = $geny_intranet_tag->getAllIntranetTagsOrderByName();
					$intranet_tag_id = GenyTools::getParam( 'intranet_tag_id', 'NULL' );

					foreach( $intranet_tags as $intranet_tag ) {
						
						if( $intranet_tag_id == $intranet_tag->id ) {
							echo "<option value=\"".$intranet_tag->id."\" selected>".$intranet_tag->name."</option>\n";
						}
						else {
							echo "<option value=\"".$intranet_tag->id."\">".$intranet_tag->name."</option>\n";
						}
					}
					if( $geny_intranet_tag->id < 0 ) {
						$geny_intranet_tag->loadIntranetTagById( $intranet_tags[0]->id );
					}
					?>
				</select>
			</p>
			<p>
			<input type="checkbox" name="force_remove" value="true" class="validate[required] checkbox" /> Veuillez cocher cette case pour confirmer la suppression du tag Intranet. <strong>La suppression est définitive et ne pourra pas être annulée.</strong>
			</p>
			<p>
				<input type="submit" value="Supprimer" /> ou <a href="loader.php?module=intranet_tag_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/intranet_tag_list.dock.widget.php');
?>
