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

session_start();
$required_group_rights = 1;
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
						echo json_encode( array( "status" => "success", "status_message" => "Property option have been updated successfully.") );
						exit;
					}
					else {
						echo json_encode( array( "status" => "error", "status_message" => "Fatal error: Property option couldn't be updated, please check the parameters and server logs." ) );
						exit;
					}
				}
				else {
					echo json_encode( array( "status" => "error", "status_message" => "Fatal error: Content field is empty" ) );
					exit;
				}
			}
			else {
				echo json_encode( array( "status" => "error", "status_message" => "Fatal error: Id is empty or negative" ) );
				exit;
			}
		}
		else if( $param_action == "add" ) {
			if( $param_property_id > 0 ) {
				if( $param_content != "" ) {
					$geny_property_option_id = $geny_property_option->insertNewPropertyOption( $param_content, $param_property_id );
					if( $geny_property_option_id != -1 ) {
						echo json_encode( array( "status" => "success", "status_message" => "Property option have been created successfully.", "new_property_option_id" => $geny_property_option_id ) );
						exit;
					}
					else {
						echo json_encode( array( "status" => "error", "status_message" => "Fatal error: Property option couldn't be created, please check the parameters and server logs." ) );
						exit;
					}
				}
				else {
					echo json_encode( array( "status" => "error", "status_message" => "Fatal error: Content is empty" ) );
					exit;
				}
			}
			else {
				echo json_encode( array( "status" => "error", "status_message" => "Fatal error: Property Id is empty or negative" ) );
				exit;
			}
		}
		else if( $param_action == "delete" ) {
			if( $param_id > 0 ) {
				$geny_property_option->loadPropertyOptionById( $param_id );
				if( $geny_property_option->deletePropertyOption() > 0 ) {
					echo json_encode( array( "status" => "success", "status_message" => "Property option have been deleted successfully." ) );
					exit;
				}
				else {
					echo json_encode( array( "status" => "error", "status_message" => "Fatal error: Property option couldn't be deleted, please check the parameters and server logs." ) );
					exit;
				}
			}
			else {
				echo json_encode( array( "status" => "error", "status_message" => "Fatal error: Id is empty or negative" ) );
				exit;
			}
		}
		else {
			echo json_encode( array( "status" => "error", "status_message" => "Fatal error: Action is empty or not properly set" ) );
			exit;
		}
	}
} catch (Exception $e) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>