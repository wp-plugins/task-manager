<?php
/**
 * Plugin Name: Task Manager
 * Description: Quick and easy to use, manage all your tasks and your time with this plugin. / Rapide et facile à utiliser, gérer toutes vos tâches et votre temps avec cette extension.
 * Version: 1.1
 * Author: Eoxia <dev@eoxia.com>
 * Author URI: http://www.eoxia.com/
 * License: GPL2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Bootstrap file for plugin. Do main includes and create new instance for plugin components
 *
 * @author Eoxia <dev@eoxia.com>
 * @version 1.1
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** Define */
DEFINE( 'WPEOMTM_TASK_VERSION', 1.1 );
DEFINE( 'WPEOMTM_TASK_DIR', basename( dirname( __FILE__ ) ) );
DEFINE( 'WPEOMTM_TASK_PATH', str_replace( "\\", "/", plugin_dir_path( __FILE__ ) ) );
DEFINE( 'WPEOMTM_TASK_URL', str_replace( str_replace( "\\", "/", ABSPATH), site_url() . '/', WPEOMTM_TASK_PATH ) );

DEFINE( 'WPEOMTM_TASK_EXPORT_URL', WPEOMTM_TASK_URL . '/asset/exports/');
DEFINE( 'WPEOMTM_TASK_EXPORT_DIR',  WPEOMTM_TASK_PATH . '/asset/exports/' );
DEFINE( 'WPEOMTM_TASK_ASSETS_DIR',  WPEOMTM_TASK_PATH . '/asset/' );

DEFINE( 'WPEOMTM_TASK_TEMPLATES_MAIN_DIR', WPEOMTM_TASK_PATH . '/template/');

/**	Load plugin translation	*/
load_plugin_textdomain( 'wpeotasks-i18n', false, dirname( plugin_basename( __FILE__ ) ) . '/language/' );

/** Require */
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

require_once( WPEOMTM_TASK_PATH . '/controller/wpeo_tasks_ctr.php' );
require_once( WPEOMTM_TASK_PATH . '/controller/wpeo_tasks_points_ctr.php' );
require_once( WPEOMTM_TASK_PATH . '/controller/wpeo_tasks_custom_ctr.php' );
require_once( WPEOMTM_TASK_PATH . '/controller/wpeo_tasks_my_account_ctr.php' );
require_once( WPEOMTM_TASK_PATH . '/controller/wpeo_tasks_utils_ctr.php' );
require_once( WPEOMTM_TASK_PATH . '/controller/wpeo_tasks_frontend_ctr.php' );
require_once( WPEOMTM_TASK_PATH . '/controller/wpeoTasksTemplate_ctr.php' );

/** API Rest */
// require_once( WPEOMTM_TASK_PATH . '/core/wpeo_entity_model.01.php' );
// require_once( WPEOMTM_TASK_PATH . '/model/wpeo_task_model.01.php' );
// require_once( WPEOMTM_TASK_PATH . '/core/wpeo_response_controller.01.php' );
// require_once( WPEOMTM_TASK_PATH . '/controller/wpeo_task_controller.01.php' );

require_once( WPEOMTM_TASK_PATH . '/model/wpeo_tasks_mod.php' );
require_once( WPEOMTM_TASK_PATH . '/model/wpeo_tasks_json_mod.php' );
require_once( WPEOMTM_TASK_PATH . '/model/wpeo_tasks_points_mod.php' );

new wpeo_tasks_points_ctr();
new wpeo_tasks_custom_ctr();
new wpeo_tasks_my_account_ctr();
new wpeo_tasks_ctr();
new wpeo_tasks_frontend_ctr();
?>
