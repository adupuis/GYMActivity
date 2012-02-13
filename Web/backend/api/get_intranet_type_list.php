<?php

//  Copyright (C) 2011 by GENYMOBILE

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
	$intranet_types = array();
	if( $auth_granted ) {
		$tmp_intranet_type = new GenyIntranetType();
		$results = array();
		$intranet_category_id = getParam( "intranet_category_id", -1 );
		
		if( $intranet_category_id > 0 ) {
			$results = $tmp_intranet_type->getIntranetTypesByCategoryId( $intranet_category_id );
		}
		else {
			$results = $tmp_intranet_type->getAllIntranetTypes();
		}

		foreach( $results as $type ){
			$tmp = array();
			foreach( get_object_vars( $type ) as $field => $value ) {
				$tmp[$field] = $type->$field ;
			}
			$intranet_types[] = $tmp;
		}
		$data = json_encode( $intranet_types );
		echo $data;
	}
} catch ( Exception $e ) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>
