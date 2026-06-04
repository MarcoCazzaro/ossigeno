<?php
/**
 * Slider Block template.
 *
 * @param array $block The block settings and attributes.
 */
$sorgente_schede = get_field('sorgente_schede') ?? 'input-schede';
$altezza_slide = (get_field('altezza_slide') ?? '400') . 'px';
$object_fit = ((get_field('riempimento_immagine') ?? 'cover') === 'cover') ? 'object-cover' : 'object-contain';
?>
<div class="ssnail-slider max-w-none" x-data="sliderData">
    <!-- Main slider container with horizontal scrolling and flex layout -->
    <div x-ref="slidesContainer" class="slides-container flex gap-8 overflow-x-auto overflow-y-hidden scrollbar-hide w-full snap-x snap-mandatory" style="min-height: <?php echo $altezza_slide; ?>;">
        <?php if ($sorgente_schede === 'input-schede' && have_rows('schede')) { 
            $index = 0;
            while (have_rows('schede')) { 
                the_row();
                $slide_args = [
                    'titolo' => get_sub_field('titolo'),
                    'descrizione' => get_sub_field('descrizione'),
                    'immagine' => get_sub_field('immagine'),
                    'link' => get_sub_field('link'),
                    'altezza_slide' => $altezza_slide,
                    'object_fit' => $object_fit,
                    'slide_index' => $index
                ];
                get_template_part( 'inc/acf-blocks/slider/slider-slide', '', $slide_args );
                $index++;
            } 
        } elseif ($sorgente_schede === 'elenco-post' && get_field('elenco_post')) {
            $index = 0; // Initialize index for this section
            $posts = get_field('elenco_post');
            foreach ($posts as $post) {
                $slide_args = [
                    'titolo' => $post->post_title,
                    'descrizione' => strip_tags(get_the_excerpt($post->ID)),
                    'immagine' => get_post_thumbnail_id($post->ID),
                    'link' => get_permalink($post->ID),
                    'altezza_slide' => $altezza_slide,
                    'object_fit' => $object_fit,
                    'slide_index' => $index
                ];
                get_template_part( 'inc/acf-blocks/slider/slider-slide', '', $slide_args );
                $index++;
            }
        } 
        ?>
    </div>
  
    <!-- Navigation controls positioned below the slider -->
    <div class="controls flex justify-center mt-4 gap-4">
        <button @click="prevSlide" class="slider-nav-btn h-12 w-12 grid place-items-center text-background bg-secondary hover:bg-primary transition-all shadow cursor-pointer rounded-full">
            <span class="material-symbols-outlined aspect-square">arrow_back</span>
        </button>
        <button @click="nextSlide" class="slider-nav-btn h-12 w-12 grid place-items-center text-background bg-secondary hover:bg-primary transition-all shadow cursor-pointer rounded-full">
            <span class="material-symbols-outlined aspect-square">arrow_forward</span>
        </button>
    </div>

    <!-- Lightbox overlay -->
    <div x-show="lightboxOpen" 
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         class="lightbox-overlay hidden fixed inset-0 z-50 bg-black bg-opacity-90 p-2 lg:p-8"
         @click="closeLightbox()"
         @keydown.escape="closeLightbox()">
        
        <!-- Close button -->
        <button class="absolute cursor-pointer top-4 right-4 z-60 grid place-items-center bg-secondary hover:bg-tertiary transition-colors rounded-full w-12 h-12 aspect-[1/1]"
                @click.stop="closeLightbox()">
            <span class="material-symbols-outlined text-foreground text-2xl rotate-45">add</span>
        </button>
        
        <!-- Main lightbox container -->
        <div class="relative w-full h-full flex items-center justify-center" @click.stop>
            
            <!-- Previous button -->
            <button class="absolute cursor-pointer left-4 top-1/2 -translate-y-1/2 z-60 grid place-items-center bg-secondary hover:bg-tertiary transition-colors rounded-full w-12 h-12 aspect-[1/1]"
                    @click.stop="prevLightboxSlide()">
                <span class="material-symbols-outlined text-foreground text-2xl">chevron_left</span>
            </button>
            
            <!-- Image container -->
            <div class="relative max-w-full max-h-full flex items-center justify-center">
                <img :src="lightboxImages[lightboxSlide]?.src || ''"
                     :alt="lightboxImages[lightboxSlide]?.alt || ''"
                     class="max-w-full max-h-full object-contain"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95" />
            </div>
            
            <!-- Next button -->
            <button class="absolute cursor-pointer right-4 top-1/2 -translate-y-1/2 z-60 grid place-items-center bg-secondary hover:bg-tertiary transition-colors rounded-full w-12 h-12 aspect-[1/1]"
                    @click.stop="nextLightboxSlide()">
                <span class="material-symbols-outlined text-foreground text-2xl">chevron_right</span>
            </button>
            
            <!-- Image counter -->
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black bg-opacity-50 text-foreground px-3 py-1 rounded-full text-sm">
                <span x-text="lightboxSlide + 1"></span> / <span x-text="lightboxImages.length"></span>
            </div>
        </div>
    </div>
</div>