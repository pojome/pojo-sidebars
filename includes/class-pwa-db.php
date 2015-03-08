<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class PWA_DB {
	
	const SETTING_KEY = 'pojo_widgets_area';
	
	protected $_default_sidebar_args = array();

	public function _default_args( $sidebar ) {
		return wp_parse_args(
			$sidebar,
			$this->_default_sidebar_args
		);
	}

	public function get_sidebars() {
		$sidebars = get_option( self::SETTING_KEY, array() );
		$sidebars = array_map( array( &$this, '_default_args' ), $sidebars );
		
		return $sidebars;
	}

	public function has_sidebars() {
		$sidebars = $this->get_sidebars();
		return ! empty( $sidebars );
	}

	public function get_sidebar( $id ) {
		$sidebars = $this->get_sidebars();
		if ( isset( $sidebars[ $id ] ) ) {
			return $sidebars[ $id ];
		}
		return false;
	}

	public function remove_sidebar( $id ) {
		$sidebars = $this->get_sidebars();
		if ( isset( $sidebars[ $id ] ) )
			unset( $sidebars[ $id ] );
		
		update_option( self::SETTING_KEY, $sidebars );
	}

	public function update_sidebar( $args, $id = null ) {
		if ( is_null( $id ) )
			$id = uniqid();
		
		$args = array_map( 'trim', $args );
		if ( empty( $args['name'] ) )
			return new WP_Error( 'no_press_name', __( 'You must press name.', 'pojo-widgets-area' ) );
		
		$sidebars = $this->get_sidebars();
		$sidebars[ $id ] = $args;

		update_option( self::SETTING_KEY, $sidebars );
		
		return $id;
	}

	public function __construct() {
		$this->_default_sidebar_args = array(
			'name' => __( 'Sidebar', 'pojo-widgets-area' ),
			'description' => __( 'Description', 'pojo-widgets-area' ),
			'css_classes' => '',
		);
	}
	
}