<?php
session_start();
function __autoload($class_name) {
    include 'classes/'.$class_name . '.php';
}
header('Content-type:text/javascript;charset=UTF-8');
try {
    $checkId_obj = new CheckIdentity();
    $profiles = array();
    if(isset($_SESSION['LOGGEDIN']) &&  $_SESSION['LOGGEDIN'] == 1){
	if( $checkId_obj->isAllowed($_SESSION['USERID'],1) ){
		$handle = mysql_connect("localhost","genymobile","toto");
		mysql_select_db("GYMActivity");
		$query = "SELECT profile_id,profile_login,profile_firstname,profile_lastname,profile_email,profile_is_active,profile_needs_password_reset,rights_group_id FROM Profiles WHERE md5(profile_login)='$username'";
		$result = mysql_query($query, $handle);
		$retArray = array();
		while ($row = mysql_fetch_row($result)) {
			$retArray[] = $row;
		}
// 		$idx=0;
// 		foreach($result as $row){
// 			foreach($row as $key => $value) {
// 				$profiles[$idx][''] = $row[''];
// 				$profiles[$idx][''] = $row[''];
// 				$profiles[$idx][''] = $row[''];
// 				$profiles[$idx][''] = $row[''];
// 				$profiles[$idx][''] = $row[''];
// 				$profiles[$idx][''] = $row[''];
// 				$profiles[$idx][''] = $row[''];
// 				$profiles[$idx][''] = $row[''];
// 			}
// 		}
		$data = json_encode($retArray);
		$ret = "{data:" . $data .",\n";
		$ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
		$ret .= "recordType : 'array'}";
		echo $ret;
	}
	else
		$data = json_encode(array('error'=>'User not allowed'));
		$ret = "{data:" . $data .",\n";
		$ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
		$ret .= "recordType : 'array'}";
		echo $ret;
    }
    else {
	$data = json_encode(array('error'=>'Authentication required.'));
	$ret = "{data:" . $data .",\n";
	$ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
	$ret .= "recordType : 'array'}";
	echo $ret;
    }
} catch (Exception $e) {
    echo $e->getMessage(), "\n";
}

?>