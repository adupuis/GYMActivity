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

include_once 'GenyWebConfig.php';
include_once 'GenyProfile.php';
include_once 'GenyDatabaseTools.php';

class GenyCareerEvent extends GenyDatabaseTools {
	public $id = -1;
	public $profile_id = GENYMOBILE_FALSE;
	public $timestamp = -1;
	public $type = "neutral";
	public $title = "";
	public $text = "";
	public $attachement = "";
	public $manager_agreement = false;
	public $employee_agreement = false;
	public function __construct($id = -1){
		parent::__construct("CareerEvents",
				    "career_event_id");
		$this->id = -1;
		$this->profile_id = GENYMOBILE_FALSE;
		$this->timestamp = -1;
		$this->type = "neutral";
		$this->title = "";
		$this->text = "";
		$this->attachement = "";
		$this->manager_agreement = false;
		$this->employee_agreement = false;
		if($id > -1)
			$this->loadCareerEventById($id);
	}
	public function insertNewCareerEvent($profile_id,$type,$title,$text,$attachement = "",$manager_agreement,$employee_agreement){
		if( ! is_numeric($profile_id) )
			return GENYMOBILE_FALSE;
		
		if( $type != "neutral" && $type != "positive" && $type != "negative" )
			return GENYMOBILE_FALSE;
		
		if( $manager_agreement != 'true' && $manager_agreement != 'false' && $manager_agreement != 0 && $manager_agreement != 1)
			return GENYMOBILE_FALSE;
		
		if( $employee_agreement != 'true' && $employee_agreement != 'false' && $employee_agreement != 0 && $employee_agreement != 1)
			return GENYMOBILE_FALSE;
		
		$query = "INSERT INTO CareerEvents VALUES(0,$profile_id,".time().",'".mysql_real_escape_string($type)."','".mysql_real_escape_string($title)."','".mysql_real_escape_string($text)."','".mysql_real_escape_string($attachement)."',$manager_agreement,$employee_agreement)";
		
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyCareerEvent MySQL query : $query",0);
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return -1;
		}
	}
	public function getCareerEventListWithRestrictions($restrictions,$restriction_type = "AND"){
		// $restrictions is in the form of array("profile_id=1","profile_status_id=2")
		$last_index = count($restrictions)-1;
		$query = "SELECT career_event_id,profile_id,career_event_timestamp,career_event_type,career_event_title,career_event_text,career_event_attachement,career_event_manager_agreement,career_event_employee_agreement FROM CareerEvents";
		if(count($restrictions) > 0){
			$query .= " WHERE ";
			$op = mysql_real_escape_string($restriction_type);
			foreach($restrictions as $key => $value) {
				$query .= $value;
				if($key != $last_index){
					$query .= " $op ";
				}
			}
		}
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyCareerEvent MySQL query : $query",0);
		$result = mysql_query($query, $this->handle);
		$gce_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_gce = new GenyCareerEvent();
				$tmp_gce->id = $row[0];
				$tmp_gce->profile_id = $row[1];
				$tmp_gce->timestamp = $row[2];
				$tmp_gce->type = $row[3];
				$tmp_gce->title = $row[4];
				$tmp_gce->text = $row[5];
				$tmp_gce->attachement = $row[6];
				$tmp_gce->manager_agreement = $row[7];
				$tmp_gce->employee_agreement = $row[8];
				$gce_list[] = $tmp_gce;
			}
		}
// 		mysql_close();
		return $gce_list;
	}
	public function searchCareerEvent($term){
		$q = mysql_real_escape_string($term);
		return $this->getCareerEventListWithRestrictions( array("career_event_title LIKE '%$q%'","career_event_text LIKE '%$q%'"), "OR" );
	}
	public function getAllCareerEvent(){
		return $this->getCareerEventListWithRestrictions( array() );
	}
	public function getCareerEventListByProfileId($id){
		if( ! is_numeric($id) )
			return GENYMOBILE_FALSE;
		
		return $this->getCareerEventListWithRestrictions(array("profile_id=$id"));
	}
	public function getAllUnagreedCareerEvent(){
		return $this->getCareerEventListWithRestrictions(array("career_event_manager_agreement=false","career_event_employee_agreement=false"),"AND");
	}
	public function getCareerEventListByType($type){
		if( $type != "positive" && $type != "negative" && $type != "neutral" )
			return GENYMOBILE_FALSE;
		
		return $this->getCareerEventListWithRestrictions(array("career_event_type='$type'"));
	}
	public function loadCareerEventById($id){
		if( ! is_numeric($id) )
			return GENYMOBILE_FALSE;
		$career_events = $this->getCareerEventListWithRestrictions(array("career_event_id=$id"));
		$career_event = $career_events[0];
		if(isset($career_event) && $career_event->id > -1){
			$this->id = $career_event->id;
			$this->profile_id = $career_event->profile_id;
			$this->timestamp = $career_event->timestamp;
			$this->type = $career_event->type;
			$this->title = $career_event->title;
			$this->text = $career_event->text;
			$this->attachement = $career_event->attachement;
			$this->manager_agreement = $career_event->manager_agreement;
			$this->employee_agreement = $career_event->employee_agreement;
		}
	}
	public function loadCareerEventByProfileId($id){
		if( ! is_numeric($id) )
			return GENYMOBILE_FALSE;
		$career_events = $this->getCareerEventListWithRestrictions(array("profile_id=$id"));
		$career_event = $career_events[0];
		if(isset($career_event) && $career_event->id > -1){
			$this->id = $career_event->id;
			$this->profile_id = $career_event->profile_id;
			$this->timestamp = $career_event->timestamp;
			$this->type = $career_event->type;
			$this->title = $career_event->title;
			$this->text = $career_event->text;
			$this->attachement = $career_event->attachement;
			$this->manager_agreement = $career_event->manager_agreement;
			$this->employee_agreement = $career_event->employee_agreement;
		}
	}
	
}
?>