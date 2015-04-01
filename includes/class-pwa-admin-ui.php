<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class PWA_Admin_UI {

	protected $_capability = 'edit_theme_options';
	protected $_page_id = 'pojo-widgets-area';

	public function get_setting_page_link( $message_id = '' ) {
		$link_args = array(
			'page' => $this->_page_id,
		);
		
		if ( ! empty( $message_id ) )
			$link_args['message'] = $message_id;
		
		return add_query_arg( $link_args, admin_url( 'admin.php' ) );
	}

	public function get_remove_sidebar_link( $sidebar_id ) {
		return add_query_arg(
			array(
				'action' => 'pma_remove_sidebar',
				'sidebar_id' => $sidebar_id,
				'_nonce' => wp_create_nonce( 'pma-remove-sidebar-' . $sidebar_id ),
			),
			admin_url( 'admin-ajax.php' )
		);
	}

	public function ajax_pma_remove_sidebar() {
		if ( ! isset( $_GET['sidebar_id'] ) || ! check_ajax_referer( 'pma-remove-sidebar-' . $_GET['sidebar_id'], '_nonce', false ) || ! current_user_can( $this->_capability ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'pojo-widgets-area' ) );
		}
		
		Pojo_Widgets_Area::instance()->db->remove_sidebar( $_GET['sidebar_id'] );
		
		wp_redirect( $this->get_setting_page_link() );
		die();
	}

	public function manager_actions() {
		if ( empty( $_POST['pma_action'] ) )
			return;
		
		switch ( $_POST['pma_action'] ) {
			case 'add_sidebar' :
				if ( ! check_ajax_referer( 'pwa-add-sidebar', '_nonce', false ) || ! current_user_can( $this->_capability ) ) {
					wp_die( __( 'You do not have sufficient permissions to access this page.', 'pojo-widgets-area' ) );
				}
				
				$return = Pojo_Widgets_Area::instance()->db->update_sidebar(
					array(
						'name' => $_POST['name'],
						'description' => $_POST['description'],
					)
				);
				
				if ( is_wp_error( $return ) ) {
					wp_die( $return->get_error_message() );
				}
				
				wp_redirect( $this->get_setting_page_link() );
				die;
				
			case 'update_sidebar' :
				if ( ! isset( $_POST['sidebar_id'] ) || ! check_ajax_referer( 'pwa-update-sidebar-' . $_POST['sidebar_id'], '_nonce', false ) || ! current_user_can( $this->_capability ) ) {
					wp_die( __( 'You do not have sufficient permissions to access this page.', 'pojo-widgets-area' ) );
				}

				$return = Pojo_Widgets_Area::instance()->db->update_sidebar(
					array(
						'name' => $_POST['name'],
						'description' => $_POST['description'],
						'css_classes' => $_POST['css_classes'],
					),
					$_POST['sidebar_id']
				);

				if ( is_wp_error( $return ) ) {
					wp_die( $return->get_error_message() );
				}

				wp_redirect( $this->get_setting_page_link() );
				die;
		}
	}

	public function register_sidebars() {
		if ( ! Pojo_Widgets_Area::instance()->db->has_sidebars() )
			return;
		
		$sidebars = Pojo_Widgets_Area::instance()->db->get_sidebars();
		
		foreach ( $sidebars as $sidebar_id => $sidebar_data ) {
			$sidebar_classes = array( 'pwa-sidebar' );
			if ( ! empty( $sidebar_data['css_classes'] ) )
				$sidebar_classes[] = $sidebar_data['css_classes'];
			
			register_sidebar(
				array(
					'id'            => 'pwa-' . sanitize_title( $sidebar_id ),
					'name'          => $sidebar_data['name'],
					'description'   => $sidebar_data['description'],
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
			__( 'Widgets Area', 'pojo-widgets-area' ),
			__( 'Widgets Area', 'pojo-widgets-area' ),
			$this->_capability,
			'pojo-widgets-area',
			array( &$this, 'display_page' )
		);		
	}

	public function display_page() {
		$sidebars = Pojo_Widgets_Area::instance()->db->get_sidebars();
		?>
		<div class="wrap">

			<div id="icon-themes" class="icon32"></div>
			<h2><?php _e( 'Widgets Area', 'pojo-widgets-area' ); ?></h2>

			<?php // Add Sidebar ?>
			<div>
				<form action="" method="post">
					<input type="hidden" name="pma_action" value="add_sidebar" />
					<input type="hidden" name="_nonce" value="<?php echo wp_create_nonce( 'pwa-add-sidebar' ) ?>" />

					<div>
						<label>
							<?php _e( 'Name', 'pojo-widgets-area' ); ?>:
							<input type="text" name="name" />
						</label>
					</div>

					<div>
						<label>
							<?php _e( 'Description', 'pojo-widgets-area' ); ?>:
							<input type="text" name="description" />
						</label>
					</div>

					<div>
						<p><button type="submit" class="button"><?php _e( 'Create', 'pojo-widgets-area' ); ?></button></p>
					</div>
				</form>
			</div>
			
			<?php // All Sidebars ?>
			<div>
				<?php if ( ! empty( $sidebars ) ) : ?>
					<?php foreach ( $sidebars as $sidebar_id => $sidebar_data ) : ?>
						<form action="" method="post">
							<input type="hidden" name="pma_action" value="update_sidebar" />
							<input type="hidden" name="sidebar_id" value="<?php echo esc_attr( $sidebar_id ); ?>" />
							<input type="hidden" name="_nonce" value="<?php echo wp_create_nonce( 'pwa-update-sidebar-' . $sidebar_id ) ?>" />
								
							<h3><?php echo $sidebar_data['name']; ?></h3>
							
							<div>
								<a href="<?php echo $this->get_remove_sidebar_link( $sidebar_id ); ?>"><?php _e( 'Remove', 'pojo-widgets-area' ); ?></a>
							</div>

							<div>
								<label>
									<?php _e( 'Name', 'pojo-widgets-area' ); ?>:
									<input type="text" name="name" value="<?php echo esc_attr( $sidebar_data['name'] ); ?>" />
								</label>
							</div>

							<div>
								<label>
									<?php _e( 'Description', 'pojo-widgets-area' ); ?>:
									<input type="text" name="description" value="<?php echo esc_attr( $sidebar_data['description'] ); ?>" />
								</label>
							</div>

							<div>
								<label>
									<?php _e( 'CSS Classes', 'pojo-widgets-area' ); ?>:
									<input type="text" name="css_classes" placeholder="<?php _e( '(Optional)', 'pojo-widgets-area' ); ?>" value="<?php echo esc_attr( $sidebar_data['css_classes'] ); ?>" />
								</label>
							</div>

							<div>
								<p><button type="submit" class="button"><?php _e( 'Update', 'pojo-widgets-area' ); ?></button></p>
							</div>
						</form>
					<?php endforeach; ?>
				<?php else : ?>
				<p><?php _e( 'No have any sidebars.', 'pojo-widgets-area' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	<?php
	}

	public function __construct() {
		$this->manager_actions();
		$this->register_sidebars();

		add_action( 'admin_menu', array( &$this, 'register_menu' ), 400 );
		add_action( 'wp_ajax_pma_remove_sidebar', array( &$this, 'ajax_pma_remove_sidebar' ) );
	}
	
}