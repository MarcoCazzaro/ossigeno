<?php

/**
 * Template part for displaying pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Ossigeno
 */

defined('ABSPATH') || exit;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(['ssnail-article', 'page']); ?>>

	<header class="entry-header">
		<?php
		if (!is_front_page()) {
			the_title('<h1 class="entry-title">', '</h1>');
		} else {
			the_title('<h2 class="entry-title">', '</h2>');
		}
		?>
	</header><!-- .entry-header -->

	<?php ssnail__post_thumbnail(); ?>

	<div <?php ssnail__content_class('entry-content mt-8'); ?>>
		<?php

		the_content();

		wp_link_pages(
			array(
				'before' => '<div>' . __('Pages:', 'ossigeno'),
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->

	<?php if (get_edit_post_link()) : ?>
		<footer class="entry-footer">
			<?php
			edit_post_link(
				sprintf(
					wp_kses(
						/* translators: %s: Name of current post. Only visible to screen readers. */
						__('Edit <span class="sr-only">%s</span>', 'ossigeno'),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					get_the_title()
				)
			);
			?>
		</footer><!-- .entry-footer -->
	<?php endif; ?>

</article><!-- #post-<?php the_ID(); ?> -->