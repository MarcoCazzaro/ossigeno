<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some functionality here could be replaced by core features.
 *
 * @package Ossigeno
 */

if ( ! function_exists( 'ssnail_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	function ssnail_posted_on() {
		$time_string = '<time class="published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);

		printf(
			'<a href="%1$s" rel="bookmark" class="inline-flex items-center gap-1 text-foreground/60 hover:text-primary transition-colors"><span class="material-symbols-outlined text-base" aria-hidden="true">calendar_today</span>%2$s</a>',
			esc_url( get_permalink() ),
			$time_string // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}
endif;

if ( ! function_exists( 'ssnail_posted_by' ) ) :
	/**
	 * Prints HTML with meta information about theme author.
	 */
	function ssnail_posted_by() {
		printf(
		/* translators: 1: posted by label, only visible to screen readers. 2: author link. 3: post author. */
			'<span class="sr-only">%1$s</span><span class="author vcard"><a class="url fn n inline-flex items-center gap-1 text-foreground/60 hover:text-primary transition-colors" href="%2$s"><span class="material-symbols-outlined text-base" aria-hidden="true">person</span>%3$s</a></span>',
			esc_html__( 'Posted by', 'ossigeno' ),
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_html( get_the_author() )
		);
	}
endif;

if ( ! function_exists( 'ssnail_comment_count' ) ) :
	/**
	 * Prints HTML with the comment count for the current post.
	 */
	function ssnail_comment_count() {
		if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="inline-flex items-center gap-1 text-foreground/60"><span class="material-symbols-outlined text-base" aria-hidden="true">chat_bubble</span>';
			/* translators: %s: Name of current post. Only visible to screen readers. */
			comments_popup_link( sprintf( __( 'Leave a comment<span class="sr-only"> on %s</span>', 'ossigeno' ), get_the_title() ), null, null, 'hover:text-primary transition-colors' );
			echo '</span>';
		}
	}
endif;

if ( ! function_exists( 'ssnail_entry_meta' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 * This template tag is used in the entry header.
	 */
	function ssnail_entry_meta() {

		// Hide author, post date, category and tag text for pages.
		if ( 'post' === get_post_type() ) {

			// Posted by.
			ssnail_posted_by();

			// Posted on.
			ssnail_posted_on();
		}

		// Comment count.
		if ( ! is_singular() ) {
			ssnail_comment_count();
		}

		// Edit post link.
		edit_post_link(
			sprintf(
				wp_kses(
				/* translators: %s: Name of current post. Only visible to screen readers. */
					__( 'Edit <span class="sr-only">%s</span>', 'ossigeno' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			)
		);
	}
endif;

if ( ! function_exists( 'ssnail_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function ssnail_entry_footer() {

		// Hide author, post date, category and tag text for pages.
		if ( 'post' === get_post_type() ) {

			// Posted by.
			ssnail_posted_by();

			// Posted on.
			ssnail_posted_on();

			/* translators: used between list items, there is a space after the comma. */
			$categories_list = get_the_category_list( __( ', ', 'ossigeno' ) );
			if ( $categories_list ) {
				printf(
				/* translators: 1: posted in label, only visible to screen readers. 2: list of categories. */
					'<span class="inline-flex items-center gap-1 text-foreground/60"><span class="material-symbols-outlined text-base" aria-hidden="true">folder</span><span class="sr-only">%1$s</span>%2$s</span>',
					esc_html__( 'Posted in', 'ossigeno' ),
					$categories_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}
		}

		// Comment count.
		if ( ! is_singular() ) {
			ssnail_comment_count();
		}

		// Edit post link.
		edit_post_link(
			sprintf(
				wp_kses(
				/* translators: %s: Name of current post. Only visible to screen readers. */
					__( 'Edit <span class="sr-only">%s</span>', 'ossigeno' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			)
		);
	}
endif;

if ( ! function_exists( 'ssnail_get_thumbnail_id' ) ) :
	function ssnail_get_thumbnail_id( $post_id = null ) {
		$thumb_id = get_post_thumbnail_id( $post_id );
		if ( $thumb_id ) {
			return (int) $thumb_id;
		}
		$placeholder = function_exists( 'get_field' ) ? get_field( 'ssnail_opt_placeholder_image', 'option' ) : false;
		return $placeholder ? (int) $placeholder : 0;
	}
endif;

if ( ! function_exists( 'ssnail_post_thumbnail' ) ) :
	/**
	 * Displays an optional post thumbnail, wrapping the post thumbnail in an
	 * anchor element except when viewing a single post.
	 */
	function ssnail_post_thumbnail( string $additional_classes = '' ) {
		$attr = $additional_classes ? [ 'class' => $additional_classes ] : [];

		if ( ssnail_can_show_post_thumbnail() ) {
			$image_html = get_the_post_thumbnail( null, 'post-thumbnail', $attr );
		} else {
			$placeholder_id = function_exists( 'get_field' ) ? get_field( 'ssnail_opt_placeholder_image', 'option' ) : false;
			if ( ! $placeholder_id ) {
				return;
			}
			$image_html = wp_get_attachment_image( $placeholder_id, 'post-thumbnail', false, $attr );
		}

		if ( ! $image_html ) {
			return;
		}

		if ( is_singular() ) :
			?>

			<figure class="ssnail-post-thumbnail">
				<?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</figure><!-- .post-thumbnail -->

			<?php
		else :
			?>

			<figure class="ssnail-post-thumbnail">
				<a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
					<?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			</figure>

			<?php
		endif; // End is_singular().
	}
endif;

if ( ! function_exists( 'ssnail_comment_avatar' ) ) :
	/**
	 * Returns the HTML markup to generate a user avatar.
	 *
	 * @param mixed $id_or_email The Gravatar to retrieve. Accepts a user_id, gravatar md5 hash,
	 *                           user email, WP_User object, WP_Post object, or WP_Comment object.
	 */
	function ssnail_get_user_avatar_markup( $id_or_email = null ) {

		if ( ! isset( $id_or_email ) ) {
			$id_or_email = get_current_user_id();
		}

		return sprintf( '<div class="vcard">%s</div>', get_avatar( $id_or_email, ssnail_get_avatar_size() ) );
	}
endif;

if ( ! function_exists( 'ssnail_discussion_avatars_list' ) ) :
	/**
	 * Displays a list of avatars involved in a discussion for a given post.
	 *
	 * @param array $comment_authors Comment authors to list as avatars.
	 */
	function ssnail_discussion_avatars_list( $comment_authors ) {
		if ( empty( $comment_authors ) ) {
			return;
		}
		echo '<ol>', "\n";
		foreach ( $comment_authors as $id_or_email ) {
			printf(
				"<li>%s</li>\n",
				ssnail_get_user_avatar_markup( $id_or_email ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
		}
		echo '</ol>', "\n";
	}
endif;

if ( ! function_exists( 'ssnail_the_posts_navigation' ) ) :
	/**
	 * Wraps `the_posts_pagination` for use throughout the theme.
	 */
	function ssnail_the_posts_navigation() {
		the_posts_pagination(
			array(
				'mid_size'  => 2,
				'prev_text' => __( 'Newer posts', 'ossigeno' ),
				'next_text' => __( 'Older posts', 'ossigeno' ),
			)
		);
	}
endif;

if ( ! function_exists( 'ssnail_content_class' ) ) :
	/**
	 * Displays the class names for the post content wrapper.
	 *
	 * This allows us to add Tailwind Typography’s modifier classes throughout
	 * the theme without repeating them in multiple files. (They can be edited
	 * at the top of the `../functions.php` file via the
	 * SSNAIL_TYPOGRAPHY_CLASSES constant.)
	 *
	 * Based on WordPress core’s `body_class` and `get_body_class` functions.
	 *
	 * @param string|string[] $classes Space-separated string or array of class
	 *                                 names to add to the class list.
	 */
	function ssnail_content_class( $classes = '' ) {
		$all_classes = array( $classes, SSNAIL_TYPOGRAPHY_CLASSES );

		foreach ( $all_classes as &$class_groups ) {
			if ( ! empty( $class_groups ) ) {
				if ( ! is_array( $class_groups ) ) {
					$class_groups = preg_split( '#\s+#', $class_groups );
				}
			} else {
				// Ensure that we always coerce class to being an array.
				$class_groups = array();
			}
		}

		$combined_classes = array_merge( $all_classes[0], $all_classes[1] );
		$combined_classes = array_map( 'esc_attr', $combined_classes );

		// Separates class names with a single space, preparing them for the
		// post content wrapper.
		echo 'class="' . esc_attr( implode( ' ', $combined_classes ) ) . '"';
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

if ( ! function_exists( 'ssnail_post_tags_pills' ) ) :
	function ssnail_post_tags_pills() {
		$tags = get_the_tags();
		if ( ! $tags ) {
			return;
		}
		echo '<div class="flex flex-wrap gap-2 ssnail-container my-6">';
		foreach ( $tags as $tag ) {
			printf(
				'<a href="%s" class="px-3 py-1 rounded-full text-sm bg-secondary text-background no-underline hover:opacity-80 transition-opacity">%s</a>',
				esc_url( get_tag_link( $tag->term_id ) ),
				esc_html( $tag->name )
			);
		}
		echo '</div>';
	}
endif;

if ( ! function_exists( 'ssnail_share_button' ) ) :
	function ssnail_share_button() {
		global $post;
		?>
		<div
			x-data="{
				copied: false,
				url: '',
				title: '',
				share() {
					const done = () => {
						this.copied = true;
						setTimeout( () => { this.copied = false; }, 2000 );
					};
					if ( navigator.share ) {
						navigator.share( { url: this.url, title: this.title } ).then( done ).catch( () => {} );
					} else if ( navigator.clipboard ) {
						navigator.clipboard.writeText( this.url ).then( done ).catch( () => {} );
					} else {
						const ta = document.createElement( 'textarea' );
						ta.value = this.url;
						ta.style.cssText = 'position:fixed;opacity:0';
						document.body.appendChild( ta );
						ta.focus(); ta.select();
						try { document.execCommand( 'copy' ); done(); } catch ( _ ) {}
						document.body.removeChild( ta );
					}
				}
			}"
			x-init="url = $el.dataset.url; title = $el.dataset.title"
			data-url="<?php echo esc_attr( get_permalink( $post->ID ) ); ?>"
			data-title="<?php echo esc_attr( get_the_title( $post->ID ) ); ?>"
		>
			<button @click="share()" class="cursor-pointer flex items-center gap-2 text-sm text-foreground/60 hover:text-primary transition-colors">
				<span class="material-symbols-outlined text-base leading-none" x-text="copied ? 'check' : 'share'"></span>
				<span x-text="copied ? '<?php echo esc_js( __( 'Copiato!', 'ossigeno' ) ); ?>' : '<?php echo esc_js( __( 'Condividi', 'ossigeno' ) ); ?>'"></span>
			</button>
		</div>
		<?php
	}
endif;

if ( ! function_exists( 'ssnail_post_categories' ) ) :
	function ssnail_post_categories() {
		$categories = get_the_category();
		if ( ! $categories ) {
			return;
		}
		echo '<div class="flex flex-wrap gap-8">';
		foreach ( $categories as $category ) {
			printf(
				'<a href="%s" class="text-sm font-semibold uppercase tracking-widest text-primary no-underline hover:opacity-80 transition-opacity">%s</a>',
				esc_url( get_category_link( $category->term_id ) ),
				esc_html( $category->name )
			);
		}
		echo '</div>';
	}
endif;