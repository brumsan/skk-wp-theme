<?php
/**
 * Heisenberg functions and definitions
 *
 * @package Heisenberg
 */

if ( ! isset( $content_width ) ) {
    $content_width = 640;
}

if ( ! function_exists( 'heisenberg_setup' ) ) :
    function heisenberg_setup() {
        load_theme_textdomain( 'heisenberg', get_template_directory() . '/languages' );

        add_theme_support( 'automatic-feed-links' );
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );
        set_post_thumbnail_size( 200, 200, true );

        register_nav_menus( array(
            'primary' => __( 'Primary Menu', 'heisenberg' ),
        ) );

        add_theme_support( 'html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'script',
            'style',
        ) );

        add_theme_support( 'post-formats', array(
            'aside', 'image', 'video', 'quote', 'link',
        ) );

        add_theme_support( 'custom-background', apply_filters( 'heisenberg_custom_background_args', array(
            'default-color' => 'ffffff',
            'default-image' => '',
        ) ) );
    }
endif;
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
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'heisenberg_widgets_init' );

/**
 * Enqueue styles & scripts with dynamic cache-busting
 */
function heisenberg_assets() {
    $css_file = WP_DEBUG ? '/assets/dist/css/app.css' : '/assets/dist/css/app.min.css';
    $js_file  = WP_DEBUG ? '/assets/dist/js/app.js'  : '/assets/dist/js/app.min.js';

    $css_ver = file_exists( get_template_directory() . $css_file ) 
        ? filemtime( get_template_directory() . $css_file ) 
        : '1.0.0';

    $js_ver  = file_exists( get_template_directory() . $js_file ) 
        ? filemtime( get_template_directory() . $js_file ) 
        : '1.0.0';

    // Styles
    wp_enqueue_style( 'heisenberg_styles', get_template_directory_uri() . $css_file, array(), $css_ver );

    // External dependencies (Säkerställ inbyggt jQuery)
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/assets/components/modernizr/modernizr.js', array(), '2.8.3', false );
    wp_enqueue_script( 'foundation-js', get_template_directory_uri() . '/assets/components/foundation/js/foundation.min.js', array( 'jquery' ), '5.5.3', true );

    // Main Compiled JS
    wp_enqueue_script( 'heisenberg_appjs', get_template_directory_uri() . $js_file, array( 'jquery', 'foundation-js' ), $js_ver, true );

    wp_enqueue_script( 'heisenberg-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '1.0.0', true );
    wp_enqueue_script( 'heisenberg-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '1.0.0', true );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }

    // Loadmore functionality on blog archive
    if ( is_home() ) {
        global $wp_query;

        wp_enqueue_script( 'loadmore', get_template_directory_uri() . '/js/loadmore.js', array( 'jquery' ), '1.0.0', true );

        $max   = $wp_query->max_num_pages;
        $paged = ( get_query_var( 'paged' ) > 1 ) ? get_query_var( 'paged' ) : 1;

        wp_localize_script( 'loadmore', 'pbd_alp', array(
            'startPage' => $paged,
            'maxPages'  => $max,
            'nextLink'  => next_posts( $max, false ),
        ) );
    }
}
add_action( 'wp_enqueue_scripts', 'heisenberg_assets' );

/**
 * Force HTTPS and core jQuery in wp-admin to avoid Mixed Content / 403 blocks
 */
function skk_fix_admin_jquery_mixed_content() {
    wp_deregister_script( 'jquery-cdn' );
    wp_enqueue_script( 'jquery' );
}
add_action( 'admin_enqueue_scripts', 'skk_fix_admin_jquery_mixed_content', 1 );

/**
 * Modular includes
 */
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/extras.php';
require get_template_directory() . '/inc/customizer.php';

if ( defined( 'JETPACK__VERSION' ) ) {
    require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Primary Menu Display Helper
 */
function display_primary_menu() {
    wp_nav_menu( array(
        'theme_location' => 'primary',
        'menu'           => 'Primary Menu',
        'container'      => false,
        'menu_class'     => 'top-bar-menu',
        'depth'          => 5,
        'fallback_cb'    => false,
        'walker'         => new top_bar_walker(),
    ) );
}

/**
 * Customized menu output for Foundation Top Bar (PHP 8 compatible)
 */
class top_bar_walker extends Walker_Nav_Menu {
    public function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args = array(), &$output = '' ) {
        $element->has_children = ! empty( $children_elements[ $element->ID ] );
        $element->classes[]    = ( $element->current || $element->current_item_ancestor ) ? 'active' : '';
        $element->classes[]    = ( $element->has_children ) ? 'has-dropdown not-click' : '';
        parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    }

    public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
        $item_html = '';
        parent::start_el( $item_html, $data_object, $depth, $args, $current_object_id );
        
        $output .= ( 0 === $depth ) ? '<li class="divider"></li>' : '';
        $classes = empty( $data_object->classes ) ? array() : (array) $data_object->classes;

        if ( in_array( 'label', $classes, true ) ) {
            $output    .= '<li class="divider"></li>';
            $item_html  = preg_replace( '/<a[^>]*>(.*)<\/a>/iU', '<label>$1</label>', $item_html );
        }

        if ( in_array( 'divider', $classes, true ) ) {
            $item_html = preg_replace( '/<a[^>]*>( .* )<\/a>/iU', '', $item_html );
        }

        $output .= $item_html;
    }

    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $output .= "\n<ul class=\"sub-menu dropdown\">\n";
    }
}

/**
 * Safe Custom Excerpt Generator (PHP 8 & null safe)
 */
function get_excerpt( $post = null ) {
    $post_obj = get_post( $post );

    if ( ! $post_obj ) {
        return '';
    }

    $excerpt = $post_obj->post_content;
    $excerpt = strip_shortcodes( $excerpt );
    $excerpt = preg_replace( '~(\[.*?\])~', '', $excerpt );
    $excerpt = wp_strip_all_tags( $excerpt );

    if ( mb_strlen( $excerpt ) > 275 ) {
        $excerpt    = mb_substr( $excerpt, 0, 275 );
        $last_space = mb_strrpos( $excerpt, ' ' );
        if ( false !== $last_space ) {
            $excerpt = mb_substr( $excerpt, 0, $last_space );
        }
        return trim( preg_replace( '/\s+/', ' ', $excerpt ) ) . '...';
    }

    return $excerpt;
}