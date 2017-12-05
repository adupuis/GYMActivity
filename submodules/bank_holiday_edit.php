<?php
//  Copyright (C) 2011 by GENYMOBILE & Arnaud Dupuis
//  adupuist@genymobile.com
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


$geny_project = new GenyProject();
$geny_country = new GenyCountry();
$geny_bh = new GenyBankHoliday();
$gritter_notifications = array();

if( isset($_POST['create_bank_holiday']) && $_POST['create_bank_holiday'] == "true" ){
    $tmp_name = GenyTools::getParam('bank_holiday_name',"");
    $tmp_project_id = GenyTools::getParam('project_id',-1);
    $tmp_task_id = GenyTools::getParam('bank_holiday_type',-1);
    $tmp_start_date = GenyTools::getParam('bank_holiday_start_date','1979-01-01');
    $tmp_stop_date = GenyTools::getParam('bank_holiday_stop_date','1979-01-01');
    $tmp_country_id = GenyTools::getParam('country_id',-1);
	if( $tmp_name != "" && $tmp_project_id > -1 && $tmp_task_id > -1 && $tmp_start_date != '1979-01-01' && $tmp_stop_date != '1979-01-01' && $tmp_country_id > -1 ){
		$new_bh_id = $geny_bh->insertNewBankHoliday(0,$tmp_name,$tmp_project_id,$tmp_task_id,$tmp_start_date,$tmp_stop_date,$tmp_country_id);
		if( $new_bh_id > -1 ){
			$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Jour férié créé avec succès.");
			$geny_bh->loadBankHolidayById($new_bh_id);
		}
		else{
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de la création du jour férié.");
		}
	}
	else {
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Certains champs obligatoires sont manquant. Merci de les remplir.");
	}
}
else if( GenyTools::getParam('load_bank_holiday',"") == "true" ){
    $tmp_bh_id = GenyTools::getParam('bank_holiday_id',-1);
	if($tmp_bh_id > -1){
		$geny_bh->loadBankHolidayById($tmp_bh_id);
	}
	else  {
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Impossible de charger le jour férié','msg'=>"id non spécifié.");
	}
}
else if( GenyTools::getParam('edit_bank_holiday',"") == "true" ){
    $bank_holiday_id = GenyTools::getParam('bank_holiday_id',-1) ;
    $tmp_name = GenyTools::getParam('bank_holiday_name',"");
    $tmp_project_id = GenyTools::getParam('project_id',-1);
    $tmp_task_id = GenyTools::getParam('bank_holiday_type',-1);
    $tmp_start_date = GenyTools::getParam('bank_holiday_start_date','1979-01-01');
    $tmp_stop_date = GenyTools::getParam('bank_holiday_stop_date','1979-01-01');
    $tmp_country_id = GenyTools::getParam('country_id',-1);
	if( $bank_holiday_id > -1 ){
		$geny_bh->loadBankHolidayById($bank_holiday_id);
		
		if( $tmp_name != "" ){
			$geny_bh->updateString('bank_holiday_name',$tmp_name);
		}
		if( $tmp_project_id > -1 ){
            $geny_bh->updateInt('bank_holiday_project_id',$tmp_project_id);
		}
		if( $tmp_task_id > -1 ){
            $geny_bh->updateInt('bank_holiday_task_id',$tmp_task_id);
		}
		if( $tmp_start_date != '1979-01-01' ){
            $geny_bh->updateString('bank_holiday_start_date',$tmp_start_date);
		}
		if( $tmp_stop_date != '1979-01-01' ){
            $geny_bh->updateString('bank_holiday_stop_date',$tmp_start_date);
		}
		if( $tmp_country_id > -1 ){
            $geny_bh->updateInt('bank_holiday_country_id',$tmp_country_id);
		}
		
		
		if($geny_bh->commitUpdates()){
			$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Jour férié mis à jour avec succès.");
			$geny_bh->loadBankHolidayById($bank_holiday_id);
		}
		else{
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la mise à jour du jour férié.");
		}
	}
	else  {
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Impossible de modifier le jour férié','msg'=>"id non spécifié.");
	}
}
else{
// 	$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Aucune action spécifiée.");
}

