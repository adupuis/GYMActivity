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


$html = '<ul class="notifications_list">';
$html .= '<li class="notification_type_ok"><img src="images/'.$web_config->theme.'/notifications/ok.png" /><span>Vous n\'avez pas de notifications non lues.</span></li>';
$html .= '</ul>';

?>

<style>
	@import 'styles/<?php echo $web_config->theme ?>/notifications.css';
</style>


<script>
	var init_count=0;
// 	Récupération du nombre de notifications non lues
	function get_unread_notification_count(){
		$.get("backend/api/get_notification_list.php?profile_id="+<?php echo $profile->id; ?>+"&state=unread&action=count_unread", function(data){
// 			var init_count = $("span.notification_count_content").text();
// 			$("span.notification_count_content").empty();
// 			$("span.notification_count_content").append(data.count_unread);
			if( data.count_unread > 0 ){
				$("#notification_content").empty();
				$("#notification_content").append("Vous avez "+data.count_unread+" nouvelle(s) notification(s).");
			}
			else {
				$("#notification_content").empty();
				$("#notification_content").append("Vous n'avez aucune nouvelle notification.");
			}
			$("#menu_notification_count").empty();
			$("#menu_notification_count").append(data.count_unread);
			var diff_count = data.count_unread - init_count;
			if( diff_count > 0 ){
				$.gritter.add({
					// (string | mandatory) the heading of the notification
					title: 'Nouvelles notifications',
					// (string | mandatory) the text inside the notification
					text: 'Vous avez reçu '+diff_count+' nouvelle(s) notification(s).',
					// (string | optional) the image to display on the left
					image: 'images/default/notification.png',
					// (bool | optional) if you want it to fade out on its own or just sit there
					sticky: false,
					// (int | optional) the time you want it to be alive for before fading out
					time: ''
				});
				console.log("data.count_unread="+data.count_unread);
				console.log("init_count="+init_count);
				console.log("diff_count="+diff_count);
				init_count=data.count_unread;
			}
		},"json");
	}
	get_unread_notification_count();
	
	jQuery.timerDelayCall({
		 interval: 10000,
		 repeat: true,
		 callback: get_unread_notification_count
	});
	
// 	Récupération de la liste des notifications non lues et affichage dans la popup.
	function get_unread_notification_list(){
		$.get("backend/api/get_notification_list.php?profile_id="+<?php echo $profile->id; ?>+"&state=unread&action=list", function(data){
			$("#notifications-dialog-message").empty();
			var html = "<ul class='notifications_list'>";
			var notification_count=0;
			jQuery.each(data, function(row,content){
				html += '<li class="notification_type_'+content.type+'"><img src="images/'+<?php echo "'".$web_config->theme."'"; ?>+'/notifications/'+content.type+'.png" /><span>'+content.text+'</span></li>';
				notification_count++;
			});
			if( notification_count == 0 ){
				html += '<li class="notification_type_ok"><img src="images/'+<?php echo "'".$web_config->theme."'"; ?>+"/notifications/ok.png\" /><span>Vous n\'avez pas de notifications non lues.</span></li>";
			}
			html += "</ul>";
			$("#notifications-dialog-message").append(html);
		},"json");
	}
	get_unread_notification_list();
	
	jQuery.timerDelayCall({
		 interval: 8000,
		 repeat: true,
		 callback: get_unread_notification_list
	});
	
	function mark_all_notifications_as_read(){
		$.get("backend/api/mark_all_notifications_as_read.php?profile_id="+<?php echo $profile->id; ?>, function(){
			get_unread_notification_count();
			get_unread_notification_list();
		});
	}
	
// 	$(function() {
// 		$( "#notifications-dialog-message" ).dialog({
// 			modal: true,
// 			autoOpen: false,
// 			width: 900,
// 			show: "slide",
// 			hide: "explode",
// 			buttons: {
// 				Ok: function() {
// 					mark_all_notifications_as_read();
// 					$( this ).dialog( "close" );
// 					
// 				}
// 			}
// 		});
// 	});
</script>

<li class="notifications">
<!-- 	<a href="#" onClick='$( "#notifications-dialog-message" ).dialog( "open" )'> -->
	<a href="#notifications-dialog-message" rel='prettyPhoto[notifications]'>
<!-- 		<span class="notifications_count"><span class="notification_count_content">0</span></span> -->
		<span class="dock_item_title">Notifications</span><br/>
		<span class="dock_item_content"><span id="notification_content">Vous n'avez aucune nouvelle notification.</span></span>
	</a>
</li>

<div id="notifications-dialog-message" title="Notifications" style="display: none;">
<!-- 	<p> -->
		<?php echo $html; ?>
<!-- 	</p> -->
</div>
<script>
$("a[rel='prettyPhoto[notifications]']").prettyPhoto({modal: 'true',animation_speed:'fast',slideshow:false, hideflash: true, social_tools: '<div class="pp_social"></div>', theme: 'pp_default', callback: function() {mark_all_notifications_as_read();}});
</script>


