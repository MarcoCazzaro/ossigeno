<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Ossigeno
 */

/**
 * Register ACF Options Page for global site settings (phone, email, offices).
 *
 * Fields are defined in inc/acf-blocks/acf-fields.php under group_ssnail_options.
 * The menu slug 'acf-options' is the location key used by that field group.
 */
if ( function_exists( 'acf_add_options_page' ) ) {
	acf_add_options_page(
		array(
			'page_title' => __( 'Opzioni sito', 'ossigeno' ),
			'menu_title' => __( 'Opzioni sito', 'ossigeno' ),
			'menu_slug'  => 'acf-options',
			'capability' => 'manage_options',
			'redirect'   => false,
		)
	);
}

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function ssnail_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'ssnail_pingback_header' );

/**
 * Changes comment form default fields.
 *
 * @param array $defaults The default comment form arguments.
 *
 * @return array Returns the modified fields.
 */
function ssnail_comment_form_defaults( $defaults ) {
	$comment_field = $defaults['comment_field'];

	// Adjust height of comment form.
	$defaults['comment_field'] = preg_replace( '/rows="\d+"/', 'rows="5"', $comment_field );

	return $defaults;
}
add_filter( 'comment_form_defaults', 'ssnail_comment_form_defaults' );

/**
 * Filters the default archive titles.
 */
function ssnail_get_the_archive_title() {
	if ( is_category() ) {
		$title = __( 'Category Archives: ', 'ossigeno' ) . '<span>' . single_term_title( '', false ) . '</span>';
	} elseif ( is_tag() ) {
		$title = __( 'Tag Archives: ', 'ossigeno' ) . '<span>' . single_term_title( '', false ) . '</span>';
	} elseif ( is_author() ) {
		$title = __( 'Author Archives: ', 'ossigeno' ) . '<span>' . get_the_author_meta( 'display_name' ) . '</span>';
	} elseif ( is_year() ) {
		$title = __( 'Yearly Archives: ', 'ossigeno' ) . '<span>' . get_the_date( _x( 'Y', 'yearly archives date format', 'ossigeno' ) ) . '</span>';
	} elseif ( is_month() ) {
		$title = __( 'Monthly Archives: ', 'ossigeno' ) . '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', 'ossigeno' ) ) . '</span>';
	} elseif ( is_day() ) {
		$title = __( 'Daily Archives: ', 'ossigeno' ) . '<span>' . get_the_date() . '</span>';
	} elseif ( is_post_type_archive() ) {
		$cpt   = get_post_type_object( get_queried_object()->name );
		$title = sprintf(
			/* translators: %s: Post type singular name */
			esc_html__( '%s Archives', 'ossigeno' ),
			$cpt->labels->singular_name
		);
	} elseif ( is_tax() ) {
		$tax   = get_taxonomy( get_queried_object()->taxonomy );
		$title = sprintf(
			/* translators: %s: Taxonomy singular name */
			esc_html__( '%s Archives', 'ossigeno' ),
			$tax->labels->singular_name
		);
	} else {
		$title = __( 'Archives:', 'ossigeno' );
	}
	return $title;
}
add_filter( 'get_the_archive_title', 'ssnail_get_the_archive_title' );

/**
 * Determines whether the post thumbnail can be displayed.
 */
function ssnail_can_show_post_thumbnail() {
	return apply_filters( 'ssnail_can_show_post_thumbnail', ! post_password_required() && ! is_attachment() && has_post_thumbnail() );
}

/**
 * Returns the size for avatars used in the theme.
 */
function ssnail_get_avatar_size() {
	return 60;
}

/**
 * Create the continue reading link
 *
 * @param string $more_string The string shown within the more link.
 */
function ssnail_continue_reading_link( $more_string ) {

	if ( ! is_admin() ) {
		$continue_reading = sprintf(
			/* translators: %s: Name of current post. */
			wp_kses( __( 'Continue reading %s', 'ossigeno' ), array( 'span' => array( 'class' => array() ) ) ),
			the_title( '<span class="sr-only">"', '"</span>', false )
		);

		$more_string = '<a href="' . esc_url( get_permalink() ) . '">' . $continue_reading . '</a>';
	}

	return $more_string;
}

