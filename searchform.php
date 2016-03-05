<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme;

?>
<form action="<?php echo home_url( '/' ); ?>" method="get" class="form-inline">
	<fieldset>
		<div class="input-group">
			<label class="sr-only" for="search"><?php _e( 'Search', 'wp-starter-theme' ); ?></label>
			<input type="text" id="search" name="s" placeholder="<?php _e( 'Search', 'wp-starter-theme' ); ?>" value="<?php the_search_query(); ?>" class="form-control">
			<span class="input-group-btn">
				<button type="submit" class="btn btn-secondary"><?php _e( 'Search', 'wp-starter-theme' ); ?></button>
			</span>
		</div>
	</fieldset>
</form>
