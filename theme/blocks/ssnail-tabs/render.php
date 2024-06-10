<div class="wp-block-snappysnail-ssnail-tabs ssnail-tabs" x-data="{
        activeTab: 0,
        currentElementLeft: 0,
        currentElementWidth: 0,
        updateSelectorWidthAndPosition() {
            let activeButton = this.$refs['selector' + this.activeTab];
            this.currentElementLeft = activeButton.offsetLeft + 'px';
            this.currentElementWidth = activeButton.offsetWidth + 'px';
        }
    }" x-init="updateSelectorWidthAndPosition()">
    <div class="ssnail-tabs-selector flex gap-3 font-bold">
        <?php
        $tabLabels = $attributes['tabLabels'] ?? [];
        $tabWidth = 100 / count($tabLabels);
        foreach ($tabLabels as $key => $label) {
        ?>
            <button class="ssnail-tabs-selector-item text-primary" x-ref="selector<?php echo $key; ?>" :class="{ 'active': activeTab == <?php echo $key; ?> }" @click="activeTab = <?php echo $key; ?>; updateSelectorWidthAndPosition()">
                <?php echo $label; ?>
            </button>
        <?php
        }
        ?>
    </div>
    <div class="ssnail-tabs-selector-indicator-wrapper relative h-[1px] w-full border-t border-primary">
        <div class="ssnail-tabs-selector-indicator absolute h-[3px] bg-primary" x-bind:style="'transition: transform 0.3s ease-in-out, width 0.3s ease-in-out; transform: translateX('+currentElementLeft+') translateY(-50%); width: ' + currentElementWidth"></div>
    </div>
    <div class="ssnail-tabs-list">
        <?php echo $content; ?>
    </div>
</div>