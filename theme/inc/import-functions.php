<?php
/**
 * Shared import logic for Ossigeno demo data.
 *
 * Consumed by:
 *   - bin/import-demo-data.php   (WP-CLI entry point)
 *   - theme/inc/admin-import.php (WP admin entry point)
 *
 * Every public function accepts a $log callable with signature:
 *   callable( string $type, string $message ): void
 * where $type is one of 'line', 'warning', 'success'.
 *
 * Image paths are passed explicitly via the $images_dir parameter so
 * both entry points can point to their respective bin/images/ location.
 *
 * @package Ossigeno
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ABSPATH . 'wp-admin/includes/image.php';

// =============================================================================
// LOW-LEVEL HELPERS
// =============================================================================

/**
 * Returns the post ID of an already-imported post, or null if not found.
 */
function ssnail_import_find( string $guid ): ?int {
	global $wpdb;
	$post_id = $wpdb->get_var( $wpdb->prepare(
		"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'ssnail-post-import-guid' AND meta_value = %s LIMIT 1",
		$guid
	) );
	return $post_id ? (int) $post_id : null;
}

/**
 * Copies a local file from $images_dir into the WP media library,
 * attaches it to $post_id, and returns the attachment ID or null on failure.
 */
function ssnail_import_local_image( string $filename, int $post_id, string $title, string $images_dir, callable $log ): ?int {
	$src = trailingslashit( $images_dir ) . $filename;

	if ( ! file_exists( $src ) ) {
		$log( 'warning', "    Image not found: {$filename} -- skipping" );
		return null;
	}

	$mime = wp_check_filetype( $filename );
	if ( empty( $mime['type'] ) ) {
		$log( 'warning', "    Unrecognised mime type for {$filename} -- skipping" );
		return null;
	}

	$upload = wp_upload_bits( $filename, null, file_get_contents( $src ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( ! empty( $upload['error'] ) ) {
		$log( 'warning', "    Upload failed for {$filename}: {$upload['error']}" );
		return null;
	}

	$att_id = wp_insert_attachment(
		array(
			'post_title'     => $title ?: pathinfo( $filename, PATHINFO_FILENAME ),
			'post_status'    => 'inherit',
			'post_mime_type' => $mime['type'],
			'guid'           => $upload['url'],
		),
		$upload['file'],
		$post_id
	);

	if ( is_wp_error( $att_id ) ) {
		$log( 'warning', "    Attachment insert failed for {$filename}: " . $att_id->get_error_message() );
		return null;
	}

	wp_update_attachment_metadata( $att_id, wp_generate_attachment_metadata( $att_id, $upload['file'] ) );

	return (int) $att_id;
}

/**
 * Returns a core/spacer block string for use in post_content.
 */
function ssnail_import_spacer( string $height = '5rem' ): string {
	return "\n<!-- wp:spacer {\"height\":\"{$height}\"} -->\n<div style=\"height:{$height}\" aria-hidden=\"true\" class=\"wp-block-spacer\"></div>\n<!-- /wp:spacer -->\n";
}

/**
 * Serializes an ACF block into a Gutenberg block comment for post_content.
 */
function ssnail_import_block( string $name, array $data, array $attrs = array() ): string {
	static $counter = 0;
	$id    = 'block_' . substr( md5( $name . ( ++$counter ) ), 0, 8 );
	$block = array_merge(
		array( 'id' => $id, 'name' => $name, 'data' => $data, 'mode' => 'preview' ),
		$attrs
	);
	// JSON_UNESCAPED_UNICODE keeps accented chars as UTF-8 bytes instead of
	// \uXXXX escapes, which MySQL would strip (unknown \u sequence = backslash dropped).
	return "\n<!-- wp:{$name} " . wp_json_encode( $block, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . " /-->\n";
}

// =============================================================================
// DESTRUCTIVE CLEANUP
// =============================================================================

/**
 * Deletes all content previously created by the import script.
 */
function ssnail_import_cleanup( callable $log ): void {
	// Sample news posts.
	$news_guids   = array(
		'ssnail-post-sample-1',
		'ssnail-post-sample-2',
		'ssnail-post-sample-3',
	);
	$deleted_news = 0;
	foreach ( $news_guids as $guid ) {
		$id = ssnail_import_find( $guid );
		if ( $id ) {
			$thumb = get_post_thumbnail_id( $id );
			if ( $thumb ) {
				wp_delete_attachment( $thumb, true );
			}
			wp_delete_post( $id, true );
			$deleted_news++;
		}
	}
	$log( 'line', "  Sample news posts deleted: {$deleted_news}" );

	// Home page + its media attachments.
	$home_id = ssnail_import_find( 'ssnail-homepage' );
	if ( $home_id ) {
		$attachments = get_posts( array(
			'post_type'      => 'attachment',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'post_parent'    => $home_id,
			'fields'         => 'ids',
		) );
		foreach ( $attachments as $att_id ) {
			wp_delete_attachment( $att_id, true );
		}
		wp_delete_post( $home_id, true );
		$log( 'line', '  Home page deleted (+ ' . count( $attachments ) . ' attachment(s)).' );
	} else {
		$log( 'line', '  No home page found.' );
	}

	// Placeholder featured image (stored in ACF Options).
	$old_placeholder_id = get_field( 'ssnail_opt_placeholder_image', 'option' );
	if ( $old_placeholder_id ) {
		wp_delete_attachment( (int) $old_placeholder_id, true );
		$log( 'line', '  Placeholder featured image deleted.' );
	}

	// News page.
	$news_page_del_id = ssnail_import_find( 'ssnail-newspage' );
	if ( $news_page_del_id ) {
		wp_delete_post( $news_page_del_id, true );
		$log( 'line', '  News page deleted.' );
	} else {
		$log( 'line', '  No news page found.' );
	}

	// Site logo attachment.
	$old_logo_id = get_theme_mod( 'custom_logo' );
	if ( $old_logo_id ) {
		wp_delete_attachment( (int) $old_logo_id, true );
		remove_theme_mod( 'custom_logo' );
		$log( 'line', '  Site logo deleted.' );
	}

	// Site icon attachment.
	$old_icon_id = (int) get_option( 'site_icon' );
	if ( $old_icon_id ) {
		wp_delete_attachment( $old_icon_id, true );
		update_option( 'site_icon', 0 );
		$log( 'line', '  Site icon deleted.' );
	}

	// Placeholder pages.
	foreach ( array( 'ssnail-page-privacy-policy', 'ssnail-page-cookie-policy' ) as $pg_guid ) {
		$pg_id = ssnail_import_find( $pg_guid );
		if ( $pg_id ) {
			wp_delete_post( $pg_id, true );
			$log( 'line', "  Placeholder page \"{$pg_guid}\" deleted." );
		}
	}

	// Navigation menus.
	foreach ( array( 'Primary', 'Footer', 'Footer Legal', 'Social' ) as $mn ) {
		$menu_obj = wp_get_nav_menu_object( $mn );
		if ( $menu_obj ) {
			wp_delete_nav_menu( $menu_obj->term_id );
			$log( 'line', "  Menu \"{$mn}\" deleted." );
		}
	}
}

// =============================================================================
// SECTION FUNCTIONS
// =============================================================================

/**
 * Imports the site logo and site icon.
 */
function ssnail_import_site_identity( callable $log, bool $destructive, string $images_dir ): void {
	$existing_logo_id = get_theme_mod( 'custom_logo' );
	if ( ! $destructive && $existing_logo_id ) {
		$log( 'line', '  [skip] Site logo already set.' );
	} else {
		$logo_att_id = ssnail_import_local_image( 'ossigeno-logo.svg', 0, 'Ossigeno', $images_dir, $log );
		if ( $logo_att_id ) {
			set_theme_mod( 'custom_logo', $logo_att_id );
			$log( 'success', "  [ok]   Site logo set (attachment ID {$logo_att_id})." );
		}
	}

	$existing_icon_id = (int) get_option( 'site_icon' );
	if ( ! $destructive && $existing_icon_id ) {
		$log( 'line', '  [skip] Site icon already set.' );
	} else {
		$icon_att_id = ssnail_import_local_image( 'site-icon.png', 0, 'Ossigeno Icon', $images_dir, $log );
		if ( $icon_att_id ) {
			update_option( 'site_icon', $icon_att_id );
			$log( 'success', "  [ok]   Site icon set (attachment ID {$icon_att_id})." );
		}
	}
}

/**
 * Sets ACF Options values (phone, email, WhatsApp, offices, placeholder image).
 */
function ssnail_import_acf_options( callable $log, bool $destructive, string $images_dir ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	update_field( 'ssnail_opt_phone',            '+39 02 0000 0000',          'option' );
	update_field( 'ssnail_opt_whatsapp_number',  '+39 333 0000000',           'option' );
	update_field( 'ssnail_opt_whatsapp_cta',     'Scrivici su WhatsApp',      'option' );
	update_field( 'ssnail_opt_email',            'info@esempio.it',           'option' );
	update_field( 'ssnail_opt_scroll_to_top',    false,                       'option' );

	$placeholder_id = ssnail_import_local_image( 'ossigeno-placeholder.webp', 0, 'Immagine segnaposto articoli', $images_dir, $log );
	if ( $placeholder_id ) {
		update_field( 'ssnail_opt_placeholder_image', $placeholder_id, 'option' );
	}

	update_field( 'ssnail_opt_offices', array(
		array(
			'city'     => 'Milano',
			'address'  => 'Via Esempio 1',
			'maps_url' => 'https://maps.google.com/maps?q=Milano,+Italy&output=embed',
		),
	), 'option' );

	$log( 'success', '  Options updated (phone, email, WhatsApp, 1 office).' );
}

/**
 * Imports 3 sample news posts.
 */
function ssnail_import_news_posts( callable $log, bool $destructive, string $images_dir, ?int $index = null ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	$sample_posts = array(
		array(
			'guid'    => 'ssnail-post-sample-1',
			'title'   => 'Titolo articolo di esempio 1',
			'excerpt' => 'Breve descrizione del primo articolo di esempio per mostrare il layout della griglia post.',
			'date'    => '2024-03-10 09:00:00',
			'photo'   => 'ossigeno-placeholder.webp',
		),
		array(
			'guid'    => 'ssnail-post-sample-2',
			'title'   => 'Titolo articolo di esempio 2',
			'excerpt' => 'Breve descrizione del secondo articolo di esempio per mostrare il layout della griglia post.',
			'date'    => '2024-02-15 09:00:00',
			'photo'   => 'ossigeno-placeholder.webp',
		),
		array(
			'guid'    => 'ssnail-post-sample-3',
			'title'   => 'Titolo articolo di esempio 3',
			'excerpt' => 'Breve descrizione del terzo articolo di esempio per mostrare il layout della griglia post.',
			'date'    => '2024-01-20 09:00:00',
			'photo'   => 'ossigeno-placeholder.webp',
		),
	);

	if ( null !== $index ) {
		$sample_posts = isset( $sample_posts[ $index ] ) ? array( $sample_posts[ $index ] ) : array();
	}

	foreach ( $sample_posts as $sp ) {
		$existing = ssnail_import_find( $sp['guid'] );
		if ( $existing ) {
			$log( 'line', "  [skip] {$sp['title']} (ID {$existing})" );
			continue;
		}

		$post_id = wp_insert_post( array(
			'post_type'     => 'post',
			'post_title'    => $sp['title'],
			'post_excerpt'  => $sp['excerpt'],
			'post_content'  => "<p>{$sp['excerpt']}</p>",
			'post_status'   => 'publish',
			'post_date'     => $sp['date'],
			'post_date_gmt' => get_gmt_from_date( $sp['date'] ),
		), true );

		if ( is_wp_error( $post_id ) ) {
			$log( 'warning', "  [error] {$sp['title']}: " . $post_id->get_error_message() );
			continue;
		}

		update_post_meta( $post_id, 'ssnail-post-import-guid', $sp['guid'] );

		$thumb_id = ssnail_import_local_image( $sp['photo'], $post_id, $sp['title'], $images_dir, $log );
		if ( $thumb_id ) {
			set_post_thumbnail( $post_id, $thumb_id );
		}

		$log( 'success', "  [ok]   {$sp['title']} (ID {$post_id})" );
	}
}

/**
 * Creates the Home page with 4 ACF blocks pre-filled.
 * Returns the home page ID (existing or newly created), or 0 on failure.
 */
function ssnail_import_home_page( callable $log, bool $destructive, string $images_dir ): int { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	$page_guid     = 'ssnail-homepage';
	$existing_page = ssnail_import_find( $page_guid );

	if ( $existing_page ) {
		$log( 'line', "  [skip] Home page already exists (ID {$existing_page}). Delete post {$existing_page} to reimport." );
		return $existing_page;
	}

	$page_id = wp_insert_post( array(
		'post_type'   => 'page',
		'post_title'  => 'Home',
		'post_status' => 'draft',
	), true );

	if ( is_wp_error( $page_id ) ) {
		$log( 'warning', '  [error] Could not create home page: ' . $page_id->get_error_message() );
		return 0;
	}

	$cover_img_id = ssnail_import_local_image( 'ossigeno-placeholder.webp', $page_id, 'Cover home page', $images_dir, $log );
	$section_img_id = ssnail_import_local_image( 'ossigeno-placeholder.webp', $page_id, 'Immagine sezione', $images_dir, $log );

	// -- Block 1: Splash Cover ---------------------------------------------------
	$cover_data = array(
		'tipo_cover'   => 'immagine',
		'_tipo_cover'  => 'field_686655a57c8ab',
		'altezza'      => 600,
		'_altezza'     => 'field_6866552144b47',
		'titolo'       => 'Titolo Principale',
		'_titolo'      => 'field_686e22d85ae9a',
		'sottotitolo'  => 'Sottotitolo descrittivo del sito',
		'_sottotitolo' => 'field_686e22e55ae9b',
		'immagine'     => $cover_img_id ?: '',
		'_immagine'    => 'field_686655ce7c8ac',
	);

	// -- Block 2: Image with Text ------------------------------------------------
	$image_text_data = array(
		'layout'       => 'immagine-destra',
		'_layout'      => 'field_68667ee7ed92b',
		'immagine'     => $section_img_id ?: '',
		'_immagine'    => 'field_68667ee7ed96a',
		'titolo'       => 'Il Nostro Approccio',
		'_titolo'      => 'field_68667ee7ed9e2',
		'descrizione'  => '<p>Inserire qui una descrizione della sezione. Questo testo è un segnaposto da sostituire con il contenuto definitivo.</p>',
		'_descrizione' => 'field_68667f0131676',
	);

	// -- Block 3: Posts Grid -----------------------------------------------------
	$posts_grid_data = array(
		'numero_post'         => 6,
		'_numero_post'        => 'field_6862b7125b15a',
		'stile'               => 'chiaro',
		'_stile'              => 'field_6862b6a64567f',
		'layout'              => 'container',
		'_layout'             => 'field_6862b8306577d',
		'bottone_vedi_tutti'  => 1,
		'_bottone_vedi_tutti' => 'field_68638aea28241',
	);

	// -- Block 4: Contact --------------------------------------------------------
	$contact_data = array(
		'ssnail_contact_heading'     => 'Contattaci',
		'_ssnail_contact_heading'    => 'field_ssnail_contact_heading',
		'ssnail_contact_form_intro'  => 'Compila il modulo per ricevere maggiori informazioni o fissare un appuntamento.',
		'_ssnail_contact_form_intro' => 'field_ssnail_contact_form_intro',
	);

	$content  = ssnail_import_block( 'ossigeno/splash-cover',    $cover_data );
	$content .= ssnail_import_spacer();
	$content .= ssnail_import_block( 'ossigeno/image-with-text', $image_text_data );
	$content .= ssnail_import_spacer();
	$content .= ssnail_import_block( 'ossigeno/posts-grid',      $posts_grid_data );
	$content .= ssnail_import_spacer();
	$content .= ssnail_import_block( 'ossigeno/contact',         $contact_data, array( 'anchor' => 'contatti' ) );

	wp_update_post( array(
		'ID'           => $page_id,
		'post_content' => $content,
		'post_status'  => 'publish',
	) );

	update_post_meta( $page_id, 'ssnail-post-import-guid', $page_guid );
	update_post_meta( $page_id, '_wp_page_template', 'page-templates/homepage.php' );

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $page_id );

	$log( 'success', "  [ok]   Home page created (ID {$page_id}), set as static front page." );

	return $page_id;
}

/**
 * Creates the News (blog) page and sets it as the static posts page.
 * Returns the news page ID (existing or newly created), or 0 on failure.
 */
function ssnail_import_news_page( callable $log, bool $destructive, int $home_page_id ): int { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed, Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterfaceAfterLastUsed
	$news_page_guid     = 'ssnail-newspage';
	$existing_news_page = ssnail_import_find( $news_page_guid );

	if ( $existing_news_page ) {
		$log( 'line', "  [skip] News page already exists (ID {$existing_news_page}). Delete post {$existing_news_page} to reimport." );
		return $existing_news_page;
	}

	$news_page_id = wp_insert_post( array(
		'post_type'    => 'page',
		'post_title'   => 'Blog',
		'post_status'  => 'publish',
		'post_content' => '',
	), true );

	if ( is_wp_error( $news_page_id ) ) {
		$log( 'warning', '  [error] Could not create news page: ' . $news_page_id->get_error_message() );
		return 0;
	}

	update_post_meta( $news_page_id, 'ssnail-post-import-guid', $news_page_guid );
	update_option( 'page_for_posts', $news_page_id );
	$log( 'success', "  [ok]   News page created (ID {$news_page_id}), set as static posts page." );

	return (int) $news_page_id;
}

/**
 * Creates Privacy Policy and Cookie Policy placeholder pages.
 * Returns an array mapping import GUID to post ID.
 */
function ssnail_import_placeholder_pages( callable $log, bool $destructive ): array { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	$pages   = array(
		array( 'guid' => 'ssnail-page-privacy-policy', 'title' => 'Privacy Policy' ),
		array( 'guid' => 'ssnail-page-cookie-policy',  'title' => 'Cookie Policy' ),
	);
	$page_ids = array();

	foreach ( $pages as $page ) {
		$existing = ssnail_import_find( $page['guid'] );
		if ( $existing ) {
			$log( 'line', "  [skip] {$page['title']} (ID {$existing})" );
			$page_ids[ $page['guid'] ] = $existing;
			continue;
		}

		$pp_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_title'   => $page['title'],
				'post_status'  => 'publish',
				'post_content' => '',
			),
			true
		);

		if ( is_wp_error( $pp_id ) ) {
			$log( 'warning', "  [error] {$page['title']}: " . $pp_id->get_error_message() );
			continue;
		}

		update_post_meta( $pp_id, 'ssnail-post-import-guid', $page['guid'] );
		$page_ids[ $page['guid'] ] = $pp_id;
		$log( 'success', "  [ok]   {$page['title']} (ID {$pp_id})" );
	}

	return $page_ids;
}

