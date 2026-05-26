<?php
/**
 * Posts Grid Block template.
 *
 * @param array $block The block settings and attributes.
 */

// Block parameters from ACF fields
$categoria = get_field('categoria') ?? 'insights-and-stories';
$numero_post = intval(get_field('numero_post') ?? 8);
$stile = get_field('stile') ?? 'chiaro';
$bottone_vedi_tutti = get_field('bottone_vedi_tutti') ?? false;
$layout = get_field('layout') ?? 'container';
$layout_class = '';
if ($layout === 'container') {
    $layout_class = 'ssnail-container';
}

// Prepare query arguments
$args = array(
    'post_type'      => 'post',
    'posts_per_page' => $numero_post,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'suppress_filters' => false
);

// Add category filter if specified
if (!empty($categoria) && $categoria !== 'all') {
    // Handle if categoria is an array (ACF field might return array)
    if (is_array($categoria)) {
        // If it's an array, get the first value
        $categoria = $categoria[0];
    }
    
    // Convert category slug to ID if it's a string
    if (is_string($categoria)) {
        $category_obj = get_category_by_slug($categoria);
        if ($category_obj) {
            $args['category__in'] = array($category_obj->term_id);
        }
    } else {
        // If it's already a numeric ID
        $args['category__in'] = array($categoria);
    }
}

$posts_query = new WP_Query($args);
?>

<div class="ssnail-posts-grid grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 <?php echo $layout_class; ?>">
    <?php
    if ($posts_query->have_posts()) {
        while ($posts_query->have_posts()) {
            $posts_query->the_post();
            get_template_part('inc/acf-blocks/insight-card/insight-card', '', [
                'use_post' => true,
                'stile'    => $stile
            ]);
        }
        wp_reset_postdata();
        if ($bottone_vedi_tutti) {
            // Determine the appropriate URL for the "View all" button
            $view_all_url = '';
            if (!empty($categoria)) {
                // If a category is selected, link to that category's archive page
                $view_all_url = get_category_link($categoria);
            } else {
                // If no category is selected, link to the posts page set in WordPress Reading settings
                $posts_page_id = get_option('page_for_posts');
                if ($posts_page_id) {
                    $view_all_url = get_permalink($posts_page_id);
                } else {
                    // Fallback to post type archive if no posts page is set
                    $view_all_url = get_post_type_archive_link('post');
                }
            }
            ?>
            <div class="col-span-full text-center">
                <a href="<?php echo esc_url($view_all_url); ?>" class="btn <?php echo $stile === 'chiaro' ? 'btn-tertiary' : 'btn-primary'; ?>">
                    <?php esc_html_e('View all', 'ossigeno'); ?>
                </a>
            </div>
            <?php
        }
    } else {
    ?>    
        <div class="col-span-full text-center mt-8">
            <p><?php esc_html_e('No posts found.', 'ossigeno'); ?></p>
        </div>
    <?php } ?>
</div>