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
class GYMLogger {
	public function __construct(){
		$this->config = new GenyWebConfig();
		$this->handle = mysql_connect($this->config->db_host,$this->config->db_user,$this->config->db_password);
		mysql_select_db($this->config->db_name);
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