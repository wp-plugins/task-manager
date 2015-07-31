<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class wpeo_user_ctr {
	public function __construct() {
		/** Give me my JS ! */
		add_action( 'admin_enqueue_scripts', array( &$this, 'callback_admin_enqueue_scripts' ), 1 );
		
		/**	For display the list of user where we want */
		add_shortcode( 'wpeouser', array( &$this, 'shortcode_display_user' ) );
		
		add_action( 'wp_ajax_wpeo-view-user', array( &$this, 'ajax_view_user' ) );
		add_action( 'wp_ajax_wpeo-update-user', array( &$this, 'ajax_update_user' ) );
	}
	
	public function callback_admin_enqueue_scripts() {
		/** My js */
		wp_enqueue_script( 'wpeo-user-js', WPEOMTM_USER_URL . '/asset/js/backend.js', array( "jquery" ), WPEOMTM_USER_VERSION );
		
		/** My css */
		wp_register_style( 'wpeo-user-css', WPEOMTM_USER_URL . '/asset/css/backend.css', '', WPEOMTM_USER_VERSION );
		wp_enqueue_style( 'wpeo-user-css' );
	}
	
	/**
	 * Display the list of user in post
	 * @param array $args
	 * @param $args['id'] Required
	 * @param $args['use_dashicons'] True or false
	 * @param $args['dashicons'] default : dashicons-plus
	 * @param $args['callback_js'] default : callback_user ( This callback is called when the action wpeo-update-users is done )
	 * @param $args['type'] default: post ( Where the post meta is saving in post, comment or whatever ? )
	 * @param $args['user_role'] default: administrator 
	 * @example [wpeouser id="10" dashicons="dashicons-setting" callback_js="my_js_callback" ]
	 * @return string ( Template : backend/display-user )
	 */
	public function shortcode_display_user( $args ) {
		if( empty( $args['id'] ) )
			return __( 'The value post_id is required.', 'wpeouser-i18n' );
		
		/** Special use dashicons */
		$use_dashicons = '';
		if( empty( $args[ 'use_dashicons' ] ) ) $use_dashicons = 'true';
		
		/** Variables required */
		$id 			= $args['id'];
		$use_dashicons 	= ( $use_dashicons == "true" ) ? true : false;
		$dashicons 		= ( !empty( $args['add_dashicons'] ) ) ? $args['add_dashicons'] : 'dashicons-plus';
		$callback_js	= ( !empty( $args['callback_js'] ) ) ? $args['callback_js'] : 'callback_user';
		$type			= ( !empty( $args['type'] ) ) ? $args['type'] : 'post';
		$user_role		= ( !empty( $args['user_role'] ) ) ? $args['user_role'] : 'administrator';
		
		/** We need you user in this post for this template ! */
		$array_user_in = wpeo_user_mod::get_users_in( ( int ) $id , $type );
		$array_user_in = wpeo_user_mod::get_users_info( $array_user_in );
		
		/** All users */
		$array_user = get_users( array( 'role' => $user_role, ) );
		
		ob_start();
		require( wpeo_user_template_ctr::get_template_part( WPEOMTM_USER_DIR, WPEOMTM_USER_TEMPLATES_MAIN_DIR, 'backend', 'display', 'user' ) );
		return ob_get_clean();
	}
	
	/**
	 * AJAX - Get all users in this task, get all users in wordpress and display the users views backend/users.php
	 * @param int $_GET['id'] - The task id
	 * @param string $_GET['type'] - The type can be "task" or "point"
	 * @return JSON response
	 */
	public function ajax_view_user() {
		header('Content-Type: application/json');
	
		$response = new wpeo_user_response_json_mod();
	
// 		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'eo_nonce_view_user' ) ) {
// 			$response->setCode(22);
// 			$response->setState( 'error', __( 'You are not allowed to edit this post : Incorrect nonce', 'wpeouser-i18n' ) );
// 		}
	
		if( !$response->check_have_error() ) {
 			$array_user = get_users();
 			
 			$array_user_in_id = wpeo_user_mod::get_users_in( ( int ) $_POST['id'], $_POST['type'] );
			
			ob_start();
			require( wpeo_user_template_ctr::get_template_part( WPEOMTM_USER_DIR, WPEOMTM_USER_TEMPLATES_MAIN_DIR, 'backend', 'list', 'user' ) );
			$res_html = ob_get_clean();
				
			$response->setCode(0);
			$response->setState( 'success', __( sprintf( 'View user' ), 'wpeouser-i18n' ) );
			$response->setData( array ( 'html' => $res_html, ) );
		}
	
		wp_die( $response->output_html() );
	}
	
	/**
	 * AJAX - Insert all user in the $_POST['array_id'] checked by the form for this task
	 * Delete all user unchecked by the form for this task
	 * @param int $_POST['post_id'] The post id
	 * @param array $_POST['array_id'] The array of user id
	 * @param string $_POST['type'] The type can be post, comment or whatever
	 * @param string $_POST['_wpnonce'] The wordpress nonce
	 * @return JSON Response
	 */
	public function ajax_update_user() {
		header('Content-Type: application/json');
	
		$response = new wpeo_user_response_json_mod();
		$response->setObjectId( $_POST['post_id'] );
	
// 		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'eo_nonce_update_user' ) ) {
// 			$response->setCode(22);
// 			$response->setState( 'error', __( 'You are not allowed to edit this task : Incorrect nonce', 'wpeouser-i18n' ) );
// 		}
	
		if( !$response->check_have_error() ) {
			$id = $_POST['post_id'];
			
			$response->setCode(0);
				
			$string_user = "No user";
			if( !empty( $_POST['array_id'] ) ) {
				$string_user = "";
				foreach( $_POST['array_id'] as $user ) {
					$string_user .= $user . ', ';
				}
				
				$string_user = substr($string_user, 0, -2);
			}
			
			$response->setData( array('notification_message' => __( sprintf( 'Set user %s in post %d', $string_user, $id ), 'wpeouser-i18n' ) ) );
				
			if( !empty( $_POST['array_id'] ) ) {
				wpeo_user_mod::update_user_in( ( int ) $id, $_POST['array_id'], $_POST['type'] );
				$response->setState( 'success', __( sprintf( 'Set user %s in post %d', $string_user, $id ), 'wpeouser-i18n' ) );
				
			}
			else {
				wpeo_user_mod::clean_user_in( ( int )$id, $_POST['type'] );
			}
				
		}
	
		wp_die( $response->output_json() );
	}
}