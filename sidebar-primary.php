<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme;

?>
				<?php if ( is_active_sidebar( 'primary' ) ) : ?>
					<aside id="sidebar" class="col-md-3" role="complementary">
						<?php dynamic_sidebar( 'primary' ); ?>
					</aside>
				<?php endif; ?>
