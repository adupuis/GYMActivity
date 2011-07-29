<?php
include_once 'GenyWebConfig.php';
class GYMLogger {
	public function __construct(){
		$this->config = new GenyWebConfig();
		$this->handle = mysql_connect($this->config->db_host,$this->config->db_user,$this->config->db_password);
		mysql_select_db($this->db_name);
	}
	public function log_access($access_successful){
		// Get user id
		$query = "SELECT profile_id FROM Profiles WHERE md5(profile_login)='".$_SESSION['USERID']."'";
		$result = mysql_query($query, $this->handle);
		$sqldata = mysql_fetch_assoc($result);
		if (mysql_num_rows($result)!=0){
			$query = "INSERT INTO AccessLogs VALUES(NULL,'".date("c")."','"$sqldata['profile_id']."','".$_SERVER['REMOTE_ADDR']."',$access_successful)";
		}
	}
}
?>