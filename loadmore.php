<?php

	// Include wp-load 
	define('WP_USE_THEMES', false);  
	require_once('../../../wp-load.php');

	//Variables from JS
	if( isset($_GET['numPosts']) ) { 
		$numPosts = $_GET['numPosts']; 
	} else { $numPosts = 5; }

	if( isset($_GET['pageNumber']) ) {
		$page = $_GET['pageNumber'];
	} else { $page = 1; }

	if( isset($_GET['categoryName']) ) {
		$category = $_GET['categoryName'];
	} else { $category = ''; }

	$offset = $page * $numPosts;	

	//The Query			
	$my_query = new WP_Query('category_name='.$category.'&posts_per_page='.$numPosts.'&offset='.$offset);

	$posts = $my_query->get_posts();
	
	//The Loop
	foreach ($posts as $post) {
			setup_postdata( $post );
			get_template_part( 'page-templates/partials/content', 'frontpage' );
	}

?>				