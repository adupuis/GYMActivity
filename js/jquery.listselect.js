(function ($) {
    $.fn.listselect = function (options) {
        var defaults = {
            size: 10,
            listTitle: "All",
            selectedTitle: "Selected"
        }

        var options = $.extend(defaults, options);

        return this.each(function () {
            var listContainer = $("<div style=\"margin-right: 5px; float: left; width: 200px;\"></div>");
            if (options.listTitle && options.listTitle != '') listContainer.append("<strong>" + options.listTitle + "</strong>");

            var selectedContainer = $("<div style=\"float: left; width: 200px;\"></div>");
            if (options.selectedTitle && options.selectedTitle != '') selectedContainer.append("<strong>" + options.selectedTitle + "</strong>");

            var selected = $(this);
            listContainer.insertBefore(selected);
            selectedContainer.insertAfter(listContainer);
            selected.remove();
			
			var hiddenFieldsContainer = $("<div style=\"clear: both;\"></div>");
			hiddenFieldsContainer.insertAfter(selectedContainer);
			
            var list = $("<select></select>");
            var selectedValues = null;
            if (selected.attr("selected")) {
                selectedValues = selected.attr("selected").split(",");
            }

            var fieldname = selected.attr('name');
			var fieldID = fieldname.replace("[]" , "");
            selected.attr('name', '');
            selected.attr('id', '');
            selected.css("width", "100%");
            list.css("width", "100%");

            list.html(selected.html());
            selected.html('');

            selected.attr('size', options.size);
            list.attr('size', options.size);

            listContainer.append(list);
            selectedContainer.append(selected);

            list.bind("click", function () {
				var option = $(this).find("option[value='" + $(this).val() + "']");
				if(option.length > 0){
					var hiddenObj = $("<input type=\"checkbox\" style=\"display: none\" checked=\"checked\" name=\"" + fieldname + "\" id=\"listselect_" + fieldID + "_" + $(this).val() + "\" value=\"" + $(this).val() + "\" />");
	
					option.remove();
					selected.append(option);
					hiddenFieldsContainer.append(hiddenObj);
	
					selected.val('');
					list.val('');
				}
            });

            selected.bind("click", function () {
				var option = $(this).find("option[value='" + $(this).val() + "']");
				if(option.length > 0){
					var hiddenObj = $("#listselect_" + fieldID + "_" + $(this).val());
					option.remove();
					list.append(option);
					hiddenObj.remove();
	
					selected.val('');
					list.val('');
				}
            });
			
			hiddenFieldsContainer.append($("<input type=\"checkbox\" style=\"display: none\" name=\"" + fieldname + "\">"));
            if (selectedValues != null && selectedValues.length > 0) {
                for (var i = 0; i < selectedValues.length; i++) {
					var option = list.find("option[value='" + selectedValues[i] + "']");
					if(option.length > 0){;
						var hiddenObj = $("<input type=\"checkbox\" style=\"display: none\" checked=\"checked\" name=\"" + fieldname + "\" id=\"listselect_" + fieldID + "_" + selectedValues[i] + "\" value=\"" + selectedValues[i] + "\" />");

						option.remove();
						selected.append(option);
						hiddenFieldsContainer.append(hiddenObj);
					}
                }
            }

            selected.val('');
            list.val('');

        });
    };
})(jQuery);  