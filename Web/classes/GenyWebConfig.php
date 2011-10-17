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

define("GENYMOBILE_TRUE",1);
define("GENYMOBILE_FALSE",-1);
define("GENYMOBILE_ERROR",-2);

class GenyWebConfig {
	public function __construct(){
		$this->db_host = "localhost";
		$this->db_user = "genymobile";
		$this->db_password = "toto";
		$this->theme = "default";
		$this->debug = false;
		$this->db_name = "GYMActivity";
		$this->company_name = "GENYMOBILE";
		$this->version = "0.4.8";
		$this->company_corner_logo = "logo_genymobile_writting_small.jpg";
		$this->company_index_logo = "logo_genymobile.jpg";
		$this->gmail_username = "yourusername";
		$this->gmail_password = "yourpassword";
	}
}
?>
