<?php
defined('ABSPATH') || exit;
?>
<form class="ssnail-form ssnail-search-form" id="searchform" method="get" action="<?php echo esc_url(home_url('/')); ?>">
    <label class="" for="searchform-input"><?php echo __('Search', 'ossigeno') ?></label>
    <div class="flex gap-2">
        <input id="searchform-input" type="text" class="form-input" placeholder="<?php echo __('Search', 'ossigeno') ?>" aria-label="<?php echo __('Search', 'ossigeno') ?>" aria-describedby="search-panel-search-button" name="s" value="<?= get_query_var('s') ?>">
        <button type="submit" id="search-panel-search-button"><span class="material-symbols-outlined">arrow_forward</span></button>
    </div>
</form>