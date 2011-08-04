<?php
// 	Here is the menu loader
	if( file_exists("menu_".$web_config->theme.".php") )
		include_once "menu_".$web_config->theme.".php";
	else
		include_once "menu_default.php";
?>