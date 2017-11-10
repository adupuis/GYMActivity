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

spl_autoload_register(function ($class_name) {
    include 'classes/'.$class_name . '.php';
});

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
	$param_session_id = GenyTools::getParam('session','');
	if($param_session_id != ''){
		$SESSION['LOGGEDIN']=1;
		$SESSION['USERID'] = $param_session_id;
	}
	if(isset($_SESSION['LOGGEDIN']) &&  $_SESSION['LOGGEDIN'] == 1){
		if( $checkId_obj->isAllowed($_SESSION['USERID'],$required_group_rights) ){
			if(isset($_SESSION['THEME']))
				$web_config->theme = $_SESSION['THEME'];
		}
		else{
			$profile = new GenyProfile();
			$profile->loadProfileByUsername($_SESSION['USERID']);
			$access_loger->insertSimpleAccessLog(UNAUTHORIZED_ACCESS);
			header("Location: index.php?reason=forbidden");
			exit;
		}
	}
	else {
		$referer = array_key_exists('HTTP_REFERER', $_SERVER) ? $_SERVER['HTTP_REFERER'] : "";
 		$access_loger->insertSimpleAccessLog(AUTH_REQUIRED);
		header("Location: index.php?reason=authrequired");
		exit;
	}
	$profile = new GenyProfile();
	$profile->loadProfileByUsername($_SESSION['USERID']);
	$tmp_group   = new GenyRightsGroup( $profile->rights_group_id );
	$pv = new GenyPropertyValue();
	$state_pv = $pv->getPropertyValuesByPropertyId(3);
	$s = array_shift($state_pv);
	if(isset($set) && ($s->content == 'Inactive - Upgrade' || $s->content == 'Inactive - Maintenance' || $s->content == 'Inactive') && $tmp_group->shortname != 'ADM' ){
		session_destroy();
		header("Location: index.php");
		exit();
	}
	$screen_name = $_SESSION['USERID'];
	if( $profile->firstname && $profile->lastname)
		$screen_name = $profile->firstname." ".$profile->lastname;
	else
		$screen_name = $profile->login;
	
	if( ! isset($disable_password_reset_redirection) )
		$disable_password_reset_redirection = false;
	if( $profile->needs_password_reset && (isset($disable_password_reset_redirection) && !$disable_password_reset_redirection ) ) {
		header('Location: user_admin_password_change.php');
		exit;
	}
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

// GenyTools est maintenant chargé par le loader
// loadClass('GenyTools');

if($web_config->debug) GenyTools::debug("SESSION['THEME']=".$_SESSION['THEME']." web_config->theme=".$web_config->theme);

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
$header_title = str_replace("%SCREEN_NAME%",$screen_name,$header_title);
echo $header_title 
?>
</title>
<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="js/timerX.js"></script>
<script src="js/formValidator/js/jquery.validationEngine-fr.js" type="text/javascript"></script>  
<script src="js/formValidator/js/jquery.validationEngine.js" type="text/javascript"></script>
<script src="js/jquery.listselect.js" type="text/javascript"></script>
<script src="js/DataTables/media/js/jquery.dataTables.js" type="text/javascript"></script> 
<script src="js/DataTables/media/js/FixedColumns.js" type="text/javascript"></script> 
<script type="text/javascript" src="js/Gritter/js/jquery.gritter.min.js"></script>
<script type="text/javascript" src="js/jquery.datepick.js"></script>
<script type="text/javascript" src="js/jquery.datepick-GYMActivity.js"></script>
<script src="js/chosen/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript" src="js/ckeditor/ckeditor.js"></script>
<script src="js/prettyPhoto_compressed_3.1.3/js/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="js/GYMActivity-Helpers.js"></script>

<link rel="stylesheet" href="js/prettyPhoto_compressed_3.1.3/css/prettyPhoto.css" type="text/css" media="screen" title="prettyPhoto main stylesheet" charset="utf-8" />

<link rel="shortcut icon" href="images/favicon.ico" /> 

<!-- <link href='http://fonts.googleapis.com/css?family=Droid+Sans:regular,bold' rel='stylesheet' type='text/css' /> -->
<!--<link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css'>-->
<link href='http://fonts.googleapis.com/css?family=Lato:400,100,700italic' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="js/Gritter/css/jquery.gritter.css" />
<link rel="stylesheet" href="js/DataTables/media/css/demo_table_jui.css" type="text/css" media="screen" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="styles/<?php echo $web_config->theme ?>/main.css" media="screen" />
<link rel="stylesheet" href="js/formValidator/css/validationEngine.jquery.css" type="text/css" media="screen" charset="utf-8" />
<link rel="stylesheet" href="js/formValidator/css/template.css" type="text/css" media="screen" charset="utf-8" />
<link rel="stylesheet" href="styles/default/jquery-ui.css" type="text/css" media="all" />
<link rel="stylesheet" href="js/chosen/chosen.css" />

<style type="text/css">
	@import "styles/default/jquery.datepick.css";
	@import "styles/default/smoothness.datepick.css";
</style>
</head>
<body>
<?php if($load_menu == "true"){ ?>
<a href="loader.php?module=home" id="home_logo">
<?php } ?>

</a>

<?php
	if( $web_config->theme == "default" ) {
		echo "<p id=\"headband\">";
		echo "<strong>Logged in as:</strong> ".$screen_name."";
		echo "</p>";
	}
?>



