<?php
/**
 * Plugin Name:     Clasp
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     A hook based approach to WordPress theming
 * Author:          Ryan Kanner
 * Author URI:      http://rkanner.com
 * Text Domain:     wp-clasp
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         WP_Clasp
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Clasp' ) ) {

	final class Clasp {
		
		private $instance;

		public $context;
		
		public function run() {
			
			if ( ! isset( $this->instance ) && ( ! $this->instance instanceof Clasp ) ) {
				$this->instance = new Clasp();
				$this->setup_constants();
				$this->includes();
				$this->context = new \Clasp\Context();
			}

			do_action( 'clasp_init', $this->instance, $this->context );

			return $this->instance;

		}

		/**
		 * Throw error on object clone.
		 * The whole idea of the singleton design pattern is that there is a single object
		 * therefore, we don't want the object to be cloned.
		 *
		 * @since  0.1.0
		 * @access public
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_html__( 'The Clasp class should not be cloned.', 'wp-clasp' ), '0.1.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @since  0.1.0
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {
			// De-serializing instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_html__( 'De-serializing instances of the Clasp class is not allowed', 'wp-clasp' ), '0.1.0' );
		}

		private function setup_constants() {

			if ( ! defined( 'CLASP_VERSION' ) ) {
				define( 'CLASP_VERSION', '0.1.0' );
			}

			if ( ! defined( 'CLASP_PLUGIN_DIR' ) ) {
				define( 'CLASP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( ! defined( 'CLASP_PLUGIN_URL' ) ) {
				define( 'CLASP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			if ( ! defined( 'CLASP_PLUGIN_FILE' ) ) {
				define( 'CLASP_PLUGIN_FILE', __FILE__ );
			}

		}

		private function includes() {

			// Manually include autoloader
			require_once( CLASP_PLUGIN_DIR . 'vendor/autoload.php' );

		}
	}
}

function wp_clasp() {
	$clasp_instance = new Clasp();
	$clasp_instance->run();
	return $clasp_instance;
}
