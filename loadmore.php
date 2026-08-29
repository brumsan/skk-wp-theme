<?php
// Include wp-load 
define('WP_USE_THEMES', false);  
require_once('../../../wp-load.php');

// Variables from JS
$numPosts = isset($_GET['numPosts']) ? intval($_GET['numPosts']) : 5;
$page     = isset($_GET['pageNumber']) ? intval($_GET['pageNumber']) : 1;
$category = isset($_GET['categoryName']) ? sanitize_text_field($_GET['categoryName']) : '';

$offset = $page * $numPosts;    

// The Query (använd array-syntax istället för query string för säkerhets skydd)
$args = array(
    'category_name'  => $category,
    'posts_per_page' => $numPosts,
    'offset'         => $offset,
    'post_status'    => 'publish'
);

$my_query = new WP_Query($args);

// The Loop
global $post; // MÅSTE deklareras för att setup_postdata ska fungera korrekt

if ($my_query->have_posts()) :
    while ($my_query->have_posts()) : $my_query->the_post();
        
        // Nu kommer hela partial-filen laddas exakt som på startsidan
        get_template_part( 'page-templates/partials/content', 'frontpage' );

    endwhile;
    wp_reset_postdata(); // Återställ global postdata
endif;
?>