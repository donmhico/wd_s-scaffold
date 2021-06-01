<?php
/**
 * WD_S Scaffold Command functionality
 *
 * @package WD_S_Scaffold_CLI
 */

namespace WebDevStudios\WD_S_Scaffold_CLI;

use WP_CLI;
use WP_CLI\Utils;

/**
 * Generates a new theme based on wd_s.
 *
 * @see WP_CLI_Command
 */
class Command extends \WP_CLI_Command {
	/**
	 * URL of WD_s generator
	 *
	 * @var string
	 */
	const WD_S_GENERATOR_URL = 'https://wdunderscores.com/';

	/**
	 * Generates wd_s starter theme.
	 *
	 * ## OPTIONS
	 *
	 * <theme_name>
	 * : What to put in the 'Theme Name:' header in 'style.css'.
	 *
	 * [--slug]
	 * : The slug for the generated theme.
	 *
	 * [--description=<description>]
	 * : Theme description.
	 *
	 * [--theme_uri=<theme_uri>]
	 * : Theme URI.
	 *
	 * [--author=<author>]
	 * : Theme author.
	 *
	 * [--author_email=<author_email>]
	 * : Author's email.
	 *
	 * [--author_uri=<author_uri>]
	 * : Author URI.
	 *
	 * [--dev_uri=<dev_uri>]
	 * : Developer URI.
	 *
	 * [--activate]
	 * : Activate the newly generated theme.
	 *
	 * ## EXAMPLES
	 *
	 *    # Generate a new wd_s starter theme with theme name "Acme Theme".
	 *    $ wp scaffold wd_s "Acme Theme"
	 *    Success: Created theme 'Acme Theme'.
	 *
	 * @param array $args       Passed args. E.g. "Acme Theme".
	 * @param array $assoc_args Passed associated args. E.g. --slug=acme.
	 */
	public function __invoke( $args, $assoc_args ) {
		$theme_name = $args[0];
		$timeout    = 180;
		$theme_path = WP_CONTENT_DIR . '/themes';

		if ( ! preg_match( '/^[a-z0-9\s\-\_]+$/i', $theme_name ) ) {
			WP_CLI::error( 'Invalid theme name specified. Theme slugs can only contain alphanumeric, whitespace, underscores and hyphens.' );
		}

		$theme_slug = Utils\get_flag_value( $assoc_args, 'slug' );
		// Check if --slug is provided.
		if ( is_null( $theme_slug ) ) {
			// If --slug isn't provided, use "Theme name" as the slug.
			$theme_slug = sanitize_title_with_dashes( $theme_name );
		} else {
			// If --slug is provided, make sure it has proper values.
			if ( ! preg_match( '/^[a-z_]\w+$/i', str_replace( '-', '_', $theme_slug ) ) ) {
				WP_CLI::error( 'Invalid theme slug specified. Theme slugs can only contain letters, numbers, underscores and hyphens, and can only start with a letter or underscore.' );
			}
		}

		// Terminate if a theme with the same folder name exists.
		if ( file_exists( $theme_path . '/' . $theme_slug ) ) {
			WP_CLI::error( 'Theme path already exists.' );
		}

		$temp_filename = wp_tempnam( self::WD_S_GENERATOR_URL );

		$body = array(
			'wds_wdunderscores_generate' => 1,
			'wds_wdunderscores_name'     => $theme_name,
			'wds_wdunderscores_slug'     => $theme_slug,
		);

		// Make sure provided uris are valid.
		$uris = array(
			'theme_uri',
			'author_uri',
			'dev_uri',
		);

		foreach ( $uris as $uri ) {
			$$uri = Utils\get_flag_value( $assoc_args, $uri );
			if ( ! is_null( $$uri ) ) {
				if ( ! $this->is_valid_url( $$uri ) ) {
					WP_CLI::error( "Please provide a valid {$uri}." );
				}
				$body[ "wds_wdunderscores_{$uri}" ] = $$uri;
			}
		}

		$author = Utils\get_flag_value( $assoc_args, 'author' );
		if ( ! is_null( $author ) ) {
			$body['wds_wdunderscores_author'] = sanitize_text_field( $author );
		}

		$author_email = Utils\get_flag_value( $assoc_args, 'author_email' );
		if ( ! is_null( $author_email ) && is_email( $author_email ) ) {
			$body['wds_wdunderscores_author_email'] = sanitize_email( $author_email );
		}

		$description = Utils\get_flag_value( $assoc_args, 'description' );
		if ( ! is_null( $description ) ) {
			$body['wds_wdunderscores_description'] = sanitize_text_field( $description );
		}

		$post_args = array(
			'timeout'  => $timeout,
			'stream'   => true,
			'filename' => $temp_filename,
			'body'     => $body,
		);

		$response = wp_remote_post( self::WD_S_GENERATOR_URL, $post_args );

		if ( is_wp_error( $response ) ) {
			WP_CLI::error( $response );
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== (int) $response_code ) {
			WP_CLI::error( "Couldn't create theme (received {$response_code} response)." );
		}

		$this->init_wp_filesystem();

		$unzip = unzip_file( $temp_filename, $theme_path );
		unlink( $temp_filename );

		if ( is_wp_error( $unzip ) ) {
			WP_CLI::error( $unzip );
		}

		WP_CLI::success( "Created theme '{$theme_name}'." );

		if ( Utils\get_flag_value( $assoc_args, 'activate' ) ) {
			WP_CLI::run_command( array( 'theme', 'activate', $theme_slug ) );
		}
	}

	/**
	 * Initialize WP Filesystem.
	 *
	 * @author Michael Joseph Panaga <michael.panaga@webdevstudios.com>
	 *
	 * @global $wp_filesystem
	 * @see https://developer.wordpress.org/reference/functions/wp_filesystem/
	 *
	 * @return WP_Filesystem_Base
	 */
	protected function init_wp_filesystem() {
		global $wp_filesystem;
		WP_Filesystem();

		return $wp_filesystem;
	}

	/**
	 * Whether a url is valid or not.
	 *
	 * @author Michael Joseph Panaga <michael.panaga@webdevstudios.com>
	 *
	 * @param string $url URL to check.
	 * @return boolean
	 */
	private function is_valid_url( $url ) {
		return esc_url_raw( $url ) === $url;
	}
}
