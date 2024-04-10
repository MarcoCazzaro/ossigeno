<?php

/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Ossigeno
 */

if (!function_exists('ssnail__enqueue_custom_blocks')) {

	function ssnail__enqueue_custom_blocks()
	{
		// Dynamically register all the blocks contained in get_template_directory_uri() . '/blocks folder
		$blocks_folder = get_template_directory() . '/blocks';

		// Search also in the child theme
		$child_blocks_folder = get_stylesheet_directory() . '/blocks';

		$blocks_by_source['parent'] = scandir($blocks_folder);
		if (is_dir($child_blocks_folder)) {
			$blocks_by_source['child'] = scandir($child_blocks_folder);
		}
		foreach ($blocks_by_source as $source => $blocks) {
			foreach ($blocks as $block) {
				if ($block === '.' || $block === '..') {
					continue;
				}
				$block_path = ($source === 'parent') ? ($blocks_folder . '/' . $block) : ($child_blocks_folder . '/' . $block);
				if (is_dir($block_path)) {
					$block_json = $block_path . '/block.json';
					if (file_exists($block_json)) {
						register_block_type($block_path);
					}
				}
			}
		}
	}

	add_action('init', 'ssnail__enqueue_custom_blocks');
}

if (!function_exists('ssnail_get_site_logo')) {
	function ssnail_get_site_logo()
	{
		$image_type = "png";
		$logo_image_url = get_template_directory_uri() . "/images/ossigeno-logo.png";
		$logo_width = 264;
		$logo_height = 70;
		if (function_exists('the_custom_logo') && has_custom_logo()) {
			$custom_logo_id = get_theme_mod('custom_logo');
			$image = wp_get_attachment_image_src($custom_logo_id, 'medium');
			if (isset($image[0]) && $image[0] !== "") {
				$logo_image_url = $image[0];
				$logo_width = $image[1];
				$logo_height = $image[2];
			}
			$image_type = pathinfo(strtolower($logo_image_url))['extension'] ?? 'png';
		}
		if ($image_type === 'svg') {
			ssnail_print_svg($logo_image_url);
		} else {
?>
			<img src="<?= $logo_image_url ?>" alt="<?= get_bloginfo('name') ?>" width="<?= $logo_width ?>" height="<?= $logo_height ?>">
		<?php
		}
	}
}

if (!function_exists('ssnail_print_svg')) {
	function ssnail_print_svg($file_path, $position = 'no-repeat center / contain')
	{
		$logo_id = uniqid('ssnail-svg-');
		?>
		<style>
			#<?= $logo_id ?> {
				-webkit-mask: url(<?= $file_path ?>) <?= $position ?>;
				mask: url(<?= $file_path ?>) <?= $position ?>;
			}
		</style>
		<div id="<?= $logo_id ?>" class="ssnail-svg"></div>
		<?php
	}
}

if (!function_exists('ssnail_print_menu_with_social_icons')) {
	function ssnail_print_menu_with_social_icons($menu_location_name, $title, $icon_size = '')
	{
		$current_menu = false;
		$menu_locations = get_nav_menu_locations();
		if (isset($menu_locations[$menu_location_name])) {
			$current_menu = get_term($menu_locations[$menu_location_name], 'nav_menu');
		}
		if ($current_menu) {
			$current_menu_items = wp_get_nav_menu_items($current_menu);
			if ($current_menu_items && is_array($current_menu_items)) {
		?>
				<h3><?= $title ?></h3>
				<ul class="ssnail-menu-items flex gap-5 items-center justify-start">
					<?php
					foreach ($current_menu_items as $key => $item) {
					?>
						<li class="ssnail-menu-item">
							<a href="<?= $item->url ?>" target="<?= $item->target ?? '' ?>" class="hover:text-primary transition-colors">
								<?php
								$social_providers = ["facebook", "x-twitter", "instagram", "linkedin"];
								if (in_array(strtolower($item->title), $social_providers)) {
								?>
									<i class="fab fa-<?= strtolower($item->title) ?> <?= $icon_size ?>"></i>
								<?php
								} else {
									echo $item->title;
								}
								?>
							</a>
						</li>
					<?php
					}
					?>
				</ul>
	<?php
			}
		}
	}
}

