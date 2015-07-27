<?php
/*
 * Plugin Name: Files management by Eoxia
 * Description: This plugins allows to associate files to any element / Vous allez pouvoir associer des fichiers aux différents éléments présent dans votre installation de wordpress
 * Version: 1.0
 * Author: Eoxia dev team <dev@eoxia.com>
 * Author URI: http://www.eoxia.com/
 * License: GPL2
 */

/**
 * Bootstrap file for plugin. Do main includes and create new instance for plugin components
 *
 * @author Eoxia <dev@eoxia.com>
 * @version 1.0
 */

DEFINE( 'WPEOMTM_FILES_VERSION', '1.0' );
DEFINE( 'WPEOMTM_FILES_DIR', basename(dirname(__FILE__)));
DEFINE( 'WPEOMTM_FILES_PATH', dirname( __FILE__ ) );
DEFINE( 'WPEOMTM_FILES_URL', str_replace( str_replace( "\\", "/", ABSPATH), site_url() . '/', str_replace( "\\", "/", WPEOMTM_FILES_PATH ) ) );

/**	Load plugin translation	*/
load_plugin_textdomain( 'wpeo-files-i18n', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

/**	Define the templates directories	*/
DEFINE( 'WPEOMTM_FILES_TEMPLATES_MAIN_DIR', WPEOMTM_FILES_PATH . '/templates/');

require_once( WPEOMTM_FILES_PATH . '/controller/wpeoFiles_ctr.php' );
require_once( WPEOMTM_FILES_PATH . '/model/wpeo_files_json_mod.php' );

/**	Instanciate task management*/
$wpeoTMFilesController = new wpeoTMFilesController();
?>