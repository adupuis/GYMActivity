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


$gritter_notifications = array();
$geny_bank_holiday = new GenyBankHoliday();

if( isset($_POST['remove_bank_holiday']) && $_POST['remove_bank_holiday'] == "true" ){
	if(isset($_POST['bank_holiday_id'])){
		if( isset($_POST['force_remove']) && $_POST['force_remove'] == "true" ){
			$id = mysql_real_escape_string($_POST['bank_holiday_id']);
			if( $geny_bank_holiday->deleteBankHoliday($id) ){
				$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Tâche supprimée avec succès.");
			}
			else
				$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la suppression du jour férié.");
		}
		else{
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Veuillez cochez la case acquittant votre compréhension de la portée de l'opération en cours.");
		}
	}
	else  {
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Impossible de supprimer le tâche ','msg'=>"id non spécifié.");
	}
}
else{
// 	$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Aucune action spécifiée.");
}


?>
<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/bank_holiday_remove.png"></img>
		<span class="bank_holiday_remove">
			Supprimer un jour férié
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de <strong>supprimer définitivement</strong> un jour férié dans la base.
		</p>
		<script>
			<?php
				// Cette fonction est définie dans header.php
				displayStatusNotifications($gritter_notifications,$web_config->theme);
			?>
		</script>
		<script>
			jQuery(document).ready(function(){
				$("#select_login_form").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#select_login_form").validationEngine('attach');
			});
			
		</script>
		<form id="select_login_form" action="loader.php?module=bank_holiday_remove" method="post">
			<input type="hidden" name="remove_bank_holiday" value="true" />
			<p>
				<label for="bank_holiday_id">Sélection tâche</label>

				<select name="bank_holiday_id" id="bank_holiday_id" class="chzn-select">
					<?php
						foreach( $geny_bank_holiday->getAllBankHolidays() as $bh ){
							if( (isset($_POST['bank_holiday_id']) && $_POST['bank_holiday_id'] == $bh->id) || (isset($_GET['bank_holiday_id']) && $_GET['bank_holiday_id'] == $bh->id) )
								echo "<option value=\"".$bh->id."\" selected>".$bh->name."</option>\n";
							else if( isset($_POST['bank_holiday_name']) && $_POST['bank_holiday_name'] == $bh->name )
								echo "<option value=\"".$bh->id."\" selected>".$bh->name."</option>\n";
							else
								echo "<option value=\"".$bh->id."\">".$bh->name."</option>\n";
						}
					?>
				</select>
			</p>
			<p>
			<input type="checkbox" name="force_remove" value="true" class="validate[required] checkbox" /> Veuillez cocher cette case pour confirmer la suppression du jour férié. <strong>La suppression est définitive et ne pourra pas être annulée.</strong>
			</p>
			<p>
				<input type="submit" value="Supprimer" /> ou <a href="loader.php?module=bank_holiday_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/bank_holiday_list.dock.widget.php','backend/widgets/bank_holiday_add.dock.widget.php');
?>
