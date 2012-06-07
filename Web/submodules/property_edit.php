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


$properties = new GenyProperty();
$propertyValue = new GenyPropertyValue();
$propertyType = new GenyPropertyType();
$gritter_notifications = array();

if( isset($_POST['create_property']) && $_POST['create_property'] == "true" ){
	if( isset($_POST['property_name']) && $_POST['property_name'] != "" && isset($_POST['property_label']) && $_POST['property_label'] != "" && isset($_POST['property_type']) && $_POST['property_type'] != "" ){
		if( $properties->insertNewProperty($_POST['property_name'], $_POST['property_label'], $_POST['property_type']) ){
			$properties->loadPropertyByName($_POST['property_name']);
			$propertyValue->insertNewPropertyValue("", $properties->id);
			$gritter_notifications[] = array('status'=>'success', 'title' => 'Propriétée créé avec succès.','msg'=>"La propriété a été correctement créé.");
			$properties->loadPropertyByName($_POST['property_name']);
		}
		else{
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur.','msg'=>"Erreur lors de la création de la propriété.");
		}
	}
	else {
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur.','msg'=>"Certains champs obligatoires sont manquant. Merci de les remplir.");
	}
}
else if( isset($_POST['load_property']) && $_POST['load_property'] == "true" ){
	if(isset($_POST['property_id'])){
		$properties->loadPropertyById($_POST['property_id']);
	}
	else  {
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Chargement impossible','msg'=>"Impossible de charger la propriété : id non spécifié.");
	}
}
else if( isset($_GET['load_property']) && $_GET['load_property'] == "true" ){
	if(isset($_GET['property_id'])){
		$properties->loadPropertyById($_GET['property_id']);
	}
	else  {
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Chargement impossible','msg'=>"Impossible de charger la propriété : id non spécifié.");
	}
}
else if( isset($_POST['edit_property']) && $_POST['edit_property'] == "true" ){
	
	if(isset($_POST['property_id'])){
		
		$properties->loadPropertyById($_POST['property_id']);
		
		if( isset($_POST['property_name']) && $_POST['property_name'] != "" && $properties->name != $_POST['property_name'] ){
			$properties->updateString('property_name',$_POST['property_name']);
		}
		
		if( isset($_POST['property_label']) && $_POST['property_label'] != "" && $properties->label != $_POST['property_name'] ){
			$properties->updateString('property_label',$_POST['property_label']);
		}
		
		if( isset($_POST['property_type']) && $_POST['property_type'] != "" && $properties->type_id != $_POST['property_type'] ){
			$properties->updateInt('property_type_id',intval($_POST['property_type']));
		}
		
		if( isset($_POST['property_value']) && $_POST['property_value'] != "" ){
		
			$vals = $properties->getPropertyValues();
									
			if( isset($_POST['property_type']) ) $proptype = $_POST['property_type'];
			else $proptype = $properties->type_id;
			
			switch ( $proptype ) {
			
			case 1: // BOOL
				if($_POST['property_value'] == "0" || $_POST['property_value'] == "false") $string="false";
				else $string="true";
				if(count($vals)>1)
				{
					foreach($vals as $v)
						$v->deletePropertyValue($v->id);
					$vals = $properties->getPropertyValues();
				}
				if(count($vals) == 0)
				{
					$vals = new GenyPropertyValue;
					$vals->insertNewPropertyValue('true', $properties->id);
					$vals = $properties->getPropertyValues();
				}
				if($vals[0]->content != $string)
				{
					$vals[0]->updateString('property_value_content',$string);
					$vals[0]->commitUpdates();
				}
				break;
				
			case 2: // COMBOBOX
				if(count($vals) != count($_POST['property_value']))
				{
					foreach($vals as $v)
						$v->deletePropertyValue($v->id);
					$vals = new GenyPropertyValue;
					foreach($_POST['property_value'] as $v)
						$vals->insertNewPropertyValue('0', $properties->id);
					$vals = $properties->getPropertyValues();
				}
				$cpt = -1;
				foreach($_POST['property_value'] as $e) {
					$cpt++;
					if( is_numeric( $e ) &&  $e != "")
					{
						$vals[$cpt]->updateString('property_value_content',$e);
						$vals[$cpt]->commitUpdates();
					}
				}
				break;
			
			case 3: // SELECT
				if(count($vals)>1)
				{
					foreach($vals as $v)
						$v->deletePropertyValue($v->id);
					$vals = $properties->getPropertyValues();
				}
				if(count($vals) == 0)
				{
					$vals = new GenyPropertyValue;
					$vals->insertNewPropertyValue('0', $properties->id);
					$vals = $properties->getPropertyValues();
				}
				if( is_numeric( $_POST['property_value'] ) && $_POST['property_value'] != $vals[0]->content && $_POST['property_value'] != "")
				{
					$vals[0]->updateString('property_value_content',$_POST['property_value']);
					$vals[0]->commitUpdates();
				}
				break;
			
			case 4; // SHORT_TEXT
				if(count($vals)>1)
				{
					foreach($vals as $v)
						$v->deletePropertyValue($v->id);
					$vals = $properties->getPropertyValues();
				}
				if(count($vals) == 0)
				{
					$vals = new GenyPropertyValue;
					$vals->insertNewPropertyValue('not defined', $properties->id);
					$vals = $properties->getPropertyValues();
				}
				if( $_POST['property_value'] != $vals[0]->content )
				{
					$vals[0]->updateString('property_value_content',$_POST['property_value']);
					$vals[0]->commitUpdates();
				}
				break;
			
			case 5; // TEXTAREA
				if(count($vals)>1)
				{
					foreach($vals as $v)
						$v->deletePropertyValue($v->id);
					$vals = $properties->getPropertyValues();
				}
				if(count($vals) == 0)
				{
					$vals = new GenyPropertyValue;
					$vals->insertNewPropertyValue('', $properties->id);
					$vals = $properties->getPropertyValues();
				}
				if( $_POST['property_value'] != "" && $_POST['property_value'] != $vals->content)
				{
					$vals[0]->updateString('property_value_content',$_POST['property_value']);
					$vals[0]->commitUpdates();
				}
				break;
			
			case 6; // DATE
				if(count($vals)>1)
				{
					foreach($vals as $v)
						$v->deletePropertyValue($v->id);
					$vals = $properties->getPropertyValues();
				}
				if(count($vals) == 0)
				{
					$vals = new GenyPropertyValue;
					$vals->insertNewPropertyValue('', $properties->id);
					$vals = $properties->getPropertyValues();
				}
				if( preg_match( '/^\d{4}-\d{1,2}-\d{1,2}$/', $_POST['property_value'] ) && $_POST['property_value'] != $vals[0]->content )
				{
					$vals[0]->updateString('property_value_content',$_POST['property_value']);
					$vals[0]->commitUpdates();
				}
				else if(!preg_match( '/^\d{4}-\d{1,2}-\d{1,2}$/', $vals[0]->content ))
				{
					$vals[0]->updateString('property_value_content',"0000-00-00");
					$vals[0]->commitUpdates();
				}
				break;
			    
			default: // problème
				$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur : mauvais type de propriété");
				break;
			    
			}
		}
		
		if($properties->commitUpdates()){
			$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Propriété mise à jour avec succès.");
			$properties->loadPropertyById($_POST['property_id']);
		}
		else{
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la mise à jour de la propriété.");
		}
	}
	else  {
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Modification impossible','msg'=>"Impossible de modifier la propriété : id non spécifié.");
	}
}


