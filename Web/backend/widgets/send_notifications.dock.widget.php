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

?>

<style>
	@import 'styles/<?php echo $web_config->theme ?>/send_notifications.dock.widget.css';
	.ui-autocomplete-loading { background: white url('images/default/ui-anim_basic_16x16.gif') right center no-repeat; }
</style>
<script>
	$(function() {
		
		$( "#notification_to" ).autocomplete({
			source: "backend/api/get_profile_list.php",
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
		$.get("backend/api/send_notification.php?to_type=pm&message="+$("#notification_message").val()+"&to="+$("#notification_to").val());
		$("#notification_message").val("");
		$("#notification_to").val("");
	}
</script>