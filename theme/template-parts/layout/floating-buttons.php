<?php
defined('ABSPATH') || exit;
$whatsapp_number = get_option('ossigeno_whatsapp_number');
$whatsapp_cta = get_option('ossigeno_whatsapp_cta');
$scroll_to_top_button = get_option('ossigeno_show_scroll_to_top_button');
if ($whatsapp_number || $scroll_to_top_button) {
?>
    <div class="ssnail-floating-buttons fixed bottom-0 right-0 flex flex-col items-end p-2 gap-2">
        <?php
        get_template_part('template-parts/layout/scroll-to-top-button', '', compact('scroll_to_top_button'));
        get_template_part('template-parts/layout/whatsapp-floating-button', '', compact('whatsapp_number', 'whatsapp_cta'));
        ?>
    </div>
<?php
}
