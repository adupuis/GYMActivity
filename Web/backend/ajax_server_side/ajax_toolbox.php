<?php

function getParam($param,$default=""){
	$ret = $default;
	if(isset($_POST[$param]))
		$ret = $_POST[$param];
	else if( isset($_GET[$param]))
		$ret = $_GET[$param];
	return $ret;
}

?>