<?php
/**
 * WD_S Scaffold Command
 *
 * @package WD_S_Scaffold_CLI
 */

namespace WebDevStudios\WD_S_Scaffold_CLI;

use WP_CLI;

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

$autoloader = dirname( __FILE__ ) . '/vendor/autoload.php';
if ( file_exists( $autoloader ) ) {
	require_once $autoloader;
}


WP_CLI::add_command( 'scaffold wd_s', __NAMESPACE__ . '\Command' );
