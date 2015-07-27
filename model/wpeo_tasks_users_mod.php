<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class wpeo_tasks_users_mod {
	public static $meta_key = 'wpeo_task_users';
	public static $metakey_user_write = 'wpeo_project_task_user_write';

	/**
	 * Add the user to the meta by the $type
	 * $type equals task : Add to the post_meta 
	 * $type equals point : Add to the comment_meta
	 * Check if the user_id is not already added
	 * @param int $id Can be task_id or point_id
	 * @param int $user_id
	 * @param string $type
	 * @return Ambigous <number, boolean, mixed>
	 */
	public static function add_user_in( $id, $user_id, $type ) {
		if( !is_int( $id ) || empty( $id ) ) wp_die( __( 'You need to use an integer', 'wpeotasks-i18n' ) );
		if( !is_int( $user_id ) || empty( $user_id ) ) wp_die( __( 'You need to use an integer', 'wpeotasks-i18n' ) );

		$users = self::get_users_in( $id, $type );

		/** Check if this user is not already in this task */
		if( in_array( $user_id, is_array( $users ) ? $users : array() ) ) wp_die( __( sprintf( 'This user (%d) is already assigned in this task (%d)', $user_id, $id ), 'wpeotasks-i18n' ) );

		/** Add my user */
		$users[] = ( int ) $user_id;

		if( $type == 'task' ) {
			$result = update_post_meta( $id, self::$meta_key, $users );
		}
		else {
			$result = update_comment_meta( $id, self::$meta_key, $users );
		}

		return $result;
	}
	
	/**
	 * Add the user in the post meta : $metakey_user_write
	 * @param int $task_id
	 * @param int $user_id
	 * @return boolean
	 */
	public static function add_user_write( $task_id, $user_id ) {
		if( !is_int( $task_id ) || empty( $task_id ) )
			return false;
		if( !is_int( $user_id ) || empty( $user_id ) )
			return false;
		
		$result = update_post_meta( $task_id, self::$metakey_user_write, $user_id );
		
		return $result;
	}
	
	/**
	 * Set post meta wpeo_task_user_write to null
	 * @param int $task_id
	 * @return boolean
	 */
	public static function remove_user_write( $task_id ) {
		if( !is_int( $task_id ) || empty( $task_id ) )
			return false;
		
		$result = update_post_meta( $task_id, self::$metakey_user_write, '' );
	}
	
	/**
	 * Check if the user can write in this task
	 * @param int $task_id
	 * @param int $user_id
	 * @return boolean
	 */
	public static function check_user_write_mode ( $task_id, $user_id ) {
		if( !is_int( $task_id ) || empty( $task_id ) )
			return false;
		if( !is_int( $user_id ) || empty( $user_id ) )
			return false;

		$result = get_post_meta ( $task_id, self::$metakey_user_write, $user_id, true );

		if( $user_id != $result )
			return 0;
		
		return 1;
	}

	/**
	 * Update users in meta by the $type
	 * $type equals task : Add to the post_meta 
	 * $type equals point : Add to the comment_meta
	 * @param int $id Can be task_id or point_id
	 * @param Array $array_user_id
	 * @param string $type
	 * @return Ambigous <number, boolean, mixed>
	 */
	public static function update_user_in( $id, $array_user_id, $type ) {
		if( !is_int( $id ) || empty( $id ) ) wp_die( __( 'You need to use an integer', 'wpeotasks-i18n' ) );
		
		if( 'task' == $type ) {
			$result = update_post_meta( $id, self::$meta_key, $array_user_id );
		}
		else {
			$result = update_comment_meta( $id, self::$meta_key, $array_user_id );
		}

		return $result;
	}

	/**
	 * Get user in $type
	 * $type equals task : Get user in task by the post_meta
	 * $type equals points : Get user in task by the comment_meta
	 * @param int $id Can be the task_id or point_id
	 * @param string $type
	 * @return NULL|Ambigous <mixed, boolean, string, multitype:, unknown, string>
	 */
	public static function get_users_in( $id, $type ) {
		if( !is_int( $id ) || empty( $id ) ) wp_die( __( 'You need to use an integer', 'wpeotasks-i18n' ) );

		if( 'task' == $type ) {
			$users = get_post_meta( $id, self::$meta_key, true );
		}
		else {
			$users = get_comment_meta( $id, self::$meta_key, true );
		}

		if( empty( $users ) ) return null;

		return $users;
	}
	
	/**
	 * Check if the user is in task
	 * @param int $task_id
	 * @param int $user_id
	 * @return boolean
	 */
	public static function get_user_in( $task_id, $user_id ) {
		$users = get_post_meta( $task_id, self::$meta_key, true );
		
		if( empty ($users ) ) return false;
		
		if( !in_array( $user_id, $users ) ) return false;
		
		return true;
	}
	
	/**
	 * Clean user meta by the $type
	 * $type equal task : Delete post meta
	 * $type equal point : Delete comment meta
	 * @param int $id Can be task_id or point_id
	 * @param string $type
	 * @return void
	 */
	public static function clean_user_in( $id, $type ) {
		if( !is_int( $id ) || empty( $id ) ) wp_die( __( 'You need to use an integer', 'wpeotasks-i18n' ) );

		if( 'task' == $type ) {
			delete_post_meta( $id, self::$meta_key );
		}
		else {
			delete_comment_meta( $id, self::$meta_key );
		}

	}

	public static function get_users_info( $array_id ) {
		$users = array();
		
		if( !empty( $array_id ) ) {
			foreach ( $array_id as $id ) {
				$users[$id] = get_userdata( $id );
			}
		}
		
		return $users;
	}
}