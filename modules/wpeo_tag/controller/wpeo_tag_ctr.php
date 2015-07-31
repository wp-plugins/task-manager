<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class wpeo_tag_ctr {
	public function __construct() {
		add_action( 'init', array( &$this, 'callback_admin_init' ) );
		add_action( 'admin_init', array( &$this, 'callback_admin_init' ) );
		
		/**	For display the list of user where we want */
		add_shortcode( 'wpeotags', array( &$this, 'shortcode_display_tags' ) );
		
		/** Give me my JS ! */
		add_action( 'admin_enqueue_scripts', array( &$this, 'callback_admin_enqueue_scripts' ), 1 );
		
		/** Ajax view tag */
		add_action( 'wp_ajax_wpeo-view-tag', array( &$this, 'ajax_view_tag' ) );
		
		add_action( 'wp_ajax_wpeo-update-tag', array( &$this, 'ajax_update_tag' ) );
	}
	
	public function callback_admin_init( ) {
		register_taxonomy( wpeo_tag_mod::$taxonomy, 'wpeo_tags', array() );
	}
	
	public function shortcode_display_tags( $args ) {
		if( empty( $args['id'] ) )
			return __( 'The value post_id is required.', 'wpeotags-i18n' );
		
		$id 			= $args['id'];
		$callback_js 	= !empty( $args['callback_js'] ) ? $args['callback_js'] : '';
		$dashicons 		= ( !empty( $args['add_dashicons'] ) ) ? $args['add_dashicons'] : 'dashicons-plus';
		$array_tag_in 	= wpeo_tag_mod::get_tag_in( $id );
		
		require( wpeo_tag_template_ctr::get_template_part( WPEOMTM_TAG_DIR, WPEOMTM_TAG_TEMPLATES_MAIN_DIR, 'backend', 'display', 'tag' ) );
	}
	
	public function callback_admin_enqueue_scripts() {
		/** My js */
		wp_enqueue_script( 'wpeo-tags-js', WPEOMTM_TAG_URL . '/asset/js/backend.js', array( "jquery", "jquery-ui-sortable" ), WPEOMTM_TAG_VERSION );
		
		/** My css */
		wp_register_style( 'wpeo-tags-css', WPEOMTM_TAG_URL . '/asset/css/backend.css', '', WPEOMTM_TAG_VERSION );
		wp_enqueue_style( 'wpeo-tags-css' );
	}

	public function ajax_view_tag() {
		header('Content-Type: application/json');
		
		$response = new wpeo_tag_response_json_mod();
		$response->setObjectId( $_POST['id'] );
		
// 		if ( !wp_verify_nonce( $_GET['_wpnonce'], 'wp_nonce_view_tag' ) ) {
// 			$response->setCode(22);
// 			$response->setState( 'error', __( 'You are not allowed to edit this task : Incorrect nonce', 'wpeotasks-i18n' ) );
// 		}

		
		if( !$response->check_have_error() ) {
			$array_tag_in = wpeo_tag_mod::get_tag_in( (int) $_POST['id'] );
			$array_tag = wpeo_tag_mod::get_tag();
				
			ob_start();
			require( wpeo_tag_template_ctr::get_template_part( WPEOMTM_TAG_DIR, WPEOMTM_TAG_TEMPLATES_MAIN_DIR, 'backend', 'list', 'tag' ) );
			$res_html = ob_get_clean();
				
			$response->setCode(0);
			$response->setState( 'success', __( sprintf( 'View tag' ), 'wpeotag-i18n' ) );
			$response->setData( array ( 'html' => $res_html, ) );
		}
	
		wp_die( $response->output_html() );
	}
	
	/**
	 * Create tag with the name_tag and add it to the task_id with add_tag_in
	 * @param int $id - The post ID
	 * @param string $name_tag - The name tag
	 * @return void
	 */
	public function create_tag( $id, $tag_name ) {
		$slug = wpeo_tag_mod::create_tag ( $tag_name );	
		wpeo_tag_mod::add_tag_in( (int) $id, $slug );
		
		return $slug;
	}
	
	/**
	 * AJAX - Set all tags selected
	 * @param int $_POST['post_id'] - The post ID
	 * @param string $_POST['tag_name'] - The tag name
	 * @param array $_POST['array_slug'] - Array of all tags slug
	 * @return void
	 */
	public function ajax_update_tag() {
		header('Content-Type: application/json');
		
		$response = new wpeo_tasks_json_mod();
		$response->setObjectId( $_POST['post_id'] );
		
// 		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'eo_nonce_update_tag' ) ) {
// 			$response->setCode(22);
// 			$response->setState( 'error', __( 'You are not allowed to edit this task : Incorrect nonce', 'wpeotag-i18n' ) );
// 		}
		
		if ( !$response->check_have_error() ) {
			$array_slug_tag = !empty( $_POST['array_slug'] ) ? $_POST['array_slug'] : array();
			wpeo_tag_mod::update_tag_in( (int) $_POST['post_id'], $array_slug_tag );
				
			$string_tags_id = "No tags";
				
			if( !empty( $array_slug_tag ) ) {
				$string_tags_id = "";
				foreach( $array_slug_tag as $tag ) {
					$string_tags_id .= $tag . ', ';
				}
				
				$string_tags_id = substr($string_tags_id, 0, -2);
			}
			
// 			if( !empty( $_POST['tag_name'] ) ) $string_tags_id .= ', ' . $this->create_tag( $_POST['post_id'], $_POST['tag_name'] );
		
			$response->setCode( 0 );
			$response->setState( 'success', __( sprintf( 'The tag %s has been affected to the post %d', $string_tags_id, $_POST['post_id'] ), 'wpeotag-i18n' ) );
			$response->setData( array( 
				'tags' => str_replace( ', ', ' ', $string_tags_id ),
				'notification_message' =>  __( sprintf( 'The tag %s has been affected to the post %d', $string_tags_id, $_POST['post_id'] ), 'wpeotag-i18n' ),
			) );
		
		}
		
		wp_die( $response->output_json() );
	}
}