/**
 * Creates and assigns the Primary and Footer navigation menus.
 */
function ssnail_import_menus( callable $log, bool $destructive, int $home_page_id, int $news_page_id, array $page_ids ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	$home_url    = $home_page_id ? get_permalink( $home_page_id ) : get_home_url( null, '/' );
	$news_url    = $news_page_id ? get_permalink( $news_page_id ) : $home_url;
	$privacy_url = isset( $page_ids['ssnail-page-privacy-policy'] )
		? get_permalink( $page_ids['ssnail-page-privacy-policy'] )
		: '#';
	$cookie_url  = isset( $page_ids['ssnail-page-cookie-policy'] )
		? get_permalink( $page_ids['ssnail-page-cookie-policy'] )
		: '#';

	$primary_items = array(
		array( 'menu-item-title' => 'Home',     'menu-item-url' => $home_url,   'menu-item-status' => 'publish', 'menu-item-type' => 'custom' ),
		array( 'menu-item-title' => 'Blog',     'menu-item-url' => $news_url,   'menu-item-status' => 'publish', 'menu-item-type' => 'custom' ),
		array( 'menu-item-title' => 'Contatti', 'menu-item-url' => '#contatti', 'menu-item-status' => 'publish', 'menu-item-type' => 'custom' ),
	);

	$footer_legal_items = array(
		array( 'menu-item-title' => 'Privacy Policy', 'menu-item-url' => $privacy_url, 'menu-item-status' => 'publish', 'menu-item-type' => 'custom' ),
		array( 'menu-item-title' => 'Cookie Policy',  'menu-item-url' => $cookie_url,  'menu-item-status' => 'publish', 'menu-item-type' => 'custom' ),
	);

	$social_items = array(
		array( 'menu-item-title' => 'LinkedIn', 'menu-item-url' => 'https://www.linkedin.com/company/snappysnail-di-marco-cazzaro', 'menu-item-status' => 'publish', 'menu-item-type' => 'custom', 'menu-item-target' => '_blank' ),
		array( 'menu-item-title' => 'GitHub',   'menu-item-url' => 'https://github.com/MarcoCazzaro',                                'menu-item-status' => 'publish', 'menu-item-type' => 'custom', 'menu-item-target' => '_blank' ),
		array( 'menu-item-title' => 'Email',    'menu-item-url' => 'mailto:info@snappysnail.io',                                      'menu-item-status' => 'publish', 'menu-item-type' => 'custom' ),
	);

	$menus = array(
		array(
			'name'     => 'Primary',
			'items'    => $primary_items,
			'location' => 'primary-menu',
		),
		array(
			'name'     => 'Footer',
			'items'    => $primary_items,
			'location' => 'footer-menu',
		),
		array(
			'name'     => 'Footer Legal',
			'items'    => $footer_legal_items,
			'location' => 'footer-legal',
		),
		array(
			'name'     => 'Social',
			'items'    => $social_items,
			'location' => 'social-menu',
		),
	);

	$location_map = get_theme_mod( 'nav_menu_locations', array() );

	foreach ( $menus as $menu_def ) {
		$existing_menu = wp_get_nav_menu_object( $menu_def['name'] );
		if ( $existing_menu ) {
			$log( 'line', "  [skip] Menu \"{$menu_def['name']}\" already exists." );
			$menu_id = (int) $existing_menu->term_id;
		} else {
			$menu_id = wp_create_nav_menu( $menu_def['name'] );
			if ( is_wp_error( $menu_id ) ) {
				$log( 'warning', "  [error] Could not create menu \"{$menu_def['name']}\": " . $menu_id->get_error_message() );
				continue;
			}
			foreach ( $menu_def['items'] as $item ) {
				wp_update_nav_menu_item( $menu_id, 0, $item );
			}
			$log( 'success', "  [ok]   Menu \"{$menu_def['name']}\" created (" . count( $menu_def['items'] ) . ' items).' );
		}
		$location_map[ $menu_def['location'] ] = $menu_id;
	}

	set_theme_mod( 'nav_menu_locations', $location_map );
	$log( 'success', '  [ok]   Menu locations assigned.' );
}

