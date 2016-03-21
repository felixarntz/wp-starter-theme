<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme;

function theme() {
	return Base\Theme::instance();
}

function add_image_size( $name, $width, $height, $crop = false, $selectable = false ) {
	Base\Util\Images::add_size( $name, $width, $height, $crop, $selectable );
}

function get_template_part( $slug, $data = array(), $cache = false ) {
	return Base\Util\Template::render( $slug, $data, $cache );
}

function get_header( $data = array(), $cache = false ) {
	return Base\Util\Template::render( 'header', $data, $cache );
}

function get_footer( $data = array(), $cache = false ) {
	return Base\Util\Template::render( 'footer', $data, $cache );
}

function get_sidebar( $data = array(), $cache = false ) {
	return Base\Util\Template::render( 'sidebar', $data, $cache );
}

function the_post_meta( $post = null ) {
	Base\Util\TemplateTags::the_post_meta( $post );
}

function get_the_post_meta( $post = null ) {
	return Base\Util\TemplateTags::get_the_post_meta( $post );
}

function the_post_date( $post = null ) {
	Base\Util\TemplateTags::the_post_date( $post );
}

function get_the_post_date( $post = null ) {
	return Base\Util\TemplateTags::get_the_post_date( $post );
}

function the_post_modified_date( $post = null ) {
	Base\Util\TemplateTags::the_post_modified_date( $post );
}

function get_the_post_modified_date( $post = null ) {
	return Base\Util\TemplateTags::get_the_post_modified_date( $post );
}

function human_time_diff( $compare, $current = '', $format = false, ) {
	return Base\Util\TemplateTags::human_time_diff( $compare, $current, $format );
}

function the_post_format( $post = null ) {
	Base\Util\TemplateTags::the_post_format( $post );
}

function get_the_post_format( $post = null ) {
	return Base\Util\TemplateTags::get_the_post_format( $post );
}

function the_comments_popup_link( $post = null ) {
	Base\Util\TemplateTags::the_comments_popup_link( $post );
}

function get_the_comments_popup_link( $post = null ) {
	return Base\Util\TemplateTags::get_the_comments_popup_link( $post );
}

function edit_post_link( $text = null, $before = '', $after = '', $id = 0, $class = 'post-edit-link' ) {
	Base\Util\TemplateTags::edit_link( $text, $before, $after, $id, $class );
}

function get_post_type_object( $post_type = null ) {
	return Base\Util\TemplateTags::get_post_type_object( $post_type );
}

function wp_list_comments( $args = array() ) {
	return Base\Util\BootstrapComments::wp_list_comments( $args );
}

function comment_form( $args = array() ) {
	Base\Util\BootstrapComments::comment_form( $args );
}

function get_the_post_navigation( $args = array() ) {
	return Base\Util\BootstrapContentNavigation::get_the_post_navigation( $args );
}

function the_post_navigation( $args = array() ) {
	echo Base\Util\BootstrapContentNavigation::get_the_post_navigation( $args );
}

function get_the_posts_navigation( $args = array() ) {
	return Base\Util\BootstrapContentNavigation::get_the_posts_navigation( $args );
}

function the_posts_navigation( $args = array() ) {
	echo Base\Util\BootstrapContentNavigation::get_the_posts_navigation( $args );
}

function get_the_posts_pagination( $args = array() ) {
	return Base\Util\BootstrapContentNavigation::get_the_posts_pagination( $args );
}

function the_posts_pagination( $args = array() ) {
	echo Base\Util\BootstrapContentNavigation::get_the_posts_pagination( $args );
}

function get_the_comments_navigation( $args = array() ) {
	return Base\Util\BootstrapContentNavigation::get_the_comments_navigation( $args );
}

function the_comments_navigation( $args = array() ) {
	echo Base\Util\BootstrapContentNavigation::get_the_comments_navigation( $args );
}

function get_the_comments_pagination( $args = array() ) {
	return Base\Util\BootstrapContentNavigation::get_the_comments_pagination( $args );
}

function the_comments_pagination( $args = array() ) {
	echo Base\Util\BootstrapContentNavigation::get_the_comments_pagination( $args );
}

function wp_link_pages( $args = array() ) {
	echo Base\Util\BootstrapContentNavigation::wp_link_pages( $args );
}

function wp_nav_menu( $args = array() ) {
	return Base\Util\BootstrapNavMenu::render( $args );
}
