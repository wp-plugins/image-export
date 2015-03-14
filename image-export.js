(function($) {
	$(document).ready(function() {
		$('#post-query-submit').parent('div').append('<button type="button" id="ie-excute" class="button button-primary">' + message.m001 + '</button>');
		
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
	});
})(jQuery);
