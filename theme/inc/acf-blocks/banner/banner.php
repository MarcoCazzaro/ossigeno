<?php
/**
 * Banner Block template.
 *
 * @param array $block The block settings and attributes.
 */

$immagine_desktop = get_field( 'immagine_desktop' );
$immagine_mobile  = get_field( 'immagine_mobile' );
$link             = get_field( 'link' );

if ( ! $immagine_desktop && ! $immagine_mobile ) {
	return;
}

$tag_open  = '';
$tag_close = '';
if ( $link && ! $is_preview ) {
	$target    = ! empty( $link['target'] ) ? ' target="' . esc_attr( $link['target'] ) . '"' : '';
	$tag_open  = '<a href="' . esc_url( $link['url'] ) . '"' . $target . ' class="block">';
	$tag_close = '</a>';
}
?>
<div class="ssnail-banner">
	<?php echo $tag_open; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<?php if ( $immagine_desktop ) :
		$src    = wp_get_attachment_image_src( $immagine_desktop, 'full' );
		$srcset = wp_get_attachment_image_srcset( $immagine_desktop, 'full' );
		$alt    = get_post_meta( $immagine_desktop, '_wp_attachment_image_alt', true );
		?>
		<img
			class="ssnail-banner-img-desktop hidden lg:block w-full"
			src="<?php echo esc_url( $src[0] ); ?>"
			<?php if ( $srcset ) : ?>
			srcset="<?php echo esc_attr( $srcset ); ?>"
			<?php endif; ?>
			sizes="100vw"
			alt="<?php echo esc_attr( $alt ); ?>"
			fetchpriority="high"
		>
	<?php endif; ?>
	<?php if ( $immagine_mobile ) :
		$src    = wp_get_attachment_image_src( $immagine_mobile, 'full' );
		$srcset = wp_get_attachment_image_srcset( $immagine_mobile, 'full' );
		$alt    = get_post_meta( $immagine_mobile, '_wp_attachment_image_alt', true );
		?>
		<img
			class="ssnail-banner-img-mobile lg:hidden w-full"
			src="<?php echo esc_url( $src[0] ); ?>"
			<?php if ( $srcset ) : ?>
			srcset="<?php echo esc_attr( $srcset ); ?>"
			<?php endif; ?>
			sizes="100vw"
			alt="<?php echo esc_attr( $alt ); ?>"
		>
	<?php endif; ?>
	<?php echo $tag_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
