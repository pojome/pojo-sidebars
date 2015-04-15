<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_Sidebars_Admin_UI {

	protected $_capability = 'edit_theme_options';
	
	public function register_sidebars() {
		if ( ! Pojo_Sidebars::instance()->db->has_sidebars() )
			return;
		
		$sidebars = Pojo_Sidebars::instance()->db->get_sidebars();
		
		foreach ( $sidebars as $sidebar ) {
			$sidebar_classes = array( 'pojo-sidebar' );
			
			register_sidebar(
				array(
					'id'            => 'pojo-sidebar-' . sanitize_title( $sidebar->term_id ),
					'name'          => $sidebar->name,
					'description'   => $sidebar->description,
					'before_widget' => '<section id="%1$s" class="widget ' . esc_attr( implode( ' ', $sidebar_classes ) ) . ' %2$s"><div class="widget-inner">',
					'after_widget'  => '</div></section>',
					'before_title'  => '<h5 class="widget-title"><span>',
					'after_title'   => '</span></h5>',
				)
			);
		}
	}

	public function register_menu() {
		add_submenu_page(
			'pojo-home',
			__( 'Sidebars', 'pojo-sidebars' ),
			__( 'Sidebars', 'pojo-sidebars' ),
			$this->_capability,
			'edit-tags.php?taxonomy=pojo_sidebars'
		);
	}

	public function pojo_get_core_sidebars( $sidebars ) {
		$our_sidebars = array();
		foreach ( Pojo_Sidebars::instance()->db->get_sidebars() as $sidebar_term ) {
			$our_sidebars[] = 'pojo-sidebar-' . $sidebar_term->term_id;
		}

		foreach ( $sidebars as $sidebar_id => $sidebar_name ) {
			if ( in_array( $sidebar_id, $our_sidebars ) )
				unset( $sidebars[ $sidebar_id ] );
		}

		return $sidebars;
	}

	public function __construct() {
		$this->register_sidebars();

		add_action( 'admin_menu', array( &$this, 'register_menu' ), 400 );
		add_filter( 'pojo_get_core_sidebars', array( &$this, 'pojo_get_core_sidebars' ) );
	}
	
}