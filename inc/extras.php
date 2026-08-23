<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * @package Heisenberg
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function heisenberg_body_classes( $classes ) {
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	return $classes;
}
add_filter( 'body_class', 'heisenberg_body_classes' );

if ( version_compare( $GLOBALS['wp_version'], '4.1', '<' ) ) :
	/**
	 * Filters wp_title to print a neat <title> tag based on what is being viewed.
	 */
	function heisenberg_wp_title( $title, $sep ) {
		if ( is_feed() ) {
			return $title;
		}

		global $page, $paged;

		$title .= get_bloginfo( 'name', 'display' );

		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) ) {
			$title .= " $sep $site_description";
		}

		if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
			$title .= " $sep " . sprintf( __( 'Page %s', 'heisenberg' ), max( $paged, $page ) );
		}

		return $title;
	}
	add_filter( 'wp_title', 'heisenberg_wp_title', 10, 2 );

	function heisenberg_render_title() {
		?>
		<title><?php wp_title( '|', true, 'right' ); ?></title>
		<?php
	}
	add_action( 'wp_head', 'heisenberg_render_title' );
endif;