// =============================================================================
// ORCHESTRATOR
// =============================================================================

/**
 * Runs the full import sequence.
 *
 * @param callable $log          Logger callable: function( string $type, string $message ): void
 * @param bool     $destructive  True = delete existing content first; false = skip existing.
 * @param string   $images_dir  Absolute path to the directory containing import images.
 */
function ssnail_run_import( callable $log, bool $destructive, string $images_dir ): void {
	if ( $destructive ) {
		$log( 'line', '' );
		$log( 'line', '-- Deleting existing content ---------------------------------' );
		ssnail_import_cleanup( $log );
		$log( 'success', 'Cleanup complete. Proceeding with fresh import...' );
	}

	$log( 'line', '' );
	$log( 'line', '-- Site identity ----------------------------------------------' );
	ssnail_import_site_identity( $log, $destructive, $images_dir );

	$log( 'line', '' );
	$log( 'line', '-- ACF Options ------------------------------------------------' );
	ssnail_import_acf_options( $log, $destructive, $images_dir );

	$log( 'line', '' );
	$log( 'line', '-- Sample news posts ------------------------------------------' );
	ssnail_import_news_posts( $log, $destructive, $images_dir );

	$log( 'line', '' );
	$log( 'line', '-- Home page --------------------------------------------------' );
	$home_page_id = ssnail_import_home_page( $log, $destructive, $images_dir );

	$log( 'line', '' );
	$log( 'line', '-- News page --------------------------------------------------' );
	$news_page_id = ssnail_import_news_page( $log, $destructive, $home_page_id );

	$log( 'line', '' );
	$log( 'line', '-- Placeholder pages ------------------------------------------' );
	$page_ids = ssnail_import_placeholder_pages( $log, $destructive );

	$log( 'line', '' );
	$log( 'line', '-- Navigation menus -------------------------------------------' );
	ssnail_import_menus( $log, $destructive, $home_page_id, $news_page_id, $page_ids );

	$log( 'line', '' );
	$log( 'success', 'Import complete.' );
}
