(function($){
	$( '#search-submit' ).on( 'click', function(){
		var ajaxurl    = $(this).parent('#my_great_action_form').attr('data-ajaxurl');
		var searchText = $(this).prev('#search-text').val();
		var pluginPage = $(this).prevAll('input[name="plugin-page"]').val();
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				'action' : 'view_plugins_themes',
				'search' : searchText,
				'plugin-page' : pluginPage
			},
			success: function( response, status ){
				$('#the-list').append(response);
			}
		});
		return false;
	});
})(jQuery);
