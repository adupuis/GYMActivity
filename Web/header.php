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
function __autoload($class_name) {
    include 'classes/'.$class_name . '.php';
}

try {
    $checkId_obj = new CheckIdentity();
    $web_config = new GenyWebConfig();
    if(isset($_SESSION['LOGGEDIN']) &&  $_SESSION['LOGGEDIN'] == 1){
	if( $checkId_obj->isAllowed($_SESSION['USERID'],$required_group_rights) ){
		if(isset($_SESSION['THEME']))
			$web_config->theme = $_SESSION['THEME'];
	}
	else
		header("Location: index.php?reason=forbidden");
    }
    else {
	header("Location: index.php?reason=authrequired");
    }
    $profile = new GenyProfile();
    $profile->loadProfileByUsername($_SESSION['USERID']);
    if( $profile->needs_password_reset && (isset($disable_password_reset_redirection) && !$disable_password_reset_redirection ) )
	header('Location: user_admin_password_change.php');
} catch (Exception $e) {
    //echo $e->getMessage(), "\n";
}

function displayStatusNotifications($gritter_notifications,$theme="default"){
	foreach( $gritter_notifications as $notif ){
		if( $notif['status'] == "success" )
			echo "$.gritter.add({title: 'Rapport mis à jour avec succès',text: '".$notif['msg']."',image: 'images/".$theme."/button_success.png',sticky: false,time: ''});";
		else
			echo "$.gritter.add({title: 'Erreur durant la mise à jour du rapport',text: \"".$notif['msg']."\",image: 'images/".$theme."/button_error.png',sticky: false,time: ''});";
	}
}

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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

<link rel="shortcut icon" href="images/favicon.ico" /> 

<link rel="stylesheet" href="js/DataTables/media/css/demo_table_jui.css" type="text/css" media="screen" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="styles/<?php echo $web_config->theme ?>/main.css" media="screen" />
<link rel="stylesheet" href="js/formValidator/css/validationEngine.jquery.css" type="text/css" media="screen" charset="utf-8" />
<link rel="stylesheet" href="js/formValidator/css/template.css" type="text/css" media="screen" charset="utf-8" />
<link rel="stylesheet" href="styles/default/jquery-ui.css" type="text/css" media="all" />
<link rel="stylesheet" type="text/css" href="js/Gritter/css/jquery.gritter.css" />

<style type="text/css">
</style>
</head>
<body>
<img id="logo" src="images/<?php echo $web_config->theme ?>/logo_genymobile_writting_small.jpg" alt="<?php echo $web_config->company_name ?> Logo"/>

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



