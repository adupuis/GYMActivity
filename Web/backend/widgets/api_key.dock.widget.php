<?php
$apikey = new GenyApiKey();
$apikey->loadApiKeyByProfileId( $profile->id );
if( $apikey->id <= 0 ){
	$tmp_key = $apikey->generateApiKey($profile);
	$key_id = $apikey->insertNewApiKey(0,$profile->id, $tmp_key);
	$apikey->loadApiKeyById($key_id);
}



?>

<li class="user_admin_password_change">
	<a href="#" onClick='$( "#dialog_api_key" ).dialog( "open" )'>
		<span class="dock_item_title">Clé API</span><br/>
		<span class="dock_item_content">En cliquant sur ce widget vous pourrez visualiser votre clé API ainsi que voir le QRCode vous permettant d'initialiser l'application mobile.<br/>
	</a>
</li>

<style>
	#dialog_api_key p input {
		width: 100%;
	}
</style>

<div id="dialog_api_key" title="Votre clé API">
	<p>
		<input id="key" type="text" disabled="true" value="<?php echo $apikey->data; ?>" />
	</p>
	<p>
		<img src="https://chart.googleapis.com/chart?chs=230x230&cht=qr&chl=<?php echo $apikey->data; ?>" />
	</p>
	<style>
		@import 'styles/<?php echo $web_config->theme ?>/api_key.dock.widget.css';
	</style>
	<ul id="key_status_message" class="status_message">
	</ul>
</div>

<script>
	$(function() {
		$( "#dialog_api_key" ).dialog({
			modal: true,
			autoOpen: false,
			width: 250,
			show: "slide",
			hide: "explode",
			buttons: {
				Ok: function() {
					$( this ).dialog( "close" );
					
				},
				"Regénérer une clé" : function(){
					$.get("backend/ajax_server_side/generate_new_api_key.php",
					{"old_key":"<?php echo $apikey->data; ?>", "owner" : "<?php echo $apikey->profile_id; ?>", "ts" : "<?php echo $apikey->timestamp; ?>"},
					function(returned_data){
						if(returned_data.status == "success"){
							$("#key_status_message").append("<li class=\"status_message_success\">"+returned_data.success_string+"</li>");
							$("#key").val(returned_data.new_key);
						}
						else{
							$("#key_status_message").append("<li class=\"status_message_error\">"+returned_data.error_string+"</li>");
						}
					},"json");
				}
			}
		});
	});
</script>