<?php
/**
 * Custom template tags for this theme.
 *
 * @package Heisenberg
 */

if ( ! function_exists( 'the_posts_navigation' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 */
function the_posts_navigation() {
	if ( isset( $GLOBALS['wp_query']->max_num_pages ) && $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}
	?>
	<nav class="navigation posts-navigation" role="navigation">
		<h2 class="screen-reader-text"><?php _e( 'Posts navigation', 'heisenberg' ); ?></h2>
		<div class="nav-links">

			<?php if ( get_next_posts_link() ) : ?>
			<div class="nav-previous"><?php next_posts_link( __( 'Older posts', 'heisenberg' ) ); ?></div>
			<?php endif; ?>

			<?php if ( get_previous_posts_link() ) : ?>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts', 'heisenberg' ) ); ?></div>
			<?php endif; ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'the_post_navigation' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 */
function the_post_navigation() {
	$post = get_post();
	$previous = ( is_attachment() && $post && isset( $post->post_parent ) ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}
	?>
	<nav class="navigation post-navigation" role="navigation">
		<h2 class="screen-reader-text"><?php _e( 'Post navigation', 'heisenberg' ); ?></h2>
		<div class="nav-links">
			<?php
				previous_post_link( '<div class="nav-previous">%link</div>', '%title' );
				next_post_link( '<div class="nav-next">%link</div>', '%title' );
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'heisenberg_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function heisenberg_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = sprintf(
		'<p>' . $time_string . '</p>'
	);

	$author_id = get_the_author_meta( 'ID' );
	$author_link = $author_id ? get_author_posts_url( $author_id ) : '#';

	$byline = sprintf(
		_x( 'by %s', 'post author', 'heisenberg' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( $author_link ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);

	echo '<span class="posted-on">' . $posted_on . '</span>';
}
endif;

if ( ! function_exists( 'heisenberg_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function heisenberg_entry_footer() {
	if ( 'post' == get_post_type() ) {
		$tags_list = get_the_tag_list( '', __( ', ', 'heisenberg' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">' . __( 'Tagged %1$s', 'heisenberg' ) . '</span>', $tags_list );
		}
		if ( mb_strlen( get_the_content() ) > 275 && is_home() ) {
			echo '<a id="more" href="' . get_the_permalink() . '">Läs mer ></a>';
		}
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		comments_popup_link( __( 'Leave a comment', 'heisenberg' ), __( '1 Comment', 'heisenberg' ), __( '% Comments', 'heisenberg' ) );
		echo '</span>';
	}

	edit_post_link( __( 'Edit', 'heisenberg' ), '<span class="edit-link">', '</span>' );
}
endif;

if ( ! function_exists( 'the_archive_title' ) ) :
/**
 * Display the archive title based on the queried object.
 */
function the_archive_title( $before = '', $after = '' ) {
	if ( is_category() ) {
		$title = sprintf( __( 'Category: %s', 'heisenberg' ), single_cat_title( '', false ) );
	} elseif ( is_tag() ) {
		$title = sprintf( __( 'Tag: %s', 'heisenberg' ), single_tag_title( '', false ) );
	} elseif ( is_author() ) {
		$title = sprintf( __( 'Author: %s', 'heisenberg' ), '<span class="vcard">' . get_the_author() . '</span>' );
	} elseif ( is_year() ) {
		$title = sprintf( __( 'Year: %s', 'heisenberg' ), get_the_date( _x( 'Y', 'yearly archives date format', 'heisenberg' ) ) );
	} elseif ( is_month() ) {
		$title = sprintf( __( 'Month: %s', 'heisenberg' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'heisenberg' ) ) );
	} elseif ( is_day() ) {
		$title = sprintf( __( 'Day: %s', 'heisenberg' ), get_the_date( _x( 'F j, Y', 'daily archives date format', 'heisenberg' ) ) );
	} elseif ( is_tax( 'post_format' ) ) {
		if ( is_tax( 'post_format', 'post-format-aside' ) ) {
			$title = _x( 'Asides', 'post format archive title', 'heisenberg' );
		} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
			$title = _x( 'Galleries', 'post format archive title', 'heisenberg' );
		} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
			$title = _x( 'Images', 'post format archive title', 'heisenberg' );
		} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
			$title = _x( 'Videos', 'post format archive title', 'heisenberg' );
		} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
			$title = _x( 'Quotes', 'post format archive title', 'heisenberg' );
		} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
			$title = _x( 'Links', 'post format archive title', 'heisenberg' );
		} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
			$title = _x( 'Statuses', 'post format archive title', 'heisenberg' );
		} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
			$title = _x( 'Audio', 'post format archive title', 'heisenberg' );
		} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
			$title = _x( 'Chats', 'post format archive title', 'heisenberg' );
		}
	} elseif ( is_post_type_archive() ) {
		$title = sprintf( __( 'Archives: %s', 'heisenberg' ), post_type_archive_title( '', false ) );
	} elseif ( is_tax() ) {
		$queried = get_queried_object();
		if ( $queried && isset( $queried->taxonomy ) ) {
			$tax = get_taxonomy( $queried->taxonomy );
			$tax_name = ( $tax && isset( $tax->labels->singular_name ) ) ? $tax->labels->singular_name : '';
			$title = sprintf( __( '%1$s: %2$s', 'heisenberg' ), $tax_name, single_term_title( '', false ) );
		} else {
			$title = __( 'Archives', 'heisenberg' );
		}
	} else {
		$title = __( 'Archives', 'heisenberg' );
	}

	$title = apply_filters( 'get_the_archive_title', $title );

	if ( ! empty( $title ) ) {
		echo $before . $title . $after;
	}
}
endif;

if ( ! function_exists( 'the_archive_description' ) ) :
/**
 * Display category, tag, or term description.
 */
function the_archive_description( $before = '', $after = '' ) {
	$description = apply_filters( 'get_the_archive_description', term_description() );

	if ( ! empty( $description ) ) {
		echo $before . $description . $after;
	}
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 */
function heisenberg_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'heisenberg_categories' ) ) ) {
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			'number'     => 2,
		) );

		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'heisenberg_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Flush out the transients used in heisenberg_categorized_blog.
 */
function heisenberg_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	delete_transient( 'heisenberg_categories' );
}
add_action( 'edit_category', 'heisenberg_category_transient_flusher' );
add_action( 'save_post',     'heisenberg_category_transient_flusher' );