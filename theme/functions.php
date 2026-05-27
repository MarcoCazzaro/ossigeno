<?php
/**
 * Ossigeno functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Ossigeno
 */

if ( ! defined( 'SSNAIL_VERSION' ) ) {
	/*
	 * Set the theme’s version number.
	 *
	 * This is used primarily for cache busting. If you use `npm run bundle`
	 * to create your production build, the value below will be replaced in the
	 * generated zip file with a timestamp, converted to base 36.
	 */
	define( 'SSNAIL_VERSION', '3.0.0' );
}

if ( ! defined( 'SSNAIL_TYPOGRAPHY_CLASSES' ) ) {
	/*
	 * Set Tailwind Typography classes for the front end, block editor and
	 * classic editor using the constant below.
	 *
	 * For the front end, these classes are added by the `ssnail_content_class`
	 * function. You will see that function used everywhere an `entry-content`
	 * or `page-content` class has been added to a wrapper element.
	 *
	 * For the block editor, these classes are converted to a JavaScript array
	 * and then used by the `./javascript/block-editor.js` file, which adds
	 * them to the appropriate elements in the block editor (and adds them
	 * again when they’re removed.)
	 *
	 * For the classic editor (and anything using TinyMCE, like Advanced Custom
	 * Fields), these classes are added to TinyMCE’s body class when it
	 * initializes.
	 */
	define(
		'SSNAIL_TYPOGRAPHY_CLASSES',
		'prose prose-neutral max-w-none prose-a:text-primary'
	);
}

if ( ! function_exists( 'ssnail_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function ssnail_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Ossigeno, use a find and replace
		 * to change 'ossigeno' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'ossigeno', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in two locations.
		register_nav_menus(
			array(
				'menu-1' => __( 'Primary', 'ossigeno' ),
				'menu-2' => __( 'Footer Menu', 'ossigeno' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Add support for editor styles.
		add_theme_support( 'editor-styles' );

		// Enqueue editor styles.
		add_editor_style( 'style-editor.css' );

		// Add support for responsive embedded content.
		add_theme_support( 'responsive-embeds' );

		// Remove support for block templates.
		remove_theme_support( 'block-templates' );
	}
endif;
add_action( 'after_setup_theme', 'ssnail_setup' );

if (!function_exists('ssnail_enqueue_google_material_icons')) :
	/**
	 * Enqueue Google Material Icons.
	 */
	function ssnail_enqueue_google_material_icons()
	{
		wp_enqueue_style('ssnail-google-material-icons', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,300,0..1,0', array(), null);
	}
endif;

/**
 * Enqueue scripts and styles.
 */
function ssnail_scripts() {
	ssnail_enqueue_google_material_icons();
	ssnail_enqueue_dynamic_fonts();
	wp_enqueue_style( 'ossigeno-style', get_stylesheet_uri(), array(), SSNAIL_VERSION );
	wp_enqueue_script( 'ossigeno-script', 
		get_template_directory_uri() . '/js/script.min.js', 
		array(), 
		SSNAIL_VERSION, 
		array(
			'in_footer' => true,
			'strategy'  => 'defer'
		) 
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'ssnail_scripts' );

/**
 * Enqueue the block editor script.
 */
function ssnail_enqueue_block_editor_script() {
	$current_screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	if (
		$current_screen &&
		$current_screen->is_block_editor() &&
		'widgets' !== $current_screen->id
	) {
		ssnail_enqueue_google_material_icons();
		ssnail_enqueue_dynamic_fonts();
		wp_enqueue_script(
			'ossigeno-editor',
			get_template_directory_uri() . '/js/block-editor.min.js',
			array(
				'wp-blocks',
				'wp-edit-post',
			),
			SSNAIL_VERSION,
			true
		);
		wp_add_inline_script( 'ossigeno-editor', "tailwindTypographyClasses = '" . esc_attr( SSNAIL_TYPOGRAPHY_CLASSES ) . "'.split(' ');", 'before' );
	}
}
add_action( 'enqueue_block_assets', 'ssnail_enqueue_block_editor_script' );

/**
 * Add the Tailwind Typography classes to TinyMCE.
 *
 * @param array $settings TinyMCE settings.
 * @return array
 */
function ssnail_tinymce_add_class( $settings ) {
	$settings['body_class'] = SSNAIL_TYPOGRAPHY_CLASSES;
	return $settings;
}
add_filter( 'tiny_mce_before_init', 'ssnail_tinymce_add_class' );

/**
 * Limit the block editor to heading levels supported by Tailwind Typography.
 *
 * @param array  $args Array of arguments for registering a block type.
 * @param string $block_type Block type name including namespace.
 * @return array
 */
function ssnail_modify_heading_levels( $args, $block_type ) {
	if ( 'core/heading' !== $block_type ) {
		return $args;
	}

	// Remove <h1>, <h5> and <h6>.
	$args['attributes']['levelOptions']['default'] = array( 2, 3, 4 );

	return $args;
}
add_filter( 'register_block_type_args', 'ssnail_modify_heading_levels', 10, 2 );

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Widget areas.
 */
require get_template_directory() . '/inc/widget-areas.php';

/**
 * Theme Customizer.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Custom post types and taxonomies.
 */
require get_template_directory() . '/inc/custom-post-types.php';

/**
 * Custom shortcodes.
 */
require get_template_directory() . '/inc/shortcodes.php';

/**
 * Custom ACF Blocks configuration.
 */
require get_template_directory() . '/inc/acf-blocks.php';

/**
 * ACF local field groups for custom blocks.
 */
require get_template_directory() . '/inc/acf-blocks/acf-fields.php';