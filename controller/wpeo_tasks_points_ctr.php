<?php
/**
 * Point controller file for task module
 *
 * @author Eoxia development team <dev@eoxia.com>
 * @version 1.0
 */

/**
 * Point controller class for task module
 *
 * @author Eoxia development team <dev@eoxia.com>
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class wpeo_tasks_points_ctr {
	
	/**
	 * Call the method initialize_ajax
	 * @return void
	 */
	public function __construct() {
		$this->initialize_ajax();
	}
	
	/**
	 * All the add_action ajax for task (Add point, delete point, update point, view time point, save time point and set order point)
	 * @return void
	 */
	private function initialize_ajax() {
		add_action( 'wp_ajax_wpeo-add-point', array( &$this, 'ajax_add_point' ) );
		add_action( 'wp_ajax_wpeo-delete-point', array( &$this, 'ajax_delete_point' ) );
		add_action( 'wp_ajax_wpeo-cancel-delete-point', array( &$this, 'ajax_cancel_delete_point' ) );
		add_action( 'wp_ajax_wpeo-update-point', array( &$this, 'ajax_update_point' ) );
		add_action( 'wp_ajax_wpeo-set-order-point', array( &$this, 'ajax_set_order_point' ) );
		add_action( 'wp_ajax_wpeo-view-time-point', array( &$this, 'ajax_view_time_point' ) );
		add_action( 'wp_ajax_wpeo-save-time-point', array( &$this, 'ajax_save_time_point' ) );
		add_action( 'wp_ajax_wpeo-point-done', array( &$this, 'ajax_point_done' ) );
		add_action( 'wp_ajax_wpeo-delete-comment', array( &$this, 'ajax_delete_comment' ) );
	}
	
	/**
	 * AJAX - Add point to task by the wpeo_tasks_points_mod and display it
	 * with the template backend/point/point.php
	 * @param int $_POST['task_id'] - The task ID
	 * @param string $_POST['message'] - The message
	 * @param bool $_POST['can_empty'] - The message can be empty or not
	 * @return void
	 */
	public function ajax_add_point() {
		header('Content-Type: application/json');
		
		$_POST['can_empty'] = (int) $_POST['can_empty'];
		
		$response = new wpeo_tasks_json_mod();
		$response->setObjectId( $_POST['task_id'] );
		
		
		if ( class_exists( 'wpeo_user_ctr' ) && !wpeo_user_mod::check_user_write_mode( ( int ) $_POST['task_id'], ( int ) get_current_user_id() ) ) {
			$response->setCode(21);
			$response->setState( 'error', __( 'You are not allowed to edit this task : You need to be in write mode', 'wpeotasks-i18n' ) );
		}
		
		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'nonce_add_point' ) ) {
			$response->setCode(22);
			$response->setState( 'error', __( 'You are not allowed to edit this task : Incorrect nonce', 'wpeotasks-i18n' ) );
		}

		if( !$response->check_have_error() ) {
			$point_id = wpeo_tasks_points_mod::add_point_to( (int) $_POST['task_id'], (string) $_POST['message'], (int) $_POST['can_empty'] );	
			
			if( null === $point_id && !$_POST['can_empty'] ) {
				$response->setCode(22);
				$response->setState( 'error', __( 'You tried to add an empty point', 'wpeotasks-i18n' ) );
			}
			
			if( !$response->check_have_error() ) {
				$response->setCode(0);
				$response->setState( 'success', __( sprintf( 'The point %d was added to the task %d', $point_id, $_POST['task_id'] ), 'wpeotasks-i18n' ) );
				
				$point = wpeo_tasks_points_mod::get_point( $_POST['task_id'], $point_id );
				
				/** For the template */
				$task = wpeo_tasks_mod::get_task( (int) $_POST['task_id'] );
				
				ob_start();
				require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, 'backend', 'point') );
				$response->setData( array ( 
					'template' => ob_get_clean(), 
					'point_id' => $point_id, 
					'notification_message' => __( sprintf( 'The point %d was added to the task %d', $point_id, $_POST['task_id'] ), 'wpeotasks-i18n' ),
				) );
			}
		}
		
		wp_die( $response->output_json() );
	}
	
	/**
	 * AJAX - Delete point by the task_id and point_id
	 * @param int $_POST['task_id'] - The task id
	 * @param int $_POST['point_id'] - The point id
	 * @return void
	 */
	public function ajax_delete_point() {
		header('Content-Type: application/json');
		
		$response = new wpeo_tasks_json_mod();
		$response->setObjectId( $_POST['task_id'] );
		
		if ( class_exists( 'wpeo_user_ctr' ) && !wpeo_user_mod::check_user_write_mode( ( int ) $_POST['task_id'], ( int ) get_current_user_id() ) ) {
			$response->setCode(21);
			$response->setState( 'error', __( 'You are not allowed to edit this task : You need to be in write mode', 'wpeotasks-i18n' ) );
		}
		
		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'nonce_point_trash' ) ) {
			$response->setCode(22);
			$response->setState( 'error', __( 'You are not allowed to edit this task : Incorrect nonce', 'wpeotasks-i18n' ) );
		}
		
		if( !$response->check_have_error() ) {
			wpeo_tasks_points_mod::delete_point( (int) $_POST['task_id'], (int) $_POST['point_id'] );
			
			$response->setCode(0);
			$response->setState( 'success', __( sprintf( 'The point %d was deleted from the task %d', $_POST['point_id'], $_POST['task_id'] ), 'wpeotasks-i18n' ) );
		}
		
		wp_die( $response->output_json() );
	}
	
	/**
	 * AJAX - Cancel point deleted
	 * @param int $_POST['point_id'] The point id
	 * @return void
	 */
	public function ajax_cancel_delete_point() {
		header('Content-Type: application/json');
		
		$response = new wpeo_tasks_json_mod();
		$response->setObjectId( $_POST['point_id'] );
		
		$args = array(
			'comment_ID' 		=> $_POST['point_id'],
			'comment_approved' 	=> wpeo_tasks_points_mod::$comment_approved,
		);
		
		wp_update_comment ( $args );
		
		wp_die( $response->output_json() );
	}
	
	/**
	 * AJAX - Update point with the message
	 * @param string $_POST['message'] The message
	 * @param int $_POST['task_id'] The task id
	 * @param int $_POST['point_id'] The point id
	 * @return JSON response
	 */
	public function ajax_update_point() {
		header('Content-Type: application/json');
		
		$response = new wpeo_tasks_json_mod();
		$response->setObjectId( $_POST['task_id'] );
		
		if ( class_exists( 'wpeo_user_ctr' ) && !wpeo_user_mod::check_user_write_mode( ( int ) $_POST['task_id'], ( int ) get_current_user_id() ) ) {
			$response->setCode(21);
			$response->setState( 'error', __( 'You are not allowed to edit this task : You need to be in write mode', 'wpeotasks-i18n' ) );
		}
		
		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'wp_nonce_update_point' ) ) {
			$response->setCode(22);
			$response->setState( 'error', __( 'You are not allowed to edit this task : Incorrect nonce', 'wpeotasks-i18n' ) );
		}
		
		if( !$response->check_have_error() ) {
			wpeo_tasks_points_mod::update( $_POST['task_id'], $_POST['point_id'], trim( $_POST['message'] ) );
			
			$response->setCode(0);
			$response->setState( 'success', __( sprintf( 'The text of point %d was edited from the task %d to %s', $_POST['point_id'], $_POST['task_id'], $_POST['message'] ), 'wpeotasks-i18n' ) );
			$response->setData( array (
					'notification_message' => __( sprintf( 'The text of point %d was edited from the task %d to %s', $_POST['point_id'], $_POST['task_id'], $_POST['message'] ), 'wpeotasks-i18n' ),
			) );
		}
		
		wp_die( $response->output_json() );
	}

	/**
	 * AJAX - Update the post meta for set the order point
	 * @param int $_POST['task_id'] - The task id
	 * @param array $_POST['array_id'] - The array id of points
	 * @return void
	 */
	public function ajax_set_order_point() {
		header('Content-Type: application/json');
		
		$response = new wpeo_tasks_json_mod();
		$response->setObjectId( $_POST['task_id'] );
		
		if ( class_exists( 'wpeo_user_ctr' ) && !wpeo_user_mod::check_user_write_mode( ( int ) $_POST['task_id'], ( int ) get_current_user_id() ) ) {
			$response->setCode(21);
			$response->setState( 'error', __( 'You are not allowed to edit this task : You need to be in write mode', 'wpeotasks-i18n' ) );
		}
		
		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'nonce_point_order' ) ) {
			$response->setCode(22);
			$response->setState( 'error', __( 'You are not allowed to edit this task : Incorrect nonce', 'wpeotasks-i18n' ) );
		}

		if( !$response->check_have_error() ) {
			update_post_meta( $_POST['task_id'], wpeo_tasks_points_mod::$metakey_order, $_POST['array_id'] );
			
			if( !empty( $_POST['array_id'] ) ) {
				foreach( $_POST['array_id'] as $id ) {
					wp_update_comment( 
						array (
							'comment_ID' => $id,
							'comment_post_ID' => $_POST['task_id'],
						)
					);
				}
			}
		
			$response->setCode(0);
			$response->setState( 'success', __( sprintf( 'The order of the points in the task %d is reassigned', $_POST['task_id'], $_POST['array_id'] ), 'wpeotasks-i18n' ) );
			$response->setData( array (
					'notification_message' => __( sprintf( 'The order of the points in the task %d is reassigned', $_POST['task_id'], $_POST['array_id'] ), 'wpeotasks-i18n' ),
			) );
		}
	
		wp_die( $response->output_json() );
	}
	
	/**
	 * AJAX - This method is called when click on the dashicons-clock
	 * Check if the points is done or not
	 * Display the form for add comment and time with the template backend/point/form.php
	 * Get all comments in this point and display it with the form backend/point/list-comments.php
	 * @param int $_GET['point_id'] - The point id
	 * @param int $_GET['task_id'] - The task id
	 * @return void
	 */
	public function ajax_view_time_point() {
		header('Content-Type: application/json');
		
		$response = new wpeo_tasks_json_mod();
		$response->setObjectId( $_GET['task_id'] );
		
		if ( !wp_verify_nonce( $_GET['_wpnonce'], 'wp_nonce_view_time' ) ) {
			$response->setCode(22);
			$response->setState( 'error', __( 'You are not allowed to edit this task : Incorrect nonce', 'wpeotasks-i18n' ) );
		}
	
		if( !$response->check_have_error() ) {
			$done = wpeo_tasks_points_mod::get_done( (int) $_GET['point_id'] );
			
			ob_start();
			require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, 'backend', 'form-comment') );
			$html_form = ob_get_clean();
			
			$comments = wpeo_tasks_points_mod::get_comments_in_points( (int) $_GET['task_id'] , (int) $_GET['point_id'] );
			
			ob_start();
			require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, 'backend', 'comments') );
			$html_comments = ob_get_clean();
			
			$response->setData( array( 'html' => array( 'form' => $html_form, 'comments' => $html_comments ) ) );
			
			$response->setCode(0);
			$response->setState( 'success', __( sprintf( 'Open the time viewer for the point %d in the task %d', $_GET['point_id'], $_GET['task_id'] ), 'wpeotasks-i18n' ) );
		}
		
		wp_die( $response->output_html() );
	}

	/**
	 * AJAX - Set if the point is done or not
	 * Add the comment in the point
	 * @param int $_POST['point_id'] - The point id
	 * @param string $_POST['done_point'] - The checkbox
	 * @param int $_POST['task_id'] - The task id
	 * @param string $_POST['message'] - The comment
	 * @param string $_POST['minute'] - The minute
	 * @return void
	 */
	public function ajax_save_time_point() {
		header('Content-Type: application/json');

		$response = new wpeo_tasks_json_mod();
		$response->setObjectId( $_POST['point_id'] );
		
		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'wp_nonce_add_time' ) ) {
			$response->setCode(22);
			$response->setState( 'error', __( 'You are not allowed to edit this task : Incorrect nonce', 'wpeotasks-i18n' ) );
		}
			
		if( !$response->check_have_error() ) {
			wpeo_tasks_points_mod::add_comment( (int) $_POST['task_id'], (int) $_POST['point_id'], (string) $_POST['message'], $_POST['date'], $_POST['minute'] );
		
			$response->setCode(0);
			$response->setState( 'success', __( sprintf( 'Add comment %s with time %d for the point %d in the task %d', $_POST['message'], $_POST['minute'], $_POST['point_id'], $_POST['task_id'] ), 'wpeotasks-i18n' ) );
		
			$response->setData( array(
					'time_in_task' => wpeo_tasks_mod::get_time_in_task( ( int ) $_POST['task_id'] ),
					'time_in_point' => wpeo_tasks_points_mod::get_point( ( int ) $_POST['task_id'], ( int ) $_POST['point_id'] )->informations->comment_time,
					'point_id' => $_POST['point_id'],
					'task_id' => $_POST['task_id'],
				)
			);
		}
				
		wp_die( $response->output_json() );
	}

	/**
	 * AJAX - When click on the checkbox for done/undone point and return the template of this
	 * @param int $_POST['task_id'] The task id
	 * @param int $_POST['point_id'] The point id
	 * @param string $_POST['done'] The state of the point
	 * @return JSON Response
	 */
	public function ajax_point_done() {
		header('Content-Type: application/json');
		
		$response = new wpeo_tasks_json_mod();
		$response->setObjectId( $_POST['task_id'] );
		
		if ( class_exists( 'wpeo_user_ctr' ) && !wpeo_user_mod::check_user_write_mode( ( int ) $_POST['task_id'], ( int ) get_current_user_id() ) ) {
			$response->setCode(21);
			$response->setState( 'error', __( 'You are not allowed to edit this task : You need to be in write mode', 'wpeotasks-i18n' ) );
		}
		
		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'wp_nonce_done' ) ) {
			$response->setCode(22);
			$response->setState( 'error', __( 'You are not allowed to edit this task : Incorrect nonce', 'wpeotasks-i18n' ) );
		}

		if( !$response->check_have_error() ) {
			wpeo_tasks_points_mod::set_done( (int) $_POST['task_id'], (int) $_POST['point_id'], $_POST['done'] );
			
			$response->setCode(0);
			
			if($_POST['done'] == 'true')
				$response->setState( 'success', __( sprintf( 'Point %d is completed now for the task %d', $_POST['point_id'], $_POST['task_id'] ), 'wpeotasks-i18n' ) );
			else
				$response->setState( 'success', __( sprintf( 'Point %d isn\'t completed now for the task %d', $_POST['point_id'], $_POST['task_id'] ), 'wpeotasks-i18n' ) );
			
			$point = wpeo_tasks_points_mod::get_point( (int) $_POST['task_id'], (int) $_POST['point_id'] );
			
			/** For the template */
			$_GET['task_id'] = $_POST['task_id'];

			/** For the template */
			$task = wpeo_tasks_mod::get_task( (int) $_POST['task_id'] );
			
			
			ob_start();
			require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, 'backend', 'point') );
			
			$response->setData(
					array(
						'html' => ob_get_clean(),
						'done' => $_POST['done'],
					)
			);
		}
		
		wp_die( $response->output_json() );
	}

	/**
	 * AJAX - Delete comment in point by the comment_ID
	 * @param int $_POST['task_id'] The task id
	 * @param int $_POST['point_id'] The point id
	 * @param int $_POST['comment_id'] The comment id
	 * @return JSON Response
	 */
	public function ajax_delete_comment() {
		header('Content-Type: application/json');
		
		$response = new wpeo_tasks_json_mod();
		$response->setObjectId( $_POST['task_id'] );
		
		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'nonce_comment_trash' ) ) {
			$response->setCode(22);
			$response->setState( 'error', __( 'You are not allowed to edit this task : Incorrect nonce', 'wpeotasks-i18n' ) );
		}
		
		if( !$response->check_have_error() ) {
			wpeo_tasks_points_mod::delete_comment( (int) $_POST['task_id'], (int) $_POST['point_id'], (int) $_POST['comment_id'] );
			
			$response->setCode(0);
			$response->setState( 'success', __( sprintf( 'The comment %d has been deleted from the point %d', $_POST['comment_id'], $_POST['point_id'] ), 'wpeotasks-i18n' ) );
			$response->setData( array( 'notification_message' => __( sprintf('The comment %d has been deleted from the point %d', $_POST['comment_id'], $_POST['point_id']), 'wpeotasks-i18n' ) ) );
		}
		
		wp_die( $response->output_json() );
	}
}

?>
