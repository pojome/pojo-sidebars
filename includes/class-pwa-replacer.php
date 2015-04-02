<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class PWA_Replacer {
	
	protected $_original_wp_sidebars_widgets;

	public function sidebar_replace() {
		global $_wp_sidebars_widgets;
		
		$this->_original_wp_sidebars_widgets = $_wp_sidebars_widgets;
		
		$core_sidebars = Pojo_Widgets_Area::instance()->db->get_core_sidebars();
		if ( empty( $core_sidebars ) )
			return;
		
		foreach ( $core_sidebars as $sidebar_id => $sidebar_args ) {
			$override_sidebar = atmb_get_field( 'pwa_override_sidebar_' . $sidebar_id );
			if ( empty( $override_sidebar ) || ! isset( $this->_original_wp_sidebars_widgets[ $override_sidebar ] ) )
				continue;
			
			$_wp_sidebars_widgets[ $sidebar_id ] = $this->_original_wp_sidebars_widgets[ $override_sidebar ];
		}
	}
	
	public function __construct() {
		if ( ! is_admin() ) {
			add_action( 'wp_head', array( &$this, 'sidebar_replace' ) );
		}
	}
	
}