<?php
/**
 * My account controller file for task module
*
* @author Eoxia development team <dev@eoxia.com>
* @version 1.0
*/

/**
 * My account controller class for task module
*
* @author Eoxia development team <dev@eoxia.com>
* @version 1.0
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class wpeo_tasks_my_account_ctr {

	/**
	 * Call private function initialize_ajax
	 * @return void
	 */
	public function __construct() {
		$this->initialize_ajax();

		add_filter( 'wps_my_account_extra_part_menu', array( $this, 'callback_my_account_menu' ) );
		add_filter( 'wps_my_account_extra_panel_content', array( $this, 'callback_my_account_content' ), 10, 2 );
	}

	/**
	 * All the add_action ajax for task ()
	 * @return void
	 */
	private function initialize_ajax() {

	}
	
	public function callback_my_account_menu( $content ) {
		require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, 'frontend/account', 'menu' ) );
	}
	
	public function callback_my_account_content( $output, $dashboard_part ) {
		if( 'my-task' === $dashboard_part ) {
			?>
			aa
			<?php 
		}
	}
}

?>
