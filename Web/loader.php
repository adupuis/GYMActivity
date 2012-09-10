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

include 'classes/GenyTools.php';

// Variable to configure global behaviour
$header_title = '%COMPANY_NAME% - Home';
$required_group_rights = 6;
$is_cached = false;
$expiration_freq = 24*60*60; // in seconds
$dyn_params = array();

$load_menu = GenyTools::getParam("load_menu","true");
$load_bottomdock = GenyTools::getParam("load_bottomdock","true");
$submod = GenyTools::getParam("module","bork");
$force_refresh = (bool) GenyTools::getParam("force_refresh",false);

if( file_exists( 'submodules/'.$submod.'.php.meta' ) ){
	include_once('submodules/'.$submod.'.php.meta');
}

$expire_timestamp = time() - $expiration_freq;

include_once 'header.php';

if($web_config->debug)
	GenyTools::debug("load_menu=$load_menu");
	
if($load_menu == "true")
	include_once 'menu.php';
?>

<div id="wrapper">
	<div id="content">
		<?php
			if( isset( $is_cached ) && $is_cached == true ) {
				// détermination du chemin du cache
				$path = 'cache/'.$submod;
				foreach($dyn_params as $param) {
					$path.= '-' . $param . '=' . GenyTools::getParam($param,"non_defined");
				}
				$path.='.php';
				
				// affichage de l'url pour rafraichir la page
				if( preg_match( '', $_SERVER['REQUEST_URI'] ) ) {
					$refresh_url = $_SERVER['REQUEST_URI'] . "&force_refresh=true";
				}
				else {
					$refresh_url = $_SERVER['REQUEST_URI'] . "?force_refresh=true";
				}
				
				// si la page est en cache et valide, on l'affiche
				if( file_exists( $path ) && filemtime( $path ) > $expire_timestamp && ! $force_refresh ) {
					readfile( $path );
				}
				// sinon on la regenère
				else {
					ob_start();
					if( file_exists( 'submodules/'.$submod.'.php' ) )
						include_once('submodules/'.$submod.'.php');
					else
						include_once('submodules/bork.php');
					$page = ob_get_contents();
					ob_end_clean();
					file_put_contents($path, $page) ;
					echo $page ;
				}
			}
			else if( file_exists( 'submodules/'.$submod.'.php' ) )
				include_once('submodules/'.$submod.'.php');
			else
				include_once('submodules/bork.php');
		?>
	</div>
</div>

<?php

if( isset( $is_cached ) && $is_cached == true )
	echo '<a href="'. $refresh_url .'">Rafraichir le cache</a>';

	if( $load_bottomdock == "true" ) {
		if( !isset( $bottomdock_items ) ) {
			$bottomdock_items = array();
		}
		
		if( count( $bottomdock_items ) == 0 || $bottomdock_items[0] != 'backend/widgets/notifications.dock.widget.php' ) {
			array_unshift( $bottomdock_items, 'backend/widgets/notifications.dock.widget.php' );
		}
		array_push($bottomdock_items, 'backend/widgets/logout.dock.widget.php');
		echo "<div id='separator_top'></div>\n<div id='bottomdock'>\n<h3 class='italic'>Liens rapides</h3>\n<div id='services'>\n<ul>";
		foreach ( $bottomdock_items as $item ) {
			include "$item";
		}
		echo "</ul>\n</div>\n</div>\n<div id='separator_bottom'></div>";
	}

include_once 'footer.php';
?>

<script type="text/javascript"> $(".chzn-select").chosen();</script>
