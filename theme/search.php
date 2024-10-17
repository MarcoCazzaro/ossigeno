<?php

/**
 * The template for displaying search results
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Ossigeno
 */

defined('ABSPATH') || exit;
get_header();
?>

<section id="primary" class="w-full">
	<main id="main">

		<?php if (have_posts()) : ?>

			<header class="page-header">
				<?php
				printf(
					/* translators: 1: search result title. 2: search term. */
					'<h1 class="page-title">%1$s <span>%2$s</span></h1>',
					esc_html__('Search results for:', 'ossigeno'),
					get_search_query()
				);
				?>
			</header><!-- .page-header -->
			<div class="ssnail-posts grid md:grid-cols-2 gap-12">
				<?php
				// Start the Loop.
				$counter = 0;
				while (have_posts()) :
					$counter++;
					the_post();
					$layout = 'list';
					$wrapper_class = "md:col-span-2";
				?>
					<div class="<?php echo $wrapper_class; ?>">
						<?php get_template_part('template-parts/content/content', 'search', compact('layout')); ?>
					</div>
				<?php
				// End the loop.
				endwhile;

				// Previous/next page navigation.
				ssnail_the_posts_navigation();
				?>
			</div>
		<?php

		else :

			// If no content is found, get the `content-none` template part.
			get_template_part('template-parts/content/content', 'none');

		endif;
		?>
	</main><!-- #main -->
</section><!-- #primary -->

<?php
get_footer();
