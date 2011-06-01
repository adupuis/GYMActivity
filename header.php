<?php

session_start();
function __autoload($class_name) {
    include 'classes/'.$class_name . '.php';
}

try {
    $checkId_obj = new CheckIdentity();
    if(isset($_SESSION['LOGGEDIN']) &&  $_SESSION['LOGGEDIN'] == 1){
	if( $checkId_obj->isAllowed($_SESSION['USERID'],$required_group_rights) ){
		
	}
	else
		header("Location: index.php?reason=forbidden");
    }
    else {
	header("Location: index.php?reason=authrequired");
    }
    $profile = new GenyProfile();
    $profile->loadProfileByUsername($_SESSION['USERID']);
//     if( $profile->needs_password_reset )
// 	header('Location: user_admin_password_change.php');
    $web_config = new GenyWebConfig();
} catch (Exception $e) {
    //echo $e->getMessage(), "\n";
}

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>
<?php 
echo $header_title 
?>
</title>
<script type="text/javascript" src="js/jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="js/timerX.js"></script>
<script src="js/formValidator/js/jquery.validationEngine-fr.js" type="text/javascript"></script>  
<script src="js/formValidator/js/jquery.validationEngine.js" type="text/javascript"></script>
<script src="js/jquery.listselect.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="styles/<?php echo $web_config->theme ?>/main.css" media="screen" />
<link rel="stylesheet" href="js/formValidator/css/validationEngine.jquery.css" type="text/css" media="screen" charset="utf-8" />
<link rel="stylesheet" href="js/formValidator/css/template.css" type="text/css" media="screen" charset="utf-8" />
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/themes/base/jquery-ui.css" type="text/css" media="all" />
<style type="text/css">
</style>
</head>
<body>
<img id="logo" src="images/<?php echo $web_config->theme ?>/logo_genymobile_writting_small.jpg" alt="GenY Mobile Logo"/>

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



