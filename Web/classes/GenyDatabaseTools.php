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

class GenyDatabaseTools {
	// keep it public, it can be R/W from anywhere
	public    $id;

	protected $config;
	protected $handle;

	private   $tableName;
        private   $primKeyName;
	private   $updates;

	public function __construct($tableName, $primKeyName, $id = -1) {
		$this->updates = array();
		$this->id = $id;
		$this->tableName = $tableName;
		$this->primKeyName = $primKeyName;
		$this->config = new GenyWebConfig();
		$this->handle = mysql_connect($this->config->db_host,
					      $this->config->db_user,
					      $this->config->db_password);
		mysql_select_db($this->config->db_name);
		mysql_query("SET NAMES 'utf8'");
	}

	public function updateString($key,$value){
		if( is_string($value) ) {
			$this->updates[] = "$key='".mysql_real_escape_string($value)."'";
		} else if( $this->config->debug ) {
			error_log("[GYMActivity::DEBUG] $value is not a string");
		}
	}
	public function updateInt($key,$value){
		if( is_numeric($value) ) {
			$this->updates[] = "$key=".mysql_real_escape_string($value)."";
		}else if( $this->config->debug ) {
			error_log("[GYMActivity::DEBUG] $value is not numeric");
		}
	}
	public function updateBool($key,$value){
		if (is_bool($value)    ||
		    $value == "true"   ||
		    $value == "false") {
			$this->updates[] = "$key=".mysql_real_escape_string($value)."";
		}else if( $this->config->debug ) {
			error_log("[GYMActivity::DEBUG] $value is not boolean");
		}
	}

	public function commitUpdates(){
		$query = "UPDATE ".$this->tableName." SET ";
		foreach($this->updates as $up) {
			$query .= "$up,";
		}
		$query = rtrim($query, ",");
		$query .= " WHERE ".$this->primKeyName."=".$this->id;
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyActivity MySQL query : $query",0);
		return mysql_query($query, $this->handle);
	}
	public function setDebug($bool=false){
		if( is_bool($bool) )
			$this->config->debug = $bool;
	}
}

?>