<?php
$apikey = new GenyApiKey();
$apikey->loadApiKeyByProfileId( $profile->id );
$data = "";
if( $apikey->id <= 0 ){
	$tmp_key = $apikey->generateApiKey($profile);
	$apikey->insertNewApiKey(0,$profile->id, $tmp_key);
	$data = $tmp_key;
}
else
	$data = $apikey->data;



?>

<li class="user_admin_password_change">
	<a href="#" onClick='$( "#dialog_api_key" ).dialog( "open" )'>
		<span class="dock_item_title">Clé API</span><br/>
		<span class="dock_item_content">En cliquant sur ce widget vous pourrez visualiser votre clé API ainsi que voir le QRCode vous permettant d'initialiser l'application mobile.<br/>
	</a>
</li>

<div id="dialog_api_key" title="Votre clé API">
	<p>
		<input id="key" type="text" disabled="true" value="<?php echo $data; ?>" />
	</p>
	<p>
		<img src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo $data; ?>" />
	</p>
</div>

<script>
	$(function() {
		$( "#dialog_api_key" ).dialog({
			modal: true,
			autoOpen: false,
			width: 200,
			show: "slide",
			hide: "explode",
			buttons: {
				Ok: function() {
					$( this ).dialog( "close" );
					
				}
			}
		});
	});
</script>