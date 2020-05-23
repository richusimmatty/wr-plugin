<?php


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
if ( ! class_exists( 'WPEX_Theme_Options' ) ) {

	class WPEX_Theme_Options {

		/**
		 * Start things up
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// We only need to register the admin panel on the back-end
			if ( is_admin() ) {
				add_action( 'admin_menu', array( 'WPEX_Theme_Options', 'add_admin_menu' ) );
				add_action( 'admin_init', array( 'WPEX_Theme_Options', 'register_settings' ) );
			}

		}

		/**
		 * Returns all theme options
		 *
		 * @since 1.0.0
		 */
		public static function get_theme_options() {
			return get_option( 'theme_options' );
		}

		/**
		 * Returns single theme option
		 *
		 * @since 1.0.0
		 */
		public static function get_theme_option( $id ) {
			$options = self::get_theme_options();
			if ( isset( $options[$id] ) ) {
				return $options[$id];
			}
		}

		/**
		 * Add sub menu page
		 *
		 * @since 1.0.0
		 */
		public static function add_admin_menu() {
			add_menu_page(
				esc_html__( 'WR Mailchimp Settings', 'text-domain' ),
				esc_html__( 'WR Mailchimp Settings', 'text-domain' ),
				'manage_options',
				'theme-settings',
				array( 'WPEX_Theme_Options', 'create_admin_page' )
			);
		}

		/**
		 * Register a setting and its sanitization callback.
		 *
		 * We are only registering 1 setting so we can store all options in a single option as
		 * an array. You could, however, register a new setting for each option
		 *
		 * @since 1.0.0
		 */
		public static function register_settings() {
			register_setting( 'theme_options', 'theme_options', array( 'WPEX_Theme_Options', 'sanitize' ) );
		}

		/**
		 * Sanitization callback
		 *
		 * @since 1.0.0
		 */
		public static function sanitize( $options ) {

			// If we have options lets sanitize them
			if ( $options ) {



				// Input
				if ( ! empty( $options['input_api'] ) ) {
					$options['input_api'] = sanitize_text_field( $options['input_api'] );
				} else {
					unset( $options['input_api'] ); // Remove from options if empty
				}

								// Input
				if ( ! empty( $options['input_listid'] ) ) {
					$options['input_listid'] = sanitize_text_field( $options['input_listid'] );
				} else {
					unset( $options['input_listid'] ); // Remove from options if empty
				}

	

			}

			// Return sanitized options
			return $options;

		}

		/**
		 * Settings page output
		 *
		 * @since 1.0.0
		 */
		public static function create_admin_page() { ?>

			<div class="wrap">

				<h1><?php esc_html_e( 'WR Mailchimp Options', 'text-domain' ); ?></h1>

				<form method="post" action="options.php">

					<?php settings_fields( 'theme_options' ); ?>

					<table class="form-table wpex-custom-admin-login-table">



						<?php // Text input example ?>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'API Key', 'text-domain' ); ?></th>
							<td>
								<?php $value = self::get_theme_option( 'input_api' ); ?>
								<input type="text" name="theme_options[input_api]" value="<?php echo esc_attr( $value ); ?>">
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'List ID', 'text-domain' ); ?></th>
							<td>
								<?php $value = self::get_theme_option( 'input_listid' ); ?>
								<input type="text" name="theme_options[input_listid]" value="<?php echo esc_attr( $value ); ?>">
							</td>
						</tr>

					

					</table>

					<?php submit_button(); ?>

				</form>

				<div>Shortcode : <strong>[wrg-subscribe]</strong></div>

			</div><!-- .wrap -->
		<?php }

	}
}
new WPEX_Theme_Options();

// Helper function to use in your theme to return a theme option value
function myprefix_get_theme_option( $id = '' ) {
	return WPEX_Theme_Options::get_theme_option( $id );
}