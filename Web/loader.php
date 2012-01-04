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

// Variable to configure global behaviour
$header_title = '%COMPANY_NAME% - Home';
$required_group_rights = 6;

include_once 'header.php';
include_once 'menu.php';

?>

<div id="wrapper">
<!-- 	<span id="load"> </span> -->
	<div id="content">
		<?php
			// Here is the code for submodule loading
			$submod = GenyTools::getParam("module","home");
			if( file_exists( 'submodules/'.$submod.'.php' ) )
				include_once('submodules/'.$submod.'.php');
			else
				include_once('submodules/bork.php');
		?>
	</div>
</div>
<div id="separator_top"></div>
<div id="bottomdock">
<?php
	include 'backend/widgets/cra_add.dock.widget.php';
	include 'backend/widgets/cra_list.dock.widget.php';
	include 'backend/widgets/conges_add.dock.widget.php';
?>
</div>
<div id="separator_bottom"></div>
<?php
// include_once 'footer.php';
?>
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


