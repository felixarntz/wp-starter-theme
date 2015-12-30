<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme;

function theme() {
	return Base\Theme::instance();
}

function get_template_part( $slug, $args = array(), $echo = true, $cache = true ) {
	return Base\Util\Template::render( $slug, $args, $echo, $cache );
}
