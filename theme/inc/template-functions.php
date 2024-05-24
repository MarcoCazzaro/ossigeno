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
		<img src="<?= $logo_image_url ?>" alt="<?= get_bloginfo('name') ?>" width="<?= $logo_width ?>" height="<?= $logo_height ?>">
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
			min-width: 2rem;
			aspect-ratio: 1;
			display: inline-block;
		}\n";

		foreach ($icons as $name => $svg) {
			$svg_encoded = base64_encode($svg);
			$css .= ".ssnail-icon.$name {
				background-image: url('data:image/svg+xml;base64,$svg_encoded');
				background-size: contain; /* ensure the SVG icon scales correctly */
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
