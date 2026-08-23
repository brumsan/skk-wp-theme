<?php
/**
 * The template for displaying search results pages.
 *
 * @package Heisenberg
 */

get_header(); ?>

<div class="row"><!-- .row start -->

	<div class="medium-8 small-12 columns"><!-- .columns start -->

		<section id="primary" class="content-area">
			<main id="main" class="site-main" role="main">

			<?php if ( have_posts() ) : ?>

				<header class="page-header">
					<h1 class="page-title"><?php printf( __( 'Sökresultat för: %s', 'heisenberg' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
				</header><!-- .page-header -->

				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>

					<?php
					/**
					 * Run the loop for the search to output the results.
					 * If you want to overload this in a child theme then include a file
					 * called content-search.php and that will be used instead.
					 */
					get_template_part( 'page-templates/partials/content', 'search' );
					?>

				<?php endwhile; ?>

				<?php the_posts_navigation(); ?>

			<?php else : ?>

				<?php get_template_part( 'page-templates/partials/content', 'none' ); ?>

			<?php endif; ?>

			</main><!-- #main -->
		</section><!-- #primary -->
	</div>
	<div class="medium-4 small-12 columns"><!-- .columns start -->

		<?php get_sidebar(); ?>

	</div><!-- .columns end -->
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