// Filter the excerpt more link.
add_filter( 'excerpt_more', 'ssnail_continue_reading_link' );

// Filter the content more link.
add_filter( 'the_content_more_link', 'ssnail_continue_reading_link' );

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
function ssnail_html5_comment( $comment, $args, $depth ) {
	$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';

	$commenter          = wp_get_current_commenter();
	$show_pending_links = ! empty( $commenter['comment_author'] );

	if ( $commenter['comment_author_email'] ) {
		$moderation_note = __( 'Your comment is awaiting moderation.', 'ossigeno' );
	} else {
		$moderation_note = __( 'Your comment is awaiting moderation. This is a preview; your comment will be visible after it has been approved.', 'ossigeno' );
	}
	?>
	<<?php echo esc_attr( $tag ); ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( $comment->has_children ? 'parent' : '', $comment ); ?>>
		<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
			<footer class="comment-meta">
				<div class="comment-author vcard">
					<?php
					if ( 0 !== $args['avatar_size'] ) {
						echo get_avatar( $comment, $args['avatar_size'] );
					}
					?>
					<?php
					$comment_author = get_comment_author_link( $comment );

					if ( '0' === $comment->comment_approved && ! $show_pending_links ) {
						$comment_author = get_comment_author( $comment );
					}

					printf(
						/* translators: %s: Comment author link. */
						wp_kses_post( __( '%s <span class="says">says:</span>', 'ossigeno' ) ),
						sprintf( '<b class="fn">%s</b>', wp_kses_post( $comment_author ) )
					);
					?>
				</div><!-- .comment-author -->

				<div class="comment-metadata">
					<?php
					printf(
						'<a href="%s"><time datetime="%s">%s</time></a>',
						esc_url( get_comment_link( $comment, $args ) ),
						esc_attr( get_comment_time( 'c' ) ),
						esc_html(
							sprintf(
							/* translators: 1: Comment date, 2: Comment time. */
								__( '%1$s at %2$s', 'ossigeno' ),
								get_comment_date( '', $comment ),
								get_comment_time()
							)
						)
					);

					edit_comment_link( __( 'Edit', 'ossigeno' ), ' <span class="edit-link">', '</span>' );
					?>
				</div><!-- .comment-metadata -->

				<?php if ( '0' === $comment->comment_approved ) : ?>
				<em class="comment-awaiting-moderation"><?php echo esc_html( $moderation_note ); ?></em>
				<?php endif; ?>
			</footer><!-- .comment-meta -->

			<div <?php ssnail_content_class( 'comment-content' ); ?>>
				<?php comment_text(); ?>
			</div><!-- .comment-content -->

			<?php
			if ( '1' === $comment->comment_approved || $show_pending_links ) {
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

if (!function_exists('ssnail_check_required_plugins')) {
	function ssnail_check_required_plugins()
	{
		if (!function_exists('get_field')) {
			?>
			<div class="notice notice-error is-dismissible">
				<p><?= ucfirst(__("Il tema richiede l'installazione del plugin", 'ossigeno')) ?> <b>Advanced Custom Fields PRO</b>.</p>
			</div>
		<?php
		}
	}
	add_action('admin_notices', 'ssnail_check_required_plugins');
}

/**
 * Allow SVG uploads in the WordPress Media Library.
 *
 * SVGs are blocked by default. We whitelist the MIME type and fix the
 * file-content verification added in WP 4.7 so existing SVG files are also
 * recognised correctly. Access is limited to users with upload_files (Editor+).
 */
function ssnail_allow_svg_uploads( array $mimes ): array {
	if ( current_user_can( 'upload_files' ) ) {
		$mimes['svg']  = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';
	}
	return $mimes;
}
add_filter( 'upload_mimes', 'ssnail_allow_svg_uploads' );

/**
 * Fix SVG MIME-type detection in wp_check_filetype_and_ext().
 *
 * WP verifies file contents against the declared MIME type; SVGs fail that
 * check unless we explicitly confirm the type for .svg/.svgz extensions.
 *
 * @param array  $data     Filetype data.
 * @param string $file     Full path to the file.
 * @param string $filename Filename with extension.
 * @param array  $mimes    Allowed MIME types.
 */
function ssnail_fix_svg_mime_check( array $data, string $file, string $filename, ?array $mimes ): array {
	if ( ! $data['type'] ) {
		$ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
		if ( in_array( $ext, array( 'svg', 'svgz' ), true ) ) {
			$data['type'] = 'image/svg+xml';
			$data['ext']  = $ext;
		}
	}
	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'ssnail_fix_svg_mime_check', 10, 4 );

/**
 * Imports the theme's bundled images into the WordPress Media Library,
 * then auto-wires them as defaults for the logotype, site icon, and placeholder slots.
 *
 * The guard is per-image: each image is tracked by its own option so a single
 * failure (e.g. SVG blocked) does not permanently prevent the others from
 * being retried. The transient is set only once all three images are present,
 * giving a fast no-op path on subsequent requests.
 *
 * Attachment IDs stored in options:
 *   - ssnail_img_logo_svg    → ossigeno-logo.svg
 *   - ssnail_img_logo_png    → ossigeno-logo.png
 *   - ssnail_img_placeholder → ossigeno-placeholder.webp
 *
 * Auto-wired defaults (only when the slot is currently empty):
 *   - ssnail_custom_logotype          (Customizer) ← SVG
 *   - site_icon                       (WP core)    ← PNG  (generates all favicon sizes)
 *   - ssnail_opt_placeholder_image    (ACF option) ← WebP
 */
function ssnail_import_theme_images(): void {
	// Fast path: all images already imported and transient is warm.
	if ( get_transient( 'ssnail_theme_images_imported' ) ) {
		return;
	}

	$images = array(
		'ssnail_img_logo_svg'    => 'ossigeno-logo.svg',
		'ssnail_img_logo_png'    => 'ossigeno-logo.png',
		'ssnail_img_placeholder' => 'ossigeno-placeholder.webp',
	);

	// Collect already-imported IDs; identify which images still need importing.
	$ids     = array();
	$pending = array();

	foreach ( $images as $option_key => $filename ) {
		$existing = get_option( $option_key );
		if ( $existing ) {
			$ids[ $option_key ] = (int) $existing;
		} else {
			$pending[ $option_key ] = $filename;
		}
	}

	// All done — re-arm the transient and bail.
	if ( empty( $pending ) ) {
		set_transient( 'ssnail_theme_images_imported', true, YEAR_IN_SECONDS );
		return;
	}

	// At least one image is missing — load WP media helpers.
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	foreach ( $pending as $option_key => $filename ) {
		$source = get_template_directory() . '/images/' . $filename;

		if ( ! file_exists( $source ) ) {
			continue;
		}

		// media_handle_sideload() needs a temp copy it can move/delete freely.
		$tmp = wp_tempnam( $filename );
		if ( ! copy( $source, $tmp ) ) {
			continue;
		}

		$attachment_id = media_handle_sideload(
			array( 'name' => $filename, 'tmp_name' => $tmp ),
			0 // not attached to any post
		);

		if ( ! is_wp_error( $attachment_id ) ) {
			$ids[ $option_key ] = $attachment_id;
			update_option( $option_key, $attachment_id, false );
		}
	}

	// Auto-wire defaults — only when each slot is currently empty so admin
	// choices made after the first install are never silently overwritten.
	if ( ! empty( $ids['ssnail_img_logo_svg'] ) && ! get_theme_mod( 'ssnail_custom_logotype' ) ) {
		set_theme_mod( 'ssnail_custom_logotype', $ids['ssnail_img_logo_svg'] );
	}

	if ( ! empty( $ids['ssnail_img_logo_png'] ) && ! get_option( 'site_icon' ) ) {
		update_option( 'site_icon', $ids['ssnail_img_logo_png'] );
	}

	if (
		! empty( $ids['ssnail_img_placeholder'] )
		&& function_exists( 'update_field' )
		&& ! get_field( 'ssnail_opt_placeholder_image', 'option' )
	) {
		update_field( 'ssnail_opt_placeholder_image', $ids['ssnail_img_placeholder'], 'option' );
	}

	// Arm the transient only once every image is present.
	if ( count( $ids ) === count( $images ) ) {
		set_transient( 'ssnail_theme_images_imported', true, YEAR_IN_SECONDS );
	}
}
add_action( 'init', 'ssnail_import_theme_images' );