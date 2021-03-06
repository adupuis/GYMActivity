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


$geny_profile = new GenyProfile();
$geny_rg = new GenyRightsGroup();

?>
<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/profile_add.png"></img>
		<span class="profile_add">
			Ajouter un profil
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet d'ajouter un profil dans la base des utilisateurs. Tous les champs doivent être remplis.
		</p>
		 <script>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
			});
			
		</script>
		<form id="formID" action="loader.php?module=profile_edit" method="post">
			<input type="hidden" name="create_profile" value="true" />
			<p>
				<label for="profile_login">Login</label>
				<input name="profile_login" id="profile_login" type="text" class="validate[required,length[2,100]] text-input" />
			</p>
			<script>
				function updateVals() {
					var text = $("#profile_login").val();
					$("#profile_email").val("");
					$("#profile_email").val(text+"@genymobile.com");
				}

				$("#profile_login").change(updateVals);
				updateVals();

			</script>
			<p>
				<label for="profile_firstname">Prénom</label>
				<input name="profile_firstname" id="profile_firstname" type="text" class="validate[required,length[2,100]] text-input" />
			</p>
			<p>
				<label for="profile_lastname">Nom de famille</label>
				<input name="profile_lastname" id="profile_lastname" type="text" class="validate[required,length[2,100]] text-input" />
			</p>
			<p>
				<label for="profile_password">Mot de passe</label>
				<input name="profile_password" id="profile_password" type="password" class="validate[required,length[8,100]] text-input" />
			</p>
			<p>
				<label for="profile_email">E-Mail</label>
				<input name="profile_email" class="validate[required,custom[email]] text-input" id="profile_email" type="text" />
			</p>
			<p>
				<label for="profile_is_active">Profil actif</label>
				<select name="profile_is_active" id="profile_is_active" />
					<option value="true">Oui</option>
					<option value="false">Non</option>
				</select>
			</p>
			<p>
				<label for="profile_needs_password_reset">R-à-Z password</label>
				<select name="profile_needs_password_reset" id="profile_needs_password_reset"/>
					<option value="true">Oui</option>
					<option value="false">Non</option>
				</select>
			</p>
			<p>
				<label for="rights_group_id">Groupe</label>
				<select name="rights_group_id" id="rights_group_id" class="chzn-select">
					<?php
						foreach( $geny_rg->getAllRightsGroups() as $group ){
							if($geny_profile->rights_group_id == $group->id)
								echo "<option value=\"".$group->id."\" title=\"".$group->description."\" selected>".$group->name."</option>\n";
							else
								echo "<option value=\"".$group->id."\" title=\"".$group->description."\">".$group->name."</option>\n";
						}
					?>
				</select>
			</p>
			<p>
				<label for="group_leader_id">Group Leader</label>
				<select name="group_leader_id" id="group_leader_id" class="chzn-select">
				<?php
					foreach( $geny_profile->getProfilesListWithRestrictions( array("rights_group_id=".$geny_rg->getIdByShortname('ADM'), "rights_group_id=".$geny_rg->getIdByShortname('TM'), "rights_group_id=".$geny_rg->getIdByShortname('GL') ), "OR" ) as $pfl ){
						echo "<option value=\"".$pfl->id."\">".GenyTools::getProfileDisplayName($pfl)."</option>\n";
					}
				?>
				</select>
			</p>
			<p>
				<label for="technology_leader_id">Technology Leader</label>
				<select name="technology_leader_id" id="technology_leader_id" class="chzn-select">
				<?php
					foreach( $geny_profile->getProfilesListWithRestrictions( array("rights_group_id=".$geny_rg->getIdByShortname('ADM'), "rights_group_id=".$geny_rg->getIdByShortname('TM'), "rights_group_id=".$geny_rg->getIdByShortname('TL') ), "OR" ) as $pfl ){
						echo "<option value=\"".$pfl->id."\">".GenyTools::getProfileDisplayName($pfl)."</option>\n";
					}
				?>
				</select>
			</p>
			<p>
				<label for="pmd_category">Catégorie</label>
				<select name="pmd_category" id="pmd_category" class="chzn-select"	>
					<?php
					$geny_property = new GenyProperty();
					$geny_property->loadPropertyByName('PROP_PROFILE_CATEGORY');
					foreach( $geny_property->getPropertyOptions() as $option ){
						if($option->content == $web_config->default_profile_category){
							echo "<option value='".$option->id."' selected>".$option->content."</option>";
						}
						else{
							echo "<option value='".$option->id."'>".$option->content."</option>";
						}
					}
					?>
				</select>
			</p>
			<p>
				<label for="pmd_is_billable">Profil facturable</label>
				<select name="pmd_is_billable" id="pmd_is_billable" >
					<option value="true" selected>Oui</option>
					<option value="false">Non</option>
				</select>
			</p>
			<p>
				<label for="pmd_country">Pays</label>
				<select name="pmd_country" id="pmd_country" >
					<?php
                        $country = new GenyCountry();
                        foreach($country->getAllCountries() as $c){
                            if($c->id == $geny_pmd->country_id){
                                echo "<option value='".$c->id."' selected>".$c->name."</option>";
                            }
                            else{
                                echo "<option value='".$c->id."'>".$c->name."</option>";
                            }
                        }
					?>
				</select>
			</p>
			<p>
				<label for="pmd_salary">Salaire fixe (€ brut/an)</label>
				<input name="pmd_salary" id="pmd_salary" value="0" type="text" class="validate[required,custom[reallyOnlyNumber]] text-input" />
			</p>
			<p>
				<label for="pmd_variable_salary">Salaire Var. (€ brut/an)</label>
				<input name="pmd_variable_salary" id="pmd_variable_salary" value="0" type="text" class="validate[required,custom[reallyOnlyNumber]] text-input" />
			</p>
			<p>
				<label for="pmd_objectived_salary">Prime / Obj. (€ brut/an)</label>
				<input name="pmd_objectived_salary" id="pmd_objectived_salary" value="0" type="text" class="validate[required,custom[reallyOnlyNumber]] text-input" />
			</p>
			
			<script type="text/javascript">
				$(function() {
					$( "#pmd_recruitement_date" ).datepicker();
					$( "#pmd_recruitement_date" ).datepicker('setDate', new Date());
					$( "#pmd_recruitement_date" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#pmd_recruitement_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
					$( "#pmd_recruitement_date" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
					$( "#pmd_recruitement_date" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
					$( "#pmd_recruitement_date" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
					$( "#pmd_recruitement_date" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
					$( "#pmd_recruitement_date" ).datepicker( "option", "firstDay", 1 );
					
					$( "#pmd_availability_date" ).datepicker();
					$( "#pmd_availability_date" ).datepicker('setDate', new Date());
					$( "#pmd_availability_date" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#pmd_availability_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
					$( "#pmd_availability_date" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
					$( "#pmd_availability_date" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
					$( "#pmd_availability_date" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
					$( "#pmd_availability_date" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
					$( "#pmd_availability_date" ).datepicker( "option", "firstDay", 1 );
				});
			</script>
			<p>
				<label for="pmd_recruitement_date">Date d'embauche</label>
				<input name="pmd_recruitement_date" id="pmd_recruitement_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="pmd_availability_date">Date de disponibilité</label>
				<input name="pmd_availability_date" id="pmd_availability_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			
			<p>
				<input type="submit" value="Créer" /> ou <a href="loader.php?module=profile_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php','backend/widgets/profile_list.dock.widget.php');
?>
