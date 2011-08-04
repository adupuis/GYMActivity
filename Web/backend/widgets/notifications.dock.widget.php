<?php

$html = '<ul class="notifications_list">';
$html .= '<li class="notification_type_ok"><img src="images/'.$web_config->theme.'/notifications/ok.png" /><span>Vous n\'avez pas de notifications non lues.</span></li>';
$html .= '</ul>';

?>

<style>
	@import 'styles/<?php echo $web_config->theme ?>/notifications.css';
</style>

<li class="notifications">
	<a href="#" onClick='$( "#notifications-dialog-message" ).dialog( "open" )'>
		<span class="notifications_count"><span class="notification_count_content">0</span></span>
		<span class="dock_item_title">Notifications</span><br/>
		<span class="dock_item_content">La liste de toutes les notifications que le système vous a envoyé. Le cercle rouge contient le nombre de notifications non lus que vous avez.</span>
	</a>
</li>

<div id="notifications-dialog-message" title="Notifications">
	<p>
		<?php echo $html; ?>
	</p>
</div>



<script>
// 	Récupération du nombre de notifications non lues
	function get_unread_notification_count(){
		$.get("backend/ajax_server_side/get_notification_list.php?profile_id="+<?php echo $profile->id; ?>+"&state=unread&action=count_unread", function(data){
			$("span.notification_count_content").empty();
			$("span.notification_count_content").append(data.count_unread);
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
		$.get("backend/ajax_server_side/get_notification_list.php?profile_id="+<?php echo $profile->id; ?>+"&state=unread&action=list", function(data){
			$("#notifications-dialog-message").empty();
			var html = "<p><ul class='notifications_list'>";
			var notification_count=0;
			jQuery.each(data, function(row,content){
				html += '<li class="notification_type_'+content.type+'"><img src="images/'+<?php echo "'".$web_config->theme."'"; ?>+'/notifications/'+content.type+'.png" /><span>'+content.text+'</span></li>';
				notification_count++;
			});
			if( notification_count == 0 ){
				html += '<li class="notification_type_ok"><img src="images/'+<?php echo "'".$web_config->theme."'"; ?>+"/notifications/ok.png\" /><span>Vous n\'avez pas de notifications non lues.</span></li>";
			}
			html += "</ul></p>";
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
		$.get("backend/ajax_server_side/mark_all_notifications_as_read.php?profile_id="+<?php echo $profile->id; ?>, function(){
			get_unread_notification_count();
			get_unread_notification_list();
		});
	}
	
	$(function() {
		$( "#notifications-dialog-message" ).dialog({
			modal: true,
			autoOpen: false,
			width: 900,
			show: "slide",
			hide: "explode",
			buttons: {
				Ok: function() {
					mark_all_notifications_as_read();
					$( this ).dialog( "close" );
					
				}
			}
		});
	});
</script>
