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
date_default_timezone_set('Europe/Paris');

function __autoload($class_name) {
	include 'classes/'.$class_name . '.php';
}

function loadClass($class_name) {
	include 'classes/'.$class_name . '.php';
}

try {
	$access_loger = new GenyAccessLog();
	$checkId_obj = new CheckIdentity();
	$web_config = new GenyWebConfig();
	if(isset($_SESSION['LOGGEDIN']) &&  $_SESSION['LOGGEDIN'] == 1){
		if( $checkId_obj->isAllowed($_SESSION['USERID'],$required_group_rights) ){
			if(isset($_SESSION['THEME']))
				$web_config->theme = $_SESSION['THEME'];
		}
		else{
			$tmp_profile = new GenyProfile();
			$tmp_profile->loadProfileByUsername($_SESSION['USERID']);
			$access_loger->insertNewAccessLog($tmp_profile->id,$_SERVER['REMOTE_ADDR'],'false',"check_login.php",UNAUTHORIZED_ACCESS,",referer=".$_SERVER['HTTP_REFERER'].",user_agent=".$_SERVER['HTTP_USER_AGENT']);
			header("Location: index.php?reason=forbidden");
		}
	}
	else {
		$access_loger->insertNewAccessLog(GENYMOBILE_ERROR,$_SERVER['REMOTE_ADDR'],'false',"check_login.php",AUTH_REQUIRED,",referer=".$_SERVER['HTTP_REFERER'].",user_agent=".$_SERVER['HTTP_USER_AGENT']);
		header("Location: index.php?reason=authrequired");
	}
    $profile = new GenyProfile();
    $profile->loadProfileByUsername($_SESSION['USERID']);
    if( ! isset($disable_password_reset_redirection) )
	$disable_password_reset_redirection = false;
    if( $profile->needs_password_reset && (isset($disable_password_reset_redirection) && !$disable_password_reset_redirection ) )
	header('Location: user_admin_password_change.php');
} catch (Exception $e) {
    //echo $e->getMessage(), "\n";
}

function displayStatusNotifications($gritter_notifications,$theme="default",$sticky="false",$time="''"){
	$imgs = array(
		"success" => "button_success.png",
		"error" => "button_error.png",
		"info" => "notifications/info.png",
		"idea" => "notifications/idea.png",
		"question" => "notifications/question.png",
		"warning" => "notifications/warning.png"
	);
	foreach( $gritter_notifications as $notif ){
		echo "$.gritter.add({title: \"".$notif['title']."\",text: \"".$notif['msg']."\",image: 'images/".$theme."/".$imgs[$notif['status']]."',sticky: $sticky,time: $time});";
	}
}

loadClass('GenyTools');

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--
GYMActivity v<?php echo $web_config->version ?> by GENYMOBILE - http://www.genymobile.com.
-->
<title>
<?php 
$header_title = str_replace("%COMPANY_NAME%",$web_config->company_name,$header_title);
echo $header_title 
?>
</title>
<script type="text/javascript" src="js/jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="js/timerX.js"></script>
<script src="js/formValidator/js/jquery.validationEngine-fr.js" type="text/javascript"></script>  
<script src="js/formValidator/js/jquery.validationEngine.js" type="text/javascript"></script>
<script src="js/jquery.listselect.js" type="text/javascript"></script>
<script src="js/DataTables/media/js/jquery.dataTables.min.js" type="text/javascript"></script> 
<script type="text/javascript" src="js/Gritter/js/jquery.gritter.min.js"></script>
<script type="text/javascript" src="js/jquery.datepick.js"></script>
<script type="text/javascript" src="js/jquery.datepick-GYMActivity.js"></script>

<link rel="shortcut icon" href="images/favicon.ico" /> 

<link rel="stylesheet" type="text/css" href="js/Gritter/css/jquery.gritter.css" />
<link rel="stylesheet" href="js/DataTables/media/css/demo_table_jui.css" type="text/css" media="screen" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="styles/<?php echo $web_config->theme ?>/main.css" media="screen" />
<link rel="stylesheet" href="js/formValidator/css/validationEngine.jquery.css" type="text/css" media="screen" charset="utf-8" />
<link rel="stylesheet" href="js/formValidator/css/template.css" type="text/css" media="screen" charset="utf-8" />
<link rel="stylesheet" href="styles/default/jquery-ui.css" type="text/css" media="all" />

<style type="text/css">
	@import "styles/default/jquery.datepick.css";
	@import "styles/default/smoothness.datepick.css";
</style>
</head>
<body>
<img id="logo" src="images/<?php echo $web_config->theme ?>/<?php echo $web_config->company_corner_logo ?>" alt="<?php echo $web_config->company_name ?> Logo"/>

<p id="headband">
	<?php
		$screen_name = $_SESSION['USERID'];
		if( $profile->firstname && $profile->lastname)
			$screen_name = $profile->firstname." ".$profile->lastname;
		else
			$screen_name = $profile->login;
		echo "<strong>Logged in as:</strong> ".$screen_name."";
	?>
</p>



