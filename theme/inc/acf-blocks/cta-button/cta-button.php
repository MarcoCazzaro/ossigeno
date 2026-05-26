<?php
/**
 * CTA Button block template.
 *
 * Renders an ACF link as a styled button using the project's btn utilities.
 *
 * @param array $block The block settings and attributes.
 */

$link = get_field( 'link' );
if ( ! $link || empty( $link['url'] ) ) {
	return;
}

$variante    = get_field( 'variante' ) ?: 'btn-primary';
$grande      = get_field( 'grande' );
$bordi_netti = get_field( 'bordi_netti' );

$classes = 'btn ' . $variante;
if ( $grande ) {
	$classes .= ' btn-big';
}
if ( $bordi_netti ) {
	$classes .= ' btn-sharp';
}

$align_map = [
	'left'   => 'text-left',
	'center' => 'text-center',
	'right'  => 'text-right',
];
$align       = $block['align'] ?? '';
$wrapper_class = ! empty( $align_map[ $align ] ) ? $align_map[ $align ] : '';
?>
<div<?php echo $wrapper_class ? ' class="' . esc_attr( $wrapper_class ) . '"' : ''; ?>>
	<a
		href="<?php echo esc_url( $link['url'] ); ?>"
		class="<?php echo esc_attr( $classes ); ?>"
		<?php echo ! empty( $link['target'] ) ? 'target="' . esc_attr( $link['target'] ) . '"' : ''; ?>
	>
		<?php echo esc_html( $link['title'] ); ?>
	</a>
</div>
