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
function ssnail_register_acf_options_page() {
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
}
add_action( 'init', 'ssnail_register_acf_options_page' );

/**
 * Maps a URL to a social platform key.
 */
function ssnail_url_to_platform( $url ) {
	$map = array(
		'instagram.com' => 'instagram',
		'linkedin.com'  => 'linkedin',
		'facebook.com'  => 'facebook',
		'fb.com'        => 'facebook',
		'threads.net'   => 'threads',
		'tiktok.com'    => 'tiktok',
		'twitter.com'   => 'x-twitter',
		'x.com'         => 'x-twitter',
		'whatsapp.com'  => 'whatsapp',
		'youtube.com'   => 'youtube',
		'youtu.be'      => 'youtube',
		'vimeo.com'     => 'vimeo',
		'github.com'    => 'github',
	);
	foreach ( $map as $domain => $platform ) {
		if ( false !== strpos( $url, $domain ) ) {
			return $platform;
		}
	}
	if ( 0 === strpos( $url, 'mailto:' ) ) {
		return 'email';
	}
	return '';
}

/**
 * Returns (or prints) an SVG icon for a social platform.
 *
 * @param string      $platform          Platform key.
 * @param bool        $print             Echo when true, return when false.
 * @param string      $fill_color        SVG fill value.
 * @param string|null $additional_classes Size classes on the wrapper <span>.
 * @return string|void
 */
