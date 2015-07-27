<?php
/**
 * Custom controller file for task module
 *
 * @author Eoxia development team <dev@eoxia.com>
 * @version 1.0
 */

/**
 * Custom controller class for task module
 *
 * @author Eoxia development team <dev@eoxia.com>
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class wpeo_tasks_custom_ctr {
	
	/**
	 * Call private function initialize_ajax
	 * @return void
	 */
	public function __construct() {
		$this->initialize_ajax();
		
		add_action( 'admin_enqueue_scripts', array( &$this, 'callback_admin_enqueue_scripts' ) );
		add_action( 'add_meta_boxes', array( &$this, 'callback_add_meta_boxes' ), 10, 2 );
	}
	
	/**
	 * All the add_action ajax for task ()
	 * @return void
	 */
	private function initialize_ajax() {
		
	}

	public function callback_admin_enqueue_scripts( $hook ) {
		if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
			wp_register_style( 'wpeo-task-custom-css', WPEOMTM_TASK_URL . '/assets/css/custom.css', '', WPEOMTM_TASK_VERSION );
			wp_enqueue_style( 'wpeo-task-custom-css' );
		}
	}
	
	public function callback_add_meta_boxes( $post_type, $post ) {
		add_meta_box(
			'wpeo-tasks-metabox',
			__( 'Task', 'wpeotasks-i18n' ),
			array( $this, 'callback_render_metabox' ),
			$post_type,
			'normal',
			'default'
		);
	}
	
	public function callback_render_metabox( $post ) {
		$post_id = $post->ID;

		$tasks = wpeo_tasks_mod::get_task_in( $post_id );
		
		require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, 'custom', 'main' ) );
	}
}

?>
