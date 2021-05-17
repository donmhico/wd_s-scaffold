<?php
namespace WebDevStudios\WD_S;

use WP_CLI;

/**
 * Plugin Name: WD_S Scaffold
 */

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

$autoloader = dirname( __FILE__ ) . '/vendor/autoload.php';
if ( file_exists( $autoloader ) ) {
	require_once $autoloader;
}


WP_CLI::add_command( 'scaffold wd_s', __NAMESPACE__ . '\Command' );
