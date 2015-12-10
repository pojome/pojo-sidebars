<?php
/*
Plugin Name: Pojo Sidebars
Plugin URI: http://pojo.me/
Description: Pojo Sidebars generates as many sidebars as you need. It then allows you to place them on any Post, Page or Custom Posts Type in your WordPress site.
Author: Pojo Team
Author URI: http://pojo.me/
Version: 1.0.3
Text Domain: pojo-sidebars
Domain Path: /languages/
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'POJO_SIDEBARS__FILE__', __FILE__ );
define( 'POJO_SIDEBARS_BASE', plugin_basename( POJO_SIDEBARS__FILE__ ) );

final class Pojo_Sidebars {

	/**
	 * @var Pojo_Sidebars The one true Pojo_Sidebars
	 * @since 1.0.0
	 */
	private static $_instance = null;

	/**
	 * @var Pojo_Sidebars_Admin_UI
	 */
	public $admin_ui;

	/**
	 * @var Pojo_Sidebars_DB
	 */
	public $db;

	/**
	 * @var Pojo_Sidebars_Shortcode
	 */
	public $shortcode;

	public function load_textdomain() {
		load_plugin_textdomain( 'pojo-sidebars', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'pojo-sidebars' ), '1.0.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'pojo-sidebars' ), '1.0.0' );
	}

	/**
	 * @return Pojo_Sidebars
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new Pojo_Sidebars();
		}
		return self::$_instance;
	}
	
	public function bootstrap() {
		include( 'includes/class-pojo-sidebars-db.php' );
		include( 'includes/class-pojo-sidebars-admin-ui.php' );
		include( 'includes/class-pojo-sidebars-shortcode.php' );

		$this->db        = new Pojo_Sidebars_DB();
		$this->admin_ui  = new Pojo_Sidebars_Admin_UI();
		$this->shortcode = new Pojo_Sidebars_Shortcode();
	}
	
	private function __construct() {
		add_action( 'init', array( &$this, 'bootstrap' ) );
		add_action( 'plugins_loaded', array( &$this, 'load_textdomain' ) );
	}

}

Pojo_Sidebars::instance();
// EOF