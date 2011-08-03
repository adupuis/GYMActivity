<?php
class GenyWebConfig {
	public function __construct(){
		$this->db_host = "localhost";
		$this->db_user = "genymobile";
		$this->db_password = "toto";
		$this->theme = "default";
		$this->debug = false;
		$this->db_name = "GYMActivity";
		$this->company_name = "GENYMOBILE";
		$this->version = "0.4.4";
	}
}
?>