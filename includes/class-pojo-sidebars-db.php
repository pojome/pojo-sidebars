<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_Sidebars_DB {
	
	protected $_sidebars = null;
	
	protected function _register_taxonomy() {
		// Taxonomy: pojo_sidebars.
		$labels = array(
			'name' => __( 'Sidebars', 'pojo-sidebars' ),
			'singular_name' => __( 'Sidebar', 'pojo-sidebars' ),
			'menu_name' => _x( 'Sidebars', 'Admin menu name', 'pojo-sidebars' ),
			'search_items' => __( 'Search Sidebars', 'pojo-sidebars' ),
			'all_items' => __( 'All Sidebars', 'pojo-sidebars' ),
			'parent_item' => __( 'Parent Sidebar', 'pojo-sidebars' ),
			'parent_item_colon' => __( 'Parent Sidebar:', 'pojo-sidebars' ),
			'edit_item' => __( 'Edit Sidebar', 'pojo-sidebars' ),
			'update_item' => __( 'Update Sidebar', 'pojo-sidebars' ),
			'add_new_item' => __( 'Add New Sidebar', 'pojo-sidebars' ),
			'new_item_name' => __( 'New Sidebar Name', 'pojo-sidebars' ),
		);

		$args = array(
			'hierarchical' => false,
			'labels' => $labels,
			'public' => false,
			'show_in_nav_menus' => false,
			'show_ui' => true,
			'capabilities' => array( 'edit_theme_options' ),
			'query_var' => false,
			'rewrite' => false,
		);

		register_taxonomy(
			'pojo_sidebars',
			apply_filters( 'pojo_taxonomy_objects_sidebars', array() ),
			apply_filters( 'pojo_taxonomy_args_sidebars', $args )
		);
	}

	public function get_sidebars() {
		if ( is_null( $this->_sidebars ) ) {
			$this->_sidebars = get_terms(
				'pojo_sidebars',
				array(
					'hide_empty' => false,
				)
			);
		}
		return $this->_sidebars;
	}

	public function has_sidebars() {
		$sidebars = $this->get_sidebars();
		return ! empty( $sidebars );
	}

	public function __construct() {
		$this->_register_taxonomy();
	}
	
}