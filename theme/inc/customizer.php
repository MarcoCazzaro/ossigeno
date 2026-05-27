<?php
/**
 * Theme Customizer settings.
 *
 * @package Ossigeno
 */

/**
 * Returns the curated list of available Google Fonts.
 *
 * Keys are the font display names (stored in theme mods and used in CSS variables).
 * Values are the Google Fonts API family parameters used to build the enqueue URL.
 *
 * @return array<string, string>
 */
function ssnail_get_font_options(): array {
	return array(
		'Comfortaa'    => 'Comfortaa:wght@300..700',
		'Nunito'       => 'Nunito:wght@300..700',
		'Poppins'      => 'Poppins:wght@300;400;500;600;700',
		'Quicksand'    => 'Quicksand:wght@300..700',
		'Varela Round' => 'Varela+Round',
		'Inter'        => 'Inter:wght@300..700',
		'Montserrat'   => 'Montserrat:wght@300..700',
		'Open Sans'    => 'Open+Sans:wght@300..700',
		'Lato'         => 'Lato:wght@300;400;700',
		'Roboto'       => 'Roboto:wght@300;400;500;700',
	);
}

/**
 * Enqueues Google Fonts for the active heading/body selections and injects
 * a :root override so the Tailwind CSS variables reflect the chosen fonts.
 *
 * Called on both wp_enqueue_scripts (frontend) and enqueue_block_assets
 * (block editor), after ssnail_enqueue_google_material_icons() so that the
 * ssnail-google-material-icons handle is already registered for inline styles.
 */
function ssnail_enqueue_dynamic_fonts(): void {
	$font_options  = ssnail_get_font_options();
	$font_headings = get_theme_mod( 'font_headings', 'Comfortaa' );
	$font_body     = get_theme_mod( 'font_body', 'Varela Round' );

	$families = array();

	if ( isset( $font_options[ $font_headings ] ) ) {
		$families[] = $font_options[ $font_headings ];
	}

	if ( isset( $font_options[ $font_body ] ) && $font_body !== $font_headings ) {
		$families[] = $font_options[ $font_body ];
	}

	if ( ! empty( $families ) ) {
		$url = 'https://fonts.googleapis.com/css2?family=' . implode( '&family=', $families ) . '&display=swap';
		wp_enqueue_style( 'ssnail-google-fonts', $url, array(), null );
	}

	// Override the Tailwind @theme variables at runtime. Because @theme outputs
	// inside @layer, an unlayered :root declaration wins the cascade regardless
	// of source order, so no dependency on handle ordering is needed.
	wp_add_inline_style(
		'ssnail-google-material-icons',
		sprintf(
			':root { --font-headings: "%s", sans-serif; --font-body: "%s", sans-serif; }',
			esc_attr( $font_headings ),
			esc_attr( $font_body )
		)
	);
}

/**
 * Registers the Typography section and font controls in the Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function ssnail_customizer( WP_Customize_Manager $wp_customize ): void {

	// Logotype — added to the native Site Identity section.
	$wp_customize->add_setting( 'ssnail_custom_logotype' );
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'ssnail_custom_logotype',
			array(
				'label'    => __( 'Logotype', 'ossigeno' ),
				'section'  => 'title_tagline',
				'settings' => 'ssnail_custom_logotype',
				'priority' => 8,
				'mime_type' => 'image',
			)
		)
	);

	$wp_customize->add_section(
		'ssnail_typography',
		array(
			'title'    => __( 'Typography', 'ossigeno' ),
			'priority' => 40,
		)
	);

	$font_choices = array_combine(
		array_keys( ssnail_get_font_options() ),
		array_keys( ssnail_get_font_options() )
	);

	$wp_customize->add_setting(
		'font_headings',
		array(
			'default'           => 'Comfortaa',
			'sanitize_callback' => 'ssnail_sanitize_font_choice',
		)
	);
	$wp_customize->add_control(
		'font_headings',
		array(
			'label'   => __( 'Headings font', 'ossigeno' ),
			'section' => 'ssnail_typography',
			'type'    => 'select',
			'choices' => $font_choices,
		)
	);

	$wp_customize->add_setting(
		'font_body',
		array(
			'default'           => 'Varela Round',
			'sanitize_callback' => 'ssnail_sanitize_font_choice',
		)
	);
	$wp_customize->add_control(
		'font_body',
		array(
			'label'   => __( 'Body font', 'ossigeno' ),
			'section' => 'ssnail_typography',
			'type'    => 'select',
			'choices' => $font_choices,
		)
	);
}
add_action( 'customize_register', 'ssnail_customizer' );

/**
 * Sanitize callback — rejects any value not in the curated font list.
 *
 * @param string $value Submitted font name.
 * @return string
 */
function ssnail_sanitize_font_choice( string $value ): string {
	return array_key_exists( $value, ssnail_get_font_options() ) ? $value : 'Varela Round';
}
