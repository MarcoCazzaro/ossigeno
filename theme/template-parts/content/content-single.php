<?php

/**
 * Template part for displaying single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Ossigeno
 */

defined('ABSPATH') || exit;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(['ssnail-article', 'single', 'grid', 'gap-6', 'mb-6']); ?>>

	<header class="entry-header">
		<?php ssnail_post_categories(); ?>
		<?php the_title('<h1 class="entry-title text-4xl my-4">', '</h1>'); ?>


		<?php if (!is_page()) : ?>
			<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
				<div class="entry-meta flex gap-4">
					<?php
					ssnail_posted_by();
					ssnail_posted_on();
					?>
				</div><!-- .entry-meta -->
				<?php ssnail_share_links(''); ?>
			</div>
		<?php else : ?>
			<div class="flex items-center justify-end">
				<?php ssnail_share_links(''); ?>
			</div>
		<?php endif; ?>
	</header><!-- .entry-header -->

	<?php ssnail_post_thumbnail(); ?>

	<div <?php ssnail_content_class('entry-content'); ?>>
		<?php
		the_content(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers. */
					__('Continue reading<span class="sr-only"> "%s"</span>', 'ossigeno'),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			)
		);

		wp_link_pages(
			array(
				'before' => '<div>' . __('Pages:', 'ossigeno'),
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer border p-4 rounded-lg relative overflow-clip gap-4">
		<div class="absolute top-0 left-0 w-full h-full bg-primary opacity-20 z-0"></div>
		<div class="relative w-full flex flex-wrap gap-4 items-center">
			<?php ssnail_entry_footer(); ?>
		</div>
		<?php ssnail_share_links(''); ?>
	</footer><!-- .entry-footer -->

</article><!-- #post-${ID} -->