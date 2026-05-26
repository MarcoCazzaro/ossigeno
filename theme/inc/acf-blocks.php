<?php
/**
 * ACF Blocks
 *
 * @package Ossigeno
 * @link https://developer.wordpress.org/reference/hooks/init/
 * @link https://www.advancedcustomfields.com/resources/create-your-first-acf-block/
 */

function ossigeno_register_acf_blocks() {
	// Get the blocks directory path
	$blocks_dir = __DIR__ . '/acf-blocks';
	
	if ( ! is_dir( $blocks_dir ) ) {
		return;
	}

	$cache_key  = 'ssnail_acf_block_dirs';
	$block_dirs = get_transient( $cache_key );

	if ( false === $block_dirs ) {
		$dirs       = glob( $blocks_dir . '/*', GLOB_ONLYDIR );
		$block_dirs = array_filter(
			is_array( $dirs ) ? $dirs : array(),
			function ( $dir ) {
				return file_exists( $dir . '/block.json' );
			}
		);
		$block_dirs = array_values( $block_dirs );
		set_transient( $cache_key, $block_dirs, DAY_IN_SECONDS );
	}

	foreach ( $block_dirs as $block_dir ) {
		register_block_type( $block_dir );
	}
}
add_action( 'init', 'ossigeno_register_acf_blocks' );


/**
 * Flush the block directory cache when the theme is switched or updated.
 */
function ssnail_flush_block_dirs_cache() {
	delete_transient( 'ssnail_acf_block_dirs' );
}
add_action( 'switch_theme', 'ssnail_flush_block_dirs_cache' );
add_action( 'upgrader_process_complete', 'ssnail_flush_block_dirs_cache' );
