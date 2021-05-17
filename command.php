<?php
/**
 * Plugin Name: WD_S Scaffold
 */
use WP_CLI;

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

$autoloader = dirname( __FILE__ ) . '/vendor/autoload.php';
if ( file_exists( $autoloader ) ) {
	require_once $autoloader;
}


WP_CLI::add_command( 'scaffold wd_s', 'WDS_Command' );
