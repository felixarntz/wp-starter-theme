( function( $, wp, theme ) {

	if ( 'undefined' === theme ) {
		console.error( 'Missing WP theme object. Aborting...' );
		return;
	}

	theme.init = function() {
		//TODO: do something useful here
		console.log( 'success!' );
	};

	theme.init();

}( jQuery, wp, wp_theme ) );
