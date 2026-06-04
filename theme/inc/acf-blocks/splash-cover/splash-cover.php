<?php
/**
 * Splash Cover Block template.
 *
 * @param array $block The block settings and attributes.
 */

// Get ACF fields
$tipo_cover = get_field('tipo_cover');
$altezza = get_field('altezza');
$immagine = get_field('immagine');
$video_desktop = get_field('video_desktop');
$video_mobile = get_field('video_mobile');
$titolo = get_field('titolo');
$sottotitolo = get_field('sottotitolo');

// Set default height if not provided
if (!$altezza) {
    $altezza = '400px';
} else {
    $altezza .= 'px';
}

// Create inline style for height
$style = 'style="height: ' . esc_attr($altezza) . ';"';

// Set block class based on content type
$block_class = 'ssnail-splash-cover overflow-clip relative w-full bg-secondary/10';
if ($tipo_cover) {
    $block_class .= ' ' . esc_attr($tipo_cover);
}
?>

<div class="<?php echo esc_attr($block_class); ?>" <?php echo $style; ?>>
    <div class="ssnail-splash-cover__image-container absolute inset-0 h-full w-full">
        <?php ssnail_acf_image_with_srcset(acf_field_or_name: $immagine, class: 'h-full w-full object-cover mt-0 mb-0'); ?>
    </div>
    <?php if ($tipo_cover === 'video' && ($video_desktop || $video_mobile)) { ?>
        <div class="ssnail-splash-cover__video-container absolute inset-0 h-full w-full opacity-0 transition-opacity duration-300" data-video-url-desktop="<?php echo esc_url($video_desktop); ?>" data-video-url-mobile="<?php echo esc_url($video_mobile); ?>">
            <div class="w-full h-full">
                <div class="relative progress h-full w-full">
                    <div class="relative progress-bar h-full bg-secondary/10" style="width: 0;"></div>
                    <label class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-10 text-background text-[20vw] uppercase font-titles font-light">Loading</label>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php
    /* Rev 2: rimosso
    if ($titolo || $sottotitolo) {
        ?>
        <span aria-hidden="true" class="absolute inset-0 h-full w-full" style="background:linear-gradient(180deg,rgba(0,0,0,0) 0%,rgba(0,0,0,0) 80%,var(--wp--preset--color--tertiary) 95%)"></span>
        <?php
    }
    */
    ?>
    <?php if ($titolo || $sottotitolo) { ?>
    <div class="ssnail-titles-wrapper opacity-0 transition-opacity absolute bottom-0 w-full px-4 py-6">
        <div class="max-w-7xl mx-auto space-y-0">
            <?php if ($titolo) { ?>
            <h2 class="entry-title text-center font-titles !text-7xl xl:!text-[103px] font-light leading-tight text-white uppercase"><?php echo $titolo; ?></h2>
            <?php } ?>
            
            <?php if ($sottotitolo) { ?>
            <p class="text-center !text-2xl font-light tracking-wider mt-2 text-white uppercase"><?php echo $sottotitolo; ?></p>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
</div>