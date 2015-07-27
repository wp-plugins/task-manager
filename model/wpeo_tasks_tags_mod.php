<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class wpeo_tasks_tags_mod {
	public static $taxonomy = "wpeo_project-task-tags";

	/**
	 * Get all tags
	 * @return Ambigous <multitype:, WP_Error, mixed, string, NULL>
	 */
	public static function get_tags() {
		$terms = get_terms( self::$taxonomy, array( 'hide_empty' => 0, ) );
				
		return $terms;
	}
	
	/**
	 * Get tags in task
	 * @param int $task_id
	 * @return Ambigous <multitype:, WP_Error, mixed>
	 */
	public static function get_tags_in( $task_id ) {
		$terms = wp_get_object_terms( $task_id, self::$taxonomy, array("fields" => "slugs") );
		
		return $terms;
	}
	
	/**
	 * Create tag if name_tag is not empty and tag is not already declared
	 * @param string $name_tag
	 * @return string term slug
	 */
	public static function create_tag( $name_tag ) {
		if( !is_string( $name_tag ) && empty( $name_tag ) ) wp_die( __( sprintf( 'create_tag : The name_tag must be an string, current value : %s', $name_tag ), 'wpeotasks-i18n' ) );
		
		$term = get_term_by( 'name', $name_tag, self::$taxonomy );
		
		if( empty( $term ) ) {
			$term = wp_insert_term( $name_tag, self::$taxonomy );
			$term = get_term( $term['term_id'], self::$taxonomy );
		}
		
		return $term->slug;
	}

	/**
	 * Get current tags and new tags checked, remove all current tags not checked in the new tags 
	 * and add all new tags
	 * @param int $task_id
	 * @param array $array_tags_slug (string)
	 * @return void
	 */
	public static function update_tags_in( $task_id, $array_tags_slug ) {
		if( !is_int( $task_id ) || empty( $task_id ) ) wp_die( __( 'update_tag_in : You need to use an integer', 'wpeotasks-i18n' ) );
		
		$current_tags = self::get_tags_in ( $task_id );
		
		if( !empty($current_tags ) ){
			foreach( $current_tags as $tags ) {
				if( !in_array($tags, is_array( $array_tags_slug ) ? $array_tags_slug : array() ) ) {
					wp_remove_object_terms( $task_id, $tags, self::$taxonomy );
				}
			}
		}
		
		if( !empty( $array_tags_slug ) ) {
				/** Add new */
			foreach( $array_tags_slug as $slug ) {
				wp_set_object_terms( $task_id, $slug, self::$taxonomy, true );
			}
		}
	}
	
	/**
	 * Add tag in task by the slug
	 * @param int $task_id
	 * @param string $slug
	 * @return void
	 */
	public static function add_tag_in( $task_id, $slug ) {
		wp_set_object_terms( $task_id, $slug, self::$taxonomy, true );
	}

}