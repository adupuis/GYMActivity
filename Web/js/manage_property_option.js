function addPropertyOption(theme){
	var property_id = $("#property_id").val();
	var content = $("#new_property_option_label").val();
	if( property_id > 0 ) {
		$.get('backend/api/update_property_options.php?prop_id='+property_id+'&content='+content+'&action=add', function( data ) {
			if( data.status == "success" ) {
				$("#property_value").append('<option value='+data.new_property_option_id+'>'+content+'</option>');
				$("#new_property_option_label").val("");
				$("#property_value").trigger("liszt:updated");
				$.gritter.add( { title: data.title,
							text: data.msg,
							image: "images/"+theme+"/button_success.png",
							sticky: false,
							time: ''});
			}
			else {
				$.gritter.add( { title: data.title,
							text: data.msg,
							image: "images/"+theme+"/button_error.png",
							sticky: false,
							time: ''});
			}
		},'json');
	}
}

function deletePropertyOption(theme){
	var selected_option_ids = $("#property_value").val();
	if( $.isArray( selected_option_ids ) )
		$.each(selected_option_ids, function(index, selected_option_id) { 
			if( selected_option_id > 0 ) {
				$.get('backend/api/update_property_options.php?id='+selected_option_id+'&action=delete', function( data ) {
					if( data.status == "success" ) {
						$("#property_value option[value="+selected_option_id+"]").remove();
						$("#property_value").trigger("liszt:updated");
						$.gritter.add( { title: data.title,
									text: data.msg,
									image: "images/"+theme+"/button_success.png",
									sticky: false,
									time: ''});
					}
					else {
						$.gritter.add( { title: data.title,
									text: data.msg,
									image: "images/"+theme+"/button_error.png",
									sticky: false,
									time: ''});
					}
				},'json');
			}
		});
	else {
		var selected_option_id = selected_option_ids;
		if( selected_option_id > 0 ) {
			$.get('backend/api/update_property_options.php?id='+selected_option_id+'&action=delete', function( data ) {
				if( data.status == "success" ) {
					$("#property_value option[value="+selected_option_id+"]").remove();
					$("#property_value").trigger("liszt:updated");
					$.gritter.add( { title: data.title,
							text: data.msg,
							image: "images/"+theme+"/button_success.png",
							sticky: false,
							time: ''});
				}
				else {
					$.gritter.add( { title: data.title,
							text: data.msg,
							image: "images/"+theme+"/button_error.png",
							sticky: false,
							time: ''});
				}
			},'json');
		}
	}
}