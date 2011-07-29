<?php
include_once 'GenyWebConfig.php';
class GenyProfile {
	public $profile_id = -1;
	public $profile_login = '';
	public $profile_firstname = '';
	public $profile_lastname = '';
	public $profile_email = '';
	public $profile_is_active = false;
	public $profile_needs_password_reset = false;
	public $rights_group_id = -1;
	private $updates = array();
	public function __construct($id = -1){
		$this->config = new GenyWebConfig();
		$this->handle = mysql_connect($this->config->db_host,$this->config->db_user,$this->config->db_password);
		mysql_select_db($this->config->db_name);
		mysql_query("SET NAMES 'utf8'");
		$this->id = -1;
		$this->login = '';
		$this->firstname = '';
		$this->lastname = '';
		$this->email = '';
		$this->needs_password_reset = false;
		$this->rights_group_id = -1;
		$this->is_active = false;
		if($id > -1)
			$this->loadProfileById($id);
	}
	public function insertNewProfile($profile_id,$profile_login,$profile_firstname,$profile_lastname,$profile_password,$profile_email,$profile_is_active,$profile_needs_password_reset,$rights_group_id){
		$query = "INSERT INTO Profiles VALUES($profile_id,'".mysql_real_escape_string($profile_login)."','".mysql_real_escape_string($profile_firstname)."','".mysql_real_escape_string($profile_lastname)."','".md5(mysql_real_escape_string($profile_password))."','".mysql_real_escape_string($profile_email)."',".mysql_real_escape_string($profile_is_active).",".mysql_real_escape_string($profile_needs_password_reset).",".mysql_real_escape_string($rights_group_id).")";
		if( $this->config->debug )
			echo "<!-- DEBUG: GenyProfile MySQL query : $query -->\n";
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
		$query = "SELECT profile_id,profile_login,profile_firstname,profile_lastname,profile_email,profile_is_active,profile_needs_password_reset,rights_group_id FROM Profiles";
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
			echo "<!-- DEBUG: GenyProfile MySQL query : $query -->\n";
		$result = mysql_query($query, $this->handle);
		$profile_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_profile = new GenyProfile();
				$tmp_profile->id = $row[0];
				$tmp_profile->login = $row[1];
				$tmp_profile->firstname = $row[2];
				$tmp_profile->lastname = $row[3];
				$tmp_profile->email = $row[4];
				$tmp_profile->is_active = $row[5];
				$tmp_profile->needs_password_reset = $row[6];
				$tmp_profile->rights_group_id = $row[7];
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
	public function loadProfileByUsername($username){
		$profiles = $this->getProfilesListWithRestrictions(array("md5(profile_login)='".mysql_real_escape_string($username)."'"));
		$profile = $profiles[0];
		if(isset($profile) && $profile->id > -1){
			$this->id = $profile->id;
			$this->login = $profile->login;
			$this->firstname = $profile->firstname;
			$this->lastname = $profile->lastname;
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
			$this->email = $profile->email;
			$this->is_active = $profile->is_active;
			$this->needs_password_reset = $profile->needs_password_reset;
			$this->rights_group_id = $profile->rights_group_id;
		}
	}
	public function updateString($key,$value){
		$this->updates[] = "$key='".mysql_real_escape_string($value)."'";
	}
	public function updateInt($key,$value){
		$this->updates[] = "$key=".mysql_real_escape_string($value)."";
	}
	public function updateBool($key,$value){
		$this->updates[] = "$key=".mysql_real_escape_string($value)."";
	}
	public function commitUpdates(){
		$query = "UPDATE Profiles SET ";
		foreach($this->updates as $up) {
			$query .= "$up,";
		}
		$query = rtrim($query, ",");
		$query .= " WHERE profile_id=".$this->id;
		if( $this->config->debug )
			echo "<!-- DEBUG: GenyProfile MySQL query : $query -->\n";
		return mysql_query($query, $this->handle);
	}
}
?>