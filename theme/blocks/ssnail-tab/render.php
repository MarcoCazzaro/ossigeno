<?php $parent_id = $attributes['parentId'] ?? 'nada'; ?>
<div x-id="['tab-<?php echo $parent_id; ?>']" class="wp-block-snappysnail-ssnail-tab ssnail-tab" x-cloak x-show="$id('tab-<?php echo $parent_id; ?>') == 'tab-<?php echo $parent_id; ?>-' + (activeTab + 1)">
    <div class="ssnail-tab-content">
        <?php echo $content; ?>
    </div>
</div>