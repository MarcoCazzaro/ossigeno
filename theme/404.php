<?php

/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Ossigeno
 */

defined('ABSPATH') || exit;
get_header();
?>

<section id="primary" class="relative w-full">
	<div class="absolute w-full h-full z-0 text-[300px] text-gray-100 font-bold grid place-content-center">404</div>
	<main id="main" class="relative z-1 w-full">
		<div>
			<header class="page-header">
				<h1 class="page-title"><?php esc_html_e('Page Not Found', 'ossigeno'); ?></h1>
			</header><!-- .page-header -->

			<div <?php ssnail_content_class('page-content'); ?>>
				<p><?php esc_html_e('This page could not be found. It might have been removed or renamed, or it may never have existed.', 'ossigeno'); ?></p>
				<?php get_search_form(); ?>
			</div><!-- .page-content -->
		</div>

	</main><!-- #main -->
</section><!-- #primary -->

<?php
get_footer();
