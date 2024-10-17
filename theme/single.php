<?php

/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Ossigeno
 */

defined('ABSPATH') || exit;
get_header();
?>

<section id="primary">
	<main id="main">

		<?php
		/* Start the Loop */
		while (have_posts()) :
			the_post();
			get_template_part('template-parts/content/content', 'single');

			if (is_singular('post')) {
				// Previous/next post navigation.
				the_post_navigation(
					array(
						'next_text' => '<div class="inline-flex items-center"><span aria-hidden="true">' . __('Next Post', 'mom') . '</span> ' .
							'<span class="sr-only">' . __('Next post:', 'mom') . '</span><span class="material-symbols-outlined">arrow_right_alt</span></div><br/>' .
							'<span>%title</span>',
						'prev_text' => '<div class="inline-flex items-center"><span class="material-symbols-outlined">arrow_left_alt</span><span aria-hidden="true">' . __('Previous Post', 'mom') . '</span> ' .
							'<span class="sr-only">' . __('Previous post:', 'mom') . '</span></div><br/>' .
							'<span>%title</span>',
					)
				);
			}

			// If comments are open, or we have at least one comment, load
			// the comment template.
			if (comments_open() || get_comments_number()) {
				comments_template();
			}

		// End the loop.
		endwhile;
		?>

	</main><!-- #main -->
</section><!-- #primary -->

<?php
get_footer();
