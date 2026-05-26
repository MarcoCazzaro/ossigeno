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

if (!function_exists('ssnail_get_related_posts')) {
	function ssnail_get_related_posts(int $post_count = 3)
	{
		$related_posts = false;
		$post_id = get_the_ID();
		// Get tags of the current post
		$post_tags = wp_get_post_tags($post_id, array('fields' => 'ids'));

		$args = array(
			'post_type' => 'post',
			'posts_per_page' => $post_count,
			'post__not_in' => array($post_id),
			'tag__in' => $post_tags,
		);
		$query = new WP_Query($args);
		$related_posts = $query->have_posts() ? $query->posts : false;
		// If the number of the related posts is less than the required number, get the rest of the posts from the same category
		if ($query->post_count < $post_count) {
			if (!is_array($related_posts)) {
				$related_posts = array();
			}
			$posts_ids_by_tag = array_map(function ($post) {
				return $post->ID;
			}, $related_posts);
			if (!in_array($post_id, $posts_ids_by_tag)) {
				$posts_ids_by_tag[] = $post_id;
			}
			$categories = get_the_category($post_id);
			$category_ids = array();
			foreach ($categories as $category) {
				$category_ids[] = $category->term_id;
			}
			$args = array(
				'post_type' => 'post',
				'posts_per_page' => $post_count - $query->post_count,
				'post__not_in' => $posts_ids_by_tag,
				'category__in' => $category_ids,
			);
			$query = new WP_Query($args);
			$related_posts = $query->have_posts() ? array_merge($related_posts, $query->posts) : $related_posts;
		}
		wp_reset_postdata();
		return $related_posts;
	}
}
