<?php
/**
 * Insight Card Block template.
 *
 * @package Ossigeno
 * @subpackage ACF_Blocks
 * @param array $block The block settings and attributes.
 */

// Check if ACF functions are available
if ( ! function_exists( 'get_field' ) ) {
    return;
}

// Get field values
if ($args['use_post'] ?? false) {
    $stile = $args['stile'] ?? 'chiaro';
    $layout = $args['layout'] ?? 'verticale-immagine-testo';
    $titolo = get_the_title();
    $sottotitolo = get_the_date(); // Will be used as date
    $descrizione = strip_tags(get_the_excerpt());
    $immagine = 'from_post';
    $link = true;
    $link_url = get_the_permalink();
    $link_title = __('Read more', 'ossigeno');
    $link_target = "_self";
    $max_width_class = 'w-full';
    $post_type = get_post_type();
    $show_author = true;
} else {
    $stile = get_field('stile') ?: 'chiaro';
    $layout = get_field('layout') ?: 'verticale-immagine-testo';
    $titolo = get_field('titolo');
    $sottotitolo = get_field('sottotitolo'); // Will be used as date
    $descrizione = get_field('descrizione');
    $immagine = get_field('immagine');
    $link = get_field('link');
    if ($link) {
        $link_url = $link['url'];
        $link_title = $link['title'];
        $link_target = $link['target'];
    }
    $max_width_class = 'md:max-w-[400px]';
    $post_type = "manual";
    $show_author = false;
}

// Set style-based classes
$is_dark = $stile === 'scuro';

// Gradient backgrounds based on style
$gradient_bg = 'relative before:content-[\'\'] before:z-0 before:-mt-24 before:absolute before:h-24 before:top-0 before:w-full before:left-0 before:bg-gradient-to-t before:to-transparent ' . ($is_dark ? 'bg-tertiary before:from-tertiary' : 'bg-background-alt before:from-background-alt');
$text_color = ($is_dark) ? 'text-background' : 'text-foreground';
$title_color = ($is_dark) ? 'text-background-alt' : 'text-foreground';
$date_color = ($is_dark) ? 'text-background-alt' : 'text-foreground';

$show_image = (!str_contains($layout, 'solo-testo'));
$text_wrapper_classes = "-mt-8 min-h-[calc(33%+3rem)]";
$title_margin_top = "-mt-4";
$titolo_wrapper_classes = "md:h-24 flex flex-col justify-between";
$descrizione_wrapper_classes = "md:h-32";
if ($layout === 'orizzontale') {
    $card_classes = "relative overflow-hidden aspect-[1/2] lg:aspect-[3/2] flex flex-col gap-8 {$text_color} rounded-lg overflow-hidden shadow-lg transition-all duration-300 justify-end";
    $image_section_classes = "absolute top-0 left-0 h-[67%] w-full overflow-hidden";
    $inner_text_classes = "grid grid-cols-1 lg:grid-cols-2 gap-4";
    $descrizione_classes = "line-clamp-3 lg:line-clamp-5 text-sm lg:pt-6";
} else {
    $descrizione_classes = "line-clamp-3 text-sm";
    $card_classes = "relative overflow-hidden {$max_width_class} flex flex-col {$text_color} rounded-lg overflow-hidden shadow-lg transition-all duration-300";
    if ($layout === 'verticale-immagine-testo') {
        $card_classes .= " aspect-[1/2]";
    } else {
        $card_classes .= " aspect-[1/1]";
        $text_wrapper_classes = "h-full";
        $title_margin_top = "mt-8";
    }
    $image_section_classes = "relative h-2/3 overflow-hidden";
    $inner_text_classes = "flex flex-col";
    if ($layout === 'quadrato-titolo-immagine') {
        $card_classes .= " " . ($is_dark ? 'bg-tertiary' : 'bg-background-alt');
        $image_section_classes = "relative h-full overflow-hidden order-1 ml-8";
        $text_wrapper_classes = "h-auto";
        $titolo_wrapper_classes = "md:h-24 flex flex-col justify-between";
        $descrizione_wrapper_classes = "";
        $descrizione_classes = "";
    }
}
if (!$show_image) {
    $gradient_bg = ($is_dark ? 'bg-tertiary' : 'bg-background-alt');
    $image_section_classes = 'hidden';
}
$card_classes .= " " . $post_type;
$card_classes .= " stile-" . $stile;
?>

<div class="ssnail-insight-card mx-auto group <?php echo esc_attr($card_classes); ?>">
    <!-- Image section (top half) -->
    <div class="<?php echo esc_attr($image_section_classes); ?>">
        <?php if ($immagine && $show_image): ?>
            <?php 
            $image_class = "w-full h-full object-cover transition-all duration-700 group-hover:scale-125 mt-0 mb-0";
            if ($immagine === 'from_post') {
                $thumb_id = ssnail_get_thumbnail_id();
                if ( $thumb_id ) {
                    echo wp_get_attachment_image( $thumb_id, 'post-thumbnail', false, [ 'class' => $image_class ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                }
            } else {
                echo ssnail_acf_image_with_srcset($immagine, 'large', $immagine['alt'] ?? '', $image_class);
            }
            ?>
        <?php else: ?>
            <div class="w-full h-full <?php echo $gradient_bg; ?>"></div>
        <?php endif; ?>
    </div>
    
    <!-- Text content section with gradient background and overlap -->
    <div class="relative z-10 <?php echo $text_wrapper_classes; ?> <?php echo $gradient_bg; ?> rounded-b-lg px-8 bg-opacity-95">
        <div class="relative flex flex-col justify-between h-full z-10">
            <div class="relative <?php echo $title_margin_top; ?>">
                <div class="<?php echo esc_attr($inner_text_classes); ?>">
                    <div>
                        <!-- Sottotitolo -->
                        <?php if ($sottotitolo): ?>
                            <div class="<?php echo $date_color; ?> text-sm mb-0">
                                <?php echo $sottotitolo; ?>
                            </div>
                        <?php endif; ?>
                        <div class="<?php echo $titolo_wrapper_classes; ?>">
                            <!-- Titolo -->
                            <?php if ($titolo): ?>
                                <h3 class="<?php echo $title_color; ?> uppercase font-light text-lg md:text-xl mt-0 mb-0 line-clamp-3">
                                    <?php echo $titolo; ?>
                                </h3>
                            <?php endif; ?>
                        </div>
                        <?php if ($show_author): ?>
                            <div class="ssnail-author text-xs">
                                <?php ssnail_posted_by(); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="<?php echo $descrizione_wrapper_classes; ?>">
                        <!-- Descrizione -->
                        <?php if ($descrizione): ?>
                            <p class="<?php echo $descrizione_classes; ?>"><?php echo $descrizione; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Button/Link -->
    <?php if ($link): ?>
        <div class="ssnail-cta absolute bottom-[-2px] right-8 z-20">
            <a href="<?php echo esc_url($link_url); ?>" 
                class="btn <?php echo $stile === 'chiaro' ? 'btn-primary' : 'btn-secondary'; ?> rounded-b-none" 
                <?php echo $link_target ? 'target="' . esc_attr($link_target) . '"' : ''; ?>>
                <?php echo esc_html($link_title ?: 'MORE'); ?>
            </a>
        </div>
    <?php endif; ?>
</div>