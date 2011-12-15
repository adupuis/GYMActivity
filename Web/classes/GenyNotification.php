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
include_once 'GenyDatabaseTools.php';

class GenyNotification extends GenyDatabaseTools {
	public function __construct($id = -1){
		parent::__construct("Notifications",  "notification_id", $id);
		$this->profile_id = -1;
		$this->text = '';
		$this->is_unread = false;
		$this->type = 'info';
		if($id > -1)
			$this->loadNotificationById($id);
	}
	public function insertNewNotification($profile_id,$text,$type='info'){
		if( ! is_numeric($profile_id) )
			return -1;
		$query = "INSERT INTO Notifications VALUES(0,$profile_id,'".mysql_real_escape_string($text)."',true,'".mysql_real_escape_string($type)."')";
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyNotification MySQL query : $query",0);
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return -1;
		}
	}
	public function insertNewGroupNotification($rights_group_id,$text,$type="info"){
		$tmp_profile = new GenyProfile();
		foreach( $tmp_profile->getProfileByRightsGroup($rights_group_id) as $p ){
			$this->insertNewNotification( $p->id,$text,$type );
		}
	}
	public function getNotificationsListWithRestrictions($restrictions){
		// $restrictions is in the form of array("project_id=1","project_status_id=2")
		$last_index = count($restrictions)-1;
		$query = "SELECT notification_id,profile_id,notification_text,notification_is_unread,notification_type FROM Notifications";
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
			error_log("[GYMActivity::DEBUG] GenyNotification MySQL query : $query",0);
		$result = mysql_query($query, $this->handle);
		$notification_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_object = new GenyNotification();
				$tmp_object->id = $row[0];
				$tmp_object->profile_id = $row[1];
				$tmp_object->text = $row[2];
				$tmp_object->is_unread = $row[3];
				$tmp_object->type = $row[4];
				$notification_list[] = $tmp_object;
			}
		}
// 		mysql_close();
		return $notification_list;
	}
	public function getAllNotifications(){
		return $this->getNotificationsListWithRestrictions( array() );
	}
	public function getNotificationsByProfileId($id){
		if( is_numeric($id) )
			return $this->getNotificationsListWithRestrictions( array("profile_id=$id") );
		else
			return array();
	}
	public function getUnreadNotificationCountByProfileId($id){
		if( ! is_numeric($id) )
			return -1;
		
		$query = "SELECT COUNT(notification_id) as notif_count FROM Notifications WHERE profile_id=$id AND notification_is_unread=true";
		$result = mysql_query($query, $this->handle);
		if (mysql_num_rows($result) == 1){
			$row = mysql_fetch_row($result);
			return $row[0];
		}
		else
			return -2;
	}
	public function loadNotificationById($id){
		$objects = $this->getNotificationsListWithRestrictions(array("notification_id=".mysql_real_escape_string($id)));
		$object = $objects[0];
		if(isset($object) && $object->id > -1){
			$this->id = $object->id;
			$this->profile_id = $object->profile_id;
			$this->text = $object->text;
			$this->is_unread = $object->is_unread;
		}
	}
}
?>