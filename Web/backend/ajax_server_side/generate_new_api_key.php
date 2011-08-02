<?php
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
					echo json_encode(array("status" => "success","success_string" => "New API key generated."));
				}
				else
					echo json_encode(array("status" => "error","error_string" => "Insertion of the newly generated key failed."));
				
			}
		}
		else
			echo json_encode(array("status"=>"error","error_string"=>"Required parameters not provided."));
	}
} catch (Exception $e) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>