<?php
/**
 * Main controller file for files module
 *
 * @author Eoxia development team <dev@eoxia.com>
 * @version 1.0
 */

/**
 * Main controller class for files module
 *
 * @author Eoxia development team <dev@eoxia.com>
 * @version 1.0
 */
class wpeoTMFilesController {

	/**
	 * CORE - Instanciate task management
	 */
	function __construct() {
		/**	Call style for administration	*/
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_css' ) );

		/**	Include the different javascript	*/
		add_action( 'admin_enqueue_scripts', array(&$this, 'admin_js') );
		add_action( 'admin_print_scripts', array(&$this, 'admin_printed_js') );

		/**	SHORTCODE listener	*/
		add_shortcode( 'wpeofiles', array( &$this, 'shortcode_display_list' ) );

		/**	AJAX listener	*/
		add_action( 'wp_ajax_wpeofiles-associate-files', array( &$this, 'ajax_associate_file_to_element' ) );
		add_action( 'wp_ajax_wpeofiles-delete-association-file', array( &$this, 'ajax_dissociate_file_to_element' ) );
		add_action( 'wp_ajax_wpeo-template-file', array( &$this, 'ajax_template_file' ) );
		
		add_filter( 'wpeo_tasks_file_filter', array( $this, 'callback_filter' ), 10, 2 );
	}

	/**
	 * WORDPRESS HOOK - ADMIN STYLES - Include stylesheets
	 */
	function admin_css() {
		wp_register_style( 'wpeofiles-styles', WPEOMTM_FILES_URL . '/assets/css/backend.css', '', WPEOMTM_FILES_VERSION );
		wp_enqueue_style( 'wpeofiles-styles' );
	}

	/**
	 * WORDPRESS HOOK - ADMIN JAVASCRIPTS - Load the different javascript librairies
	 */
	function admin_js() {
		wp_enqueue_script( 'wpeofiles-scripts', WPEOMTM_FILES_URL . '/assets/js/backend.js', '', WPEOMTM_FILES_VERSION );
	}

