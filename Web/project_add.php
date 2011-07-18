<?php
// Variable to configure global behaviour
$header_title = 'GenY Mobile - Ajout projet';
$required_group_rights = 2;

include_once 'header.php';
include_once 'menu.php';

// $handle = mysql_connect($db_host,$db_user,$db_password);
// mysql_select_db("GYMActivity");
// mysql_query("SET NAMES 'utf8'");
$geny_client = new GenyClient();
$geny_profile = new GenyProfile();

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/project_generic.png"/><p>Projet</p>
</div>

<div id="mainarea">
	<p class="mainarea_title">
		<span class="project_add">
			Ajouter un projet
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet d'ajouter un projet dans la base des projets. Tous les champs doivent être remplis.
		</p>
		<script>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
			});
			$(document).ready(function(){
				$(".taskslistselect").listselect({listTitle: "Tâches disponibles",selectedTitle: "Tâches séléctionnées"});
			});
			$(document).ready(function(){
				$(".profileslistselect").listselect({listTitle: "Profiles disponibles",selectedTitle: "Profiles séléctionnées"});
			});
			$(function() {
				var availableTags = [
					<?php
						$tags = '';
						$project = new GenyProject();
						foreach( $project->getLocationsList() as $loc ){
							$tags .= '"'.$loc.'",';
						}
						echo rtrim($tags, ",");
					?>
				];
				$( "#project_location" ).autocomplete({
					source: availableTags
				});
			});
		</script>
		<form id="formID" action="project_edit.php" method="post">
			<input type="hidden" name="create_project" value="true" />
			<p>
				<label for="project_name">Nom du projet</label>
				<input name="project_name" id="project_name" type="text" class="validate[required,length[2,100]] text-input" />
			</p>
			<p>
				<label for="project_client">Client</label>
				<select name="project_client" id="project_client">
				<?php
					foreach( $geny_client->getAllClients() as $client ){
						echo "<option value=\"".$client->id."\">".$client->name."</option>\n";
					}
				?>
				</select>
			</p>
			<p>
				<label for="project_type">Type</label>
				<select name="project_type" id="project_type">
				<?php
					$geny_pt = new GenyProjectType();
					foreach ($geny_pt->getAllProjectTypes() as $pt){
						echo "<option value=\"".$pt->id."\" title=\"".$pt->description."\">".$pt->name."</option>\n";
					}
				?>
				</select>
			</p>
			<p>
				<label for="project_status">Status</label>
				<select name="project_status" id="project_status">
				<?php
					$geny_ps = new GenyProjectStatus();
					foreach ($geny_ps->getAllProjectStatus() as $ps){
						echo "<option value=\"".$ps->id."\" title=\"".$ps->description."\">".$ps->name."</option>\n";
					}
				?>
				</select>
			</p>
			<p>
				<label for="project_location">Localisation</label>
				<input name="project_location" id="project_location" type="text" class="validate[required,custom[onlyLetter],length[2,100]] text-input" />
			</p>
			<script type="text/javascript">
				$(function() {
					$( "#project_start_date" ).datepicker();
					$( "#project_start_date" ).datepicker('setDate', new Date());
					$( "#project_start_date" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#project_start_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
					$( "#project_end_date" ).datepicker();
					$( "#project_end_date" ).datepicker('setDate', new Date());
					$( "#project_end_date" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#project_end_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
				});
			</script>
			<p>
				<label for="project_start_date">Date de début</label>
				<input name="project_start_date" id="project_start_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="project_end_date">Date de fin</label>
				<input name="project_end_date" id="project_end_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="project_description">Description</label>
				<textarea name="project_description" id="project_description" class="validate[required] text-input"></textarea>
			</p>
			<p>
				<label for="tasks_checkboxgroup">Tâches</label>
				<select class="taskslistselect" name="project_tasks[]">
				<?php
					$geny_task = new GenyTask();
					foreach( $geny_task->getAllTasks() as $t ){
						echo "<option value=\"$t->id\" >$t->name</input></option>";
					}
				?>
				</select>
			</p>
			<p>
				<label for="profiles_checkboxgroup">Attributions</label>
				<select class="profileslistselect" name="project_profiles[]">
				<?php
					foreach( $geny_profile->getAllProfiles() as $p ){
						echo "<option value=\"$p->id\" title=\"$p->firstname $p->lastname\">$p->login</input></option>";
					}
				?>
				</select>
			</p>
			<p>
				<input type="checkbox" name="project_allow_overtime" value="true" /> Autoriser les heures supplémentaires pour tout les collaborateurs. Cette opération autorisera tous les collaborateurs ajoutés au projet à ce moment. C'est un mode de groupe afin de faciliter une opération de masse, pour autoriser les heures supplémentaires par collaborateur rendez vous sur la page de <a href="/assignement_list.php">gestion des affectactions</a>. 
			</p>
			<p>
				<input type="submit" value="Créer" /> ou <a href="#form">annuler</a>
			</p>
		</form>
	</p>
</div>
<div id="bottomdock">
	<ul>
		<?php include 'backend/widgets/project_list.dock.widget.php'; ?>
	</ul>
</div>
<?php
include_once 'footer.php';
?>
