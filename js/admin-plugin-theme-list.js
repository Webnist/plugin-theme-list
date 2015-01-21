(function($){
	$( '#search-submit' ).on( 'click', function(){
		var lastTimeSearch = $(this).parent('#search-plugins-themes').attr('data-last');
		var ajaxurl        = $(this).parent('#search-plugins-themes').attr('data-ajaxurl');
		var searchText     = $(this).prev('#search-text').val();
		var pluginPage     = $(this).prevAll('input[name="plugin-page"]').val();

		if ( lastTimeSearch !== searchText ) {

			$(this).parent('#search-plugins-themes').attr( 'data-last', searchText );
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
		}
		return false;
	});
})(jQuery);
