<?php
/**
 * Template Name: Homepage
 *
 * @package Ossigeno
 */

if ( function_exists( 'acf_form_head' ) ) {
	acf_form_head();
}
get_header();
?>
	<section id="primary">
		<main id="main">
			<?php
			the_content();
			?>
		</main><!-- #main -->
	</section><!-- #primary -->
<?php
get_footer();
