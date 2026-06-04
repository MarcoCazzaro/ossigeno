<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Ossigeno
 */

get_header();
?>

	<section id="primary" class="ssnail-coverless-page">
		<main id="main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header ssnail-container mb-16">
				<?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
			</header><!-- .page-header -->

			<div class="ssnail-container">
				<div class="ssnail-posts grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-8 gap-y-16 mb-16">
					<?php
					// Start the Loop.
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/content/content', 'excerpt' );

						// End the loop.
					endwhile;
					?>
				</div>
			<?php
			// Previous/next page navigation.
			ssnail_the_posts_navigation();
			?>
			</div>
			<?php

		else :

			// If no content, include the "No posts found" template.
			get_template_part( 'template-parts/content/content', 'none' );

		endif;
		?>
		</main><!-- #main -->
	</section><!-- #primary -->

<?php
get_footer();
