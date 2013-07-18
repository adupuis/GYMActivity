<?php

include_once 'classes/GenyRightsGroup.php';

$geny_rg = new GenyRightsGroup();
foreach( $geny_rg->getAllRightsGroups() as $group ){
	define($group->shortname, $group->id);
}

?>