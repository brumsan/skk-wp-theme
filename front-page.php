<?php
/**
 * The front-page.php template file.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Heisenberg
 */

get_header(); ?>

<div class="row"><!-- .row start  data-equalizer-->

	<div class="medium-8 small-12 columns"><!-- .columns start data-equalizer-watch-->

		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">

			<?php if ( have_posts() ) : ?>

				<?php
				//looping nyheter
				$my_query = new WP_Query('category_name=nyheter&posts_per_page=10');
				while ($my_query->have_posts()) : $my_query->the_post(); ?>

					<?php
						/* Include the Post-Format-specific template for the content.
						 * If you want to override this in a child theme, then include a file
						 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
						 */
						get_template_part( 'page-templates/partials/content', 'frontpage' );
					?>

				<?php endwhile; ?>

			<?php else : ?>

				<?php get_template_part( 'page-templates/partials/content', 'none' ); ?>

			<?php endif; ?>

			</main><!-- #main -->
		</div><!-- #primary -->
		<p id="skk-load-posts"><a href="#">Ladda fler inlägg</a></p><!--load more-->

	</div><!-- .columns end -->

	<div class="medium-4 small-12 columns"><!-- .columns start data-equalizer-watch-->

		<?php get_sidebar(); ?>

	</div><!-- .columns end -->

</div><!-- .row end -->

<?php get_footer(); ?>