if (!function_exists('ssnail_is_localhost')) {
	function ssnail_is_localhost()
	{
		return defined('SNAPPYSNAIL_LOCALHOST');
	}
}

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function ssnail__pingback_header()
{
	if (is_singular() && pings_open()) {
		printf('<link rel="pingback" href="%s">', esc_url(get_bloginfo('pingback_url')));
	}
}
add_action('wp_head', 'ssnail__pingback_header');

/**
 * Changes comment form default fields.
 *
 * @param array $defaults The default comment form arguments.
 *
 * @return array Returns the modified fields.
 */
function ssnail__comment_form_defaults($defaults)
{
	$comment_field = $defaults['comment_field'];

	// Adjust height of comment form.
	$defaults['comment_field'] = preg_replace('/rows="\d+"/', 'rows="5"', $comment_field);

	return $defaults;
}
add_filter('comment_form_defaults', 'ssnail__comment_form_defaults');

/**
 * Filters the default archive titles.
 */
function ssnail__get_the_archive_title()
{
	if (is_category()) {
		$title = __('Category Archives: ', 'ossigeno') . '<span>' . single_term_title('', false) . '</span>';
	} elseif (is_tag()) {
		$title = __('Tag Archives: ', 'ossigeno') . '<span>' . single_term_title('', false) . '</span>';
	} elseif (is_author()) {
		$title = __('Author Archives: ', 'ossigeno') . '<span>' . get_the_author_meta('display_name') . '</span>';
	} elseif (is_year()) {
		$title = __('Yearly Archives: ', 'ossigeno') . '<span>' . get_the_date(_x('Y', 'yearly archives date format', 'ossigeno')) . '</span>';
	} elseif (is_month()) {
		$title = __('Monthly Archives: ', 'ossigeno') . '<span>' . get_the_date(_x('F Y', 'monthly archives date format', 'ossigeno')) . '</span>';
	} elseif (is_day()) {
		$title = __('Daily Archives: ', 'ossigeno') . '<span>' . get_the_date() . '</span>';
	} elseif (is_post_type_archive()) {
		$cpt   = get_post_type_object(get_queried_object()->name);
		$title = sprintf(
			/* translators: %s: Post type or Taxonomy singular name */
			esc_html__('%s Archives', 'ossigeno'),
			$cpt->labels->singular_name
		);
	} elseif (is_tax()) {
		$tax   = get_taxonomy(get_queried_object()->taxonomy);
		$title = sprintf(
			/* translators: %s: Post type or Taxonomy singular name */
			esc_html__('%s Archives', 'ossigeno'),
			$tax->labels->singular_name
		);
	} else {
		$title = __('Archives:', 'ossigeno');
	}
	return $title;
}
add_filter('get_the_archive_title', 'ssnail__get_the_archive_title');

/**
 * Determines whether the post thumbnail can be displayed.
 */
function ssnail__can_show_post_thumbnail()
{
	return apply_filters('ssnail__can_show_post_thumbnail', !post_password_required() && !is_attachment());
}

/**
 * Returns the size for avatars used in the theme.
 */
function ssnail__get_avatar_size()
{
	return 60;
}

/**
 * Create the continue reading link
 *
 * @param string $more_string The string shown within the more link.
 */
function ssnail__continue_reading_link($more_string)
{

	if (!is_admin()) {
		$continue_reading = sprintf(
			/* translators: %s: Name of current post. */
			wp_kses(__('Continue reading %s', 'ossigeno'), array('span' => array('class' => array()))),
			the_title('<span class="sr-only">"', '"</span>', false)
		);

		$more_string = '<a href="' . esc_url(get_permalink()) . '">' . $continue_reading . '</a>';
	}

	return $more_string;
}

// Filter the excerpt more link.
add_filter('excerpt_more', 'ssnail__continue_reading_link');

