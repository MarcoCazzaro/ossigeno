<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no `home.php` file exists.
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

			<?php if ( is_home() && ! is_front_page() ) : ?>
				<header class="page-header ssnail-container mb-16">
					<h1 class="page-title"><?php single_post_title(); ?></h1>
				</header><!-- .page-header -->
			<?php endif; ?>

			<div class="ssnail-container">
				<div class="ssnail-posts grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-8 gap-y-16 mb-16">
					<?php
					while ( have_posts() ) {
						the_post();
						get_template_part( 'template-parts/content/content', 'excerpt' );
					}
					?>
				</div>
				<?php ssnail_the_posts_navigation(); ?>
			</div>

		<?php else : ?>

			<?php get_template_part( 'template-parts/content/content', 'none' ); ?>

		<?php endif; ?>

		</main><!-- #main -->
	</section><!-- #primary -->

<?php
get_footer();
