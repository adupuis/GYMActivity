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

include_once '../../rights_groups.php';

$required_group_rights = array(ADM, TM, USR, TL, REP, EXT, GL);

include_once 'Mail.php';
include_once 'ajax_toolbox.php';

function sendMail( $subject, $to, $body ) {
	try {
		$tmp_profile = new GenyProfile();
		$web_config = new GenyWebConfig();
		
		$from = "bot@genymobile.com";
		if( !isset( $subject ) ) {
			$subject = "empty subject";
		}
		else {
			$subject = html_entity_decode( $subject );
		}
		$to_raw = $to;

		$host = 'ssl://smtp.gmail.com';
		$port = "465";
		$username = $web_config->gmail_username;
		$password = $web_config->gmail_password;
		
		$json_messages = array();
		
		if( !isset( $body ) ) {
			$body = "<html><body>".html_entity_decode( "no body defined." )."</body></html>";
		}
		else {
			$body = "<html><body>".html_entity_decode( $body )."</body></html>";
		}
		foreach( explode(",",$to_raw) as $to ){
			$headers = array ('From' => $from,
				'To' => $to,
				'Subject' => $subject,
				'Content-Type' => 'text/html; charset=UTF-8');
				$smtp = Mail::factory('smtp',
				array ('host' => $host,
				'port' => $port,
				'auth' => true,
				'username' => $username,
				'password' => $password)
			);

			$mail = $smtp->send($to, $headers, $body);

			if (PEAR::isError($mail)) {
				$json_messages[] = array("status" => "error", "status_message" => "Error while sending message to $to: ".$mail->getMessage() );
			}
			else {
				$json_messages[] = array("status" => "success", "status_message" => "Message successfully sent to $to." );
			}
		}

// 		echo jsons_encode($json_messages);
	} catch (Exception $e) {
		echo "Exception: ".$e->getMessage(), "\n";
	}
}

?>