<?php
/**
 * Two images with description Block template.
 *
 * @param array $block The block settings and attributes.
 */

// Block parameters from ACF fields
$layout = get_field('layout') ?? 'immagine-destra';
if ($layout === 'immagine-destra') {
    $text_order_class = "order-1 pl-8 pr-8 lg:pr-0";
    $image_order_class = "order-2";
} else {
    $text_order_class = "order-2 pl-8 pr-8 lg:pl-0";
    $image_order_class = "order-1";
}
$immagine = get_field('immagine');
$titolo = get_field('titolo');
$descrizione = get_field('descrizione');
?>

<div class="ssnail-image-with-text grid gap-8 grid-cols-1 lg:grid-cols-12 <?php echo $layout; ?>">
    <div class="ssnail-text-container lg:col-span-4 pb-8 lg:pt-8 grid place-content-center <?php echo $text_order_class; ?>">
        <?php if ($titolo) { ?>
            <h2 class="ssnail-text-container-title mt-0 mb-4 font-titles uppercase text-tertiary font-light"><?php echo $titolo; ?></h2>
        <?php } ?>
        <?php if ($descrizione) { ?>
            <div class="ssnail-text-container-description"><?php echo $descrizione; ?></div>
        <?php } ?>
    </div>
    <div class="ssnail-image-side lg:col-span-8 <?php echo $image_order_class; ?>">
        <?php if ($immagine) { ?>
            <?php ssnail_acf_image_with_srcset(acf_field_or_name: $immagine, class: 'h-full w-full object-cover mt-0 mb-0'); ?>
        <?php } ?>
    </div>
</div>