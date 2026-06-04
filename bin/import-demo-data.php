<?php
/**
 * One-time demo data importer for Ossigeno — WP-CLI entry point.
 *
 * Usage:   wp eval-file bin/import-demo-data.php
 * Re-runs: safe -- conservative mode skips already-imported posts.
 *          Destructive mode deletes all related content first.
 *
 * Required images in bin/images/ before running:
 *   ossigeno-logo.svg
 *   ossigeno-placeholder.webp
 *   site-icon.png                (optional — skip logged if missing)
 *
 * @package Ossigeno
 */

global $wpdb;
$wpdb->query( 'SET NAMES utf8mb4' );

require_once get_template_directory() . '/inc/import-functions.php';

fwrite( STDOUT, "\nImport mode:\n  [c] Conservative -- skip content that already exists (default)\n  [d] Destructive  -- delete all related content first, then import fresh\nChoice [c/d]: " );
$ssnail_mode        = strtolower( trim( fgets( STDIN ) ) );
$ssnail_destructive = ( 'd' === $ssnail_mode );

if ( $ssnail_destructive ) {
	WP_CLI::warning( 'Destructive mode selected. Removing existing content...' );
} else {
	WP_CLI::line( 'Conservative mode. Existing content will be skipped.' );
}

$ssnail_log = static function ( string $type, string $message ): void {
	switch ( $type ) {
		case 'success':
			WP_CLI::success( $message );
			break;
		case 'warning':
			WP_CLI::warning( $message );
			break;
		default:
			WP_CLI::line( $message );
			break;
	}
};

ssnail_run_import( $ssnail_log, $ssnail_destructive, __DIR__ . '/images/' );
