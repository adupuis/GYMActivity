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
$required_group_rights = 1;
$auth_granted = false;

header('Content-Type: application/json;charset=UTF-8');

include_once 'ajax_authent_checking.php';
include_once 'ajax_toolbox.php';

try {
	if($auth_granted){
		$tmp_prop_option = new GenyPropertyOption();
		$content = getParam("content",-1);
		$id = getParam("id");
		$prop_id = getParam("prop_id");
		$action = getParam("action","none");
		
		if( $action == "edit"){
			if($id > 0){
				$tmp_prop_option->loadPropertyOptionById($id);
				if( $content != "" ){
					$tmp_prop_option->updateString("property_option_content",$content);
					if($tmp_prop_option->commitUpdates()) echo "1";
					else echo "-1";
				}
				else echo "-1";
			}
			else echo "-1";
		}
		else if( $action == "add"){
			if($prop_id > 0){
				if($content != "")
				{
					if($tmp_prop_option->insertNewPropertyOption($content, $prop_id))
					{
						$tmp_prop_option = $tmp_prop_option->searchPropertyOptions($content);
						if(count($tmp_prop_option) >= 1) echo $tmp_prop_option[0]->id;
						else echo "-1";
					}
					else echo "-1";
				}
				else echo "-1";
			}
			else echo "-1";
		}
		else if( $action == "delete" ){
			if($id > 0){
				$tmp_prop_option->loadPropertyOptionById($id);
				if( $tmp_prop_option->deletePropertyOption() > 0 ) echo "1";
			}
			else echo "-1";
		}
		else echo "-1";
	}
} catch (Exception $e) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>