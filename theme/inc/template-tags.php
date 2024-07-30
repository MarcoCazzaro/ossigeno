<?php

/**
 * Custom template tags for this theme
 *
 * Eventually, some functionality here could be replaced by core features.
 *
 * @package Ossigeno
 */

if (!function_exists('ssnail__posted_on')) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	function ssnail__posted_on()
	{
		echo '<div class="flex gap-1 items-center">';
		echo '<i class="fas fa-calendar text-primary"></i>';
		$time_string = '<time datetime="%1$s">%2$s</time>';
		if (get_the_time('U') !== get_the_modified_time('U')) {
			$time_string = '<time datetime="%1$s">%2$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr(get_the_modified_date(DATE_W3C)),
			esc_html(get_the_modified_date())
		);

		printf(
			'<a class="ssnail-posted-on" href="%1$s" rel="bookmark">%2$s</a>',
			esc_url(get_permalink()),
			$time_string // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
		echo '</div>';
	}
endif;

if (!function_exists('ssnail__posted_by')) :
	/**
	 * Prints HTML with meta information about theme author.
	 */
	function ssnail__posted_by()
	{
		echo '<div class="flex gap-1 items-center">';
		echo '<i class="fas fa-user text-primary"></i>';
		printf(
			/* translators: 1: posted by label, only visible to screen readers. 2: author link. 3: post author. */
			'<span class="sr-only">%1$s</span><span class="author vcard"><a class="url fn n" href="%2$s">%3$s</a></span>',
			esc_html__('Posted by', 'ossigeno'),
			esc_url(get_author_posts_url(get_the_author_meta('ID'))),
			esc_html(get_the_author())
		);
		echo '</div>';
	}
endif;

if (!function_exists('ssnail__comment_count')) :
	/**
	 * Prints HTML with the comment count for the current post.
	 */
	function ssnail__comment_count()
	{
		if (!post_password_required() && (comments_open() || get_comments_number())) {
			/* translators: %s: Name of current post. Only visible to screen readers. */
			comments_popup_link(sprintf(__('Leave a comment<span class="sr-only"> on %s</span>', 'ossigeno'), get_the_title()), false, false, 'text-xs font-bold');
		}
	}
endif;

if (!function_exists('ssnail__post_categories')) :
	function ssnail__post_categories()
	{
		if ('post' === get_post_type()) {
			/* translators: used between list items, there is a space after the comma. */
			$categories_list = get_the_category_list(__('', 'ossigeno'));
			if ($categories_list) {
				printf(
					/* translators: 1: posted in label, only visible to screen readers. 2: list of categories. */
					'<span class="sr-only">%1$s</span>%2$s',
					esc_html__('Posted in', 'ossigeno'),
					$categories_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}
		}
	}
endif;


if (!function_exists('ssnail__post_tags')) :
	function ssnail__post_tags()
	{
		if ('post' === get_post_type()) {
			/* translators: used between list items, there is a space after the comma. */
			$tags_list = get_the_tag_list('', __(', ', 'ossigeno'));
			if ($tags_list) {
				printf(
					/* translators: 1: tags label, only visible to screen readers. 2: list of tags. */
					'<span class="sr-only">%1$s</span>%2$s',
					esc_html__('Tags:', 'ossigeno'),
					$tags_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}
		}
	}
endif;

if (!function_exists('ssnail__entry_meta')) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 * This template tag is used in the entry header.
	 */
	function ssnail__entry_meta()
	{

		// Hide author, post date, category and tag text for pages.
		if ('post' === get_post_type()) {

			/* translators: used between list items, there is a space after the comma. */
			$categories_list = get_the_category_list(__('', 'ossigeno'));
			if ($categories_list) {
				printf(
					/* translators: 1: posted in label, only visible to screen readers. 2: list of categories. */
					'<span class="sr-only">%1$s</span>%2$s',
					esc_html__('Posted in', 'ossigeno'),
					$categories_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}

			// Posted by.
			ssnail__posted_by();

			// Posted on.
			ssnail__posted_on();

			/* translators: used between list items, there is a space after the comma. */
			$tags_list = get_the_tag_list('', __(', ', 'ossigeno'));
			if ($tags_list) {
				printf(
					/* translators: 1: tags label, only visible to screen readers. 2: list of tags. */
					'<span class="sr-only">%1$s</span>%2$s',
					esc_html__('Tags:', 'ossigeno'),
					$tags_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}
		}

		// Comment count.
		if (!is_singular()) {
			ssnail__comment_count();
		}

		// Edit post link.
		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers. */
					__('Edit <span class="sr-only">%s</span>', 'ossigeno'),
					array(
						'span' => array(
							'class' => array('text-xs font-bold'),
						),
					)
				),
				get_the_title()
			)
		);
	}
endif;

if (!function_exists('ssnail__entry_footer')) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function ssnail__entry_footer()
	{

		// Hide author, post date, category and tag text for pages.
		if ('post' === get_post_type()) {
			/* translators: used between list items, there is a space after the comma. */
			$categories_list = get_the_category_list(__('', 'ossigeno'));
			if ($categories_list) {
				printf(
					/* translators: 1: posted in label, only visible to screen readers. 2: list of categories. */
					'<span class="sr-only">%1$s</span>%2$s',
					esc_html__('Posted in', 'ossigeno'),
					$categories_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}

			echo '<div class="flex gap-4">';
			// Posted by.
			ssnail__posted_by();

			// Posted on.
			ssnail__posted_on();
			echo '</div>';

			/* translators: used between list items, there is a space after the comma. */
			$tags_list = get_the_tag_list('', '');
			if ($tags_list) {
				printf(
					/* translators: 1: tags label, only visible to screen readers. 2: list of tags. */
					'<div class="ssnail-tags"><span class="sr-only">%1$s</span>%2$s</div>',
					esc_html__('Tags:', 'ossigeno'),
					$tags_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}
		}

		// Comment count.
		if (!is_singular()) {
			ssnail__comment_count();
		}

		// Edit post link.
		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers. */
					__('Edit <span class="sr-only">%s</span>', 'ossigeno'),
					array(
						'span' => array(
							'class' => array('text-xs font-bold'),
						),
					)
				),
				get_the_title()
			)
		);
	}
endif;

if (!function_exists('ssnail__post_thumbnail')) :
	/**
	 * Displays an optional post thumbnail, wrapping the post thumbnail in an
	 * anchor element except when viewing a single post.
	 */
	function ssnail__post_thumbnail($current_post_id = null, $size = 'post-thumbnail', $placeholder_fallback = true, $show_caption = false)
	{
		global $post;
		if (!is_null($current_post_id)) {
			$post = get_post($current_post_id, OBJECT);
			setup_postdata($post);
		}
		if (!ssnail__can_show_post_thumbnail()) {
			return;
		}
		if (!is_singular()) {
			echo '<a href="' . get_the_permalink() . '" aria-hidden="true" tabindex="-1">';
		}
?>
		<figure class="post-thumbnail">
			<?php
			if (has_post_thumbnail()) {
				the_post_thumbnail($size);
			} else {
				if ($placeholder_fallback) {
					$placeholder_image_url = get_template_directory_uri() . "/images/ossigeno-placeholder.webp";
					$image_id = get_option('ossigeno_placeholder_image');
					if ($image_id) {
						$image = wp_get_attachment_image_src($image_id, 'full');
						if (isset($image[0]) && $image[0] !== "") {
							$placeholder_image_url = $image[0];
						}
					}
			?>
					<img src="<?= $placeholder_image_url ?>" alt="<?= get_bloginfo('name') ?>">
				<?php
				}
			}
			if ($show_caption) {
				?>
				<figcaption class="ssnail-post-thumbnail-caption">
					<?php the_post_thumbnail_caption(); ?>
				</figcaption>
			<?php
			}
			?>
		</figure><!-- .post-thumbnail -->
		<?php
		if (!is_singular()) {
			echo '</a>';
		}
	}
endif;

if (!function_exists('ssnail__comment_avatar')) :
	/**
	 * Returns the HTML markup to generate a user avatar.
	 *
	 * @param mixed $id_or_email The Gravatar to retrieve. Accepts a user_id, gravatar md5 hash,
	 *                           user email, WP_User object, WP_Post object, or WP_Comment object.
	 */
	function ssnail__get_user_avatar_markup($id_or_email = null)
	{

		if (!isset($id_or_email)) {
			$id_or_email = get_current_user_id();
		}

		return sprintf('<div class="vcard">%s</div>', get_avatar($id_or_email, ssnail__get_avatar_size()));
	}
endif;

if (!function_exists('ssnail__discussion_avatars_list')) :
	/**
	 * Displays a list of avatars involved in a discussion for a given post.
	 *
	 * @param array $comment_authors Comment authors to list as avatars.
	 */
	function ssnail__discussion_avatars_list($comment_authors)
	{
		if (empty($comment_authors)) {
			return;
		}
		echo '<ol>', "\n";
		foreach ($comment_authors as $id_or_email) {
			printf(
				"<li>%s</li>\n",
				ssnail__get_user_avatar_markup($id_or_email) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
		}
		echo '</ol>', "\n";
	}
endif;

if (!function_exists('ssnail__the_posts_navigation')) :
	/**
	 * Wraps `the_posts_pagination` for use throughout the theme.
	 */
	function ssnail__the_posts_navigation()
	{
		the_posts_pagination(
			array(
				'mid_size'  => 2,
				'prev_text' => __('Newer posts', 'ossigeno'),
				'next_text' => __('Older posts', 'ossigeno'),
			)
		);
	}
endif;

if (!function_exists('ssnail__content_class')) :
	/**
	 * Displays the class names for the post content wrapper.
	 *
	 * This allows us to add Tailwind Typography’s modifier classes throughout
	 * the theme without repeating them in multiple files. (They can be edited
	 * at the top of the `../functions.php` file via the
	 * SSNAIL__TYPOGRAPHY_CLASSES constant.)
	 *
	 * Based on WordPress core’s `body_class` and `get_body_class` functions.
	 *
	 * @param array $classes Space-separated string or array of class names to
	 *                     add to the class list.
	 */
	function ssnail__content_class($classes = '')
	{
		$all_classes = array($classes, SSNAIL__TYPOGRAPHY_CLASSES);

		foreach ($all_classes as &$class_groups) {
			if (!empty($class_groups)) {
				if (!is_array($class_groups)) {
					$class_groups = preg_split('#\s+#', $class_groups);
				}
			} else {
				// Ensure that we always coerce class to being an array.
				$class_groups = array();
			}
		}

		$combined_classes = array_merge($all_classes[0], $all_classes[1]);
		$combined_classes = array_map('esc_attr', $combined_classes);

		// Separates class names with a single space, preparing them for the
		// post content wrapper.
		echo 'class="' . esc_attr(implode(' ', $combined_classes)) . '"';
	}
endif;

// Share panel for social share links called ssnail__share_links
if (!function_exists('ssnail__share_links')) :
	function ssnail__share_links($section_title = 'Share')
	{
		$url = get_permalink($post->ID);
		$title = get_the_title($post->ID);
		$via = "ForbesItalia";
		?>
		<div class="ssnail-share-links relative">
			<h6 class="text-uppercase"><?php echo __($section_title, 'ossigeno') ?></h6>
			<div class="flex gap-3 text-xl">
				<a href="https://www.facebook.com/sharer/sharer.php?u=<?= $url ?>" target="_blank" rel="noopener" class="text-facebook hover:text-primary transition-colors"><i class="fab fa-facebook-f"></i></a>
				<a href="https://api.whatsapp.com/send?&text=<?= $url ?>" target="_blank" rel="noopener" class="text-whatsapp hover:text-primary transition-colors"><i class="fab fa-whatsapp"></i></a>
				<a target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?text=<?php echo get_the_title($post->ID); ?>&url=<?= $url ?>&via=<?= $via ?>" class="text-x-twitter hover:text-primary transition-colors"><i class="fab fa-x-twitter"></i></a>
				<a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= $url ?>&title=<?= $url ?>&summary=&source=" target="_blank" rel="noopener" class="text-linkedin hover:text-primary transition-colors"><i class="fab fa-linkedin-in"></i></a>
			</div>
		</div>
<?php
	}
endif;

if (!function_exists('ssnail_acf_image_with_srcset')) {
	function ssnail_acf_image_with_srcset($acf_field_or_name, $size = 'post-thumbnail', $alt = false, $class = 'attachment-SSNAILSIZE size-SSNAILSIZE')
	{
		switch (true) {
			case is_numeric($acf_field_or_name):
			case is_array($acf_field_or_name):
			case filter_var($acf_field_or_name, FILTER_VALIDATE_URL):
				$image = $acf_field_or_name;
				break;

			default:
				$image = get_field($acf_field_or_name);
				break;
		}
		$html = false;
		$args = [];
		if ($alt) {
			$args['alt'] = $alt;
		} else {
			$alt = '';
		}
		if ($class) {
			$class = str_replace("SSNAILSIZE", $size, $class);
			$args['class'] = $class;
		}
		if ($image) {
			switch (true) {
				case is_numeric($image):
					$html = wp_get_attachment_image($image, $size, false, $args);
					break;
				case is_array($image) && isset($image['ID']):
					$html = wp_get_attachment_image($image['ID'], $size, false, $args);
					break;

				case filter_var($image, FILTER_VALIDATE_URL):
					$html = "<img src='" . $image . "' alt='" . $alt . "' class='" . $class . "' >";
					break;

				default:
					// code...
					break;
			}
		}
		if ($html) {
			echo $html;
		}
	}
}
