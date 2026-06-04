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
	// Nothing yet
}
add_action( 'init', 'ossigeno_register_custom_post_types' );

/**
 * Register ssnail_inquiry CPT.
 *
 * Used by the Contact block's acf_form() to store submitted enquiries.
 * The post type is private (no public archive/single) but visible in the
 * admin so editors can review incoming contact requests.
 */
function ossigeno_register_inquiry_cpt() {
	register_post_type(
		'ssnail_inquiry',
		array(
			'labels'       => array(
				'name'               => __( 'Contatti', 'ossigeno' ),
				'singular_name'      => __( 'Contatto', 'ossigeno' ),
				'add_new_item'       => __( 'Aggiungi contatto', 'ossigeno' ),
				'edit_item'          => __( 'Modifica contatto', 'ossigeno' ),
				'view_item'          => __( 'Visualizza contatto', 'ossigeno' ),
				'search_items'       => __( 'Cerca contatti', 'ossigeno' ),
				'not_found'          => __( 'Nessun contatto trovato', 'ossigeno' ),
				'not_found_in_trash' => __( 'Nessun contatto nel cestino', 'ossigeno' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'supports'     => array( 'title' ),
			'menu_icon'    => 'dashicons-email-alt',
		)
	);
}
add_action( 'init', 'ossigeno_register_inquiry_cpt' );
