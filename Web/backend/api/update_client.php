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

session_start();
$required_group_rights = 2;
$auth_granted = false;

header('Content-type:text/javascript;charset=UTF-8');

include_once 'ajax_authent_checking.php';
include_once 'ajax_toolbox.php';

try {
	$clients = array();
	if($auth_granted){
		$tmp_client = new GenyClient();
		$results = array();
		$value = getParam("value",-1);
		$label = getParam("label");
		$action = getParam("action","none");
		
		if( $action == "edit"){
			if($value > 0){
				$tmp_client->loadClientById($value);
				if( $label != "" ){
					$tmp_client->updateString("client_name",$label);
					if($tmp_client->commitUpdates())
						echo json_encode(array("status" => "success", "status_message" => "Client mis à jour." ));
					else
						echo json_encode(array("status" => "error", "status_message" => "Une erreur est survenue ." ));
				}
				else
					echo json_encode(array("status" => "error", "status_message" => "Label client non définit." ));
			}
			else
				echo json_encode(array("status" => "error", "status_message" => "Value est soit invalide soit non définit." ));
		}
		else if( $action == "delete" ){
			if($value > 0){
				$tmp_client->loadClientById($value);
				if( $tmp_client->deleteClient() > 0 )
					echo json_encode(array("status" => "success", "status_message" => "Client supprimé." ));
			}
			else
				echo json_encode(array("status" => "error", "status_message" => "Value est soit invalide soit non définit." ));
		}
		else
			echo json_encode(array("status" => "error", "status_message" => "Action n'est pas définit." ));
	}
} catch (Exception $e) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>