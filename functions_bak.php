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
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function heisenberg_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Heisenberg, use a find and replace
	 * to change 'heisenberg' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'heisenberg', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	//add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'heisenberg' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
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
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
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

if ( !function_exists( 'heisenberg_styles' ) ) :

	function heisenberg_styles() {

		// Conditional css for debug or production mode

		if ( WP_DEBUG ) :

			// Enqueue our debug stylesheet [development mode - non-minified]
			wp_enqueue_style( 'heisenberg_styles', get_stylesheet_directory_uri() . '/assets/dist/css/app.css', '', '9' );

		else :

			// Enqueue our minified stylesheet [production mode - minified stylesheet]
			wp_enqueue_style( 'heisenberg_styles', get_stylesheet_directory_uri() . '/assets/dist/css/app.min.css', '', '9' );

		endif;


	}

add_action( 'wp_enqueue_scripts', 'heisenberg_styles' );

endif;

/**
 * Enqueue scripts.
 */
function heisenberg_scripts() {

	// Add modernizer.js for shimming HTML5 elements that older browsers may not detect and for mobile detection
	wp_enqueue_script ( 'modernizr', get_template_directory_uri() . '/assets/components/modernizr/modernizr.js', '', '', false );

	// Add fastclick.js file to footer (for help with devices with touch UIs)
	wp_enqueue_script ( 'fastclick_js', get_template_directory_uri() . '/assets/components/fastclick/lib/fastclick.js', '', '', true );

	// Add core Foundation js to footer
	wp_enqueue_script( 'foundation-js', get_template_directory_uri() . '/assets/components/foundation/js/foundation.min.js', array( 'jquery' ), '5', true );

	// Add our concatenated js file
	if ( WP_DEBUG ) {

		// Enqueue our full version if in development mode
		wp_enqueue_script( 'heisenberg_appjs', get_template_directory_uri() . '/assets/dist/js/app.js', array( 'jquery' ), '', true );

	} else {

		// Enqueue minified js if in production mode
		wp_enqueue_script( 'heisenberg_appjs', get_template_directory_uri() . '/assets/dist/js/app.min.js', array( 'jquery' ), '', true );
	}

	wp_enqueue_script( 'heisenberg-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );

	wp_enqueue_script( 'heisenberg-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	//enqueue ajax script if page is front page
	if ( is_home() ) {

		global $wp_query;

	    $scriptsrc = get_template_directory_uri() . '/js/';  
	    
	    wp_register_script( 'loadmore', $scriptsrc . 'loadmore.js', array( 'jquery' ) );
	    wp_enqueue_script( 'loadmore');
	    
	    // Antal maxsidor och nuvarande sida för Ajax-laddning
	    $max = $wp_query->max_num_pages;
	    $paged = ( get_query_var('paged') > 1 ) ? get_query_var('paged') : 1;

	    // Addera variabler för ajax-load.
	    wp_localize_script('loadmore', 'pbd_alp', array('startPage' => $paged, 'maxPages' => $max, 'nextLink' => next_posts($max, false)));

  	}

}
add_action( 'wp_enqueue_scripts', 'heisenberg_scripts' );

/**
 * Implement the Custom Header feature.
 */
//require get_template_directory() . '/inc/custom-header.php';

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

add_filter('wp_head','foundation_header');

function foundation_header(){
	?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$(document).foundation();
		});
	</script>
	<?php
}

/*
add_filter( 'wp_nav_menu', 'heisenberg_nav_menu', 10, 2 );

function heisenberg_nav_menu( $menu ){
	$menu = str_replace('current-menu-item', 'current-menu-item active', $menu);
	return $menu;
}
*/

// This theme uses wp_nav_menu() in one location.
register_nav_menus( array(
	'primary' => __( 'Primary Menu', 'theme_slug' ),
) );

/**
 * Primary Menu
 */
function display_primary_menu() {
	wp_nav_menu( array(
		'theme_location' => 'primary',
		'menu' => 'Primary Menu',
		'container' => false, // remove nav container
		'container_class' => '', // class of container
		'menu_class' => 'top-bar-menu', // adding custom nav class
		'before' => '', // before each link <a>
		'after' => '', // after each link </a>
		'link_before' => '', // before each link text
		'link_after' => '', // after each link text
		'depth' => 5, // limit the depth of the nav
		'fallback_cb' => false, // fallback function (see below)
		'walker' => new top_bar_walker()
	) );
}

/**
 * Customized menu output
 */
class top_bar_walker extends Walker_Nav_Menu {
	function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {
		$element->has_children = !empty( $children_elements[$element->ID] );
		$element->classes[] = ( $element->current || $element->current_item_ancestor ) ? 'active' : '';
		$element->classes[] = ( $element->has_children ) ? 'has-dropdown not-click' : '';
		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}
	function start_el( &$output, $object, $depth = 0, $args = array(), $current_object_id = 0 ) {
		$item_html = '';
		parent::start_el( $item_html, $object, $depth, $args );
		$output .= ( $depth == 0 ) ? '<li class="divider"></li>' : '';
		$classes = empty( $object->classes ) ? array() : (array) $object->classes;
		if ( in_array('label', $classes) ) {
			$output .= '<li class="divider"></li>';
			$item_html = preg_replace( '/<a[^>]*>(.*)<\/a>/iU', '<label>$1</label>', $item_html );
		}
		if ( in_array('divider', $classes) ) {
			$item_html = preg_replace( '/<a[^>]*>( .* )<\/a>/iU', '', $item_html );
		}
		$output .= $item_html;
	}
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$output .= "\n<ul class=\"sub-menu dropdown\">\n";
	}
}

if ( function_exists( 'add_theme_support' ) ) { 
add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 200, 200, true ); // default Post Thumbnail dimensions (cropped)
}

// Funktion som returnerar 275 char och slutar på ett "ord" + "..."
function get_excerpt(){
	$excerpt = get_the_content();
	if ( strlen($excerpt) > 275 ) {
		$excerpt = get_the_content();
		$excerpt = preg_replace(" (\[.*?\])",'',$excerpt);
		$excerpt = strip_shortcodes($excerpt);
		$excerpt = strip_tags($excerpt);
		$excerpt = substr($excerpt, 0, 275);
		$excerpt = substr($excerpt, 0, strripos($excerpt, " "));
		$excerpt = trim(preg_replace( '/\s+/', ' ', $excerpt));
		$excerpt = $excerpt.'...';
		return $excerpt;
	}
	else {
		return $excerpt;
	}
}
