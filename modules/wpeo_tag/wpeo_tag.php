<?php
/**
 * Plugin Name: EO Tag
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
DEFINE( 'WPEOMTM_TAG_VERSION', 1.0 );
DEFINE( 'WPEOMTM_TAG_DIR', basename( dirname( __FILE__ ) ) );
DEFINE( 'WPEOMTM_TAG_PATH', str_replace( "\\", "/", plugin_dir_path( __FILE__ ) ) );
DEFINE( 'WPEOMTM_TAG_URL', str_replace( str_replace( "\\", "/", ABSPATH), site_url() . '/', WPEOMTM_TAG_PATH ) );

DEFINE( 'WPEOMTM_TAG_ASSETS_DIR',  WPEOMTM_TAG_PATH . '/asset/' );
DEFINE( 'WPEOMTM_TAG_TEMPLATES_MAIN_DIR', WPEOMTM_TAG_PATH . '/template/');

/**	Load plugin translation	*/
load_plugin_textdomain( 'wpeotag-i18n', false, dirname( plugin_basename( __FILE__ ) ) . '/language/' );

/** Require */
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

require_once( WPEOMTM_TAG_PATH . '/model/wpeo_tag_response_json_mod.php' );
require_once( WPEOMTM_TAG_PATH . '/model/wpeo_tag_mod.php' );
require_once( WPEOMTM_TAG_PATH . '/controller/wpeo_tag_ctr.php' );
require_once( WPEOMTM_TAG_PATH . '/controller/wpeo_tag_template_ctr.php' );

new wpeo_tag_ctr();
?>
