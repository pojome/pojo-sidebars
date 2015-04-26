<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_Sidebars_Admin_UI {

	protected $_menu_parent = '';
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
			$this->_menu_parent,
			__( 'Sidebars', 'pojo-sidebars' ),
			__( 'Sidebars', 'pojo-sidebars' ),
			$this->_capability,
			'edit-tags.php?taxonomy=pojo_sidebars'
		);
	}

	public function menu_highlight() {
		global $parent_file, $submenu_file;
		
		if ( 'edit-tags.php?taxonomy=pojo_sidebars' === $submenu_file ) {
			$parent_file = $this->_menu_parent;
		}
	}

	public function manage_columns( $columns ) {
		$old_columns = $columns;
		$columns = array(
			'cb' => $old_columns['cb'],
			'name' => $old_columns['name'],
			'ID' => __( 'ID', 'pojo-sidebars' ),
			'shortcode' => __( 'Shortcode', 'pojo-sidebars' ),
			'description' => $old_columns['description'],
		);
		
		return $columns;
	}

	public function sortable_columns( $sortable_columns ) {
		$sortable_columns['ID'] = 'ID';
		return $sortable_columns;
	}

	public function manage_custom_columns( $value, $column_name, $term_id ) {
		switch ( $column_name ) {
			case 'ID' :
				$value = 'pojo-sidebar-' . $term_id;
				break;
			
			case 'shortcode' :
				$value = '<input type="text" readonly value="' . esc_attr( '[pojo-sidebar id="' . $term_id . '"]' ) . '" />';
				break;
		}
		
		return $value;
	}

	public function admin_head() {
		if ( 'edit-pojo_sidebars' !== get_current_screen()->id )
			return;
		
		?><style>#addtag div.form-field.term-slug-wrap, #edittag tr.form-field.term-slug-wrap { display: none; }</style><?php
	}

	public function admin_footer() {
		if ( 'edit-pojo_sidebars' !== get_current_screen()->id )
			return;
		
		?>
		<script>jQuery( document ).ready( function( $ ) {
				var $wrapper = $( '#addtag, #edittag' );
				$wrapper.find( 'tr.form-field.term-name-wrap p, div.form-field.term-name-wrap > p' ).text( '<?php _e( 'The name of the widgets area', 'pojo-sidebars' ); ?>' );
				$wrapper.find( 'tr.form-field.term-description-wrap p, div.form-field.term-description-wrap > p' ).text( '<?php _e( 'The description of the widgets area (optional)', 'pojo-sidebars' ); ?>' );
			} );</script><?php
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
		if ( class_exists( 'Pojo_Core' ) )
			$this->_menu_parent = 'pojo-home';
		else
			$this->_menu_parent = 'themes.php';
		
		$this->register_sidebars();

		add_action( 'admin_menu', array( &$this, 'register_menu' ), 100 );
		add_action( 'admin_head', array( &$this, 'menu_highlight' ) );

		add_filter( 'manage_edit-pojo_sidebars_columns', array( &$this, 'manage_columns' ) );
		add_filter( 'manage_pojo_sidebars_custom_column', array( &$this, 'manage_custom_columns' ), 10, 3 );
		add_filter( 'manage_edit-pojo_sidebars_sortable_columns', array( &$this, 'sortable_columns' ) );

		add_action( 'admin_head', array( &$this, 'admin_head' ) );
		add_action( 'admin_footer', array( &$this, 'admin_footer' ) );
		
		add_filter( 'pojo_get_core_sidebars', array( &$this, 'pojo_get_core_sidebars' ) );
	}
	
}