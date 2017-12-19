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

header('Content-Type: application/json;charset=UTF-8');

include_once 'ajax_authent_checking.php';
include_once 'ajax_toolbox.php';

try {
    // TODO : TESTER!!!!!
	$profiles = array();
	if($auth_granted){
		$tmp_profile = new GenyProfile();
		$results = array();
		$profile_id = getParam("id",-1);
		
		if($profile_id == -1){
            echo json_encode( array( "error" => "Fatal error: Profile ID is required." ) );
			exit;
		}
		else{
            $p = new GenyProfile($profile_id);
            if($p->id != $profile_id){
                echo json_encode( array( "error" => "Fatal error: Profile ID is invalid." ) );
                exit;
            }
            else{
                $pmd = new GenyProfileManagementData();
                $pmd->loadProfileManagementDataByProfileId( $p->id );
                if($pmd->id <= 0){
                    echo json_encode( array( "error" => "Fatal error: No profile management data for this profile." ) );
                    exit;
                }
                else{
                    $tmp_country = new GenyCountry($pmd->country_id);
                    echo json_encode( array( "profile_id" => $p->id, "country_id" => $pmd->country_id , "country_name" => $tmp_country->name) );
                }
            }
		}
	}
} catch (Exception $e) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>
