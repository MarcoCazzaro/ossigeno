<?php
defined('ABSPATH') || exit;

if (!function_exists('ssnail_related_posts_callback')) {
    // Create a shortcode for related posts
    function ssnail_related_posts_callback($atts)
    {
        $atts = shortcode_atts(array(
            'post_count' => 3
        ), $atts);
        extract($atts);
        $related_posts = ssnail_get_related_posts($post_count);
        $output = "";
        if (!empty($related_posts)) {
            global $post;
            ob_start();
?>
            <div class="ssnail-related-posts my-16">
                <h2 class="ssnail-related-posts__title font-subheadings text-xl mb-8"><?php esc_html_e('Related Posts', 'ossigeno'); ?></h2>
                <div class="ssnail-related-posts__list grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <?php foreach ($related_posts as $post) : setup_postdata($post); ?>
                        <?php get_template_part('template-parts/content/content', 'archive', ['layout' => 'tile minimal']); ?>
                    <?php endforeach;
                    wp_reset_postdata(); ?>
                </div>
            </div>
<?php
            $output = ob_get_clean();
        }
        return $output;
    }
    // Register the shortcode
    add_shortcode('ssnail_related_posts', 'ssnail_related_posts_callback');
}
