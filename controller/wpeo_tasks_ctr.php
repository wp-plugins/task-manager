<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class wpeo_tasks_ctr {
	public $task_width = array(
		1 => '100',
		2 => '50',
		3 => '33.333',
	);
	/**
	 * Call some wordpress action : init, admin_menu, admin_enqueue_scripts and admin_print_scripts
	 * Call private function initialize_ajax
	 * @return void
	 */
	public function __construct() {
		add_action( 'init', array( &$this, 'callback_init' ), 0, 0 );
		add_action( 'admin_menu', array( &$this, 'callback_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'callback_admin_enqueue_scripts' ), 1 );
 		add_action( 'admin_print_scripts', array( &$this, 'callback_admin_print_scripts') );
 		
 		add_filter( 'set-screen-option', array( &$this, 'callback_set_screen_option' ), 10, 3 );
 		
 		$this->initialize_ajax();
 		$this->install_in( 'core' );
 		//$this->install_in( 'modules' );
	}
	
	/**
	 * WP HOOK - Call private function my_custom_post_type and the function create_my_metaboxes
	 * @return void
	 */
	public function callback_init() {
		$this->my_custom_post_type();
	}
	
	/**
	 * Declare the custom post type for task
	 * @return void
	 */
	private function my_custom_post_type() {
		/**	Define tasks main post type	*/
		$labels = array(
				'name'                	=> __( 'Tasks', 'wpeotasks-i18n' ),
				'singular_name'       	=> __( 'Task', 'wpeotasks-i18n' ),
				'menu_name'           	=> __( 'Tasks', 'wpeotasks-i18n' ),
				'parent_item_colon'   	=> __( 'Parent Task:', 'wpeotasks-i18n' ),
				'all_items'           	=> __( 'Tasks', 'wpeotasks-i18n' ),
				'view_item'           	=> __( 'View Task', 'wpeotasks-i18n' ),
				'add_new_item'        	=> __( 'Add New Task', 'wpeotasks-i18n' ),
				'add_new'             	=> __( 'New Task', 'wpeotasks-i18n' ),
				'edit_item'           	=> __( 'Edit Task', 'wpeotasks-i18n' ),
				'update_item'         	=> __( 'Update Task', 'wpeotasks-i18n' ),
				'search_items'        	=> __( 'Search Tasks', 'wpeotasks-i18n' ),
				'not_found'           	=> __( 'No tasks found', 'wpeotasks-i18n' ),
				'not_found_in_trash'  	=> __( 'No tasks found in Trash', 'wpeotasks-i18n' ),
		);
		$capabilities = array(
				'edit_post'           => 'edit_wpeomtm_task',
				'read_post'           => 'read_wpeomtm_task',
				'delete_post'         => 'delete_wpeomtm_task',
				'edit_posts'          => 'edit_wpeomtm_tasks',
				'edit_others_posts'   => 'edit_others_wpeomtm_tasks',
				'publish_posts'       => 'publish_wpeomtm_tasks',
				'read_private_posts'  => 'read_private_wpeomtm_tasks',
		);
		$rewrite = array(
				'slug'                => 'time-manager-task',
				'with_front'          => true,
				'pages'               => true,
				'feeds'               => true,
		);
		$args = array(
				'label'               	=> __( 'Tasks', 'wpeotasks-i18n' ),
				'description'         	=> __( 'Tasks management', 'wpeotasks-i18n' ),
				'labels'              	=> $labels,
				'supports'            	=> array( 'title', 'editor', 'thumbnail', 'page-attributes'),
				'hierarchical'        	=> true,
				'public'              	=> true,
				'show_ui'             	=> true,
				'show_in_menu'        	=> false,
				'show_in_json'			=> true,
				'show_in_nav_menus'   	=> true,
				'show_in_admin_bar'   	=> true,
				'can_export'          	=> true,
				'has_archive'         	=> true,
				'exclude_from_search' 	=> true,
				'publicly_queryable'  	=> true,
				//'capabilities'     		=> 'post',
				'rewrite'			  	=> null,
		);
		register_post_type( wpeo_tasks_mod::$post_type, $args );
	}

	/**
	 * Get all points in task and display it with the template backend/task.php
	 * @param unknown_type $post
	 * @param Array $args (id, title, callback, args(task))
	 * @return void
	 */
	public function callback_my_task( $post, $args ) {
		$task = ( !empty( $args['args'] ) && !empty( $args['args']['task'] ) ) ? $args['args']['task'] : null;
	
		if( $task === null ) wp_die( __( 'We can\'t use this task', 'wpeotasks-i18n' ) );
	
		$_GET['ID'] = ( int ) $task->ID;
	
		/** Get points */
		//$points = wpeo_tasks_points_mod::get_points( ( int ) $task->ID, false, 5 );
		
		/** No task not done, display task done */
		//if( empty( $points ) ) $points = wpeo_tasks_points_mod::get_points( ( int ) $task->ID, true, 5 );
	
		require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, 'backend', 'task') );
	}
	
	/**
	 * WP HOOK - Add menu page task management dashboard and submenu page
	 * @return void
	 */
	public function callback_admin_menu() {
		$hook = add_menu_page( __( 'Task management dashboard', 'wpeotasks-i18n' ), __( 'Tasks manager', 'wpeotasks-i18n' ), 'manage_options', 'wpeomtm-dashboard', array( &$this, 'callback_menu_page_dashboard' ), 'dashicons-tagcloud' );
		add_action( 'load-' . $hook, array( &$this, 'callback_add_option' ) );
		
		add_submenu_page( 'wpeomtm-dashboard', __( 'Task management dashboard', 'wpeotasks-i18n' ), __( 'Tasks manager', 'wpeotasks-i18n' ), 'manage_options', 'wpeomtm-dashboard', array( &$this, 'callback_menu_page_dashboard' ) );
	}
	
	/**
	 * WP HOOK - Add JS and CSS
	 * @return void
	 */
	public function callback_admin_enqueue_scripts() {
		/** JS */
		wp_enqueue_script( 'wpeo-task-js', WPEOMTM_TASK_URL . '/asset/js/backend.js', array("jquery", "jquery-form", "jquery-ui-datepicker", "jquery-ui-sortable", "jquery-masonry"), WPEOMTM_TASK_VERSION );
		wp_enqueue_script( 'wpeo-task-hideseek-js', WPEOMTM_TASK_URL . '/asset/js/jquery.hideseek.min.js', array("jquery",), WPEOMTM_TASK_VERSION );
		wp_enqueue_script( 'wpeo-task-flextext-js', WPEOMTM_TASK_URL . '/asset/js/flextext.min.js', array("jquery",), WPEOMTM_TASK_VERSION );
	
		/** CSS */
		wp_register_style( 'wpeo-task-css', WPEOMTM_TASK_URL . '/asset/css/backend.css', '', WPEOMTM_TASK_VERSION );
		wp_register_style( 'wpeo-task-flextext-css', WPEOMTM_TASK_URL . '/asset/css/flextext.css', '', WPEOMTM_TASK_VERSION );
		wp_enqueue_style( 'wpeo-task-css' );
		wp_enqueue_style( 'wpeo-task-flextext-css' );
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'jquery-ui', 'http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css' );

		
		/** Thickbox */
		add_thickbox();
	}
	
	/**
	 * WP HOOK - Require my language.js.php for wordpress translate
	 * @return void
	 */
	public function callback_admin_print_scripts() {
		require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_ASSETS_DIR, "js", "language.js") );
	}
		
	/**
	* WP HOOK - Display the template backend/dashboard-main.php
	* @return void
	*/
	public function callback_menu_page_dashboard() {
		$tasks = wpeo_tasks_mod::get_tasks();
		$tags = wpeo_tasks_tags_mod::get_tags();
		
		/** Get options */
		$user = get_current_user_id();
		$screen = get_current_screen();
		$option = $screen->get_option( 'per_page', 'option' );
		
		$per_page = get_user_meta( $user, $option, true);
		
		if( empty( $per_page ) ) $per_page = 3; // Default value
		
		require_once( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, 'backend', 'dashboard', 'main'));
	}
	
	/**
	 * WP HOOK - Add option in the screen
	 * @return void
	 */
	public function callback_add_option() {
		/** Screen options */
		$option = "per_page";
		
		$args = array(
			'label' => __( 'Tasks per line', 'wpeotasks-i18n' ),
			'default' => 3,
			'option' => 'wpeo_task_per_line'
		);
			
		add_screen_option( $option, $args );
	}
	
	/**
	 * WP HOOK - The callback for set screen option value 
	 * @param string $status
	 * @param string $option
	 * @param int $value
	 * @return number|unknown
	 */
	public function callback_set_screen_option( $status, $option, $value ) {
		if( 'wpeo_task_per_line' == $option ) {
			if( $value > 3)
				$value = 3;
			if( $value <= 0 )
				$value = 1;
			
			return $value;
		}

		return $status;
	}
	
	/**
	 * All the add_action ajax for task (New task, view task, delete task, refresh task, export and save title)
	 * @return void
	 */
	private function initialize_ajax() {
		add_action( 'wp_ajax_wpeo-new-task', array( &$this, 'ajax_new_task' ) );
		add_action( 'wp_ajax_wpeo-delete-task', array( &$this, 'ajax_delete_task' ) );
		add_action( 'wp_ajax_wpeo-refresh-task', array( &$this, 'ajax_refresh_task' ) );
		add_action( 'wp_ajax_wpeo-export', array( &$this, 'ajax_export' ) );
		add_action( 'wp_ajax_wpeo-export-tasks', array( &$this, 'ajax_export_tasks' ) );
		add_action( 'wp_ajax_wpeo-save-title', array( &$this, 'ajax_save_title' ) );
		add_action( 'wp_ajax_wpeo-change-user-write', array( &$this, 'ajax_change_user_write' ) );
		add_action( 'wp_ajax_wpeo-save-time-estimated', array( &$this, 'ajax_save_time_estimated' ) );
		add_action( 'wp_ajax_wpeo-load-notification', array( &$this, 'ajax_load_notification' ) );
		add_action( 'wp_ajax_wpeo-cancel-delete-task', array( &$this, 'ajax_cancel_delete_task' ) );
		add_action( 'wp_ajax_wpeo-view-setting', array( &$this, 'ajax_view_setting' ) );
		add_action( 'wp_ajax_wpeo-update-setting', array( &$this, 'ajax_update_setting' ) );
	}
	
	/**
	* AJAX - Create a new task get it for display it with the template backend/task/task.php
	* @param int $_POST['post_parent'] The post parent for the task
	* @return void
	*/
	public function ajax_new_task() {
		header('Content-Type: application/json');
		
		$post_parent = !empty( $_POST['post_parent'] ) ? $_POST['post_parent'] : 0;
		
		$response = new wpeo_tasks_json_mod();
		
		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'new_task' ) ) {
			$response->setCode(22);
			$response->setState( 'error', __( 'You are not allowed to edit this task : Incorrect nonce', 'wpeotasks-i18n' ) );
		}
		
		if( !$response->check_have_error() ) {
			$task_id = wpeo_tasks_mod::add_new_task( $post_parent );
			$response->setCode(0);
			$response->setObjectId( $task_id );
			$response->setState( 'success', __( sprintf('Create new task with id : %d', $task_id ), 'wpeotasks-i18n' ) );
			
			$task = wpeo_tasks_mod::get_task( $task_id );
			
			if($task != null) {
				$new_metabox = true;
				$task->new = true;
				
				ob_start();
				require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, "backend", "task") );
				unset( $new_metabox );
				$response->setData( array ( 'template' => ob_get_clean(), 'notification_message' => __( 'You have created a new task', 'wpeotasks-i18n') ) );
			}
		}

		wp_die( $response->output_json() );
	}

	/**
	 * AJAX - Move the task in the trash
	 * @param int $_POST['task_id'] - The task id
	 * @return void
	 */
	public function ajax_delete_task() {
		header('Content-Type: application/json');
		
		$response = new wpeo_tasks_json_mod();
		
		if ( !wpeo_tasks_users_mod::check_user_write_mode( ( int ) $_POST['task_id'], ( int ) get_current_user_id() ) ) {
			$response->setState( 'error', __( 'You are not allowed to edit this task : You need to be in write mode', 'wpeotasks-i18n' ) );
			$response->setCode(21);
		}
		
		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'nonce_trash' ) ) {
			$response->setState( 'error', __( 'You are not allowed to edit this task : Incorrect nonce', 'wpeotasks-i18n' ) );
			$response->setCode(22);
		}
		
		$task_id = !empty( $_POST['task_id'] ) ? $_POST['task_id'] : null;
		
		if( null === $task_id ) {
			$response->setState( 'error', __( 'Problem to delete the task : The ID is not found', 'wpeotasks-i18n' ) );
			$response->setCode(20);
		}
		
		if( !$response->check_have_error() ) {
			/** Get task for render */
			$task = wpeo_tasks_mod::get_task( (int) $task_id );
			
			wp_trash_post( $task_id );
			$response->setCode(0);
			$response->setObjectId( $task_id );
			$response->setState( 'success', __( sprintf('The task %d is deleted', $task_id ), 'wpeotasks-i18n' ) );
						
			$response->setData( array( 
				'task_name' 			=> $task->post_title,
				'notification_message' 	=> __( 'The task was moved to the Trash', 'wpeotasks-i18n' ),
				'notification_method' 	=> 'cancel_delete_task',
			));
		}
		
		wp_die( $response->output_json() );
	}
	
	/**
	 * AJAX - Get all points in the tasks
	 * Get the title of the task
	 * Create the render for display all point
	 * Get the time in task and use json for return response
	 * @param $_POST['task_id'] - The task id
	 * @return JSON response
	 */
	public function ajax_refresh_task() {
		header('Content-Type: application/json');
		
		$response = new wpeo_tasks_json_mod();
		
		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'wp_nonce_refresh_task' ) ) {
			$response->setState( 'error', __( 'You are not allowed to edit this task : Incorrect nonce', 'wpeotasks-i18n' ) );
			$response->setCode(22);
		}
		
		$task = wpeo_tasks_mod::get_task( ( int ) $_POST['task_id'] );

		if ( !$response->check_have_error() ) {
			ob_start();
			require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, "backend", "task") );
			$response->setData( array( 'template' => ob_get_clean( ) ) );
		}
		
		wp_die( $response->output_json() );
	}

	/**
	 * AJAX - Prepare the path for the file, get all points, get title of the task and sanitize him
	 * create file with the satinize title task and current timestamp
	 * and put all content of the task in
	 * @param int $_POST['task_id'] - The task id
	 * @return string the url to file
	 */
	public function ajax_export() {
		header('Content-Type: application/json');
		
		$response = new wpeo_tasks_json_mod();
		$response->setObjectId( $_POST['task_id'] );
		
		/** Verify nonce */
		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'nonce_export' ) ) {
			$response->setState( 'error', __( 'You are not allowed to export this task : Incorrect nonce', 'wpeotasks-i18n' ) );
			$response->setCode(22);
		}
		
		/** No error */
		if( !$response->check_have_error() ) {
			$task_title = wpeo_tasks_mod::get_title_of_task( (int) $_POST['task_id'] );
			$points = array_merge( wpeo_tasks_points_mod::get_points( (int) $_POST['task_id'], true ), wpeo_tasks_points_mod::get_points( (int) $_POST['task_id'], false ) );
			
			if( empty( $points ) ) {
				$response->setState( 'error', __( sprintf( 'No data to export in the task %d', $_POST['task_id'] ), 'wpeotasks-i18n' ) );
				$response->setCode(22);
			}
			
			$title = sanitize_title($task_title) . current_time( 'timestamp' ) . '.txt';
			
			$path = WPEOMTM_TASK_EXPORT_DIR . $title;
			$url_to_file = WPEOMTM_TASK_EXPORT_URL . $title;		
			$content = $_POST['task_id'] . ' - ' . $task_title . "
			
	";
			
			foreach( $points as $point ) {
				$content .= '	' . $point->comment_ID . ' - ' . $point->comment_content . "
	";
			}
					
			if( !$this->export_to_file( $path, $content ) ) {
				$response->setState( 'error', __( sprintf( 'An error occured when opened the file : %s', $path ), 'wpeotasks-i18n' ) );
				$response->setCode(22);
			}
				
			if( !$response->check_have_error() ) {
				$response->setState( 'success', __( sprintf( 'The task %d is exported to the file %s', $_POST['task_id'], $path ), 'wpeotasks-i18n' ) );
				$response->setCode(0);
				$response->setData( array ( 'link' => $url_to_file, 'notification_message' => __( sprintf( 'The task %d is exported', $_POST['task_id'] ) , 'wpeotasks-i18n' ) ) );
			}
		}
		
		wp_die( $response->output_json() );
	}
	
	/**
	 * AJAX - Prepare the path for the file, get all tasks with their points and export it to a file
	 * @param array $_POST['array_tasks'] The tasks id
	 * @param string $_POST['_wpnonce'] Security
	 * @return JSON Response
	 */
	public function ajax_export_tasks() {
		header('Content-Type: application/json');
		
		$response = new wpeo_tasks_json_mod();
		
		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'nonce_export_all' ) ) {
			$response->setState( 'error', __( 'You are not allowed to export this task : Incorrect nonce', 'wpeotasks-i18n' ) );
			$response->setCode(22);
		}
		
		if( empty( $_POST['array_tasks'] ) ) {
			$response->setState( 'error', __( 'No data to export', 'wpeotasks-i18n' ) );
			$response->setCode(23);
		}
		
		if( !$response->check_have_error() ) {
			$array_tasks = array();
			
			$title = '';
			
			foreach ( $_POST['array_tasks'] as $task_id ) {
				$array_tasks[] = wpeo_tasks_mod::get_task( (int) $task_id );
				$title .= $task_id . '_';
			}
			$title_json = str_replace( '_', ' ', $title );
			$title .= current_time( 'timestamp' ) . '.txt';
			
			$path = WPEOMTM_TASK_EXPORT_DIR  . $title;
			$url_to_file = WPEOMTM_TASK_EXPORT_URL . $title;
			
			
			$content = '';
			
			if( !empty( $array_tasks ) ) {
				foreach( $array_tasks as $key => $array_task ) {
					if( 0 != $key ) {
					$content .= '
							
'; 
					}
					$content .= $array_task->ID . ' - ' . $array_task->post_title . '
		
';
					$points = array_merge( wpeo_tasks_points_mod::get_points( (int) $array_task->ID, true ), wpeo_tasks_points_mod::get_points( (int) $array_task->ID, false ) );
					if( !empty( $points ) ) {
						foreach( $points as $point ) {
							$content .= '	' . $point->comment_ID . ' - ' . $point->comment_content . '
';
						}
					}
				}
			}

			if( !$this->export_to_file( $path, $content ) ) {
				$response->setState( 'error', __( sprintf( 'An error occured when opened the file : %s', $path ), 'wpeotasks-i18n' ) );
				$response->setCode(22);
			}
				
			if( !$response->check_have_error() ) {
				$response->setState( 'success', __( sprintf( 'Export task %s to %s', $title_json, $path ), 'wpeotasks-i18n' ) );
				$response->setCode(0);
				$response->setData( array ( 'link' => $url_to_file, 'notification_message' => sprintf(__( 'Export task %s to %s', 'wpeotasks-i18n'), $title_json, $path ) ) );
			}
		}
		
		wp_die( $response->output_json() );
	}
	
	/**
	 * AJAX - Get the current task and update the title
	 * @param int $_POST['title'] - The task title / Le titre de la tâche
	 * @param int $_POST['task_id'] - The task ID / Le post ID
	 * @return void
	 */
	public function ajax_save_title() {
		header('Content-Type: application/json');
		
		$response = new wpeo_tasks_json_mod();
		$response->setObjectId( $_POST['task_id'] );
		
		if ( !wpeo_tasks_users_mod::check_user_write_mode( ( int ) $_POST['task_id'], ( int ) get_current_user_id() ) ) {
			$response->setState( 'error', __( 'You are not allowed to edit this task : You need to be in write mode', 'wpeotasks-i18n' ) );
			$response->setCode(21);
		}
		
		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'wp_nonce_title' ) ) {
			$response->setState( 'error', __( 'You are not allowed to edit this task : Incorrect nonce', 'wpeotasks-i18n' ) );
			$response->setCode(22);
		}
		
		/** Get current post */
		$task = get_post($_POST['task_id']);
		$strip_title = wp_strip_all_tags($_POST['title']);
		
		$post_informations = array(
				"post_title" 	=> $strip_title,
				"post_type" 	=> wpeo_tasks_mod::$post_type,
				"post_status" 	=> wpeo_tasks_mod::$task_state,
				"ID"			=> $_POST['task_id'],
				"post_author" 	=> $task->post_author,
		);
		
		wp_update_post($post_informations);
		$response->setCode(0);
		$response->setState( 'success', __( sprintf( 'Update the task %d set title %s', $_POST['task_id'], $strip_title ), 'wpeotasks-i18n' ) );
		$response->setData( array( 'notification_message' => __( sprintf( 'Update the task %d set title %s', $_POST['task_id'], $strip_title), 'wpeotasks-i18n' ) ) );
		
		wp_die( $response->output_json() );
	}
	
	/**
	 * AJAX - Change user write mode  
	 * @param int $_POST['task_id'] The task id
	 * @param string $_POST['_wpnonce'] Security
	 * @return void
	 */
	public function ajax_change_user_write() {	
		header('Content-Type: application/json');
		
		$response = new wpeo_tasks_json_mod();
		$response->setObjectId( $_POST['task_id'] );
		
		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'wp_nonce_change_user_write' ) ) {
			$response->setCode(22);
			$response->setState( 'error', __( 'You are not allowed to edit this task : Incorrect nonce', 'wpeotasks-i18n' ) );
		}
		
		$response->setCode(0);
		if( 'w' == $_POST['current_mode'] ) {
			wpeo_tasks_users_mod::remove_user_write( (int) $_POST['task_id'] );
			$response->setState( 'success', __( sprintf( 'Remove write permission on the task %d', $_POST['task_id'] ) , 'wpeotasks-i18n' ) );
		}
		else {
			wpeo_tasks_users_mod::add_user_write( (int) $_POST['task_id'], get_current_user_id() );
			$response->setState( 'success', __( sprintf( 'Put the write permission on the task %d for the user %d', $_POST['task_id'], get_current_user_id() ) , 'wpeotasks-i18n' ) );
			$response->setData( array( 'notification_message' => __( sprintf( 'Put the write permission on the task %d for the user %d', $_POST['task_id'], get_current_user_id() ), 'wpeotasks-i18n' ) ) );
		}
		wp_die( $response->output_json() );
	}
	
	/**
	 * AJAX - Save the estimated time
	 * @param int $_POST['task_id'] The task id
	 * @param string $_POST['_wpnonce'] The security
	 * @return void
	 */
	public function ajax_save_time_estimated() {
		header('Content-Type: application/json');
		
		$response = new wpeo_tasks_json_mod();
		$response->setObjectId( $_POST['task_id'] );
		
		if ( !wpeo_tasks_users_mod::check_user_write_mode( ( int ) $_POST['task_id'], ( int ) get_current_user_id() ) ) {
			$response->setState( 'error', __( 'You are not allowed to edit this task : You need to be in write mode', 'wpeotasks-i18n' ) );
			$response->setCode(21);
		}
		
		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'wp_nonce_time_estimated' ) ) {
			$response->setCode(22);
			$response->setState( 'error', __( 'You are not allowed to edit this task : Incorrect nonce', 'wpeotasks-i18n' ) );
		}
		
		if( !$response->check_have_error() ) {
			wpeo_tasks_mod::add_estimated_time ( $_POST['task_id'], $_POST['time'] );
			$response->setCode(0);
			$response->setState( 'success', __( sprintf( 'Change the time estimated to %s for the task %d', $_POST['time'], $_POST['task_id']) , 'wpeotasks-i18n' ) );
			$response->setData( array( 'notification_message' => __( sprintf( 'Change the time estimated to %s for the task %d', $_POST['time'], $_POST['task_id'] ), 'wpeotasks-i18n' ) ) );
		}
		
		wp_die( $response->output_json() );
	}
	
	/**
	 * AJAX - Load notification
	 * @param string $_POST['message'] The message
	 * @return void
	 */
	public function ajax_load_notification() {
		$message = esc_html( $_POST['message'] );
		$method = !empty($_POST['method']) ? $_POST['method'] : '';
		
		require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, "backend", "notification") );
		wp_die();
	}
	
	/**
	 * AJAX - Cancel delete task notification
	 * @param int $_POST['task_id'] The task id
	 * @return void
	 */
	public function ajax_cancel_delete_task() {
		header('Content-Type: application/json');
		
		$response = new wpeo_tasks_json_mod();
		$response->setObjectId( $_POST['task_id'] );
		
		wp_untrash_post( $_POST['task_id'] );
		
		$task = wpeo_tasks_mod::get_task( (int) $_POST['task_id'] );
			
		if($task != null) {
			$new_metabox = true;
			$task->new = true;
		
			ob_start();
			require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, "backend", "task") );
			unset( $new_metabox );
			$response->setData( array ( 'template' => ob_get_clean() ) );
		}
		
		wp_die( $response->output_json() );
	}
	
	/**
	 * AJAX - View setting of the task for set write/read mode
	 */
	public function ajax_view_setting() {
		header('Content-Type: application/json');
		
		$response = new wpeo_tasks_json_mod();
		$response->setObjectId( $_GET['task_id'] );
		
		if ( !wp_verify_nonce( $_GET['_wpnonce'], 'nonce_setting' ) ) {
			$response->setCode(22);
			$response->setState( 'error', __( 'You are not allowed to edit this task : Incorrect nonce', 'wpeotasks-i18n' ) );
		}
		
		if( !$response->check_have_error() ) {		
			$meta_setting = wpeo_tasks_mod::get_default_setting( ( int ) $_GET['task_id'] );
			
			ob_start();
			require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, 'backend', 'setting') );
			$res_html = ob_get_clean();
			
			$response->setCode(0);
			$response->setState( 'success', __( sprintf( 'View setting of the task %d', $_GET['task_id'] ), 'wpeotasks-i18n' ) );
			$response->setData( 
				array (
					'html' => $res_html,
				)
			);
		}
		
		wp_die( $response->output_html() );
	}
	
	/**
	 * AJAX - Update setting of the task
	 */
	public function ajax_update_setting() {
		header('Content-Type: application/json');
		
		$response = new wpeo_tasks_json_mod();
		$response->setObjectId( $_POST['task_id'] );
		
		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'nonce_update_setting' ) ) {
			$response->setCode(22);
			$response->setState( 'error', __( 'You are not allowed to edit this task : Incorrect nonce', 'wpeotasks-i18n' ) );
		}
		
		if( !$response->check_have_error() ) {
			
			$array_default_setting = array(
				'user' => ( bool ) !empty( $_POST['user'] ) ? 1 : 0,
				'time' => ( bool ) !empty( $_POST['time'] ) ? 1 : 0,
			);

			wpeo_tasks_mod::add_default_setting( ( int ) $_POST['task_id'], $array_default_setting );
			
			$response->setCode(0);
			$response->setState( 'success', __( sprintf( 'Change the setting for the task %d', $_POST['task_id']) , 'wpeotasks-i18n' ) );
			$response->setData( array( 'notification_message' => __( sprintf( 'Change the setting for the task %d', $_POST['task_id'] ), 'wpeotasks-i18n' ) ) );
			
		}
		
		wp_die( $response->output_json() );
	}
	
	/**
	 * Export the content to the path_file
	 * @param string $path_file
	 * @param string $content
	 * @return bool
	 */
	private function export_to_file( $path_file, $content ) {
		$fp = fopen( $path_file, 'w' );		
		fputs( $fp, $content );
		fclose( $fp );
		
		return $fp;
	}
	
	/**
	 * CORE - Install all extra-modules in "Core" folder
	 */
	private function install_in( $folder ) {
		/**     Define the directory containing all exrta-modules for current plugin    */
		$module_folder = WPEOMTM_TASK_PATH . '/' . $folder . '/';
	
		/**     Check if the defined directory exists for reading and including the different modules   */
		if( is_dir( $module_folder ) ) {
			$parent_folder_content = scandir( $module_folder );
			foreach ( $parent_folder_content as $folder ) {
				if ( $folder && substr( $folder, 0, 1) != '.' ) {
					if( is_dir ( $module_folder . $folder ) ) 
						$child_folder_content = scandir( $module_folder . $folder );
					
					if ( file_exists( $module_folder . $folder . '/' . $folder . '.php') ) {
						$f =  $module_folder . $folder . '/' . $folder . '.php';
						include( $f );
					}
				}
			}
		}
	}

}

?>
