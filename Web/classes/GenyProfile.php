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

class GenyProfile extends GenyDatabaseTools {
	public $id = -1;
	public $login = '';
	public $firstname = '';
	public $lastname = '';
	public $password = '';
	public $email = '';
	public $is_active = false;
	public $needs_password_reset = false;
	public $rights_group_id = -1;
	private $updates = array();
	public function __construct($id = -1){
		parent::__construct("Profiles",  "profile_id");
		$this->id = -1;
		$this->login = '';
		$this->firstname = '';
		$this->lastname = '';
		$this->password = '';
		$this->email = '';
		$this->is_active = false;
		$this->needs_password_reset = false;
		$this->rights_group_id = -1;
		if($id > -1)
			$this->loadProfileById($id);
	}
	public function insertNewProfile($profile_id,$profile_login,$profile_firstname,$profile_lastname,$profile_password,$profile_email,$profile_is_active,$profile_needs_password_reset,$rights_group_id){
		$query = "INSERT INTO Profiles VALUES($profile_id,'".mysql_real_escape_string($profile_login)."','".mysql_real_escape_string($profile_firstname)."','".mysql_real_escape_string($profile_lastname)."','".md5(mysql_real_escape_string($profile_password))."','".mysql_real_escape_string($profile_email)."',".mysql_real_escape_string($profile_is_active).",".mysql_real_escape_string($profile_needs_password_reset).",".mysql_real_escape_string($rights_group_id).")";
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyProfile MySQL query : $query",0);
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return -1;
		}
	}
	public function getProfilesListWithRestrictions($restrictions,$restriction_type = "AND"){
		// $restrictions is in the form of array("profile_id=1","profile_status_id=2")
		$last_index = count($restrictions)-1;
		$query = "SELECT profile_id,profile_login,profile_firstname,profile_lastname,profile_password,profile_email,profile_is_active,profile_needs_password_reset,rights_group_id FROM Profiles";
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
			error_log("[GYMActivity::DEBUG] GenyProfile MySQL query : $query",0);
		$result = mysql_query($query, $this->handle);
		$profile_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_profile = new GenyProfile();
				$tmp_profile->id = $row[0];
				$tmp_profile->login = $row[1];
				$tmp_profile->firstname = $row[2];
				$tmp_profile->lastname = $row[3];
				$tmp_profile->password = $row[4];
				$tmp_profile->email = $row[5];
				$tmp_profile->is_active = $row[6];
				$tmp_profile->needs_password_reset = $row[7];
				$tmp_profile->rights_group_id = $row[8];
				$profile_list[] = $tmp_profile;
			}
		}
// 		mysql_close();
		return $profile_list;
	}
	public function searchProfiles($term){
		$q = mysql_real_escape_string($term);
		return $this->getProfilesListWithRestrictions( array("profile_login LIKE '%$q%'","profile_firstname LIKE '%$q%'","profile_lastname LIKE '%$q%'"), "OR" );
	}
	public function getAllProfiles(){
		return $this->getProfilesListWithRestrictions( array() );
	}
	public function getProfileByLogin($login){
		return $this->getProfilesListWithRestrictions( array("profile_login='".mysql_real_escape_string($login)."'") );
	}
	public function getProfileByActivation($is_active){
		return $this->getProfilesListWithRestrictions( array("profile_is_active=".mysql_real_escape_string($is_active)) );
	}
	public function getProfileByRightsGroup($group){
		return $this->getProfilesListWithRestrictions( array("rights_group_id=".mysql_real_escape_string($group)) );
	}
	public function getAllProfilesByProjectId($proj_id) {
		$query = "SELECT Profiles.profile_id, profile_login, profile_firstname, profile_lastname, profile_password, profile_email, profile_is_active, profile_needs_password_reset, rights_group_id FROM Profiles, Assignements where Profiles.profile_id = Assignements.profile_id and Assignements.project_id=".$proj_id;
		$result = mysql_query($query, $this->handle);
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyProfile MySQL query : $query",0);

// 		var_dump($result);
		$profile_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_profile = new GenyProfile();
				$tmp_profile->id = $row[0];
				$tmp_profile->login = $row[1];
				$tmp_profile->firstname = $row[2];
				$tmp_profile->lastname = $row[3];
				$tmp_profile->password = $row[4];
				$tmp_profile->email = $row[5];
				$tmp_profile->is_active = $row[6];
				$tmp_profile->needs_password_reset = $row[7];
				$tmp_profile->rights_group_id = $row[8];
				$profile_list[] = $tmp_profile;
			}
		}
		return $profile_list;
	}
	public function loadProfileByUsername($username){
		$profiles = $this->getProfilesListWithRestrictions(array("md5(profile_login)='".mysql_real_escape_string($username)."'"));
		$profile = $profiles[0];
		if(isset($profile) && $profile->id > -1){
			$this->id = $profile->id;
			$this->login = $profile->login;
			$this->firstname = $profile->firstname;
			$this->lastname = $profile->lastname;
			$this->password = $profile->password;
			$this->email = $profile->email;
			$this->is_active = $profile->is_active;
			$this->needs_password_reset = $profile->needs_password_reset;
			$this->rights_group_id = $profile->rights_group_id;
		}
	}
	public function loadProfileByLogin($login){
		$profiles = $this->getProfilesListWithRestrictions(array("profile_login='".mysql_real_escape_string($login)."'"));
		$profile = $profiles[0];
		if(isset($profile) && $profile->id > -1){
			$this->id = $profile->id;
			$this->login = $profile->login;
			$this->firstname = $profile->firstname;
			$this->lastname = $profile->lastname;
			$this->password = $profile->password;
			$this->email = $profile->email;
			$this->is_active = $profile->is_active;
			$this->needs_password_reset = $profile->needs_password_reset;
			$this->rights_group_id = $profile->rights_group_id;
		}
	}
	public function loadProfileById($id){
		$profiles = $this->getProfilesListWithRestrictions(array("profile_id=".mysql_real_escape_string($id)));
		$profile = $profiles[0];
		if(isset($profile) && $profile->id > -1){
			$this->id = $profile->id;
			$this->login = $profile->login;
			$this->firstname = $profile->firstname;
			$this->lastname = $profile->lastname;
			$this->password = $profile->password;
			$this->email = $profile->email;
			$this->is_active = $profile->is_active;
			$this->needs_password_reset = $profile->needs_password_reset;
			$this->rights_group_id = $profile->rights_group_id;
		}
	}
}
?>