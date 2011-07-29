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
		$tmp_profile = new GenyProfile();
		$results = array();
		$term = getParam("term");
		
		if( $term != "" )
			$results = $tmp_profile->searchProfiles($term);
		else
			$results = $tmp_profile->getAllProfiles();
		foreach( $results as $p ){
			if( $p->firstname == "" && $p->lastname == "")
				$p->firstname = $p->login;
			$profiles[] = array( "value" => $p->login, "label" => $p->firstname." ".$p->lastname );
		}
		$data = json_encode($profiles);
		echo $data;
	}
} catch (Exception $e) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>