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

$load_menu = GenyTools::getParam("load_menu","true");
$load_bottomdock = GenyTools::getParam("load_bottomdock","true");
// Here is the code for submodule metadata loading
$submod = GenyTools::getParam("module","bork");
if( file_exists( 'submodules/'.$submod.'.php.meta' ) ){
	include_once('submodules/'.$submod.'.php.meta');
}
GenyTools::debug("load_menu=$load_menu");
include_once 'header.php';
if($load_menu == "true")
	include_once 'menu.php';

?>

<div id="wrapper">
<!-- 	<span id="load"> </span> -->
	<div id="content">
		<?php
			// Here is the code for submodule loading
			if( file_exists( 'submodules/'.$submod.'.php' ) )
				include_once('submodules/'.$submod.'.php');
			else
				include_once('submodules/bork.php');
		?>
	</div>
</div>
<!--<div id='separator_top'></div>
<div id='bottomdock'>
<h3 class='italic'>Liens rapides</h3>
<div id='services' class='widget clearfix'>
<ul>-->
<?php
	if( $load_bottomdock == "true" ) {
		if( !isset( $bottomdock_items ) ) {
			$bottomdock_items = array();
		}
		
		if( count( $bottomdock_items ) == 0 || $bottomdock_items[0] != 'backend/widgets/notifications.dock.widget.php' ) {
			array_unshift( $bottomdock_items, 'backend/widgets/notifications.dock.widget.php' );
		}
		echo "<div id='separator_top'></div>\n<div id='bottomdock'>\n<h3 class='italic'>Liens rapides</h3>\n<div id='services'>\n<ul>";
		foreach ( $bottomdock_items as $item ) {
			include "$item";
		}
		echo "</ul>\n</div>\n</div>\n<div id='separator_bottom'></div>";
	}
?>
<!--</ul>
</div>
</div>
<div id='separator_bottom'></div>-->
<?php
include_once 'footer.php';
?>

<script type="text/javascript"> $(".chzn-select").chosen();</script>

<!--<script type="text/javascript">
	$(document).ready(function() {
		$('#sdt_menu li a').click(function(){
			var destHref = $(this).attr('href');
			var reg = new RegExp("module=", "g");
			var table = destHref.split(reg);
			console.log("Résultat du split[1]="+table[1]);
// 			var toLoad = destHref+' #content';
			var toLoad = "submodules/"+table[1]+".php";
			$('#content').hide('fast',loadContent);  
			$('#load').remove();  
			$('#wrapper').append('<span id="load">LOADING...</span>');  
			$('#load').fadeIn('normal');
			function loadContent() {  
				$('#content').load(toLoad,'',showNewContent())  
			}  
			function showNewContent() {  
				$('#content').show('normal',hideLoader());  
			}  
			function hideLoader() {  
// 				$('#load').fadeOut('normal');  
			}  
			return false;  
		});  
	}); 
</script>-->


