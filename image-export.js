(function($) {
	$(document).ready(function() {
		$('#ie-excute').click(function() {
			var checked = $('#the-list input[type=checkbox]:checked');
			
	    	if (checked.length < 1) {
        		alert(message.m002);
        		return false;
	    	}
	    	
        	var valArr = [];
        	checked.each(function(idx) {
    			valArr.push($(this).val());
    		});
        	
			var params = {
				action : 'ie_execute',
				mode   : 'upload',
				id     : valArr.join(',')
			};
			
			$.post(
				obj.link,
				params,
				function(data){
					var ret_obj = $.parseJSON(data);
					$('#wpbody-content .wrap').prepend(ret_obj.msg);
					
					if (ret_obj.url != '') {
						window.location = ret_obj.url;
					}
				}
			);
	    });
		
		$('.ie-export-post').click(function() {
			var params = {
				action : 'ie_execute',
				mode   : 'post',
				id     : $(this).attr("data-id")
			};
			
			$.post(
				obj.link,
				params,
				function(data){
					var ret_obj = $.parseJSON(data);
					$('#wpbody-content .wrap').prepend(ret_obj.msg);
					
					if (ret_obj.url != '') {
						window.location = ret_obj.url;
					}
				}
			);
		});
	});
})(jQuery);