// Filter the content more link.
add_filter('the_content_more_link', 'ssnail__continue_reading_link');

/**
 * Outputs a comment in the HTML5 format.
 *
 * This function overrides the default WordPress comment output in HTML5
 * format, adding the required class for Tailwind Typography. Based on the
 * `html5_comment()` function from WordPress core.
 *
 * @param WP_Comment $comment Comment to display.
 * @param array      $args    An array of arguments.
 * @param int        $depth   Depth of the current comment.
 */
function ssnail__html5_comment($comment, $args, $depth)
{
	$tag = ('div' === $args['style']) ? 'div' : 'li';

	$commenter          = wp_get_current_commenter();
	$show_pending_links = !empty($commenter['comment_author']);

	if ($commenter['comment_author_email']) {
		$moderation_note = __('Your comment is awaiting moderation.', 'ossigeno');
	} else {
		$moderation_note = __('Your comment is awaiting moderation. This is a preview; your comment will be visible after it has been approved.', 'ossigeno');
	}
	?>
	<<?php echo esc_attr($tag); ?> id="comment-<?php comment_ID(); ?>" <?php comment_class($comment->has_children ? 'parent' : '', $comment); ?>>
		<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
			<footer class="comment-meta">
				<div class="comment-author vcard">
					<?php
					if (0 !== $args['avatar_size']) {
						echo get_avatar($comment, $args['avatar_size']);
					}
					?>
					<?php
					$comment_author = get_comment_author_link($comment);

					if ('0' === $comment->comment_approved && !$show_pending_links) {
						$comment_author = get_comment_author($comment);
					}

					printf(
						/* translators: %s: Comment author link. */
						wp_kses_post(__('%s <span class="says">says:</span>', 'ossigeno')),
						sprintf('<b class="fn">%s</b>', wp_kses_post($comment_author))
					);
					?>
				</div><!-- .comment-author -->

				<div class="comment-metadata">
					<?php
					printf(
						'<a href="%s"><time datetime="%s">%s</time></a>',
						esc_url(get_comment_link($comment, $args)),
						esc_attr(get_comment_time('c')),
						esc_html(
							sprintf(
								/* translators: 1: Comment date, 2: Comment time. */
								__('%1$s at %2$s', 'ossigeno'),
								get_comment_date('', $comment),
								get_comment_time()
							)
						)
					);

					edit_comment_link(__('Edit', 'ossigeno'), ' <span class="edit-link">', '</span>');
					?>
				</div><!-- .comment-metadata -->

				<?php if ('0' === $comment->comment_approved) : ?>
					<em class="comment-awaiting-moderation"><?php echo esc_html($moderation_note); ?></em>
				<?php endif; ?>
			</footer><!-- .comment-meta -->

			<div <?php ssnail__content_class('comment-content'); ?>>
				<?php comment_text(); ?>
			</div><!-- .comment-content -->

			<?php
			if ('1' === $comment->comment_approved || $show_pending_links) {
				comment_reply_link(
					array_merge(
						$args,
						array(
							'add_below' => 'div-comment',
							'depth'     => $depth,
							'max_depth' => $args['max_depth'],
							'before'    => '<div class="reply">',
							'after'     => '</div>',
						)
					)
				);
			}
			?>
		</article><!-- .comment-body -->
	<?php
}

if (!function_exists('ssnail_add_edit_hp_link_on_admin_bar')) {
	function ssnail_add_edit_hp_link_on_admin_bar($wp_admin_bar)
	{
		if (current_user_can('edit_pages')) {
			$args = [
				'id' => 'bfc-edit-hp',
				'title' => __('Edit Home page', 'ossigeno'),
				'href' => get_edit_post_link(get_option('page_on_front')),
				'meta' => ['class' => 'bfc-edit-hp-button']
			];
			$wp_admin_bar->add_node($args);
		}
	}
	add_action('admin_bar_menu', 'ssnail_add_edit_hp_link_on_admin_bar', 50);
}
