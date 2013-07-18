<?php

//  Copyright (C) 2011 by GENYMOBILE & Quentin Désert
//  qdesert@genymobile.com
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

session_start();
$required_group_rights = array(ADM, TM, USR, TL, REP, EXT, GL);
$auth_granted = false;
$authorized_auth_method = "all";

header('Content-Type: application/json;charset=UTF-8');

// include_once 'Mail.php';
include_once 'ajax_authent_checking.php';
include_once 'ajax_toolbox.php';

try {
	if( $auth_granted ) {

		$idea_message_idea_id = getParam( "idea_id" );
		$idea_message_idea = new GenyIdea();
		$idea_message_idea->loadIdeaById( $idea_message_idea_id );
		$subject = '[GYMActivity] Un commentaire a &eacute;t&eacute; ajout&eacute; &agrave; votre id&eacute;e : '.$idea_message_idea->title;

		$idea_message_profile_id = getParam( "profile_id", $profile->id );
		$idea_message_content = getParam( "idea_message_content" );
		$message_submitter_profile = new GenyProfile();
		$message_submitter_profile->loadProfileById( $idea_message_profile_id );
		$body = "Commentaire de ".$message_submitter_profile->firstname." ".$message_submitter_profile->lastname." [".$message_submitter_profile->email."] :<br/><br/>".$idea_message_content."<br/><br/>Vous pouvez consulter cette idée et répondre à ce message en allant sur <a href='http://cra.genymobile.com'>http://cra.genymobile.com</a> dans la section Boîte à Idées.<br/><br/>---<br/><font color=\"#7f7f7f\" face=\"&#39;BN Year 2000&#39;\" size=\"6\"><span style=\"line-height:36px\"><img src=\"https://lh5.googleusercontent.com/-D4J1fAOyk8A/TgbxwOsSIjI/AAAAAAAAABE/zWEpLl0Q3ZM/s144/genymobile-24.png\"><br></span></font><br/><font color=\"#666666\">Gestion de CRA</font></p><br/>";

		$idea_submitter_profile = new GenyProfile();
		$idea_submitter_profile->loadProfileById( $idea_message_idea->submitter );
		$to = $idea_submitter_profile->email;

		error_log("[GYMActivity::DEBUG] mail will be sent to: $to",0);

		$from = "bot@genymobile.com";
		$subject = html_entity_decode( $subject );

		error_log("[GYMActivity::DEBUG] subject is: $subject",0);
	}
} catch (Exception $e) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>

<script type="text/javascript">

	console.log( "javascript begin." );

</script>
