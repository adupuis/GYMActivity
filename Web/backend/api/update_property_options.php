<?php

//  Copyright (C) 2011 by GENYMOBILE & Jean-Charles Leneveu
//  jcleneveu@genymobile.com
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

include '../../rights_groups.php';

session_start();
$required_group_rights = array(Admins);
$auth_granted = false;

header('Content-Type: application/json;charset=UTF-8');

include_once 'ajax_authent_checking.php';
include_once 'ajax_toolbox.php';

try {
	if( $auth_granted ) {
		$geny_property_option = new GenyPropertyOption();
		$geny_property_option_id = -1;
		$param_content = getParam("content", "");
		$param_id = getParam("id", -1);
		$param_property_id = getParam("prop_id", -1);
		$param_action = getParam("action", "none");
		
		if( $param_action == "edit") {
			if( $param_id > 0 ) {
				$geny_property_option->loadPropertyOptionById( $param_id );
				if( $param_content != "" ) {
					$geny_property_option->updateString( "property_option_content", $param_content );
					if( $geny_property_option->commitUpdates() ) {
						echo json_encode( array( "status" => "success", 'title' => 'Option éditée avec succès', "msg" => "L'option a été éditée avec succès !") );
						exit;
					}
					else {
						echo json_encode( array( "status" => "error", 'title' => 'Erreur fatale', "msg" => "L'option n'a pas pu être éditée" ) );
						exit;
					}
				}
				else {
					echo json_encode( array( "status" => "error", 'title' => 'Erreur fatale', "msg" => "Le champ : \"content\" est vide" ) );
					exit;
				}
			}
			else {
				echo json_encode( array( "status" => "error", 'title' => 'Erreur fatale', "msg" => "Le champ : \"id\" est vide ou négatif" ) );
				exit;
			}
		}
		else if( $param_action == "add" ) {
			if( $param_property_id > 0 ) {
				if( $param_content != "" ) {
					$geny_property_option_id = $geny_property_option->insertNewPropertyOption( $param_content, $param_property_id );
					if( $geny_property_option_id != -1 ) {
						echo json_encode( array( "status" => "success", 'title' => 'Option créée avec succès', "msg" => "L'option a été créée avec succès", "new_property_option_id" => $geny_property_option_id ) );
						exit;
					}
					else {
						echo json_encode( array( "status" => "error", 'title' => 'Erreur fatale', "msg" => "L'option n'a pas pu être créée avec succès" ) );
						exit;
					}
				}
				else {
					echo json_encode( array( "status" => "error", 'title' => 'Erreur fatale', "msg" => "Le champ : \"content\" est vide" ) );
					exit;
				}
			}
			else {
				echo json_encode( array( "status" => "error", 'title' => 'Erreur fatale', "msg" => "Le champ : \"property_id\" est vide ou négatif" ) );
				exit;
			}
		}
		else if( $param_action == "delete" ) {
			if( $param_id > 0 ) {
				$geny_property_option->loadPropertyOptionById( $param_id );
				if( $geny_property_option->deletePropertyOption() > 0 ) {
					echo json_encode( array( "status" => "success", 'title' => 'Option supprimée', "msg" => "L'option a été supprimée avec succès" ) );
					exit;
				}
				else {
					echo json_encode( array( "status" => "error", 'title' => 'Erreur fatale', "msg" => "L'option n'a pas pu être supprimée" ) );
					exit;
				}
			}
			else {
				echo json_encode( array( "status" => "error", 'title' => 'Erreur fatale', "msg" => "Le champ : \"id\" est vide ou négatif" ) );
				exit;
			}
		}
		else {
			echo json_encode( array( "status" => "error", 'title' => 'Erreur fatale', "msg" => "L'action est vide ou non correctement définie" ) );
			exit;
		}
	}
} catch (Exception $e) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>