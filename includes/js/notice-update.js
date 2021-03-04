jQuery(document).on( 'click', '.honeypot4cf7-notice .notice-dismiss', function() {

	jQuery.ajax({
		url: ajaxurl,
		data: {
			action: 'honeypot4cf7_dismiss_notice'
		}
	})

})