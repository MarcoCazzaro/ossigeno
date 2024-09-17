<?php

/**
 * Ossigeno functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Ossigeno
 */

if (!defined('SSNAIL__VERSION')) {
	/*
	 * Set the theme’s version number.
	 *
	 * This is used primarily for cache busting. If you use `npm run bundle`
	 * to create your production build, the value below will be replaced in the
	 * generated zip file with a timestamp, converted to base 36.
	 */
	define('SSNAIL__VERSION', '0.7.8');
}

if (!defined('SSNAIL__TYPOGRAPHY_CLASSES')) {
	/*
	 * Set Tailwind Typography classes for the front end, block editor and
	 * classic editor using the constant below.
	 *
	 * For the front end, these classes are added by the `ssnail__content_class`
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
		'SSNAIL__TYPOGRAPHY_CLASSES',
		'prose prose-ossigeno max-w-none prose-a:text-primary'
	);
}

if (!function_exists('ssnail__setup')) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function ssnail__setup()
	{
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Ossigeno, use a find and replace
		 * to change 'ossigeno' to the name of your theme in all the template files.
		 */
		$result = load_theme_textdomain('ossigeno', get_template_directory() . '/languages');

		// Add default posts and comments RSS feed links to head.
		add_theme_support('automatic-feed-links');

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support('title-tag');

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support('post-thumbnails');

		// This theme uses wp_nav_menu() in two locations.
		register_nav_menus(
			array(
				'primary-menu' => __('Primary', 'ossigeno'),
				'footer-menu' => __('Footer Menu', 'ossigeno'),
				'social-menu' => __('Social Menu', 'ossigeno'),
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
		add_theme_support('customize-selective-refresh-widgets');

		// Add support for editor styles.
		add_theme_support('editor-styles');

		// Enqueue editor styles.
		add_editor_style('style-editor.css');
		add_editor_style('style-editor-extra.css');

		// Add support for responsive embedded content.
		add_theme_support('responsive-embeds');

		// Remove support for block templates.
		remove_theme_support('block-templates');

		// https://learn.wordpress.org/tutorial/using-block-template-parts-in-classic-themes/
		add_theme_support('block-template-parts');

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 300,
				'width'       => 300,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);
	}
endif;
add_action('after_setup_theme', 'ssnail__setup');

/**
 * Enqueue scripts and styles.
 */
function ssnail__scripts()
{
	wp_enqueue_style('ossigeno-style', get_stylesheet_uri(), array(), SSNAIL__VERSION);
	wp_enqueue_script('ossigeno-script', get_template_directory_uri() . '/js/script.min.js', array(), SSNAIL__VERSION, true);

	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
	ssnail_enqueue_font_awesome();
}
add_action('wp_enqueue_scripts', 'ssnail__scripts');

/**
 * Enqueue admin scripts and styles.
 */
function ssnail__admin_scripts()
{
	if (isset($_GET['page']) && $_GET['page'] == 'ossigeno_options_page') {
		wp_enqueue_media();
	}
}
add_action('admin_enqueue_scripts', 'ssnail__admin_scripts');

if (!function_exists('ssnail_enqueue_font_awesome')) {
	function ssnail_enqueue_font_awesome()
	{
		//FONT AWESOME - SELF HOSTED
		// First, check if there is a folder in them theme called "resources/fontawesome-free-6.5.1-web"
		// If not, use the resources hosted on snappysnail
		$hosted_path = get_template_directory() . "/resources/fontawesome-free-6.5.1-web";
		if (is_dir($hosted_path)) {
			$resources_path = get_template_directory_uri() . "/resources/";
		} else {
			// Child theme check
			$hosted_path = get_stylesheet_directory() . "/resources/fontawesome-free-6.5.1-web";
			if (is_dir($hosted_path)) {
				$resources_path = get_stylesheet_directory_uri() . "/resources/";
			} else {
				if (ssnail_is_localhost()) {
					$resources_path = "http://resources.local/";
				} else {
					$resources_path = "https://resources.snappysnail.io/";
				}
			}
			$hosted_path = get_template_directory() . "/resources/fontawesome-free-6.5.1-web";
		}
		wp_enqueue_style('font-awesome-sh', $resources_path . "fontawesome-free-6.5.1-web/css/fontawesome.min.css", array(), "6.5.1");
		wp_enqueue_style('font-awesome-sh-brands', $resources_path . "fontawesome-free-6.5.1-web/css/brands.min.css", array('font-awesome-sh'), "6.4.0");
		wp_enqueue_style('font-awesome-sh-solid', $resources_path . "fontawesome-free-6.5.1-web/css/solid.min.css", array('font-awesome-sh'), "6.5.1");
	}
}

/**
 * Enqueue the block editor script.
 */
function ssnail__enqueue_block_editor_script()
{
	wp_enqueue_script(
		'ossigeno-editor',
		get_template_directory_uri() . '/js/block-editor.min.js',
		array(
			'wp-blocks',
			'wp-edit-post',
			'wp-i18n',
			'wp-element',
			'wp-hooks',
			'wp-compose'
		),
		SSNAIL__VERSION,
		true
	);
}
add_action('enqueue_block_editor_assets', 'ssnail__enqueue_block_editor_script');

/**
 * Enqueue the script necessary to support Tailwind Typography in the block
 * editor, using an inline script to create a JavaScript array containing the
 * Tailwind Typography classes from SSNAIL__TYPOGRAPHY_CLASSES.
 */
function ssnail__enqueue_typography_script()
{
	if (is_admin()) {
		wp_enqueue_script(
			'ossigeno-typography',
			get_template_directory_uri() . '/js/tailwind-typography-classes.min.js',
			array(
				'wp-blocks',
				'wp-edit-post',
			),
			SSNAIL__VERSION,
			true
		);
		wp_add_inline_script('ossigeno-typography', "tailwindTypographyClasses = '" . esc_attr(SSNAIL__TYPOGRAPHY_CLASSES) . "'.split(' ');", 'before');
	}
}
add_action('enqueue_block_assets', 'ssnail__enqueue_typography_script');

/**
 * Add the Tailwind Typography classes to TinyMCE.
 *
 * @param array $settings TinyMCE settings.
 * @return array
 */
function ssnail__tinymce_add_class($settings)
{
	$settings['body_class'] .= ' ' . SSNAIL__TYPOGRAPHY_CLASSES;
	return $settings;
}
add_filter('tiny_mce_before_init', 'ssnail__tinymce_add_class');

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Widget areas.
 */
require get_template_directory() . '/inc/widget-areas.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Custom options.
 */
require get_template_directory() . '/inc/options.php';
