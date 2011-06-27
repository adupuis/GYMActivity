<?php
// Variable to configure global behaviour
$header_title = 'GenY Mobile - Validation CRA';
$required_group_rights = 5;

include_once 'header.php';
include_once 'menu.php';

$geny_ptr = new GenyProjectTaskRelation();
$geny_tools = new GenyTools();
date_default_timezone_set('Europe/Paris');
$db_status = "";

if(isset($_POST['create_cra']) && $_POST['create_cra'] == "true" ){
	$html_worked_days_table = '';
	if( isset($_POST['assignement_start_date']) && isset($_POST['assignement_end_date']) && isset($_POST['assignement_id']) && isset($_POST['task_id']) && isset($_POST['assignement_load']) && isset($_POST['task_id']) ){
		foreach( GenyTools::getWorkedDaysList(strtotime($_POST['assignement_start_date']), strtotime($_POST['assignement_end_date']) ) as $day ){
			$geny_activity = new GenyActivity();
			$geny_ar = new GenyActivityReport();
			$day_load = $geny_ar->getDayLoad($profile->id,$day)+$_POST['assignement_load'];
			if($day_load <= 8){
				$geny_activity_id = $geny_activity->insertNewActivity('NULL',$day,$_POST['assignement_load'],date('Y-m-j'),$_POST['assignement_id'],$_POST['task_id']);
				echo "<!-- Geny Activity ID: $geny_activity_id -->\n";
				if( $geny_activity_id > -1 ){
					$geny_ars = new GenyActivityReportStatus();
					$geny_ars->loadActivityReportStatusByShortName('P_USER_VALIDATION'); 
					echo "<!-- Inserting new activity report -->\n";
					$geny_ar_id = $geny_ar->insertNewActivityReport('NULL',-1,$geny_activity_id,$profile->id,$geny_ars->id );
					echo "<!-- Geny Activity Report ID: $geny_ar_id -->\n";
					if( $geny_ar_id > -1 )
						$db_status .= "<li class=\"status_message_success\">Rapport enregistré pour le $day (en attente de validation utilisateur).</li>\n";
					else
						$db_status .= "<li class=\"status_message_error\">Erreur lors de l'enregistrement du rapport du $day.</li>\n";
				}
				else {
					$geny_activity->removeActivity($geny_activity_id);
					$db_status .= "<li class=\"status_message_error\">Erreur lors de l'ajout d'une activité pour le $day.</li>\n";
				}
			}
			else{
				if( $day_load > 12 ){
					$db_status .= "<li class=\"status_message_error\">Erreur : Le $day, vous déclaré plus de 12 heures journalière (maximum d'heures par jour : 8h + 4h sup.).</li>\n";
				}
				else{
					$day_work_load_by_project = $geny_ar->getDayLoadByAssignement($profile->id,$day);
					// TODO: gérer la vérification de l'autorisation des heures sup. Il faut vérifier l'assignement en cours récupérer le project_id, vérifier que le projet autorise les heures sup puis vérifier la sommes des heures travaillés (<= 8 + 4 heures sup au maximum)
					$extra = '';
					for($k=0; $k < count($day_work_load_by_project); $k++ ){
						$extra .= $day_work_load_by_project[$k]['activity_date']."|".$day_work_load_by_project[$k]['sum_activity_load']."|".$day_work_load_by_project[$k]['assignement_id'].",";
					}
					$db_status .= "<li class=\"status_message_error\">Erreur : Le $day, vous déclaré plus de 8 heures journalière ($extra).</li>\n";
				}
			}
		}
	}
	else{
		$db_status .= "<li class=\"status_message_error\">Erreur : certaines informations sont manquantes.</li>\n";
	}
}

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/cra.png"/><p>CRA</p>
</div>


<div id="mainarea">
	<p class="mainarea_title">
		<span class="cra_add">
			Valider des CRA
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de valider des rapports d'activité.<br />
		<strong class="important_note">Important :</strong> Ce formulaire contient tous les rapports que vous n'avez pas validé (y compris les plus anciens que vous n'auriez pas soumis).<br />
		</p>
		<script>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
			});
		</script>
		<?php
			if( isset($db_status) && $db_status != "" ){
				echo "<ul class=\"status_message\">\n$db_status\n</ul>";
			}
		?>
		<script>
			$(".status_message").click(function () {
			$(".status_message").fadeOut("slow");
			});
		</script>
		<form id="formID" action="cra_add.php" method="post">
			<input type="hidden" name="validate_cra" value="true" />
			<p>
				<table>
					<?php
						$geny_ar = new GenyActivityReport();
						$geny_ars = new GenyActivityReportStatus();
						$geny_ars->loadActivityReportStatusByShortName('P_USER_VALIDATION');
						foreach( $geny_ar->getActivityReportsListWithRestrictions( array("activity_report_status_id=".$geny_ars->id,"profile_id=".$profile->id) ) as $ar ){
							$tmp_activity = new GenyActivity( $ar->activity_id );
							$tmp_ars = new GenyActivityReportStatus( $ar->status_id );
							$tmp_task = new GenyTask( $tmp_activity->task_id );
							$tmp_assignement = new GenyAssignement( $tmp_activity->assignement_id );
							$tmp_project = new GenyProject( $tmp_assignement->project_id );
							
							echo "<tr><td><input type='checkbox' name='activity_report_id' value=".$ar->id." /></td><td>".$tmp_activity->activity_date."</td><td>".$tmp_project->name."</td><td>".$tmp_task->name."</td><td>".$tmp_activity->load."</td><td>".$geny_ar->getDayLoad($profile->id,$tmp_activity->activity_date)."</td></tr>";
						}
					?>
				</table>
			</p>
			<p>
				<input type="submit" value="Valider" /> ou <a href="#formID">annuler</a>
			</p>
		</form>
	</p>
</div>

<?php
include_once 'footer.php';
?>
