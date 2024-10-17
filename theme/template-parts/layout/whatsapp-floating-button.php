<?php
defined('ABSPATH') || exit;
$whatsapp_number = $args['whatsapp_number'] ?? false;
$whatsapp_cta = $args['whatsapp_cta'] ?? false;
if ($whatsapp_number) {
?>
    <div class="ssnail-whatsapp-floating-button relative bg-whatsapp text-white overflow-clip flex items-center rounded-full group">
        <?php
        if ($whatsapp_cta) {
        ?>
            <span class="ssnail-wa-cta absolute transform translate-x-full group-hover:translate-x-0 group-hover:relative transition-transform duration-200 pl-3 text-sm font-bold"><?= $whatsapp_cta ?></span>
        <?php
        }
        ?>
        <a href="https://wa.me/<?= $whatsapp_number ?>" target="_blank" rel="noopener noreferrer" class="relative grid place-content-center rounded-full h-12 w-12 bg-whatsapp"><?php ssnail_get_social_icon('whatsapp'); ?></a>
    </div>
<?php
}
