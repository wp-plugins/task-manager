<?php
/**
 * Plugin Name: EO Users
 * Description: 
 * Version: 1.0
 * Author: Eoxia <dev@eoxia.com>
 * Author URI: http://www.eoxia.com/
 * License: GPL2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Bootstrap file for plugin. Do main includes and create new instance for plugin components
 *
 * @author Eoxia <dev@eoxia.com>
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** Define */
DEFINE( 'WPEOMTM_USER_VERSION', 1.0 );
DEFINE( 'WPEOMTM_USER_DIR', basename( dirname( __FILE__ ) ) );
DEFINE( 'WPEOMTM_USER_PATH', str_replace( "\\", "/", plugin_dir_path( __FILE__ ) ) );
DEFINE( 'WPEOMTM_USER_URL', str_replace( str_replace( "\\", "/", ABSPATH), site_url() . '/', WPEOMTM_USER_PATH ) );

DEFINE( 'WPEOMTM_USER_ASSETS_DIR',  WPEOMTM_USER_PATH . '/asset/' );
DEFINE( 'WPEOMTM_USER_TEMPLATES_MAIN_DIR', WPEOMTM_USER_PATH . '/template/');

/**	Load plugin translation	*/
load_plugin_textdomain( 'wpeouser-i18n', false, dirname( plugin_basename( __FILE__ ) ) . '/language/' );

/** Require */
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

require_once( WPEOMTM_USER_PATH . '/model/wpeo_user_response_json_mod.php' );
require_once( WPEOMTM_USER_PATH . '/model/wpeo_user_mod.php' );
require_once( WPEOMTM_USER_PATH . '/controller/wpeo_user_ctr.php' );
require_once( WPEOMTM_USER_PATH . '/controller/wpeo_user_template_ctr.php' );

new wpeo_user_ctr();
?>
