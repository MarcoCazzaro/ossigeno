<?php
/**
 * Two images with description Block template.
 *
 * @param array $block The block settings and attributes.
 */

// Block parameters from ACF fields
$layout = get_field('layout') ?? 'immagine-grande-destra';
if ($layout === 'immagine-grande-destra') {
    $small_image_order_class = "order-1";
    $big_image_order_class = "order-2";
    $text_alignment_class = 'lg:text-right ml-8 mr-8 lg:ml-auto lg:mr-0';
} else {
    $small_image_order_class = "order-2";
    $big_image_order_class = "order-1";
    $text_alignment_class = 'lg:text-left ml-8 mr-8 lg:mr-auto lg:ml-0';
}
$immagine_piccola = get_field('immagine_piccola');
$immagine_grande = get_field('immagine_grande');
$descrizione = get_field('descrizione');
?>

<div class="ssnail-two-images-with-description grid gap-8 grid-cols-1 lg:grid-cols-12 <?php echo $layout; ?>">
    <div class="ssnail-small-image-side lg:col-span-4 <?php echo $small_image_order_class; ?>">
        <div class="grid gap-8 lg:grid-rows-4 h-full">
            <?php if ($descrizione) { ?>
                <div class="ssnail-small-image-side__description <?php echo $text_alignment_class; ?> font-light uppercase max-w-lg">
                    <?php echo $descrizione; ?>
                </div>
            <?php } ?>
            <?php if ($immagine_piccola) { ?>
                <div class="lg:row-span-3">
                    <?php ssnail_acf_image_with_srcset(acf_field_or_name: $immagine_piccola, class: 'h-full w-full object-cover mt-0 mb-0'); ?>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="ssnail-big-image-side lg:col-span-8 <?php echo $big_image_order_class; ?>">
        <?php if ($immagine_grande) { ?>
            <?php ssnail_acf_image_with_srcset(acf_field_or_name: $immagine_grande, class: 'h-full w-full object-cover mt-0 mb-0'); ?>
        <?php } ?>
    </div>
</div>