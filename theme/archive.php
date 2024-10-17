<?php

/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Ossigeno
 */

defined('ABSPATH') || exit;
get_header();
?>

<section id="primary">
	<main id="main">

		<?php if (have_posts()) : ?>

			<header class="page-header">
				<?php the_archive_title('<h1 class="page-title">', '</h1>'); ?>
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
					if ((get_query_var('paged') ?? 0) === 0) {
						switch ($counter) {
							case 1:
								$layout = 'tile';
								break;
							case 2:
							case 3:
								$layout = 'tile';
								$wrapper_class = 'md:cols-span-1';
								break;
							default:
								$layout = 'list';
								break;
						}
					}
				?>
					<div class="<?php echo $wrapper_class; ?>">
						<?php get_template_part('template-parts/content/content', 'archive', compact('layout')); ?>
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
			// If no content, include the "No posts found" template.
			get_template_part('template-parts/content/content', 'none');

		endif;
		?>
	</main><!-- #main -->
</section><!-- #primary -->

<?php
get_footer();