function ssnail_get_social_icon( $platform, $print = true, $fill_color = 'currentColor', $additional_classes = null ) {
	$icon               = '';
	$additional_classes = $additional_classes ?? 'h-8 w-8';

	switch ( strtolower( $platform ) ) {
		case 'facebook':
			$icon = '<svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="' . $fill_color . '" fill-rule="nonzero" d="M12,2C6.477,2,2,6.477,2,12c0,5.013,3.693,9.153,8.505,9.876V14.65H8.031v-2.629h2.474v-1.749 c0-2.896,1.411-4.167,3.818-4.167c1.153,0,1.762,0.085,2.051,0.124v2.294h-1.642c-1.022,0-1.379,0.969-1.379,2.061v1.437h2.995 l-0.406,2.629h-2.588v7.247C18.235,21.236,22,17.062,22,12C22,6.477,17.523,2,12,2z"/></svg>';
			break;
		case 'instagram':
			$icon = '<svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="' . $fill_color . '" fill-rule="nonzero" d="M 8 3 C 5.239 3 3 5.239 3 8 L 3 16 C 3 18.761 5.239 21 8 21 L 16 21 C 18.761 21 21 18.761 21 16 L 21 8 C 21 5.239 18.761 3 16 3 L 8 3 z M 18 5 C 18.552 5 19 5.448 19 6 C 19 6.552 18.552 7 18 7 C 17.448 7 17 6.552 17 6 C 17 5.448 17.448 5 18 5 z M 12 7 C 14.761 7 17 9.239 17 12 C 17 14.761 14.761 17 12 17 C 9.239 17 7 14.761 7 12 C 7 9.239 9.239 7 12 7 z M 12 9 A 3 3 0 0 0 9 12 A 3 3 0 0 0 12 15 A 3 3 0 0 0 15 12 A 3 3 0 0 0 12 9 z"/></svg>';
			break;
		case 'threads':
			$icon = '<svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="' . $fill_color . '" fill-rule="nonzero" d="M 17.691406 11.125 C 17.589844 11.074219 17.484375 11.027344 17.378906 10.980469 C 17.191406 7.566406 15.328125 5.613281 12.195312 5.59375 C 12.179688 5.59375 12.167969 5.59375 12.152344 5.59375 C 10.28125 5.59375 8.722656 6.394531 7.761719 7.847656 L 9.484375 9.027344 C 10.203125 7.941406 11.324219 7.710938 12.152344 7.710938 C 12.164062 7.710938 12.171875 7.710938 12.183594 7.710938 C 13.214844 7.71875 13.992188 8.015625 14.496094 8.601562 C 14.863281 9.027344 15.105469 9.617188 15.226562 10.359375 C 14.3125 10.203125 13.324219 10.15625 12.269531 10.214844 C 9.289062 10.386719 7.375 12.125 7.503906 14.535156 C 7.570312 15.761719 8.179688 16.8125 9.222656 17.5 C 10.101562 18.082031 11.238281 18.367188 12.417969 18.304688 C 13.972656 18.21875 15.195312 17.625 16.046875 16.535156 C 16.695312 15.710938 17.105469 14.644531 17.285156 13.296875 C 18.027344 13.742188 18.578125 14.332031 18.882812 15.042969 C 19.398438 16.246094 19.425781 18.226562 17.8125 19.839844 C 16.398438 21.253906 14.695312 21.863281 12.125 21.882812 C 9.277344 21.859375 7.121094 20.945312 5.71875 19.164062 C 4.40625 17.496094 3.726562 15.085938 3.699219 12 C 3.726562 8.914062 4.40625 6.503906 5.71875 4.835938 C 7.121094 3.054688 9.277344 2.140625 12.125 2.117188 C 15 2.140625 17.191406 3.058594 18.648438 4.847656 C 19.363281 5.726562 19.898438 6.832031 20.253906 8.117188 L 22.273438 7.582031 C 21.84375 5.996094 21.167969 4.628906 20.246094 3.496094 C 18.378906 1.199219 15.648438 0.0234375 12.132812 0 L 12.121094 0 C 8.609375 0.0234375 5.910156 1.207031 4.097656 3.511719 C 2.484375 5.5625 1.652344 8.414062 1.625 11.992188 L 1.625 12.007812 C 1.652344 15.585938 2.484375 18.4375 4.097656 20.488281 C 5.910156 22.792969 8.609375 23.976562 12.121094 24 L 12.132812 24 C 15.253906 23.976562 17.453125 23.160156 19.265625 21.351562 C 21.636719 18.984375 21.5625 16.015625 20.78125 14.191406 C 20.222656 12.886719 19.152344 11.824219 17.691406 11.125 Z M 12.304688 16.1875 C 11 16.261719 9.644531 15.675781 9.578125 14.421875 C 9.527344 13.492188 10.238281 12.453125 12.386719 12.328125 C 12.632812 12.316406 12.871094 12.308594 13.109375 12.308594 C 13.886719 12.308594 14.617188 12.382812 15.28125 12.527344 C 15.03125 15.617188 13.582031 16.117188 12.304688 16.1875 Z M 12.304688 16.1875 "/></svg>';
			break;
		case 'tiktok':
			$icon = '<svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="' . $fill_color . '" fill-rule="nonzero" d="M 6 3 C 4.3550302 3 3 4.3550302 3 6 L 3 18 C 3 19.64497 4.3550302 21 6 21 L 18 21 C 19.64497 21 21 19.64497 21 18 L 21 6 C 21 4.3550302 19.64497 3 18 3 L 6 3 z M 12 7 L 14 7 C 14 8.005 15.471 9 16 9 L 16 11 C 15.395 11 14.668 10.734156 14 10.285156 L 14 14 C 14 15.654 12.654 17 11 17 C 9.346 17 8 15.654 8 14 C 8 12.346 9.346 11 11 11 L 11 13 C 10.448 13 10 13.449 10 14 C 10 14.551 10.448 15 11 15 C 11.552 15 12 14.551 12 14 L 12 7 z"/></svg>';
			break;
		case 'x-twitter':
			$icon = '<svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="' . $fill_color . '" fill-rule="nonzero" d="M 2.3671875 3 L 9.4628906 13.140625 L 2.7402344 21 L 5.3808594 21 L 10.644531 14.830078 L 14.960938 21 L 21.871094 21 L 14.449219 10.375 L 20.740234 3 L 18.140625 3 L 13.271484 8.6875 L 9.2988281 3 L 2.3671875 3 z M 6.2070312 5 L 8.2558594 5 L 18.033203 19 L 16.001953 19 L 6.2070312 5 z"/></svg>';
			break;
		case 'linkedin':
			$icon = '<svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="' . $fill_color . '" fill-rule="nonzero" d="M 5 3 C 3.9 3 3 3.9 3 5 L 3 19 C 3 20.1 3.9 21 5 21 L 19 21 C 20.1 21 21 20.1 21 19 L 21 5 C 21 3.9 20.1 3 19 3 L 5 3 z M 5 5 L 19 5 L 19 19 L 5 19 L 5 5 z M 7.8007812 6.3007812 C 6.9007812 6.3007812 6.4003906 6.8 6.4003906 7.5 C 6.4003906 8.2 6.8992188 8.6992188 7.6992188 8.6992188 C 8.5992187 8.6992187 9.0996094 8.2 9.0996094 7.5 C 9.0996094 6.8 8.6007813 6.3007812 7.8007812 6.3007812 z M 6.5 10 L 6.5 17 L 9 17 L 9 10 L 6.5 10 z M 11.099609 10 L 11.099609 17 L 13.599609 17 L 13.599609 13.199219 C 13.599609 12.099219 14.499219 11.900391 14.699219 11.900391 C 14.899219 11.900391 15.599609 12.099219 15.599609 13.199219 L 15.599609 17 L 18 17 L 18 13.199219 C 18 10.999219 17.000781 10 15.800781 10 C 14.600781 10 13.899609 10.4 13.599609 11 L 13.599609 10 L 11.099609 10 z"/></svg>';
			break;
		case 'whatsapp':
			$icon = '<svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="' . $fill_color . '" fill-rule="nonzero" d="M 12.011719 2 C 6.5057187 2 2.0234844 6.478375 2.0214844 11.984375 C 2.0204844 13.744375 2.4814687 15.462563 3.3554688 16.976562 L 2 22 L 7.2324219 20.763672 C 8.6914219 21.559672 10.333859 21.977516 12.005859 21.978516 L 12.009766 21.978516 C 17.514766 21.978516 21.995047 17.499141 21.998047 11.994141 C 22.000047 9.3251406 20.962172 6.8157344 19.076172 4.9277344 C 17.190172 3.0407344 14.683719 2.001 12.011719 2 z M 12.009766 4 C 14.145766 4.001 16.153109 4.8337969 17.662109 6.3417969 C 19.171109 7.8517969 20.000047 9.8581875 19.998047 11.992188 C 19.996047 16.396187 16.413812 19.978516 12.007812 19.978516 C 10.674812 19.977516 9.3544062 19.642812 8.1914062 19.007812 L 7.5175781 18.640625 L 6.7734375 18.816406 L 4.8046875 19.28125 L 5.2851562 17.496094 L 5.5019531 16.695312 L 5.0878906 15.976562 C 4.3898906 14.768562 4.0204844 13.387375 4.0214844 11.984375 C 4.0234844 7.582375 7.6067656 4 12.009766 4 z M 8.4765625 7.375 C 8.3095625 7.375 8.0395469 7.4375 7.8105469 7.6875 C 7.5815469 7.9365 6.9355469 8.5395781 6.9355469 9.7675781 C 6.9355469 10.995578 7.8300781 12.182609 7.9550781 12.349609 C 8.0790781 12.515609 9.68175 15.115234 12.21875 16.115234 C 14.32675 16.946234 14.754891 16.782234 15.212891 16.740234 C 15.670891 16.699234 16.690438 16.137687 16.898438 15.554688 C 17.106437 14.971687 17.106922 14.470187 17.044922 14.367188 C 16.982922 14.263188 16.816406 14.201172 16.566406 14.076172 C 16.317406 13.951172 15.090328 13.348625 14.861328 13.265625 C 14.632328 13.182625 14.464828 13.140625 14.298828 13.390625 C 14.132828 13.640625 13.655766 14.201187 13.509766 14.367188 C 13.363766 14.534188 13.21875 14.556641 12.96875 14.431641 C 12.71875 14.305641 11.914938 14.041406 10.960938 13.191406 C 10.218937 12.530406 9.7182656 11.714844 9.5722656 11.464844 C 9.4272656 11.215844 9.5585938 11.079078 9.6835938 10.955078 C 9.7955938 10.843078 9.9316406 10.663578 10.056641 10.517578 C 10.180641 10.371578 10.223641 10.267562 10.306641 10.101562 C 10.389641 9.9355625 10.347156 9.7890625 10.285156 9.6640625 C 10.223156 9.5390625 9.737625 8.3065 9.515625 7.8125 C 9.328625 7.3975 9.131125 7.3878594 8.953125 7.3808594 C 8.808125 7.3748594 8.6425625 7.375 8.4765625 7.375 z"/></svg>';
			break;
		case 'youtube':
			$icon = '<svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="' . $fill_color . '" fill-rule="nonzero" d="M21.582,6.186c-0.23-0.86-0.908-1.538-1.768-1.768C18.254,4,12,4,12,4S5.746,4,4.186,4.418 c-0.86,0.23-1.538,0.908-1.768,1.768C2,7.746,2,12,2,12s0,4.254,0.418,5.814c0.23,0.86,0.908,1.538,1.768,1.768 C5.746,20,12,20,12,20s6.254,0,7.814-0.418c0.861-0.23,1.538-0.908,1.768-1.768C22,16.254,22,12,22,12S22,7.746,21.582,6.186z M10,15.464V8.536L16,12L10,15.464z"/></svg>';
			break;
		case 'vimeo':
			$icon = '<svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="' . $fill_color . '" fill-rule="nonzero" d="M 21.988281 7.96875 C 21.902344 9.878906 20.539063 12.488281 17.910156 15.804688 C 15.191406 19.269531 12.890625 21 11.007813 21 C 9.84375 21 8.855469 19.945313 8.050781 17.835938 C 7.511719 15.902344 6.976563 13.96875 6.4375 12.035156 C 5.839844 9.925781 5.195313 8.871094 4.511719 8.871094 C 4.359375 8.871094 3.835938 9.179688 2.941406 9.792969 L 2 8.605469 C 2.988281 7.757813 3.960938 6.90625 4.917969 6.058594 C 6.234375 4.941406 7.222656 4.355469 7.882813 4.296875 C 9.4375 4.148438 10.398438 5.191406 10.757813 7.425781 C 11.144531 9.832031 11.414063 11.332031 11.5625 11.917969 C 12.011719 13.914063 12.507813 14.910156 13.046875 14.910156 C 13.464844 14.910156 14.09375 14.265625 14.933594 12.96875 C 15.769531 11.671875 16.21875 10.6875 16.277344 10.007813 C 16.398438 8.890625 15.949219 8.328125 14.933594 8.328125 C 14.453125 8.328125 13.960938 8.4375 13.453125 8.652344 C 14.433594 5.496094 16.3125 3.964844 19.085938 4.050781 C 21.140625 4.109375 22.109375 5.414063 21.988281 7.96875 Z"/></svg>';
			break;
		case 'github':
			$icon = '<svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="' . $fill_color . '" fill-rule="nonzero" d="M12 2C6.477 2 2 6.477 2 12c0 4.418 2.865 8.166 6.839 9.489.5.092.682-.217.682-.482 0-.237-.009-.868-.013-1.703-2.782.604-3.369-1.342-3.369-1.342-.454-1.155-1.11-1.463-1.11-1.463-.908-.62.069-.608.069-.608 1.003.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.087 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.269 2.75 1.025A9.578 9.578 0 0112 6.836a9.59 9.59 0 012.504.337c1.909-1.294 2.747-1.025 2.747-1.025.546 1.377.203 2.394.1 2.647.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C19.138 20.163 22 16.418 22 12c0-5.523-4.477-10-10-10z"/></svg>';
			break;
		case 'email':
			$icon = '<svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="' . $fill_color . '" aria-hidden="true"><path d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z"/><path d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z"/></svg>';
			break;
	}

	if ( '' !== $icon ) {
		$icon = '<span class="ssnail-social-icon text-inherit inline-flex overflow-clip ' . esc_attr( $additional_classes ) . '">' . $icon . '</span>';
	}

	if ( $print ) {
		echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup
	} else {
		return $icon;
	}
}

