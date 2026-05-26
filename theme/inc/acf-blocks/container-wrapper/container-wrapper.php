<?php
/**
 * Container Wrapper Block template.
 *
 * @param array $block The block settings and attributes.
 */

// Create id attribute allowing for custom "anchor" value
$id = 'container-wrapper-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values
$className = 'ssnail-container';
if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
}
if (!empty($block['align'])) {
    $className .= ' align' . $block['align'];
}
?>

<div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($className); ?>">
    <?php
    // Output InnerBlocks content
    echo '<InnerBlocks />';
    ?>
</div>
