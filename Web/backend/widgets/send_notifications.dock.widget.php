

<style>
	@import 'styles/default/send_notifications.dock.widget.css';
	.ui-autocomplete-loading { background: white url('images/default/ui-anim_basic_16x16.gif') right center no-repeat; }
</style>
<script>
	$(function() {
		
		$( "#notification_to" ).autocomplete({
			source: "backend/ajax_server_side/get_profile_list.php",
			minLength: 2
		});
	});
</script>
<li class="notifications">
	<a href="#">
		<span class="dock_item_title">Notifier</span><br/>
		<span class="dock_item_content">
			<form>
				<p>
					<label for="notification_to">A</label>
					<input name="notification_to" id="notification_to" type="text" class="validate[required,length[2,100]] text-input" />
				</p>
				<p>
					<label for="notification_message">Message</label>
					<input name="notification_message" id="notification_message" type="text" class="validate[required,length[2,200]] text-input" />
				</p>
				<p>
					<input class="submit" type="button" onClick="send_notification()" value="Envoyer" />
				</p>
			</form>
		</span>
	</a>
</li>
<script>
	function send_notification(){
		$.get("backend/ajax_server_side/send_notification.php?to_type=pm&message="+$("#notification_message").val()+"&to="+$("#notification_to").val());
		$("#notification_message").val("");
		$("#notification_to").val("");
	}
</script>