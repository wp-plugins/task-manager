<?php
/**
 * Tag controller file for task module
 *
 * @author Eoxia development team <dev@eoxia.com>
 * @version 1.0
 */

/**
 * Tag controller class for task module
 *
 * @author Eoxia development team <dev@eoxia.com>
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class wpeo_tasks_tags_ctr {
	
	/**
	 * Call some wordpress action : init
	 * Call private function initialize_ajax
	 * @return void
	 */
	public function __construct() {
		add_action( 'init', array( &$this, 'callback_init' ), 20 );
		
		$this->initialize_ajax();		
	}
	
	/**
	 * All the add_action ajax for task (Create tag, view tag and update tags)
	 * @return void
	 */
	private function initialize_ajax() {
		add_action( 'wp_ajax_create-tag', array( &$this, 'ajax_create_tag' ) );
		add_action( 'wp_ajax_view-tag', array( &$this, 'ajax_view_tag' ) );
		add_action( 'wp_ajax_update-tags', array( &$this, 'ajax_update_tags' ) );
	}

	/**
	* Register the taxonomy for tag
	* @return void
	*/
	public function callback_init() {
		register_taxonomy( wpeo_tasks_tags_mod::$taxonomy, 'wpeo_project-tasks-tags', array() );
	}

	/**
	 * AJAX - Get tags for this task, get all tags and display it
	 * with the template backend/tags.php
	 * @param int $_GET['task_id'] - The task id
	 * @return void
	 */
	public function ajax_view_tag() {
		header('Content-Type: application/json');
		
		$response = new wpeo_tasks_json_mod();
		$response->setObjectId( $_GET['task_id'] );
		
		if ( !wp_verify_nonce( $_GET['_wpnonce'], 'wp_nonce_view_tag' ) ) {
			$response->setCode(22);
			$response->setState( 'error', __( 'You are not allowed to edit this task : Incorrect nonce', 'wpeotasks-i18n' ) );
		}
		
		if( !$response->check_have_error() ) {	
			$tags_in_task = wpeo_tasks_tags_mod::get_tags_in( (int) $_GET['task_id'] );
	
	 		$tags = wpeo_tasks_tags_mod::get_tags();
			
	 		ob_start();
			require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, 'backend', 'tags') );
			$res_html = ob_get_clean();
			
			$string_tag_id = __( '"No tags"', 'wpeotasks-i18n' );
			if( !empty( $tags_in_task ) ) {
				$string_tag_id = "";
				foreach ( $tags_in_task as $tag ) {
					$string_tag_id .= $tag . ', ';
				}
			}
			
			$response->setCode(0);
			$response->setState( 'success', __( sprintf( 'View tag %s of the task %d', $string_tag_id, $_GET['task_id'] ), 'wpeotasks-i18n' ) );
			
			$response->setData( 
				array (
					'html' => $res_html,
				)
			);
		}
		
		wp_die( $response->output_html() );
	}
	
	/**
	 * AJAX - Create tag with the name_tag and add it to the task_id with add_tag_in
	 * @param string $_POST['name_tag'] - The name tag
	 * @param int $_POST['task_id'] - The task id
	 * @return void
	 */
	public function ajax_create_tag(  ) {
		header('Content-Type: application/json');
		
		$response = new wpeo_tasks_json_mod();
		$response->setObjectId( $_POST['task_id'] );
		
		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'nonce_create_tag' ) ) {
			$response->setCode(22);
			$response->setState( 'error', __( 'You are not allowed to edit this task : Incorrect nonce', 'wpeotasks-i18n' ) );
		}
		
		if( !$response->check_have_error() ) {
			$slug = wpeo_tasks_tags_mod::create_tag ( $_POST['name_tag'] );
			
			wpeo_tasks_tags_mod::add_tag_in( (int) $_POST['task_id'], $slug );
			
			$response->setCode( 0 );
			$response->setState( 'success', __( sprintf( 'The tag %s has been created and affected to the task %d', $_POST['name_tag'], $_POST['task_id'] ), 'wpeotasks-i18n' ) );
			$response->setData( 
				array( 
					'message' => __( sprintf( 'The tag %s has been created', $_POST['name_tag'] ), 'wpeotasks-i18n' ),
					'slug_tag' => $slug,
				) 
			);
		}
				
		wp_die( $response->output_json() );
	}
	
	/**
	 * AJAX - Set all tags checked in the task_id
	 * @param int $_POST['task_id'] - The task_id
	 * @param array $_POST['tags'] - Array of all tags id
	 * @return void
	 */
	public function ajax_update_tags() {
		header('Content-Type: application/json');
		
		$response = new wpeo_tasks_json_mod();
		$response->setObjectId( $_POST['task_id'] );
		
		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'nonce_update_tags' ) ) {
			$response->setCode(22);
			$response->setState( 'error', __( 'You are not allowed to edit this task : Incorrect nonce', 'wpeotasks-i18n' ) );
		}
		
		if ( !$response->check_have_error() ) {
			wpeo_tasks_tags_mod::update_tags_in( (int) $_POST['task_id'], $_POST['tags'] );
			
			$string_tags_id = "No tags";
			
			if( !empty( $_POST['tags'] ) ) {
				$string_tags_id = "";
				foreach( $_POST['tags'] as $tag ) {
					$string_tags_id .= $tag . ', ';
				}
			}
		
			$response->setCode( 0 );
			$response->setState( 'success', __( sprintf( 'The tag %s has been affected to the task %d', $string_tags_id, $_POST['task_id'] ), 'wpeotasks-i18n' ) );
			$response->setData( array( 'tags' => str_replace( ', ', ' ', $string_tags_id ) ) );
				
		}
		
		wp_die( $response->output_json() );
	}
}


?>
