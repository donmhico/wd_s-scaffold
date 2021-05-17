<?php
use WP_CLI;
use WP_CLI\Utils;

/**
 * Generates a new theme based on wd_s.
 *
 * @see WP_CLI_Command
 */
class WDS_Command extends \WP_CLI_Command {
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
	 * <slug>
	 * : The slug for the generated theme.
	 *
	 * [--activate]
	 * : Activate the newly generated theme.
	 *
	 * [--theme_name=<name>]
	 * : What to put in the 'Theme Name:' header in 'style.css'.
	 *
	 * ## EXAMPLES
	 *
	 *    # Generate a new wd_s starter theme with theme name "Acme Theme" and slug "acme".
	 *    $ wp scaffold wd_s acme --theme_name="Acme Theme"
	 *    Success: Created theme 'Acme Theme'.
	 */
	public function __invoke( $args, $assoc_args ) {
		$theme_slug = $args[0];
		$timeout    = 60;
		$theme_path = WP_CONTENT_DIR . '/themes';

		if ( ! preg_match( '/^[a-z_]\w+$/i', str_replace( '-', '_', $theme_slug ) ) ) {
			WP_CLI::error( 'Invalid theme slug specified. Theme slugs can only contain letters, numbers, underscores and hyphens, and can only start with a letter or underscore.' );
		}

		$theme_name = ucfirst( $theme_slug );
		$theme_slug = sanitize_title_with_dashes( $theme_slug );

		// Terminate if a theme with the same folder name exists.
		if ( file_exists( $theme_path . '/' . $theme_slug ) ) {
			WP_CLI::error( 'Theme path already exists.' );
		}

		// Use provided theme name.
		$theme_name = Utils\get_flag_value( $assoc_args, 'theme_name' );
		if ( ! is_null( $theme_name ) ) {
			$assoc_args_theme_name = sanitize_text_field( $theme_name );
			if ( ! empty( $assoc_args_theme_name ) ) {
				$theme_name = $assoc_args_theme_name;
			}
		}

		$temp_filename = wp_tempnam( self::WD_S_GENERATOR_URL );

		$body = array(
			'wds_wdunderscores_generate' => 1,
			'wds_wdunderscores_name'     => $theme_name,
			'wds_wdunderscores_slug'     => $theme_slug,
		);

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
	 * Initializes WP_Filesystem.
	 */
	protected function init_wp_filesystem() {
		global $wp_filesystem;
		WP_Filesystem();

		return $wp_filesystem;
	}
}
