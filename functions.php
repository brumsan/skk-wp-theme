<?php
/**
 * Heisenberg functions and definitions
 *
 * @package Heisenberg
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

if ( ! function_exists( 'heisenberg_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function heisenberg_setup() {

	/*
	 * Make theme available for translation.
	 */
	load_theme_textdomain( 'heisenberg', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 */
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 200, 200, true );

	// Register primary menu location
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'heisenberg' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, etc.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'heisenberg_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // heisenberg_setup
add_action( 'after_setup_theme', 'heisenberg_setup' );

/**
 * Register widget area.
 */
function heisenberg_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'heisenberg' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'heisenberg_widgets_init' );

/**
 * Enqueue styles.
 */
if ( ! function_exists( 'heisenberg_styles' ) ) :

	function heisenberg_styles() {

		if ( WP_DEBUG ) :
			wp_enqueue_style( 'heisenberg_styles', get_stylesheet_directory_uri() . '/assets/dist/css/app.css', '', '9' );
		else :
			wp_enqueue_style( 'heisenberg_styles', get_stylesheet_directory_uri() . '/assets/dist/css/app.min.css', '', '9' );
		endif;

	}

	add_action( 'wp_enqueue_scripts', 'heisenberg_styles' );

endif;

/**
 * Enqueue scripts.
 */
function heisenberg_scripts() {

	wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/assets/components/modernizr/modernizr.js', '', '', false );
	wp_enqueue_script( 'fastclick_js', get_template_directory_uri() . '/assets/components/fastclick/lib/fastclick.js', '', '', true );
	wp_enqueue_script( 'foundation-js', get_template_directory_uri() . '/assets/components/foundation/js/foundation.min.js', array( 'jquery' ), '5', true );

	if ( WP_DEBUG ) {
		wp_enqueue_script( 'heisenberg_appjs', get_template_directory_uri() . '/assets/dist/js/app.js', array( 'jquery' ), '', true );
	} else {
		wp_enqueue_script( 'heisenberg_appjs', get_template_directory_uri() . '/assets/dist/js/app.min.js', array( 'jquery' ), '', true );
	}

	wp_enqueue_script( 'heisenberg-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );
	wp_enqueue_script( 'heisenberg-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_home() ) {
		global $wp_query;

		$scriptsrc = get_template_directory_uri() . '/js/';

		wp_register_script( 'loadmore', $scriptsrc . 'loadmore.js', array( 'jquery' ) );
		wp_enqueue_script( 'loadmore' );

		$max   = $wp_query->max_num_pages;
		$paged = ( get_query_var( 'paged' ) > 1 ) ? get_query_var( 'paged' ) : 1;

		wp_localize_script( 'loadmore', 'pbd_alp', array(
			'startPage' => $paged,
			'maxPages'  => $max,
			'nextLink'  => next_posts( $max, false ),
		) );
	}
}
add_action( 'wp_enqueue_scripts', 'heisenberg_scripts' );

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

add_filter( 'wp_head', 'foundation_header' );

function foundation_header() {
	?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$(document).foundation();
		});
	</script>
	<?php
}

/**
 * Primary Menu
 */
function display_primary_menu() {
	wp_nav_menu( array(
		'theme_location' => 'primary',
		'menu'           => 'Primary Menu',
		'container'      => false,
		'container_class'=> '',
		'menu_class'     => 'top-bar-menu',
		'before'          => '',
		'after'           => '',
		'link_before'     => '',
		'link_after'      => '',
		'depth'           => 5,
		'fallback_cb'     => false,
		'walker'          => new top_bar_walker(),
	) );
}

/**
 * Customized menu output
 */
class top_bar_walker extends Walker_Nav_Menu {
	function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args = array(), &$output = '' ) {
		$element->has_children = ! empty( $children_elements[ $element->ID ] );
		$element->classes[]    = ( $element->current || $element->current_item_ancestor ) ? 'active' : '';
		$element->classes[]    = ( $element->has_children ) ? 'has-dropdown not-click' : '';
		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}

	function start_el( &$output, $object, $depth = 0, $args = array(), $current_object_id = 0 ) {
		$item_html = '';
		parent::start_el( $item_html, $object, $depth, $args );
		$output .= ( 0 == $depth ) ? '<li class="divider"></li>' : '';
		$classes = empty( $object->classes ) ? array() : (array) $object->classes;

		if ( in_array( 'label', $classes, true ) ) {
			$output    .= '<li class="divider"></li>';
			$item_html  = preg_replace( '/<a[^>]*>(.*)<\/a>/iU', '<label>$1</label>', $item_html );
		}

		if ( in_array( 'divider', $classes, true ) ) {
			$item_html = preg_replace( '/<a[^>]*>( .* )<\/a>/iU', '', $item_html );
		}

		$output .= $item_html;
	}

	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$output .= "\n<ul class=\"sub-menu dropdown\">\n";
	}
}

/**
 * Funktion som returnerar upp till 275 tecken och slutar pa ett helt ord "..."
 */
function get_excerpt() {
	$excerpt = get_the_content();
	$excerpt = strip_shortcodes( $excerpt );
	$excerpt = preg_replace( '~(\[.*?\])~', '', $excerpt );
	$excerpt = wp_strip_all_tags( $excerpt );

	if ( mb_strlen( $excerpt ) > 275 ) {
		$excerpt = mb_substr( $excerpt, 0, 275 );
		$last_space = mb_strrpos( $excerpt, ' ' );
		if ( false !== $last_space ) {
			$excerpt = mb_substr( $excerpt, 0, $last_space );
		}
		$excerpt = trim( preg_replace( '/\s+/', ' ', $excerpt ) );
		return $excerpt . '...';
	}

	return $excerpt;
}