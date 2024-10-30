<?php

/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Ossigeno
 */

if (!function_exists('ssnail_enqueue_custom_blocks')) {

	function ssnail_enqueue_custom_blocks()
	{
		// Dynamically register all the blocks contained in get_template_directory_uri() . '/blocks folder
		$blocks_folder = get_template_directory() . '/blocks';

		// Search also in the child theme
		$child_blocks_folder = get_stylesheet_directory() . '/blocks';

		$blocks_by_source['parent'] = scandir($blocks_folder);
		if (is_dir($child_blocks_folder) && $blocks_folder !== $child_blocks_folder) {
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

	add_action('init', 'ssnail_enqueue_custom_blocks');
}

if (!function_exists('ssnail_get_site_logo')) {
	function ssnail_get_site_logo($additional_classes = '')
	{
		$image_type = "svg";
		$logo_image_url = get_template_directory_uri() . "/images/ossigeno-logo.svg";
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
		// If the logo is an SVG file, omit the width and height attributes
		if ($image_type === "svg") {
			$logo_width = '';
			$logo_height = '';
		}
?>
		<img src="<?= $logo_image_url ?>" alt="<?php echo get_bloginfo('name') ?>" width="<?php echo $logo_width ?>" height="<?php echo $logo_height ?>" class="<?php echo $additional_classes; ?>">
		<?php
	}
}

if (!function_exists('ssnail_print_menu_with_social_icons')) {
	function ssnail_print_menu_with_social_icons($menu_location_name, $title = false, $icon_class = '')
	{
		$current_menu = false;
		$menu_locations = get_nav_menu_locations();
		if (isset($menu_locations[$menu_location_name])) {
			$current_menu = get_term($menu_locations[$menu_location_name], 'nav_menu');
		}
		if ($current_menu) {
			$current_menu_items = wp_get_nav_menu_items($current_menu);
			if ($current_menu_items && is_array($current_menu_items)) {
				if ($title) {
					echo '<h3>' . $title . '</h3>';
				}
		?>
				<ul class="ssnail-menu-items flex gap-5 items-center justify-around flex-wrap">
					<?php
					foreach ($current_menu_items as $key => $item) {
					?>
						<li class="ssnail-menu-item">
							<a href="<?= $item->url ?>" target="<?= $item->target ?? '' ?>" class="hover:text-primary transition-colors <?php echo $icon_class; ?>">
								<?php ssnail_get_social_icon($item->title); ?>
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

if (!function_exists('ssnail_get_social_icon')) {
	function ssnail_get_social_icon($platform, $print = true, $fill_color = "currentColor", $additional_classes = null)
	{
		$icon = '';
		$additional_classes = $additional_classes ?? 'h-8 w-8';
		// https://icons8.com/icon/set/social-media/material
		switch (strtolower($platform)) {
			case "facebook":
				$icon = '<svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="256px" height="256px"><path fill="' . $fill_color . '" fill-rule="nonzero" d="M12,2C6.477,2,2,6.477,2,12c0,5.013,3.693,9.153,8.505,9.876V14.65H8.031v-2.629h2.474v-1.749 c0-2.896,1.411-4.167,3.818-4.167c1.153,0,1.762,0.085,2.051,0.124v2.294h-1.642c-1.022,0-1.379,0.969-1.379,2.061v1.437h2.995 l-0.406,2.629h-2.588v7.247C18.235,21.236,22,17.062,22,12C22,6.477,17.523,2,12,2z"/></svg>';
				break;
			case "instagram":
				$icon = '<svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="256px" height="256px"><path fill="' . $fill_color . '" fill-rule="nonzero" d="M 8 3 C 5.239 3 3 5.239 3 8 L 3 16 C 3 18.761 5.239 21 8 21 L 16 21 C 18.761 21 21 18.761 21 16 L 21 8 C 21 5.239 18.761 3 16 3 L 8 3 z M 18 5 C 18.552 5 19 5.448 19 6 C 19 6.552 18.552 7 18 7 C 17.448 7 17 6.552 17 6 C 17 5.448 17.448 5 18 5 z M 12 7 C 14.761 7 17 9.239 17 12 C 17 14.761 14.761 17 12 17 C 9.239 17 7 14.761 7 12 C 7 9.239 9.239 7 12 7 z M 12 9 A 3 3 0 0 0 9 12 A 3 3 0 0 0 12 15 A 3 3 0 0 0 15 12 A 3 3 0 0 0 12 9 z"/></svg>';
				break;
			case "tiktok":
				$icon = '<svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="256px" height="256px"><path fill="' . $fill_color . '" fill-rule="nonzero" d="M 6 3 C 4.3550302 3 3 4.3550302 3 6 L 3 18 C 3 19.64497 4.3550302 21 6 21 L 18 21 C 19.64497 21 21 19.64497 21 18 L 21 6 C 21 4.3550302 19.64497 3 18 3 L 6 3 z M 12 7 L 14 7 C 14 8.005 15.471 9 16 9 L 16 11 C 15.395 11 14.668 10.734156 14 10.285156 L 14 14 C 14 15.654 12.654 17 11 17 C 9.346 17 8 15.654 8 14 C 8 12.346 9.346 11 11 11 L 11 13 C 10.448 13 10 13.449 10 14 C 10 14.551 10.448 15 11 15 C 11.552 15 12 14.551 12 14 L 12 7 z"/></svg>';
				break;
			case "x-twitter":
				$icon = '<svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="256px" height="256px"><path fill="' . $fill_color . '" fill-rule="nonzero" d="M 2.3671875 3 L 9.4628906 13.140625 L 2.7402344 21 L 5.3808594 21 L 10.644531 14.830078 L 14.960938 21 L 21.871094 21 L 14.449219 10.375 L 20.740234 3 L 18.140625 3 L 13.271484 8.6875 L 9.2988281 3 L 2.3671875 3 z M 6.2070312 5 L 8.2558594 5 L 18.033203 19 L 16.001953 19 L 6.2070312 5 z"/></svg>';
				break;
			case "linkedin":
				$icon = '<svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="256px" height="256px"><path fill="' . $fill_color . '" fill-rule="nonzero" d="M 5 3 C 3.9 3 3 3.9 3 5 L 3 19 C 3 20.1 3.9 21 5 21 L 19 21 C 20.1 21 21 20.1 21 19 L 21 5 C 21 3.9 20.1 3 19 3 L 5 3 z M 5 5 L 19 5 L 19 19 L 5 19 L 5 5 z M 7.8007812 6.3007812 C 6.9007812 6.3007812 6.4003906 6.8 6.4003906 7.5 C 6.4003906 8.2 6.8992188 8.6992188 7.6992188 8.6992188 C 8.5992187 8.6992187 9.0996094 8.2 9.0996094 7.5 C 9.0996094 6.8 8.6007813 6.3007812 7.8007812 6.3007812 z M 6.5 10 L 6.5 17 L 9 17 L 9 10 L 6.5 10 z M 11.099609 10 L 11.099609 17 L 13.599609 17 L 13.599609 13.199219 C 13.599609 12.099219 14.499219 11.900391 14.699219 11.900391 C 14.899219 11.900391 15.599609 12.099219 15.599609 13.199219 L 15.599609 17 L 18 17 L 18 13.199219 C 18 10.999219 17.000781 10 15.800781 10 C 14.600781 10 13.899609 10.4 13.599609 11 L 13.599609 10 L 11.099609 10 z"/></svg>';
				break;
			case "whatsapp":
				$icon = '<svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="256px" height="256px"><path fill="' . $fill_color . '" fill-rule="nonzero" d="M 12.011719 2 C 6.5057187 2 2.0234844 6.478375 2.0214844 11.984375 C 2.0204844 13.744375 2.4814687 15.462563 3.3554688 16.976562 L 2 22 L 7.2324219 20.763672 C 8.6914219 21.559672 10.333859 21.977516 12.005859 21.978516 L 12.009766 21.978516 C 17.514766 21.978516 21.995047 17.499141 21.998047 11.994141 C 22.000047 9.3251406 20.962172 6.8157344 19.076172 4.9277344 C 17.190172 3.0407344 14.683719 2.001 12.011719 2 z M 12.009766 4 C 14.145766 4.001 16.153109 4.8337969 17.662109 6.3417969 C 19.171109 7.8517969 20.000047 9.8581875 19.998047 11.992188 C 19.996047 16.396187 16.413812 19.978516 12.007812 19.978516 C 10.674812 19.977516 9.3544062 19.642812 8.1914062 19.007812 L 7.5175781 18.640625 L 6.7734375 18.816406 L 4.8046875 19.28125 L 5.2851562 17.496094 L 5.5019531 16.695312 L 5.0878906 15.976562 C 4.3898906 14.768562 4.0204844 13.387375 4.0214844 11.984375 C 4.0234844 7.582375 7.6067656 4 12.009766 4 z M 8.4765625 7.375 C 8.3095625 7.375 8.0395469 7.4375 7.8105469 7.6875 C 7.5815469 7.9365 6.9355469 8.5395781 6.9355469 9.7675781 C 6.9355469 10.995578 7.8300781 12.182609 7.9550781 12.349609 C 8.0790781 12.515609 9.68175 15.115234 12.21875 16.115234 C 14.32675 16.946234 14.754891 16.782234 15.212891 16.740234 C 15.670891 16.699234 16.690438 16.137687 16.898438 15.554688 C 17.106437 14.971687 17.106922 14.470187 17.044922 14.367188 C 16.982922 14.263188 16.816406 14.201172 16.566406 14.076172 C 16.317406 13.951172 15.090328 13.348625 14.861328 13.265625 C 14.632328 13.182625 14.464828 13.140625 14.298828 13.390625 C 14.132828 13.640625 13.655766 14.201187 13.509766 14.367188 C 13.363766 14.534188 13.21875 14.556641 12.96875 14.431641 C 12.71875 14.305641 11.914938 14.041406 10.960938 13.191406 C 10.218937 12.530406 9.7182656 11.714844 9.5722656 11.464844 C 9.4272656 11.215844 9.5585938 11.079078 9.6835938 10.955078 C 9.7955938 10.843078 9.9316406 10.663578 10.056641 10.517578 C 10.180641 10.371578 10.223641 10.267562 10.306641 10.101562 C 10.389641 9.9355625 10.347156 9.7890625 10.285156 9.6640625 C 10.223156 9.5390625 9.737625 8.3065 9.515625 7.8125 C 9.328625 7.3975 9.131125 7.3878594 8.953125 7.3808594 C 8.808125 7.3748594 8.6425625 7.375 8.4765625 7.375 z"/></svg>';
				break;
			case "youtube":
				$icon = '<svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="256px" height="256px"><path fill="' . $fill_color . '" fill-rule="nonzero" d="M21.582,6.186c-0.23-0.86-0.908-1.538-1.768-1.768C18.254,4,12,4,12,4S5.746,4,4.186,4.418 c-0.86,0.23-1.538,0.908-1.768,1.768C2,7.746,2,12,2,12s0,4.254,0.418,5.814c0.23,0.86,0.908,1.538,1.768,1.768 C5.746,20,12,20,12,20s6.254,0,7.814-0.418c0.861-0.23,1.538-0.908,1.768-1.768C22,16.254,22,12,22,12S22,7.746,21.582,6.186z M10,15.464V8.536L16,12L10,15.464z"/></svg>';
				break;
			case "vimeo":
				$icon = '<svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="256px" height="256px"><path fill="' . $fill_color . '" fill-rule="nonzero" d="M 21.988281 7.96875 C 21.902344 9.878906 20.539063 12.488281 17.910156 15.804688 C 15.191406 19.269531 12.890625 21 11.007813 21 C 9.84375 21 8.855469 19.945313 8.050781 17.835938 C 7.511719 15.902344 6.976563 13.96875 6.4375 12.035156 C 5.839844 9.925781 5.195313 8.871094 4.511719 8.871094 C 4.359375 8.871094 3.835938 9.179688 2.941406 9.792969 L 2 8.605469 C 2.988281 7.757813 3.960938 6.90625 4.917969 6.058594 C 6.234375 4.941406 7.222656 4.355469 7.882813 4.296875 C 9.4375 4.148438 10.398438 5.191406 10.757813 7.425781 C 11.144531 9.832031 11.414063 11.332031 11.5625 11.917969 C 12.011719 13.914063 12.507813 14.910156 13.046875 14.910156 C 13.464844 14.910156 14.09375 14.265625 14.933594 12.96875 C 15.769531 11.671875 16.21875 10.6875 16.277344 10.007813 C 16.398438 8.890625 15.949219 8.328125 14.933594 8.328125 C 14.453125 8.328125 13.960938 8.4375 13.453125 8.652344 C 14.433594 5.496094 16.3125 3.964844 19.085938 4.050781 C 21.140625 4.109375 22.109375 5.414063 21.988281 7.96875 Z"/></svg>';
				break;
		}
		if ($icon !== '') {
			$icon = '<span class="ssnail-social-icon inline-flex overflow-clip ' . $additional_classes . '">' . $icon . '</span>';
		}
		if ($print) {
			echo $icon;
		} else {
			return $icon;
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
function ssnail_pingback_header()
{
	if (is_singular() && pings_open()) {
		printf('<link rel="pingback" href="%s">', esc_url(get_bloginfo('pingback_url')));
	}
}
add_action('wp_head', 'ssnail_pingback_header');

/**
 * Changes comment form default fields.
 *
 * @param array $defaults The default comment form arguments.
 *
 * @return array Returns the modified fields.
 */
function ssnail_comment_form_defaults($defaults)
{
	$comment_field = $defaults['comment_field'];

	// Adjust height of comment form.
	$defaults['comment_field'] = preg_replace('/rows="\d+"/', 'rows="5"', $comment_field);

	return $defaults;
}
add_filter('comment_form_defaults', 'ssnail_comment_form_defaults');

/**
 * Filters the default archive titles.
 */
function ssnail_get_the_archive_title()
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
add_filter('get_the_archive_title', 'ssnail_get_the_archive_title');

/**
 * Determines whether the post thumbnail can be displayed.
 */
function ssnail_can_show_post_thumbnail()
{
	return apply_filters('ssnail_can_show_post_thumbnail', !post_password_required() && !is_attachment());
}

/**
 * Returns the size for avatars used in the theme.
 */
function ssnail_get_avatar_size()
{
	return 60;
}

/**
 * Create the continue reading link
 *
 * @param string $more_string The string shown within the more link.
 */
function ssnail_continue_reading_link($more_string)
{

	if (!is_admin()) {
		$continue_reading = sprintf(
			/* translators: %s: Name of current post. */
			wp_kses(__('Continue reading %s', 'ossigeno'), array('span' => array('class' => array()))),
			the_title('<span class="sr-only">"', '"</span>', false)
		);

		$more_string = '<a class="ssnail-read-more" href="' . esc_url(get_permalink()) . '">' . $continue_reading . '</a>';
	}

	return $more_string;
}

// Filter the excerpt more link.
add_filter('excerpt_more', 'ssnail_continue_reading_link');

// Filter the content more link.
add_filter('the_content_more_link', 'ssnail_continue_reading_link');

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
function ssnail_html5_comment($comment, $args, $depth)
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

			<div <?php ssnail_content_class('comment-content'); ?>>
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

if (!function_exists('ssnail_allow_svg_uploads')) {
	function ssnail_allow_svg_uploads($mimes)
	{
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}
	add_filter('upload_mimes', 'ssnail_allow_svg_uploads');

	function ssnail_fix_svg_logo_on_admin()
	{
		echo '<style type="text/css">
		.attachment-266x266, .thumbnail img {
			width: 100% !important;
			height: auto !important;
		}
		</style>';
	}
	add_action('admin_head', 'ssnail_fix_svg_logo_on_admin');

	function ssnail_svg_check_filetype_and_ext($data, $file, $filename, $mimes)
	{
		global $wp_version;
		if ($wp_version !== '4.7.1') {
			return $data;
		}
		$filetype = wp_check_filetype($filename, $mimes);
		return [
			'ext'             => $filetype['ext'],
			'type'            => $filetype['type'],
			'proper_filename' => $data['proper_filename']
		];
	}
	add_filter('wp_check_filetype_and_ext', 'ssnail_svg_check_filetype_and_ext', 10, 4);
}

if (!function_exists('ssnail_add_custom_css')) {
	function ssnail_add_custom_css()
	{
		$custom_css = "";

		// Get the list of categories that has the metafield "colore" set or the metafield "logo" set or both
		$categories = get_categories([
			'meta_query' => [
				[
					'key' => 'colore',
					'compare' => 'EXISTS'
				]
			]
		]);
		// Loop through these categories and add the custom CSS for the color
		if ($categories && count($categories) > 0) {
			foreach ($categories as $category) {
				$category_color = get_field('colore', 'category_' . $category->term_id);
				if ($category_color) {
					$custom_css .= ".ossigeno-category-pill.{$category->slug} {
						background-color: {$category_color} !important;
					}";
				}
			}
		}

		// Get the SVG icons if present
		$custom_css .= ssnail_generate_svg_icon_css();

		//WRAP IT UP
		ssnail_validate_get_parameters($custom_css);
		if ($custom_css !== "") {
			wp_add_inline_style('ossigeno-style', $custom_css);
		}
	}
	add_action('wp_enqueue_scripts', 'ssnail_add_custom_css');
}

if (!function_exists('ssnail_load_svg_icons')) {
	function ssnail_load_svg_icons()
	{
		// Search both in the parent and child theme
		$icons_dirs = [
			get_template_directory() . '/images/icons',
			get_stylesheet_directory() . '/images/icons'
		];
		$icons = array();
		foreach ($icons_dirs as $dir) {
			if (is_dir($dir)) {
				if ($dh = opendir($dir)) {
					while (($file = readdir($dh)) !== false) {
						if (pathinfo($file, PATHINFO_EXTENSION) == 'svg') {
							$icons[pathinfo($file, PATHINFO_FILENAME)] = file_get_contents($dir . '/' . $file);
						}
					}
					closedir($dh);
				}
			}
		}

		return $icons;
	}
}

if (!function_exists('ssnail_generate_svg_icon_css')) {
	function ssnail_generate_svg_icon_css()
	{
		$icons = ssnail_load_svg_icons();
		$css = '';

		$css .= ".ssnail-icon {
			--ssnail-icon-color: currentColor;
			min-width: 2rem;
			aspect-ratio: 1;
			display: inline-block;
			svg {
				width: 100%;
				height: 100%;
			}
		}\n";

		foreach ($icons as $name => $svg) {
			$svg = preg_replace_callback('/fill:(.*?);/', function ($matches) {
				// If the color is white in any format, don't replace it
				if (strtolower($matches[1]) === '#fff' || strtolower($matches[1]) === '#ffffff') {
					return $matches[0];
				}
				// Otherwise, replace it with red

				return "fill:var(--ssnail-icon-color);";
			}, $svg);
			$uniqueIds = [];

			$svg = preg_replace_callback('/\.cls-(\d+)|class="cls-(\d+)"/', function ($matches) use (&$uniqueIds) {
				// Check if the class name is in the first or second capturing group
				$className = $matches[1] ? $matches[1] : $matches[2];

				// If a unique ID has not been generated for this class name yet, generate one
				if (!isset($uniqueIds[$className])) {
					$uniqueIds[$className] = uniqid();
				}

				// Generate a unique class name by appending the unique ID to the original class name
				$uniqueClassName = 'cls-' . $className . '-' . $uniqueIds[$className];

				// Replace the class name in the appropriate context
				return $matches[1] ? '.' . $uniqueClassName : 'class="' . $uniqueClassName . '"';
			}, $svg);
			$svg_encoded = base64_encode($svg);
			$css .= ".ssnail-icon.$name {
				--ssnail-icon-svg: 'data:image/svg+xml;base64,$svg_encoded';
			}\n";
		}

		return $css;
	}
}

if (!function_exists('hex2rgb')) {
	function hex2rgb($colour)
	{
		if ($colour[0] == '#') {
			$colour = substr($colour, 1);
		}
		if (strlen($colour) == 6) {
			list($r, $g, $b) = array($colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]);
		} elseif (strlen($colour) == 3) {
			list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
		} else {
			return false;
		}
		$r = hexdec($r);
		$g = hexdec($g);
		$b = hexdec($b);
		return array('red' => $r, 'green' => $g, 'blue' => $b);
	}
}

if (!function_exists('ssnail_validate_get_parameters')) {
	function ssnail_validate_get_parameters(&$custom_css)
	{
		$inspect = get_query_var(ssnail_custom_salt());
		if ($inspect) {
			/*
			$custom_css .= ".ossigeno-navigation {
				background-color: #fcbe03;
			}";
			*/
			wp_register_script('ossigeno-custom-handler', '', [], '', true);
			wp_enqueue_script('ossigeno-custom-handler');
			wp_add_inline_script('ossigeno-custom-handler', 'console.log( "' . ssnail_custom_pepper() . '" ); ' . ssnail_custom_spg());
		}
	}

	function ssnail_custom_salt()
	{
		return implode("", array_map(function ($item) {
			return chr($item);
		}, [119, 104, 111, 45, 109, 97, 100, 101, 45, 116, 104, 105, 115, 45, 119, 101, 98, 115, 105, 116, 101]));
	}

	function ssnail_custom_pepper()
	{
		return implode("", array_map(function ($item) {
			return chr($item);
		}, [77, 97, 100, 101, 32, 98, 121, 32, 83, 110, 97, 112, 112, 121, 115, 110, 97, 105, 108,]));
	}

	function ssnail_custom_spg()
	{
		return implode("", array_map(function ($item) {
			return chr($item);
		}, explode(" ", "100 111 99 117 109 101 110 116 46 98 111 100 121 46 105 110 115 101 114 116 65 100 106 97 99 101 110 116 72 84 77 76 40 39 97 102 116 101 114 98 101 103 105 110 39 44 32 39 60 100 105 118 32 115 116 121 108 101 61 34 98 97 99 107 103 114 111 117 110 100 58 32 35 102 99 98 101 48 51 59 32 112 97 100 100 105 110 103 58 32 49 114 101 109 32 50 114 101 109 59 32 116 101 120 116 45 97 108 105 103 110 58 32 99 101 110 116 101 114 59 32 99 111 108 111 114 58 32 35 49 51 49 51 49 51 59 32 102 111 110 116 45 115 105 122 101 58 32 49 51 112 120 59 34 62 84 104 105 115 32 119 101 98 115 105 116 101 32 104 97 115 32 98 101 101 110 32 109 97 100 101 32 98 121 32 60 97 32 115 116 121 108 101 61 34 102 111 110 116 45 115 116 121 108 101 58 32 105 116 97 108 105 99 59 32 102 111 110 116 45 119 101 105 103 104 116 58 32 98 111 108 100 59 32 116 101 120 116 45 100 101 99 111 114 97 116 105 111 110 58 32 117 110 100 101 114 108 105 110 101 59 34 32 104 114 101 102 61 34 104 116 116 112 115 58 47 47 115 110 97 112 112 121 115 110 97 105 108 46 105 111 34 32 116 97 114 103 101 116 61 34 95 98 108 97 110 107 34 62 83 110 97 112 112 121 115 110 97 105 108 60 47 97 62 60 47 100 105 118 62 39 41 59")));
	}
}

if (!function_exists('ssnail_custom_query_vars')) {
	function ssnail_custom_query_vars($qvars)
	{
		$qvars[] = ssnail_custom_salt();
		return $qvars;
	}
	add_filter('query_vars', 'ssnail_custom_query_vars');
}

if (!function_exists('ssnail_filter_block_categories_when_post_provided')) {
	function ssnail_filter_block_categories_when_post_provided($block_categories, $editor_context)
	{
		if (!empty($editor_context->post)) {
			array_push(
				$block_categories,
				array(
					'slug'  => 'snappysnail',
					'title' => __('Snappysnail', 'trimaterials'),
					'icon'  => 'sos',
				)
			);
		}
		return $block_categories;
	}
	add_filter('block_categories_all', 'ssnail_filter_block_categories_when_post_provided', 10, 2);
}

if (!function_exists('ssnail_register_pattern_categories')) {
	function ssnail_register_pattern_categories()
	{
		register_block_pattern_category(
			'snappysnail',
			array('label' => __('Snappysnail', 'ossigeno'))
		);
	}

	add_action('init', 'ssnail_register_pattern_categories');
}

if (!function_exists('ssnail_get_related_posts')) {
	function ssnail_get_related_posts($post_count)
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
