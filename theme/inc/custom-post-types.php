<?php
/**
 * Custom Post Types and Taxonomies
 *
 * @package Ossigeno
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Register custom post types and taxonomies.
 */
function ossigeno_register_custom_post_types() {
    
}
add_action( 'init', 'ossigeno_register_custom_post_types' );
