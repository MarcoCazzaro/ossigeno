<?php
defined('ABSPATH') || exit;
$scroll_to_top_button = $args['scroll_to_top_button'] ?? false;
if ($scroll_to_top_button) {
?>
    <div x-data="{ scrolled: false }" @scroll.window.debounce="scrolled = window.scrollY > 0;">
        <button x-cloak x-show="scrolled" x-transition @click="window.scrollTo({top: 0, behavior: 'smooth'})" class="bg-primary hover:bg-secondary text-white rounded-full shadow-lg h-12 w-12 grid place-content-center">
            <span class="material-symbols-outlined">expand_less</span>
        </button>
    </div>
<?php
}