?>
<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/bank_holiday_add.png"></img>
		<span class="bank_holiday_add">
			Ajouter un jour férié (bank holiday)
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de modifier un jour férié. Tous les champs doivent être remplis.
		</p>
		<script>
			<?php
				// Cette fonction est définie dans header.php
				displayStatusNotifications($gritter_notifications,$web_config->theme);
			?>
		</script>
		<form id="select_bank_holiday_form" action="loader.php?module=bank_holiday_edit" method="post">
			<input type="hidden" name="load_bank_holiday" value="true" />
			<p>
				<label for="bank_holiday_id">Sélection jour férié</label>

				<select name="bank_holiday_id" id="bank_holiday_id" onChange="submit()" class="chzn-select">
					<?php
						$bank_holidays = $geny_bh->getAllBankHolidays();
						$param_bank_holiday_id = GenyTools::getParam('bank_holiday_id',-1);
						foreach( $bank_holidays as $bh ){
							if( $param_bank_holiday_id == $bh->id )
								echo "<option value=\"".$bh->id."\" selected>".$bh->name."</option>\n";
							else if( isset($_POST['bank_holiday_name']) && $_POST['bank_holiday_name'] == $bh->name )
								echo "<option value=\"".$bh->id."\" selected>".$bh->name."</option>\n";
							else
								echo "<option value=\"".$bh->id."\">".$bh->name."</option>\n";
						}
						if( $geny_bh->id < 0 )
							$geny_bh->loadBankHolidayById( $bank_holidays[0]->id );
					?>
				</select>
			</p>
		</form>
		
		<script>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
			});
			$(document).ready(function(){
				$(".projectlistselect").listselect({listTitle: "Profils disponibles",selectedTitle: "Profils sélectionnés"});
			});
		</script>
		<form id="formID" action="loader.php?module=bank_holiday_edit" method="post">
			<input type="hidden" name="edit_bank_holiday" value="true" />
			<input type="hidden" name="bank_holiday_id" value="<?php echo $geny_bh->id ?>" />
			<p>
				<label for="project_id">Projet</label>
				<select name="project_id" id="project_id" class="chzn-select" data-placeholder="Choisissez un projet...">
					<option value=""></option>
					<?php
                        // Get only projects that are type "Congés"
                        // TODO: This should be a Property
						foreach( $geny_project->getProjectsByTypeId(5) as $project ) {
                            if($geny_bh->project_id == $project->id)
                                echo "<option value=\"".$project->id."\" selected>".$project->name."</option>\n";
                            else
                                echo "<option value=\"".$project->id."\">".$project->name."</option>\n";
						}
					?>
				</select>
			</p>
			<p>
				<label for="bank_holiday_type">Type</label>
				<select name="bank_holiday_type" id="bank_holiday_type" class="chzn-select" data-placeholder="Choisissez un type de congé...">
					<option value=""></option>
				</select>
			</p>
			<script type="text/javascript">
                function getTasks(){
						var project_id = $("#project_id").val();
						var task_id = <?php echo $geny_bh->task_id ?>;
						if( project_id > 0 ) {
							$.get('backend/api/get_project_tasks_list.php?no_task_blacklist=1&project_id='+project_id, function(data){
								$('.bank_holiday_options').remove();
								$("#bank_holiday_type").append('<option value="" class="bank_holiday_options"></option>');
								$.each(data, function(key, val) {
                                    if( task_id == val[0] ){
                                        $("#bank_holiday_type").append('<option class="bank_holiday_options" value="' + val[0] + '" title="' + val[2] + '" selected>' + val[1] + '</option>');
                                    }
                                    else{
                                        $("#bank_holiday_type").append('<option class="bank_holiday_options" value="' + val[0] + '" title="' + val[2] + '">' + val[1] + '</option>');
                                    }
								});
								$("#bank_holiday_type").attr('data-placeholder','Choisissez un type de congé...');
								$("#bank_holiday_type").trigger("liszt:updated");
								$("span:contains('Choisissez d'abord un projet...')").text('Choisissez un type de congé...');
								

							},'json');
						}
					}
					$("#project_id").change(getTasks);
					getTasks();
				$(function() {
					$( "#bank_holiday_start_date" ).datepicker();
					$( "#bank_holiday_start_date" ).datepicker('setDate', new Date());
					$( "#bank_holiday_start_date" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#bank_holiday_start_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
					$( "#bank_holiday_start_date" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
					$( "#bank_holiday_start_date" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
					$( "#bank_holiday_start_date" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
					$( "#bank_holiday_start_date" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
					$( "#bank_holiday_start_date" ).datepicker( "option", "firstDay", 1 );
					$( "#bank_holiday_start_date" ).datepicker('setDate', "<?php echo $geny_bh->start_date ?>");
					
					$( "#bank_holiday_stop_date" ).datepicker();
					$( "#bank_holiday_stop_date" ).datepicker('setDate', new Date());
					$( "#bank_holiday_stop_date" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#bank_holiday_stop_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
					$( "#bank_holiday_stop_date" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
					$( "#bank_holiday_stop_date" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
					$( "#bank_holiday_stop_date" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
					$( "#bank_holiday_stop_date" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
					$( "#bank_holiday_stop_date" ).datepicker( "option", "firstDay", 1 );
					$( "#bank_holiday_stop_date" ).datepicker('setDate', "<?php echo $geny_bh->stop_date ?>");
				});
			</script>
			<p>
				<label for="bank_holiday_name">Nom</label>
				<input name="bank_holiday_name" id="bank_holiday_name" type="text" class="validate[required] text-input"  value="<?php echo $geny_bh->name ?>"/>
			</p>
			<p>
				<label for="bank_holiday_start_date">Début de période</label>
				<input name="bank_holiday_start_date" id="bank_holiday_start_date" type="text" value="<?php echo $geny_bh->start_date ?>" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="bank_holiday_stop_date">Fin de période</label>
				<input name="bank_holiday_stop_date" id="bank_holiday_stop_date" type="text" value="<?php echo $geny_bh->stop_date ?>" class="validate[required,custom[date]] text-input" />
			</p>
			
			<p>
				<label for="country_id">Pays concerné</label>
				<select name="country_id" id="country_id" class="chzn-select" data-placeholder="Choisissez un pays...">
					<option value=""></option>
					<?php
						foreach( $geny_country->getAllCountries() as $c ) {
                            if($geny_bh->country_id == $c->id){
                                echo "<option value=\"".$c->id."\" selected>".$c->name."</option>\n";
                            }
                            else
                                echo "<option value=\"".$c->id."\">".$c->name."</option>\n";
						}
					?>
				</select>
			</p>
			
			<p>
				<input type="submit" value="Modifier" /> ou <a href="loader.php?module=bank_holiday_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/bank_holiday_list.dock.widget.php');
?>
