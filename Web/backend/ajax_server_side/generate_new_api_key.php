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
$required_group_rights = 6;
$auth_granted = false;

header('Content-type:text/javascript;charset=UTF-8');

include_once 'ajax_authent_checking.php';
include_once 'ajax_toolbox.php';

try {
	$profiles = array();
	if($auth_granted){
		$old_key = getParam("old_key");
		$old_key_timestamp = getParam("ts",-1);
		$old_key_profile_id = getParam("owner",-1);
		
		if( $old_key != "" && $old_key_timestamp != -1 && $old_key_profile_id != -1 ){
			$key1 = new GenyApiKey();
			$key1->loadApiKeyByProfileId($old_key_profile_id);
			$key2 = new GenyApiKey();
			$key2->loadApiKeyByData($old_key);
			// key1 et key2 sont censé être les même, vérifions ça :
			if( $key1->id == $key2->id && $key1->profile_id == $key2->profile_id && $key1->data == $key2->data && $key1->timestamp == $key2->timestamp && $key1->timestamp == $old_key_timestamp ){
				// Arrivé là nous devrions être aussi certain que possible que la clé existe dans la base, que les paramètres sont cohérents entre eux et qu'ils correspondent à ce qu'il y a dans la base (y compris pour le timestamp).
				// Nous générons donc une nouvelle clé.
				$new_key = $key1->generateApiKey();
				if($key1->insertNewApiKey(0,$key1->profile_id,$new_key) > 0){
					echo json_encode(array("status" => "success","success_string" => "New API key generated.","new_key" => "$new_key"));
				}
				else
					echo json_encode(array("status" => "error","error_string" => "Insertion of the newly generated key failed."));
				
			}
			else
				echo json_encode(array("status"=>"error","error_string"=>"Parameters does not match database's data."));
		}
		else
			echo json_encode(array("status"=>"error","error_string"=>"Required parameters not provided."));
	}
} catch (Exception $e) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>