	/**
	 * WORDPRESS HOOK - ADMIN INLINE JAVASCRIPTS - Print javascript (dynamic js content) instruction into html code head.
	 */
	function admin_printed_js() {
		wp_enqueue_media();
		require_once( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_FILES_DIR, WPEOMTM_FILES_TEMPLATES_MAIN_DIR, "backend", "header.js" ) );
	}


	/**
	 * SHORTCODE - Display associated files list for a given element from a shortcode
	 *
	 * @param array $args The list of arguments passed through shortcode call
	 */
	function shortcode_display_list( $params ) {
		global $wpdb;

		/**	Get the upload directory defined into wordpress current installation	*/
		$upload_dir = wp_upload_dir();

		/**	Check if there is a list of element given or if we have to get the list of associated medias from database	*/
		if ( empty( $params[ 'file_list_association' ] ) ) {
			$files = get_post_meta( $params[ 'id' ], '_wpeofiles_associated', true );
		}
		else {
			$associated_files = $params[ 'file_list_association' ];
			$files = $this->dedupe_associated_files( $params[ 'id' ], $associated_files );
		}
		
		ob_start();
		require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_FILES_DIR, WPEOMTM_FILES_TEMPLATES_MAIN_DIR, "backend", "files", ( !empty( $params ) && !empty( $params[ 'output_type' ] ) ? $params[ 'output_type' ] : 'list' ) ) );
		$file_list_display = ob_get_contents();
		ob_end_clean();

		echo apply_filters( "wpeo-file-list-display", $file_list_display, $params );
	}

	/**
	 * Get associated attachment and attachment sended on given element in order to get an unique array without double attachment media
	 *
	 * @param integer $elementID The element identifier to check association for
	 * @param array $file_list The associated attachment
	 *
	 * @return array The final list of medias attached to given element
	 */
	function dedupe_associated_files( $elementID, $file_list ) {
		global $wpdb;
		$associated_files = array();

		/**	Get all medias attached to given element	*/
		$query = $wpdb->prepare( "SELECT GROUP_CONCAT( ID ) AS attachment_list FROM {$wpdb->posts} WHERE post_type = %s AND post_parent = %d AND post_status = %s GROUP BY post_parent", 'attachment', $elementID, 'inherit' );
		$children_files = $wpdb->get_var( $query );
		if ( !empty( $children_files ) ) {
			$children_files = explode( ",", $children_files );
		}
		else {
			$children_files = array();
		}

		/**	Build the final file list by removing duplicate entries from attached medias and associated medias	*/
		$associated_files = array_merge( array_diff( $children_files, (array)$file_list ), (array)$file_list);

		return $associated_files;
	}

	/**
	 * GETTER - Get a filename corresponding to an attachment type
	 *
	 * @param integer $post_id The attachment identifier
	 *
	 * @return string The filename corresponding to the given attachement type
	 */
	function get_icon_for_attachment( $post_id, $type = 'dashicons' ) {
		$base = includes_url( 'images/media/' );
		$type = get_post_mime_type( $post_id );
		switch ($type) {
			case 'image/jpeg':
			case 'image/png':
			case 'image/gif':
				$icon_attachment = "dashicons-format-image";
				if ( 'image' == $type ) {
					$icon_attachment = $base . "default.png";
				}
			break;
			case 'video/mpeg':
			case 'video/mp4':
			case 'video/quicktime':
				$icon_attachment = "dashicons-media-video";
				if ( 'image' == $type ) {
					$icon_attachment = $base . "video.png";
				}
			break;
			case 'text/csv':
			case 'text/plain':
			case 'text/xml':
				$icon_attachment = "dashicons-media-text";
				if ( 'image' == $type ) {
					$icon_attachment = $base . "text.png";
				}
			break;
			default:
				$icon_attachment = "dashicons-media-default";
				if ( 'image' == $type ) {
					$icon_attachment = $base . "default.png";
				}
			break;
		}

		return $icon_attachment;
	}

	/**
	 * AJAX - Save file association to an element
	 */
	function ajax_associate_file_to_element() {
		/**	First get curent file association	*/
		$current_file_association = get_post_meta( $_POST[ 'element_id' ], '_wpeofiles_associated', true );

		/**	Create the new association file array	*/
		if( empty( $current_file_association ) ) $current_file_association = array();
		
		/** Add the new entry */
		if( !empty( $_POST['files_to_associate'] ) ) {
			foreach( $_POST['files_to_associate'] as $file_id )
				$current_file_association[] = (int) $file_id;
		}

		/**	Save the new association file	*/
		update_post_meta( $_POST[ 'element_id' ], '_wpeofiles_associated', $current_file_association );

		$this->ajax_template_file();
	}

	/**
	 * AJAX - Delete file association from an element
	 */
	function ajax_dissociate_file_to_element() {
		$response = array(
			'status' => false,
			'message' => __( "An error occured while dissociating file", "wpeo-files-i18n" ),
			"class" => "wpeomtm-msg-error",
			"parent_id" => ( !empty( $_POST ) && !empty( $_POST[ 'element_id' ] ) ? $_POST[ 'element_id' ] : 0),
		);

		if ( !empty( $_POST[ 'element_id' ] ) && !empty( $_POST[ 'file_id' ] ) ) {
			$current_file_association = get_post_meta( $_POST[ 'element_id' ], '_wpeofiles_associated', true );
			if ( !empty( $current_file_association ) && in_array( $_POST[ 'file_id' ], $current_file_association ) ) {
				$position_of_file = array_keys( $current_file_association, $_POST[ 'file_id' ] );
				unset( $current_file_association[ $position_of_file[0] ] );
				if ( !in_array( $_POST[ 'file_id' ], $current_file_association )) {
					update_post_meta( $_POST[ 'element_id' ], '_wpeofiles_associated', $current_file_association );
					$response[ 'status' ] = true;
					$response[ 'message' ] = __( "File have been dissociate succesfully", "wpeo-files-i18n" );
					$response[ "class" ] = "wpeomtm-msg-success";
				}
			}
		}
		else {
			$response[ 'message' ] = __( 'A required parameter is missing, please check your request.', 'wpeo-files-i18n' );
		}

		wp_die( json_encode( $response ) );
	}
	
	/**
	 * AJAX - Create the render of template for file and return him
	 * @param int $_POST['element_id'] The element id
	 * @param array $_POST['files_to_associate'] The id of post (files) to be render
	 * @return JSON Response
	 */
	public function ajax_template_file() {
		header('Content-Type: application/json');
		
		$response = new wpeo_files_json_mod();
		$response->setObjectId( $_POST['element_id'] );
		
		$post_name = "";
		
		$upload_dir = wp_upload_dir();
		
		ob_start();
		if( !empty( $_POST['files_to_associate'] ) ) {
			foreach( $_POST['files_to_associate'] as $file_id ) {
				$post = get_post( $file_id );
				
				$post_name .= $post->post_title . ', ';
				
				require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_FILES_DIR, WPEOMTM_FILES_TEMPLATES_MAIN_DIR, "backend", "list", "render" ) );
			}
		}
		
		$response->setCode(0);
		$response->setState( 'success', __( sprintf('Associate the file %s to the element : %d', $post_name, $_POST['element_id'] ), 'wpeo-files-i18n' ) );
		$response->setData( array ( 'template' => ob_get_clean(), 'notification_message' => __( sprintf('Associate the file %s to the element : %d', $post_name, $_POST['element_id'] ), 'wpeo-files-i18n') ) );
	
		wp_die( $response->output_json() );
	}

	public function callback_filter($filter, $task_id) {
		ob_start();
		require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_FILES_DIR, WPEOMTM_FILES_TEMPLATES_MAIN_DIR, "backend", "filter", "button" ) );		
		return ob_get_clean();
	}
}

?>