<?php

$notification = new GenyNotification();
$notif_count = $notification->getUnreadNotificationCountByProfileId($profile->id);

$html = '<ul class="notifications_list">';

foreach( $notification->getNotificationsByProfileId( $profile->id ) as $notif ){
	if( $notif->is_unread ){
		$html .= '<li class="notification_type_'.$notif->type.'"><img src="images/'.$web_config->theme.'/notifications/'.$notif->type.'.png" /><span>'.$notif->text.'</span></li>';
		$notif->updateBool("notification_is_unread","false");
		$notif->commitUpdates();
	}
}

// Pas très élégant mais c'est plus efficace que de faire un nouvel appel à la fonction de comptage
if( $html == '<ul class="notifications_list">' ){
	$html .= '<li class="notification_type_ok"><img src="images/'.$web_config->theme.'/notifications/ok.png" /><span>Vous n\'avez pas de notifications non lues.</span></li>';
}

$html .= '</ul>';
?>

<style>
	@import 'styles/default/notifications.css';
</style>

<li class="notifications">
	<a href="#" onClick='$( "#notifications-dialog-message" ).dialog( "open" )'>
		<span class="notifications_count"><span class="notification_count_content"><?php echo $notif_count; ?></span></span>
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
	$(function() {
		$( "#notifications-dialog-message" ).dialog({
			modal: true,
			autoOpen: false,
			width: 1000,
			show: "slide",
			hide: "explode",
			buttons: {
				Ok: function() {
					location.reload();
					$( this ).dialog( "close" );
					
				}
			}
		});
	});
</script>
