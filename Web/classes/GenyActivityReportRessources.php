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


include_once 'GenyWebConfig.php';
include_once 'GenyDatabaseTools.php';

class GenyActivityReportRessources extends GenyDatabaseTools {
	public $project_name = '';
	public $project_type = -1;
	public $client_name = '';
	public $activity_load = -1;
	public $project_id = -1;
	
	public function __construct(){
		parent::__construct("ActivityReportsRessources",  "activity_report_id");
		
		$this->project_name = '';
		$this->project_type = -1;
		$this->client_name = '';
		$this->activity_load = -1;
		$this->project_id = -1;
	}
	
	public function getActivityReportsRessourcesWithRestrictions($restrictions){
		$last_index = count($restrictions)-1;
		$query = "SELECT * FROM ActivityReportRessources";
		if(count($restrictions) > 0){
			$query .= " WHERE ";
			foreach($restrictions as $key => $value) {
				$query .= $value;
				if($key != $last_index){
					$query .= " AND ";
				}
			}
		}
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyActivityReportRessources MySQL query : $query",0);
		$result = mysql_query($query, $this->handle);
		$obj_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_obj = new GenyActivityReportRessources();
				$tmp_obj->project_id = $row[0];
				$tmp_obj->project_name = $row[1];
				$tmp_obj->project_type = $row[2];
				$tmp_obj->client_name = $row[3];
				$tmp_obj->activity_load = $row[4];
				$obj_list[] = $tmp_obj;
			}
		}
		return $obj_list;
	}
	public function getAllActivityReportsRessources(){
		return $this->getActivityReportsRessourcesWithRestrictions( array() );
	}
	public function getActivityReportsRessourcesFromDateAndProfileId($date, $profile_id){
		return $this->getActivityReportsRessourcesWithRestrictions( array("profile_id = $profile_id", "activity_date = \"$date\"") );
	}
	public function commitUpdates(){
		return GENY_FALSE;
	}
}
?>
