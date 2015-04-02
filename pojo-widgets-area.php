<?php
/*
Plugin Name: Pojo Widgets Area
Plugin URI: http://pojo.me/
Description: This plugin allows you to add a Widgets Area widget to your WordPress site, of which works with Pojo Framework.
Author: Pojo Team
Author URI: http://pojo.me/
Version: 1.0.0
Text Domain: pojo-widgets-area
Domain Path: /languages/
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'POJO_WIDGETS_AREA__FILE__', __FILE__ );
define( 'POJO_WIDGETS_AREA_BASE', plugin_basename( POJO_WIDGETS_AREA__FILE__ ) );

final class Pojo_Widgets_Area {

	/**
	 * @var Pojo_Widgets_Area The one true Pojo_Widgets_Area
	 * @since 1.0.0
	 */
	private static $_instance = null;

	/**
	 * @var PWA_Admin_UI
	 */
	public $admin_ui;

	/**
	 * @var PWA_DB
	 */
	public $db;

	/**
	 * @var PWA_Replacer
	 */
	public $replacer;

	public function load_textdomain() {
		load_plugin_textdomain( 'pojo-widgets-area', false, basename( dirname( __FILE__ ) ) . '/languages' );
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
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'pojo-widgets-area' ), '1.0.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'pojo-widgets-area' ), '1.0.0' );
	}

	/**cd
	 * @return Pojo_Widgets_Area
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new Pojo_Widgets_Area();

		return self::$_instance;
	}

	public function admin_notices() {
		echo '<div class="error"><p>' . sprintf( __( '<a href="%s" target="_blank">Pojo Framework</a> is not active. Please activate any theme by Pojo before you are using "Pojo Widgets Area" plugin.', 'pojo-widgets-area' ), 'http://pojo.me/' ) . '</p></div>';
	}
	
	public function bootstrap() {
		// This plugin for Pojo Themes..
		if ( ! class_exists( 'Pojo_Core' ) ) {
			add_action( 'admin_notices', array( &$this, 'admin_notices' ) );
			return;
		}
		
		include( 'includes/class-pwa-db.php' );
		include( 'includes/class-pwa-admin-ui.php' );
		include( 'includes/class-pwa-replacer.php' );

		$this->db       = new PWA_DB();
		$this->admin_ui = new PWA_Admin_UI();
		$this->replacer = new PWA_Replacer();
	}
	
	private function __construct() {
		add_action( 'init', array( &$this, 'bootstrap' ), 30 );
		add_action( 'plugins_loaded', array( &$this, 'load_textdomain' ) );
	}

}

Pojo_Widgets_Area::instance();
// EOF