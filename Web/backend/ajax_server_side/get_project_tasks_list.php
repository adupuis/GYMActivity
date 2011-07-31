<?php
session_start();
function __autoload($class_name) {
    include '../../classes/'.$class_name . '.php';
}
header('Content-type:text/javascript;charset=UTF-8');
try {
    $checkId_obj = new CheckIdentity();
    $profiles = array();
    if(isset($_SESSION['LOGGEDIN']) &&  $_SESSION['LOGGEDIN'] == 1){
	if( $checkId_obj->isAllowed($_SESSION['USERID'],6) ){
		$project_id = -1;
		$assignement_id = -1;
		if(isset($_POST['project_id']))
			$project_id = $_POST['project_id'];
		else if( isset($_GET['project_id']))
			$project_id = $_GET['project_id'];
		else if( isset($_GET['assignement_id']))
			$assignement_id = $_GET['assignement_id'];
		else if( isset($_POST['assignement_id']))
			$assignement_id = $_POST['assignement_id'];
		
		if( $assignement_id > -1 ){
			$geny_assignement = new GenyAssignement($assignement_id);
			$project_id = $geny_assignement->project_id;
		}
		
		if( $project_id > -1 ){
			$geny_ptr = new GenyProjectTaskRelation();
			$retArray = array();
			$totalRec = 0;
			foreach($geny_ptr->getProjectTaskRelationsListByProjectId( $project_id ) as $ptr ){
				$t = new GenyTask( $ptr->task_id );
				$ta = array("$t->id","$t->name","$t->description");
				if (! in_array($ta, $retArray))
					$retArray[] = $ta;
				$totalRec++;
			}
			$data = json_encode($retArray);
			$ret = "{ $data }\n";
			echo $data;
		}
		else{
			$data = json_encode(array('error'=>'Project ID not specified.'));
			$ret = "{data:" . $data .",\n";
			$ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
			$ret .= "recordType : 'array'}\n";
			echo $ret;
		}
	}
	else{
		$data = json_encode(array('error'=>'User not allowed'));
		$ret = "{data:" . $data .",\n";
		$ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
		$ret .= "recordType : 'array'}\n";
		echo $ret;
	}
    }
    else {
	$data = json_encode(array('error'=>'Authentication required.'));
	$ret = "{data:" . $data .",\n";
	$ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
	$ret .= "recordType : 'array'}\n";
	echo $ret;
    }
} catch (Exception $e) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>