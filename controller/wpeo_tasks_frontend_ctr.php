<?php
/**
 * Frontend controller file for task module
 *
 * @author Eoxia development team <dev@eoxia.com>
 * @version 1.0
 */

/**
 * Frontend controller class for task module
 *
 * @author Eoxia development team <dev@eoxia.com>
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class wpeo_tasks_frontend_ctr {
	
	/**
	 * Call private function initialize_ajax
	 * @return void
	 */
	public function __construct() {
		$this->initialize_ajax();
		
		add_action( 'wp_enqueue_scripts', array( &$this, 'callback_enqueue_scripts' ) );
		add_action( 'wp_print_scripts', array( &$this, 'callback_print_scripts') );
		
		add_filter( 'the_title', array( &$this, 'callback_the_title'), 1, 2 );
		add_filter( 'the_content', array( &$this, 'callback_the_content'), 50 );
		
// 		add_shortcode( 'wpeo_project', array( &$this, 'shortcode_task' ) );
	}
	
	/**
	 * WP HOOK - Add JS and CSS
	 * @return void
	 */
	public function callback_enqueue_scripts() {
		/** JS */
// 		wp_enqueue_script( 'wpeo-task-js', WPEOMTM_TASK_URL . '/assets/js/backend.js', array("jquery", "jquery-form", "jquery-ui-datepicker", "jquery-ui-sortable", "jquery-masonry"), WPEOMTM_TASK_VERSION );
// 		wp_enqueue_script( 'wpeo-task-hideseek-js', WPEOMTM_TASK_URL . '/assets/js/jquery.hideseek.min.js', array("jquery",), WPEOMTM_TASK_VERSION );
	
		/** CSS */
		wp_register_style( 'wpeo-task-css', WPEOMTM_TASK_URL . '/asset/css/frontend.css', '', WPEOMTM_TASK_VERSION );
		wp_enqueue_style( 'wpeo-task-css' );
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'jquery-ui', 'http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css' );
	
	
		/** Thickbox */
		add_thickbox();
	}
	
	/**
	 * WP HOOK - Require my language.js.php for wordpress translate
	 * @return void
	 */
	public function callback_print_scripts() {
		require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_ASSETS_DIR, "js", "language.js") );
	}
	
	/**
	 * All the add_action ajax for task ()
	 * @return void
	 */
	private function initialize_ajax() {
		
	}

	/**
	 * Create the shortcode for frontend
	 * @param array $atts ('id')
	 * @return void
	 */
	public function shortcode_task( $atts ) {
		$atts = shortcode_atts( array(
			'id' => 0,
		), $atts, 'wpeo_project' );
		
		if( 0 === $atts['id'] ) {
			$tasks = wpeo_tasks_mod::get_tasks();
		}
		else {
			$tasks = array( wpeo_tasks_mod::get_task( (int) $atts['id'] ) );
		}
		
 		require_once( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, 'frontend', 'tasks' ) );
	}

	public function callback_the_title( $title, $id ) {
		$post = get_post( $id );

		if( wpeo_tasks_mod::$post_type !== $post->post_type )
			return $title;
		
		return $post->ID . ' - ' . $title;
	}
	public function callback_the_content( $content ) {
		global $post;

		if( wpeo_tasks_mod::$post_type !== $post->post_type )
			return $content;
		
		$task = wpeo_tasks_mod::get_task( (int) $post->ID );
				
		ob_start();
		require_once( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, 'frontend', 'task' ) );
		$content = ob_get_clean();
		
		return $content;
	}
}

?>