/**
 * Renders social icon links from the social-menu nav location.
 *
 * Each menu item's URL is inspected to pick the correct SVG icon. Items with
 * unrecognised URLs are silently skipped.
 *
 * @param string $link_class CSS classes applied to each <a> element.
 * @param string $icon_class CSS size classes applied to each <svg> element.
 */
function ssnail_render_social_menu( $link_class = '', $icon_class = 'w-5 h-5' ) {
	$locations = get_nav_menu_locations();
	if ( empty( $locations['social-menu'] ) ) {
		return;
	}
	$items = wp_get_nav_menu_items( $locations['social-menu'] );
	if ( ! $items ) {
		return;
	}
	foreach ( $items as $item ) {
		$platform = ssnail_url_to_platform( $item->url );
		$icon     = $platform ? ssnail_get_social_icon( $platform, false, 'currentColor', $icon_class ) : '';
		if ( ! $icon ) {
			continue;
		}
		$target = ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
		$rel    = ( '_blank' === $item->target ) ? ' rel="noopener noreferrer"' : '';
		printf(
			'<a href="%s" aria-label="%s"%s%s class="%s">%s</a>',
			esc_url( $item->url ),
			esc_attr( $item->title ),
			$target,
			$rel,
			esc_attr( $link_class ),
			$icon // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup
		);
	}
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

		$more_string = '<a href="' . esc_url( get_permalink() ) . '" class="ssnail-read-more">' . $continue_reading . '</a>';
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
	$mimes['svg']  = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';
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