?>

<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/property_edit.png">
		<span class="property_edit">
			Modifier une Propriété
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de modifier une propriété d'administration.
		</p>
		
		<script>
			<?php
				// Cette fonction est définie dans header.php
				displayStatusNotifications($gritter_notifications,$web_config->theme);
			?>
		</script>
		
		<form id="select_login_form" action="loader.php?module=property_edit" method="post">
			<input type="hidden" name="load_property" value="true" />
			<p>
				<label for="property_id">Sélection propriété</label>

				<select name="property_id" id="property_id" onChange="submit()" class="chzn-select">
					<?php
						$list = $properties->getPropertiesList();
						foreach( $list as $prop ){
							if( (isset($_POST['property_id']) && $_POST['property_id'] == $prop->id) || (isset($_GET['property_id']) && $_GET['property_id'] == $prop->id) )
								echo "<option value=\"".$prop->id."\" selected>".$prop->name."</option>\n";
							else if( isset($_POST['property_name']) && $_POST['property_name'] == $prop->name )
								echo "<option value=\"".$prop->id."\" selected>".$prop->name."</option>\n";
							else
								echo "<option value=\"".$prop->id."\">".$prop->name."</option>\n";
						}
						if( $properties->id < 0 )
							$properties->loadPropertyById( $list[0]->id );
					?>
				</select>
			</p>
		</form>

		<form id="start" action="loader.php?module=property_edit" method="post" name="form">
			<input type="hidden" name="edit_property" value="true" />
			<input type="hidden" name="property_id" value="<?php echo $properties->id ?>" />
			<p>
				<label for="property_name">Name</label>
				<input name="property_name" style="padding:4px 0 4px 0;" id="property_name" type="text" class="validate[required] text-input" value="<?php echo $properties->name ?>"/>
			</p>
			
			<p>
				<label for="property_label">Label</label>
				<input name="property_label" style="padding:4px 0 4px 0;" id="property_label" type="text" class="validate[required] text-input" value="<?php echo $properties->label ?>"/>
			</p>
			
			<p>
				<label for="property_type">Type</label>
				<select name="property_type" class="chzn-select" id="property_type" class="validate[required] select-input">
				<?php $propertyTypes = new GenyPropertyType();
				foreach($propertyTypes->getPropertyTypesListWithRestrictions(array()) as $type ) {
				echo '<option value="' . $type->id . '"';
				if($type->id == $properties->type_id) echo ' selected ';
				echo '>' . $type->name . '</option>';
				} ?>
				</select>
			</p>

			<p>
				<label for="property_value">Valeur</label>
				<?php

				$propertyType = new GenyPropertyType;
				$propertyType->loadPropertyTypeById($properties->type_id);
				$propertyValue = $properties->getPropertyValues();
				$propertyOption = $properties->getPropertyOptions();
				
				switch($propertyType->shortname)
				{
					case "PROP_TYPE_BOOL":
					if($propertyValue[0]->content == 'true') { $true="selected"; $false = ""; }
					else { $true=""; $false = "selected"; }
					echo '<select name="property_value" class="chzn-select" id="property_value" class="validate[required] select-input">';
					echo '<option value="1" ' . $true . ' >true</option>';
					echo '<option value="0" ' . $false . ' >false</option>';
					echo "</select>";
					break;
					
					case "PROP_TYPE_SHORT_TEXT":
					echo '<input name="property_value" id="property_value" type="text" class="validate[required] text-input" value="' . $propertyValue[0]->content . '"/>';
					break;
					
					case "PROP_TYPE_DATE":
					echo '<input name="property_value" id="property_value" type="text" class="validate[required,custom[date]] text-input" value="' . $propertyValue[0]->content . '"/>';
					break;
					
					case "PROP_TYPE_LONG_TEXT":
					echo '<textarea name="property_value" id="property_value" type="text" class="validate[required] text-input">' . $propertyValue[0]->content . '</textarea>';
					break;

					case "PROP_TYPE_MULTI_SELECT":
					$multiple = 'multiple="multiple"';
					$crochet = '[]';
					
					case "PROP_TYPE_LIST_SELECT":
					if(!isset($multiple)) $multiple = "";
					if(!isset($crochet)) $crochet = "";
					
					echo '<select ' . $multiple . ' style="width:350px;" name="property_value'. $crochet . '" class="chzn-select" id="property_value" class="validate[required] select-input">';
					foreach($propertyOption as $opt) {
						$select = "";
						foreach($propertyValue as $v) { if($v->content == $opt->id) $select = "selected"; }
						echo '<option value="' . $opt->id . '"' . $select . '>' . $opt->content . '</option>';
					}
					echo "</select>";
					echo '<input type="button" style="padding:4px;position:relative;top:-11px;margin-left:5px;" value="Supprimer" onClick="rmOpt()">';
					echo "</p>";
					
					
					echo "<p>";
					echo '<label for="property_option_add">Ajout d\'option</label>';
					echo '<input type="text" style="padding:4px 0 4px 0;width:350px;" name="ip" id="ip"><input style="padding:4px;margin-left:5px;" type="button" value="Ajouter" onClick="addOpt()">';
					echo "</p>";
					
					break;
					
					default:
					//ERROR
					break;
				}
				?>
			</p>
			
			<p>
				<input type="submit" value="Modifier"/> ou <a href="loader.php?module=property_list">annuler</a>
			</p>
			
		</form>
	</p>
	
	<script type="text/javascript">
	<!--
	function addOpt(){
		var prop_id = $("#property_id").val();
		var content = $("#ip").val();
		if( prop_id > 0 )
		{
			var result_of_query = $.get('backend/api/update_property_options.php?prop_id='+prop_id+'&content='+content+'&action=add', function( data ) {
				$("#property_value").append('<option value='+data+'>'+content+'</option>');
				$("#ip").val("");
				$("#property_value").trigger("liszt:updated");
			},'html');
		}
	}
	function rmOpt(){
		var id = $("#property_value").val();
		if($.isArray(id))
			$.each(id, function(i, id) { 
				if( id > 0 )
				{
					var result_of_query = $.get('backend/api/update_property_options.php?id='+id+'&action=delete', function( data ) {
						if(data == 1) $("#property_value option[value="+id+"]").remove();
						$("#property_value").trigger("liszt:updated");
					},'html');
				}
			});
		else if( id > 0 )
		{
			var result_of_query = $.get('backend/api/update_property_options.php?id='+id+'&action=delete', function( data ) {
				if(data == 1) $("#property_value option[value="+id+"]").remove();
				$("#property_value").trigger("liszt:updated");
			},'html');
		}
		
	$("#property_value").trigger("liszt:updated");
	}
	
	/*$("#property_type").chosen().change( function() {
		var str = "";
		$("select option:selected").each(function () {
			var prop_id = $("#property_id").val();
			var result_of_query = $.get('backend/api/get_property_form_from_type.php?id='+prop_id+'&type='+, function( data ) {
				$("#property_dyn_form").html(data);
			},'html');
		});
        }).change();*/	
	-->
	</script>
	
	<?php if($propertyType->id == 6) { ?>
	<script type="text/javascript">
		$(function () {
			$("#property_value").datepicker();
			$("#property_value").datepicker( "option", "showAnim", "slideDown" );
			$("#property_value").datepicker( "option", "dateFormat", "yy-mm-dd" );
			$("#property_value").datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
			$("#property_value").datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
			$("#property_value").datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
			$("#property_value").datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
			$("#property_value").show();
			$("#property_value").val("<?php echo $propertyValue[0]->content; ?>");
		});
	</script>
	<?php } ?>
	
	
	
</div>
<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php','backend/widgets/property_list.dock.widget.php','backend/widgets/property_add.dock.widget.php');
?>
