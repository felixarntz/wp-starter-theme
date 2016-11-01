<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme;

?>
	<footer id="footer" class="site-footer" role="contentinfo">

		<div class="container">

			<?php if ( has_nav_menu( 'social' ) ) : ?>
				<nav role="navigation">
					<?php wp_nav_menu( array( 'theme_location' => 'social', 'menu_class' => 'social-menu list-inline' ) ); ?>
				</nav>
			<?php endif; ?>

			<p class="copyright">
				&copy; <?php echo date( 'Y' ); ?>
				<a href="<?php bloginfo( 'url' ); ?>"><?php theme()->partials()->render_blogname(); ?></a>
			</p>

		</div>

	</footer><!-- #footer -->

<?php wp_footer(); ?>
</body>
</html>
