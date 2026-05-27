<?php
/**
 * Slide Item Template for slider block
 *
 * @package ossigeno
 * @since 1.0.0
 *
 * @param array $args {
 *     Array of slide item variables.
 *
 *     @type string $titolo       The slide title.
 *     @type string $descrizione  The slide description or content.
 *     @type array  $immagine     The slide image array with 'url', 'alt', 'ID'.
 *     @type mixed  $link         The slide link, array with 'url', 'target', 'title' or string URL.
 *     @type string $altezza_slide The height of the slide (with CSS units).
 *     @type string $object_fit   The object-fit CSS value for the image.
 *     @type int    $slide_index  The index of the slide in the slider.
 * }
 */

// Extract variables from $args with fallbacks.
$titolo        = $args['titolo'] ?? '';
$descrizione   = $args['descrizione'] ?? '';
$immagine      = $args['immagine'] ?? false;
$link          = $args['link'] ?? false;
$altezza_slide = $args['altezza_slide'] ?? '400px';
$object_fit    = $args['object_fit'] ?? 'object-cover';
$slide_index   = $args['slide_index'] ?? 0;

// Normalize link format - it could be a string URL or an array with 'url' and 'target'.
if ( $link ) {
    if (is_string($link)) {
        $link = array(
            'url'    => $link,
            'target' => '',
            'title'  => $titolo ?? '',
        );
    }
} else {
    $link = array(
        'url'    => '',
        'target' => '',
        'title'  => '',
    );
}
?>
<div class="slide w-auto flex-shrink-0 snap-center transition-all duration-300 group"
    data-link-url="<?php echo esc_url( $link['url'] ?? '' ); ?>"
    data-link-target="<?php echo esc_attr( $link['target'] ?? '' ); ?>">
	<a href="<?php echo esc_url( $link['url'] ); ?>" class="block w-full h-full relative"
		<?php if ( isset( $link['target'] ) && $link['target'] ) { ?>target="<?php echo esc_attr( $link['target'] ); ?>"<?php } ?>
		onclick="event.preventDefault();">
        <div class="slide-content max-w-screen relative w-auto aspect-[1/1] lg:aspect-[16/9] flex flex-col md:flex-row items-center gap-8" style="height: <?php echo esc_attr( $altezza_slide ); ?>">
            <?php if ( $immagine ) { ?>
            <div class="slide-image relative inline-flex h-full w-full justify-center overflow-clip">
                <?php 
                // Use ssnail_acf_image_with_srcset for responsive images.
                $img_alt = is_array( $immagine ) ? ( $immagine['alt'] ?? '' ) : '';
                echo ssnail_acf_image_with_srcset( $immagine, 'large', $img_alt, 'h-full w-full ' . $object_fit . ' mt-0 mb-0 transition-transform duration-1000 scale-100 group-hover:scale-125' );
                ?>
            </div>
            <?php } ?>
            <div class="slide-text absolute z-10 bottom-0 left-0 right-0 px-8 pb-8 w-full h-48 flex flex-col justify-between gap-1">
                <?php if ( $titolo ) { ?>
                <h3 class="text-white text-4xl font-bold mt-0 mb-0 uppercase font-light line-clamp-2"><?php echo esc_html( $titolo ); ?></h3>
                <?php } ?>
                <?php if ( $descrizione ) { ?>
                <div class="slide-description text-white mt-0 mb-0 line-clamp-2"><?php echo wp_kses_post( $descrizione ); ?></div>
                <?php } ?>
            </div>
        </div>
	</a>
</div>