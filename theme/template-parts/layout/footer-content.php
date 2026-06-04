<?php
/**
 * Template part for displaying the footer content
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Ossigeno
 */

$ssnail_phone   = function_exists( 'get_field' ) ? get_field( 'ssnail_opt_phone', 'option' ) : '';
$ssnail_email   = function_exists( 'get_field' ) ? get_field( 'ssnail_opt_email', 'option' ) : '';
$ssnail_pec     = function_exists( 'get_field' ) ? get_field( 'ssnail_opt_pec', 'option' ) : '';
$ssnail_offices = function_exists( 'get_field' ) ? get_field( 'ssnail_opt_offices', 'option' ) : array();
$ssnail_address = ! empty( $ssnail_offices[0]['address'] ) ? $ssnail_offices[0]['address'] : '';

$ssnail_contact_parts = array();
if ( $ssnail_address ) {
	$ssnail_contact_parts[] = esc_html( $ssnail_address );
}
if ( $ssnail_phone ) {
	$ssnail_contact_parts[] = 'Tel: ' . esc_html( $ssnail_phone );
}
if ( $ssnail_email ) {
	$ssnail_contact_parts[] = 'E-mail: ' . esc_html( $ssnail_email );
}
if ( $ssnail_pec ) {
	$ssnail_contact_parts[] = 'PEC: ' . esc_html( $ssnail_pec );
}
?>

<footer id="colophon" class="bg-secondary text-background w-full pt-20 pb-10 border-t border-background/10">

	<div class="w-full grid grid-cols-1 md:grid-cols-4 gap-12 px-6 md:px-12 mb-16">

		<div>
			<?php if ( has_custom_logo() ) : ?>
				<div class="mb-4"><?php the_custom_logo(); ?></div>
			<?php endif; ?>
			<h3 class="text-lg font-headline font-bold text-background mb-4"><?php bloginfo( 'name' ); ?></h3>
			<p class="text-sm font-body text-background/60 leading-relaxed">
				<?php bloginfo( 'description' ); ?>
			</p>
		</div>

		<div>
			<h4 class="uppercase tracking-widest text-[10px] text-background/50 mb-6"><?php esc_html_e( 'Navigazione', 'ossigeno' ); ?></h4>
			<div class="footer-nav">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer-menu',
						'container'      => false,
						'menu_class'     => 'space-y-3 text-sm font-body',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</div>
		</div>

		<div>
			<h4 class="uppercase tracking-widest text-[10px] text-background/50 mb-6"><?php esc_html_e( 'Informazioni Legali', 'ossigeno' ); ?></h4>
			<div class="footer-nav">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer-legal',
						'container'      => false,
						'menu_class'     => 'space-y-3 text-sm font-body',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</div>
		</div>

		<div>
			<h4 class="uppercase tracking-widest text-[10px] text-background/50 mb-6"><?php esc_html_e( 'Seguici', 'ossigeno' ); ?></h4>
			<div class="flex space-x-4">
				<?php ssnail_render_social_menu( 'footer-social-icon', 'w-6 h-6' ); ?>
			</div>
		</div>

	</div>

	<div class="w-full px-6 md:px-12 pt-8 border-t border-background/10 flex flex-col md:flex-row justify-between items-center gap-4">
		<div class="flex flex-col gap-4">
			<span class="text-sm font-body text-background/60">
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. All Rights Reserved.
			</span>
			<?php if ( $ssnail_contact_parts ) : ?>
				<p class="text-sm text-background/60">
					<?php echo implode( ' | ', $ssnail_contact_parts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- each part is individually escaped above ?>
				</p>
			<?php endif; ?>
		</div>
	</div>

</footer><!-- #colophon -->
