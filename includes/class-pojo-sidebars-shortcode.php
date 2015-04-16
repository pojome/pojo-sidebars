<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_Sidebars_Shortcode {

	public function render( $atts = array() ) {
		if ( empty( $atts['id'] ) )
			return '';
		
		ob_start();
		
		$sidebar_id = 'pojo-sidebar-' . $atts['id'];
		dynamic_sidebar( $sidebar_id );
		
		return ob_get_clean();
	}

	public function __construct() {
		add_shortcode( 'pojo-sidebar', array( &$this, 'render' ) );
	}
	
}