//  Copyright (C) 2012 by GENYMOBILE & Jean-Charles Leneveu
//  jcleneveu@genymobile.com
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