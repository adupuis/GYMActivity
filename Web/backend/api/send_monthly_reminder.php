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

session_start();
$required_group_rights = 2;
$auth_granted = false;
$authorized_auth_method = "api_key";

header('Content-type:text/javascript;charset=UTF-8');

include_once 'Mail.php';
include_once 'ajax_authent_checking.php';
include_once 'ajax_toolbox.php';

try {
	if($auth_granted){
		$tmp_profile = new GenyProfile();
		$web_config = new GenyWebConfig();

		$month = date('m', time());
		$year=date('Y', time());

		$start_date = GenyTools::getCurrentMonthFirstDayDate();
		$end_date = GenyTools::getCurrentMonthLastDayDate();

		$geny_ar = new GenyActivityReport();
		$worked_days = GenyTools::getWorkedDaysList(strtotime($start_date),strtotime($end_date));
		$estimated_load=0;
		foreach( $worked_days as $day ){
			$estimated_load += 8;
		}

		$user_completion_data = array();
		
		$from = "CRA Admin <admin@genymobile.com>";
		$subject = "CRA du $month/$year incomplets.";
		

		$host = "ssl://smtp.gmail.com";
		$port = "465";
		$username = $web_config->gmail_username;
		$password = $web_config->gmail_password;
		
		$json_messages = array();

		foreach( $tmp_profile->getProfilesListWithRestrictions( array("rights_group_id=3","profile_is_active=true") ) as $p ){
			$user_load=0;
			foreach( $worked_days as $day ){
				$user_load += $geny_ar->getDayLoad($p->id,$day);
			}
			if( $user_load < $estimated_load ){
				$to = "<".$p->email.">";
				$hours = $estimated_load-$user_load ;
				$hours_string = "";
				if( $hours <= 10 )
					$hours_string = "<font style='color:darkblue;'>$hours</font>";
				else if( $hours <= 30 )
					$hours_string = "<font style='color:orange;'>$hours</font>";
				else
					$hours_string = "<font style='color:red;'>$hours</font>";
				$body = "<html><body><p>Bonjour ".$p->firstname.",<br/><br/>A ce jour vos CRA du <strong>$month/$year</strong> sont incomplets. Merci de saisir dès aujourd'hui l'ensemble des ".$hours_string." heures manquantes (soit ".($hours/8)." jours).<br/>Vous pouvez saisir les CRA manquants sur <a href='http://cra.genymobile.com'>http://cra.genymobile.com</a>.<br/><br/>---<br/><font color=\"#7f7f7f\" face=\"&#39;BN Year 2000&#39;\" size=\"6\"><span style=\"line-height:36px\"><img src=\"https://lh5.googleusercontent.com/-D4J1fAOyk8A/TgbxwOsSIjI/AAAAAAAAABE/zWEpLl0Q3ZM/s144/genymobile-24.png\"><br></span></font><br/><font color=\"#666666\">Gestion de CRA</font></p><br/></body></html>";
				$headers = array ('From' => $from,
				'To' => $to,
				'Subject' => $subject,
				'Content-Type' => 'text/html; charset=UTF-8');
				$smtp = Mail::factory('smtp',
				array ('host' => $host,
				'port' => $port,
				'auth' => true,
				'username' => $username,
				'password' => $password));

				$mail = $smtp->send($to, $headers, $body);

				if (PEAR::isError($mail)) {
					$json_messages[] = array("status" => "error", "status_message" => $mail->getMessage() );
				} else {
					$json_messages[] = array("status" => "success", "status_message" => "Message successfully sent." );
				}
			}
			
		}
		echo json_encode($json_messages);
	}
} catch (Exception $e) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>