<?php

/**
 * Template part for displaying post archives and search results
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Ossigeno
 */

defined('ABSPATH') || exit;
$layout = $args['layout'] ?? 'list';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(['ssnail-article', 'archive', 'grid', 'gap-6', 'group', $layout]); ?>>
	<div class="ssnail-image-wrapper">
		<?php ssnail_post_thumbnail(); ?>
	</div>
	<div class="ssnail-text-wrapper">
		<header class="entry-header">
			<?php
			if (is_sticky() && is_home() && !is_paged()) {
				printf('%s', esc_html_x('Featured', 'post', 'ossigeno'));
			}
			the_title(sprintf('<h2 class="entry-title group-hover:text-primary transition-colors"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h2>');
			?>
		</header><!-- .entry-header -->


		<footer class="entry-footer">
			<?php ssnail_entry_footer(); ?>
		</footer><!-- .entry-footer -->
	</div>
</article><!-- #post-${ID} -->