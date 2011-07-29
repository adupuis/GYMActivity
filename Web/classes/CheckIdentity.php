<?php
include_once 'GenyWebConfig.php';
class CheckIdentity {
	public function __construct(){
		$this->config = new GenyWebConfig();
		$this->handle = mysql_connect($this->config->db_host,$this->config->db_user,$this->config->db_password);
		mysql_select_db($this->config->db_name);
	}
	public function isAllowed($username,$needed_rights){
		if(!isset($_SESSION['LOGGEDIN']))
			return false;
		$query = "SELECT profile_login,rights_group_id,profile_is_active FROM Profiles WHERE md5(profile_login)='$username'";
		$result = mysql_query($query, $this->handle);
		if (mysql_num_rows($result)<=0)
			return false;
		$sqldata = mysql_fetch_assoc($result);
		if($sqldata['rights_group_id'] > $needed_rights)
			return false;
		if(!$sqldata['profile_is_active'])
			return false;
		// If we arrive here the user exists in database, is loggedin and have a group rights id compatible with required rights
		return true;
	}
}
?>