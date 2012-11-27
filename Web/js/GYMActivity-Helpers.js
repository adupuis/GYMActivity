function toggleCheck(type) {
	if($(type + ' input:checkbox').is(':checked')) {
		$(type + ' input:checkbox' ).removeAttr('checked');
	} else {
		$(type + ' input:checkbox' ).attr('checked', 'true');
	}
}