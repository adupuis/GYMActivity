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

include '../../rights_groups.php';

session_start();
$required_group_rights = array(Admins, TopManagers, Users, TechnologyLeaders, Reporters, Externes, GroupLeaders);
$auth_granted = false;

header('Content-Type: application/json;charset=UTF-8');

include_once 'ajax_authent_checking.php';
include_once 'ajax_toolbox.php';

try {
	$clients = array();
	if($auth_granted){
		$tmp_client = new GenyClient();
		$results = array();
		$term = getParam("term");
		
		if( $term != "" )
			$results = $tmp_client->searchClients($term);
		else
			$results = $tmp_client->getAllClients();
		foreach( $results as $c ){
			$clients[] = array( "value" => $c->id, "label" => $c->name );
		}
		$data = json_encode($clients);
		echo $data;
	}
} catch (Exception $e) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>