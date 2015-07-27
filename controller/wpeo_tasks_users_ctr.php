<?php
/**
 * Users controller file for task module
 *
 * @author Eoxia development team <dev@eoxia.com>
 * @version 1.0
 */

/**
 * Users controller class for task module
 *
 * @author Eoxia development team <dev@eoxia.com>
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class wpeo_tasks_users_ctr {
	
	/**
	 * Call private function initialize_ajax
	 * @return void
	 */
	public function __construct() {
		$this->initialize_ajax();
	}
	
	/**
	 * All the add_action ajax for task (View user, update users)
	 * @return void
	 */
	private function initialize_ajax() {
		add_action( 'wp_ajax_view-user', array( &$this, 'ajax_view_user' ) );
		add_action( 'wp_ajax_wpeo-update-users', array( &$this, 'ajax_update_users' ) );
	}
	
	/**
	 * AJAX - Get all users in this task, get all users in wordpress and display the users views backend/users.php
	 * @param int $_GET['task_id'] - The task id
	 * @param string $_GET['type'] - The type can be "task" or "point"
	 * @return void
	 */
	public function ajax_view_user() {
		header('Content-Type: application/json');
		
		$response = new wpeo_tasks_json_mod();
		$response->setObjectId( $_GET['task_id'] );
		
		if ( !wp_verify_nonce( $_GET['_wpnonce'], 'view_user' ) ) {
			$response->setCode(22);
			$response->setState( 'error', __( 'You are not allowed to edit this task : Incorrect nonce', 'wpeotasks-i18n' ) );
		}
		
		if( !$response->check_have_error() ) {	
			$id = ( 'task' == $_GET['type'] ) ? (int) $_GET['task_id'] : (int) $_GET['point_id'];
			
			$users_in_task = wpeo_tasks_users_mod::get_users_in( $id, $_GET['type'] );
	
			$users = get_users();
			
			/** Add gravatar */
			if( !empty( $users ) ) {
				foreach( $users as $user ) {
					$user->avatar = get_avatar($user->ID, 32);
				}
			}
			
			$string_users = __( 'No user', 'wpeotasks-i18n' );
			
			if( !empty( $users_in_task ) ) {
				$string_users = "";
				foreach( $users_in_task as $user ) {
					$string_users .= $user . ', ';
				}
			}
			
			ob_start();
			require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, 'backend', 'users') );
			$res_html = ob_get_clean();
			
			$response->setCode(0);
			$response->setState( 'success', __( sprintf( 'View user %s of the task %d', $string_users, $_GET['task_id'] ), 'wpeotasks-i18n' ) );
			$response->setData( 
				array (
					'html' => $res_html,
				)
			);
		}
		
		wp_die( $response->output_html() );
	}
	
	/**
	* AJAX - Insert all user in the $_POST['users'] checked by the form for this task
	* Delete all user unchecked by the form for this task
	* @param int $_POST['task_id'] The task id
	* @param array $_POST['users'] The array of user id
	* @param string $_POST['type'] - The type can be "task" or "point"
	* @return void
	*/
	public function ajax_update_users() {
		header('Content-Type: application/json');
		
		$response = new wpeo_tasks_json_mod();
		$response->setObjectId( $_POST['task_id'] );
		
		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'nonce_update_users' ) ) {
			$response->setCode(22);
			$response->setState( 'error', __( 'You are not allowed to edit this task : Incorrect nonce', 'wpeotasks-i18n' ) );
		}
		
		if( !$response->check_have_error() ) {
			$id = ( $_POST['type'] == 'task' ) ? ( int ) $_POST['task_id'] : ( int ) $_POST['point_id'];
			$response->setCode(0);
			
			$string_user = "No user";
			if( !empty( $_POST['users'] ) ) {
				$string_user = "";
				foreach( $_POST['users'] as $user ) {
					$string_user .= $user . ', ';
				}
			}
			
			if( !empty( $_POST['users'] ) ) {
				wpeo_tasks_users_mod::update_user_in( $id, $_POST['users'], $_POST['type'] );
				$response->setState( 'success', __( sprintf( 'Add user %s in %s %d', $string_user, $_POST['type'], $id ), 'wpeotasks-i18n' ) );
			}
			else {
				wpeo_tasks_users_mod::clean_user_in( $id, $_POST['type'] );
			}
			
		}
		
		wp_die( $response->output_json() );
	}
	
}

?>
