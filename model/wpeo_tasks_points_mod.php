<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class wpeo_tasks_points_mod {
	public static $metakey_state = 'wpeo_points_state';
	public static $metakey_time = 'wpeo_points_time';
	public static $metakey_order = 'wpeo_points_order';
	public static $metakey_done = 'wpeo_points_done';
	public static $comment_approved = '-34070';
	
	/**
	 * Add point to task with the message
	 * @param int $task_id
	 * @param string $message
	 * @param int $can_empty The message of the point can be empty
	 * @return NULL|Ambigous <number, boolean>
	 */
	public static function add_point_to( $task_id, $message, $can_empty ) {
		if( empty( $message ) && !$can_empty ) return null;
		
		/** Check format variable */
		if( !is_int($task_id) ) wp_die( __( sprintf( 'The $task_id must be an integer, current value : %s', $task_id ), 'wpeotasks-i18n' ) );
		//if( !is_string($message) && empty($message) ) wp_die( __( sprintf( 'The $message must be an string, current value : %s', $message ), 'wpeotasks-i18n' ) );
		
		$current_user = wp_get_current_user();
		
		/** Prepare the comment */
		$data = array(
			'comment_post_ID' 		=> $task_id,
			'comment_author'		=> $current_user->user_login,
			'comment_author_email' 	=> $current_user->user_email,
			'comment_author_url' 	=> $current_user->user_url,
			'comment_content' 		=> !empty($message) ? $message : ' ',
			'comment_approved'		=> self::$comment_approved,
			'comment_author_IP'		=> $_SERVER[ 'REMOTE_ADDR' ],
			'comment_agent'			=> $_SERVER[ 'HTTP_USER_AGENT' ],
			'user_id'				=> $current_user->ID,
			'comment_date' 			=> current_time('mysql'),
		);
		
		$comment_id = wp_insert_comment($data);
		
		if( is_wp_error( $comment_id ) ) wp_die( "<pre>" . print_r($comment_id) . "</pre>" );
		
		/** Add to the order in the task */
		self::add_to_order( $task_id, $comment_id );

		/** Add the state meta */
		//update_comment_meta( $comment_id, self::$metakey_state, 'dashicons-controls-play' );
		
		return $comment_id;
	}
	
	/**
	 * Delete point by the point id and update order meta if exist
	 * @param int $task_id
	 * @param int $point_id
	 * @return void
	 */
	public static function delete_point( $task_id, $point_id ) {
		if( !is_int($task_id) ) wp_die( __( sprintf( 'The $task_id must be an integer, current value : %s', $task_id ), 'wpeotasks-i18n' ) );
		if( !is_int($point_id) ) wp_die( __( sprintf( 'The $point_id must be an integer, current value : %s', $point_id ), 'wpeotasks-i18n' ) );
		
		wp_delete_comment($point_id);
	}

	/**
	 * Get only one point by point id with comment and time
	 * @param int $task_id
	 * @param int $point_id
	 * @return Ambigous <object, multitype:, NULL, unknown, mixed>
	 */
	public static function get_point( $task_id, $point_id ) {
		$point = get_comment( $point_id );
		
		$point->comments = self::get_comments_in_points( (int) $task_id, (int) $point_id );
		
		$point->informations = self::get_point_informations( (int) $task_id, (int) $point_id );
		
		return $point;
	}
	
	/**
	 * Get points in task, get the time and if the point is done or not
	 * @param int $task_id
	 * @param int $limit default 0
	 * @return Ambigous <multitype:unknown , number, multitype:, string, NULL>
	 */
	public static function get_points( $task_id, $done = false, $limit = 0 ) {
		if( !is_int($task_id) ) wp_die( __( sprintf( 'The $task_id must be an integer, current value : %s', $task_id ), 'wpeotasks-i18n' ) );
		
		$args = array(
			'post_id' 	=> $task_id,
			'order'		=> 'ASC',
			'parent'	=> 0,
			'status' 	=> self::$comment_approved,
		);
		
		$comments = get_comments($args);
		
		/** Comments done and not done */
		$comments_done = array();
		$comments_not_done = array();
		
		/** Time and done and users */
		foreach($comments as &$comment) {
			$comment->informations = self::get_point_informations( $task_id, $comment->comment_ID );
			
			if( !empty( $comment->informations->done ) )
				$comments_done[] = $comment;
			else
				$comments_not_done[] = $comment;
			
			if( class_exists( 'wpeo_user_ctr' ) ) $comment->informations->users = wpeo_user_mod::get_users_in ( ( int ) $comment->comment_ID, "point" );
			
			/** No users found in meta */
			if( empty( $comment->informations->users ) ) {
				$comment->informations->users = array ( $comment->user_id );
			}
		}
		
		$comments = ($done) ? $comments_done : $comments_not_done;
				
		/** Check if exist the order meta */
		if(!$done) {
			$order_meta = get_post_meta( $task_id, wpeo_tasks_points_mod::$metakey_order, true );
			$tmp_comments = array();
			
			/** Sort by the order meta */
			if( !empty( $order_meta ) && !empty( $comments ) ) {
				foreach( $comments as $key => $comment ) {
					if( in_array( $comment->comment_ID, $order_meta ) ) {
						$tmp_comments[$comment->comment_ID] = $comment;
					}
				}
				
				unset($comments);
				$comments = array();
				
				/** Order */
				foreach( $order_meta as $key_order => $order_id_task ) {
					foreach( $tmp_comments as $id_task => $comment ) {
						if( $order_id_task == $id_task ) {
							$comments[] = $comment;
							break;
						}
					}
				}
			}
		}
		
		if( $limit != 0 ) {
			$comments = array_slice($comments, 0, $limit);
		}
		
		return $comments;
	}
	
	public static function get_point_informations( $task_id, $point_id ) {
		$comment = new stdClass();
		
		/** Time */
		$comment->comments = self::get_comments_in_points( (int) $task_id, (int) $point_id );
		$comment_time = 0;
		
		if( !empty( $comment->comments ) ) {
			foreach( $comment->comments as $comment_child ) {
				if( !empty( $comment_child->comment_minute ) )
					$comment_time += $comment_child->comment_minute;
			}
		}
		
		$comment->comment_minute = $comment_time;
		$comment->comment_time = ($comment_time != 0) ? $comment_time : '00';
		
		/** Done */
		$comment->done = self::get_done( (int) $point_id );
		
		return $comment;
	}
	
	/**
	 * Get comments in point by the task_id and post parent
	 * @param int $task_id
	 * @param int $comment_parent_id
	 * @param int $limit default 0
	 * @return Ambigous <number, multitype:, string, NULL>
	 */
	public static function get_comments_in_points( $task_id, $comment_parent_id, $limit = 0 ) {
		if( !is_int($task_id) ) wp_die( __( sprintf( 'The $task_id must be an integer, current value : %s', $task_id ), 'wpeotasks-i18n' ) );
		if( !is_int($comment_parent_id) ) wp_die( __( sprintf( 'The $comment_parent_id must be an integer, current value : %s', $comment_parent_id ), 'wpeotasks-i18n' ) );
		
		$args = array(
			'post_id' 	=> $task_id,
			'parent' 	=> $comment_parent_id,
			'number'	=> $limit,
			'order'		=> 'ASC',
			'status'	=> self::$comment_approved,
		);
		
		$comments = get_comments($args);
		
		if( !empty( $comments) ) {
			foreach( $comments as $comment ) {
				$comment->comment_minute = get_comment_meta( $comment->comment_ID, self::$metakey_time, true );
				$comment->comment_time = $comment->comment_minute;
			}
		}
				
		return $comments;
	}

	/**
	 * Update point and if point not exist, add it
	 * @param int $task_id
	 * @param int $point_id
	 * @param string $message
	 * @return NULL|Ambigous <number, false, boolean, mixed>
	 */
	public static function update( $task_id, $point_id, $message ) {		
		$comment = get_comment($point_id);
		
		if(empty($comment)) {
			return self::add_point_to_task( (int) $task_id, (string) $message );
		}
		
		$args = array(
			'comment_post_ID'		=> $task_id,
			'comment_ID' 			=> $point_id,
			'comment_content' 		=> $message,
		);
		
		$comment = wp_update_comment($args);
		
		return $comment;
	}

	/**
	 * Add comment if message not empty and add the meta time)
	 * @param int $task_id
	 * @param int $point_id
	 * @param string $message
	 * @param string $date
	 * @param int $minute
	 * @return NULL|Ambigous <number, boolean>
	 */
	public static function add_comment( $task_id, $point_id, $message, $date, $minute ) {
		if( empty( $message ) ) return null;
		
		/** Check format variable */
		if( !is_int($task_id) ) wp_die( __( sprintf( 'The task_id must be an integer, current value : %s', $task_id ), 'wpeotasks-i18n' ) );
		if( !is_int($point_id) ) wp_die( __( sprintf( 'The point_id must be an integer, current value : %s', $point_id ), 'wpeotasks-i18n' ) );
		if( !is_string($message) && empty($message) ) wp_die( __( sprintf( 'The $message must be an string, current value : %s', $message ), 'wpeotasks-i18n' ) );
		
		$current_user = wp_get_current_user();
		
		/** Prepare the comment */
		$data = array(
				'comment_post_ID' 		=> $task_id,
				'comment_parent'		=> $point_id,
				'comment_author'		=> $current_user->user_login,
				'comment_author_email' 	=> $current_user->user_email,
				'comment_author_url' 	=> $current_user->user_url,
				'comment_author_IP'		=> $_SERVER[ 'REMOTE_ADDR' ],
				'comment_agent'			=> $_SERVER[ 'HTTP_USER_AGENT' ],
				'comment_content' 		=> $message,
				'comment_approved'		=> self::$comment_approved,
				'user_id'				=> $current_user->ID,
				'comment_date' 			=> current_time('mysql'),
		);
		
		$comment_id = wp_insert_comment($data);
		
		if( is_wp_error( $comment_id ) ) wp_die( "<pre>" . print_r($comment_id) . "</pre>" );
		
		/** Add the time meta */
		update_comment_meta( $comment_id, self::$metakey_time, $minute );
		
		return $comment_id;
	}

	public static function delete_comment( $task_id, $point_id, $comment_id ) {
		wp_delete_comment( $comment_id );
		
		$response = delete_comment_meta( $comment_id, self::$metakey_time);
		
		return $response;
	}
	
	/**
	 * Set or unset is done for point
	 * @param int $point_id
	 * @param bool $done
	 * @return boolean
	 */
	public static function set_done( $task_id, $point_id, $done = true ) {
		if( !is_int( $point_id ) ) wp_die( __( sprintf( 'set_done : The point_id must be an integer, current value : %s', $point_id ), 'wpeotasks-i18n' ) );
	
		update_comment_meta( $point_id, self::$metakey_done, ($done == 'true') ? 'true' : '' );

		/** Remove from the post order meta */
		if( $done == 'true' ) {
			self::remove_to_order( $task_id, $point_id );
		}
		else {
			self::add_to_order( $task_id, $point_id );
		}
		
		return true;
	}
	
	/**
	 * Get if the point is done or not
	 * @param int $point_id The point id
	 * @return Ambigous <mixed, boolean, string, multitype:, unknown, string>
	 */
	public static function get_done ( $point_id ) {
		if( !is_int( $point_id ) ) wp_die( __( sprintf( 'set_done : The point_id must be an integer, current value : %s', $point_id ), 'wpeotasks-i18n' ) );
		
		$meta = get_comment_meta( $point_id, self::$metakey_done, true );
		
		return $meta;
	}

	/**
	 * Add a point to the order meta of the task
	 * @param int $task_id
	 * @param int $point_id
	 * @return void
	 */
	public static function add_to_order ( $task_id, $point_id ) {
		/** Add to the order in the task */
		$order_meta = get_post_meta( $task_id, wpeo_tasks_points_mod::$metakey_order, true );
		
		if( !empty( $order_meta ) ) {
			if( !in_array( $point_id, $order_meta ) ) {
				$order_meta[] = (int) $point_id;
				update_post_meta( $task_id, self::$metakey_order, $order_meta );
			}
		}
	}
	
	/**
	 * Remove a point to the order meta of the task
	 * @param int $task_id
	 * @param int $point_id
	 */
	public static function remove_to_order( $task_id, $point_id ) {
		/** Reorder all point in the task */
		$order_meta = get_post_meta( $task_id, wpeo_tasks_points_mod::$metakey_order, true );
		
		if( !empty( $order_meta ) ) {
			unset( $order_meta[ array_search( $point_id, $order_meta ) ] );
		
			update_post_meta( $task_id, self::$metakey_order, $order_meta );
		}
	}

}