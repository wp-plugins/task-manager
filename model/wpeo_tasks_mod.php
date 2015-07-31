<?php
/**
 * Main model file for task module
 *
 * @author Eoxia development team <dev@eoxia.com>
 * @version 1.0
 */

/**
 * Main model class for task module
 *
 * @author Eoxia development team <dev@eoxia.com>
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class wpeo_tasks_mod {
	public static $post_type = 'wpeo-tasks';
  	public static $task_state = "publish";
  	public static $task_state_archive = "archive";
  	public static $meta_key_estimated_time = "wpeo_task_estimated_time";
  	public static $meta_key_task_planing = "_wpeomtm_task_planing";
  	public static $meta_key_setting = "_wpeomtm_task_setting";
  	public static $meta_key_timeline = "wpeo_task_timeline";
  	
  	
  	/**
  	 * Add new task and add the default user in
  	 * @param int post_parent If the task got a parent post ( Default 0 : no post parent )
  	 * @return NULL|Ambigous <number, WP_Error>
  	 */
  	public static function add_new_task($post_parent = 0) {
  		$post_informations = array(
  			"post_title"	=> __("New task", "wpeotask-i18n"),
  			"post_type"		=> self::$post_type,
  			"post_status" 	=> self::$task_state,
  			"post_author" 	=> get_current_user_id(),
  		);
  		
  		if( $post_parent != 0 )
  			$post_informations['post_parent'] = $post_parent;
  	  	
  		/** Insert the post into the database */
  		$task = wp_insert_post($post_informations, true);
 	
 		if(is_wp_error($task)) {
 			echo"<pre>";print_r($task);echo"</pre>";
 			return null;
 		}
 		
 		
 		if( class_exists( 'wpeo_user_ctr' ) ) {
 			/** Insert default user */
	 		wpeo_user_mod::add_user_in( (int) $task, (int) get_current_user_id(), 'post' );
	 		
	 		/** You're the writer */
	 		wpeo_user_mod::add_user_write ( (int) $task, (int) get_current_user_id() );
 		}
  	
 		/** Default setting meta */
 		self::add_default_setting( ( int ) $task );
 		
 		/** Add to timeline */
 		$current_user = wp_get_current_user();
 		
 		$args = array(
 			'time' 		=> current_time( 'mysql' ),
 			'user'		=> $current_user->display_name,
 			'action'	=> __( 'Add a new task #' . $task, 'wpeotasks-i18n' ),
 		);
 		self::add_to_timeline( $task, $args );
 		
  		return $task;
  	}
  
  	/**
  	 * Get all task with WP_Query and the time in task
  	 * @return multitype:
  	 */
  	public static function get_tasks() {
  		$args = array(
      		"posts_per_page" => -1,
      		"post_type" => self::$post_type,
      		"post_status" => array( self::$task_state, self::$task_state_archive, ),
      		"orderby" => "ID",
      		"order" => "ASC",
  			"post_parent" => 0,
    	);

    	/** Get all task */
    	$array_posts = new WP_Query($args);
    	
    	if( !empty( $array_posts->posts ) ) {
    		foreach( $array_posts->posts as &$post ) {
    			$post->informations = self::get_informations_task( (int) $post->ID, $post->post_status );
    		}
    	}
    	
    	return $array_posts->posts;
 	}
 	
 	/**
 	 * Get single post by post_id
 	 * @param int $task_id
 	 * @return boolean|Ambigous <WP_Post, multitype:, NULL, unknown>
 	 */
 	public static function get_task( $task_id ) {
 		if( !is_int($task_id) ) return false;
 		
 		$post = get_post( $task_id );
 		
 		if( empty( $post ) )
 			return false;
 		
 		$post->informations = self::get_informations_task( (int) $task_id );
 		
 		return $post;
 	}
 	
 	public static function get_task_in ( $post_id ) {
 		$args = array(
 			'post_parent' => $post_id,
 			'post_type' => 'wpeo-tasks',
 		);
 		
 		$posts = get_posts( $args );
 		
 		if( !empty( $posts ) ) {
 			foreach( $posts as &$post ) {
 				$post->informations = self::get_informations_task( (int) $post->ID );
 			}
 		}
 		
 		return $posts;
 	}
 	
 	public static function get_informations_task( $task_id, $status = 'publish' ) { 	
 		$post = new stdClass();	
 		$post->time = self::get_time_in_task( $task_id );
 		
		/** Default value */
 		$post->user_can_write = 1;
 		$post->my_task = '';
 		
 		/** The module wpeo_user_mod exist ? */
 		if( class_exists( 'wpeo_user_mod' ) ) {
 			/** So let me know if i can write ! Anyway, i write if i want ! */
	 		$post->user_can_write = wpeo_user_mod::check_user_write_mode( $task_id, get_current_user_id() );
	
	 		/** So let me know who users affected to this task */
	 		$post->users = wpeo_user_mod::get_users_in( $task_id, 'task' );
	 		/** I need more information for the affected users */
	 		$post->users = wpeo_user_mod::get_users_info( $post->users );
	 		
	 		/** I'm affected to this ? */
	 		$post->my_task = ( wpeo_user_mod::get_user_in( $task_id, get_current_user_id() ) ) ? 'wpeo-my-task' : '';
 		}

 		/** The module tags exist ? Use it ! */
 		$post->class_tags = '';
 		if( class_exists( 'wpeo_tag_mod' ) ) {
 			$post->tags = wpeo_tag_mod::get_tag_in( $task_id );
 			
 			if( !empty($post->tags ) ) {
 				foreach( $post->tags as $tag ) {
 					$post->class_tags .= ' ' . $tag;
 				}
 			}
 				
 			if( self::$task_state_archive == $status ) {
 				$post->class_tags .= ' wpeo-task-archived';
 			}
 		}
 			
 		$post->count_point_completed = self::count_point( $task_id, true );
 			
 		$post->estimated_time = self::get_estimated_time( $task_id );
 		
 		$post->setting = self::get_default_setting( ( int ) $task_id );
 	
 		return $post;
 	}
  
 	/**
 	 * Get the title of the task by task id
 	 * @param int $task_id The task id
 	 * @return NULL|string
 	 */
 	public static function get_title_of_task($task_id) {
 		if( !is_int($task_id) ) wp_die( __( 'get_title_of_task : You need to use a integer value', 'wpeotasks-i18n' ) );
 		
 		$task = get_post($task_id);
 		
 		if( empty( $task ) ) return null;
 		
 		return $task->post_title;
 	}

 	/**
 	 * Get time in task by the task id
 	 * @param int $task_id The task id
 	 * @return Ambigous <number, string, void>
 	 */
 	public static function get_time_in_task( $task_id ) {
		if( !is_int($task_id) ) wp_die( __( 'get_time_in_task : You need to use a integer value', 'wpeotasks-i18n' ) );
 	
 		$points = array_merge( wpeo_tasks_points_mod::get_points( $task_id, true ), wpeo_tasks_points_mod::get_points( $task_id, false ) );
 		
 		$time = 0;
 		
 		if( !empty( $points ) ) {
 			foreach( $points as $point ) {
 				if( !empty( $point->informations->comment_minute ) ) 
 					$time += $point->informations->comment_minute;
 			}
 		}
 		
 		if( $time == 0 )
 			$time = '00';
 		else
 			$time = $time;
 		
 		return $time;
 	}

 	public static function count_point( $task_id, $completed = true ) {
 		$count = 0;
 		$points = wpeo_tasks_points_mod::get_points( $task_id, $completed );
 		
 		if( !empty($points) )
 			$count = count($points);
 		return $count;
 	}

	public static function add_estimated_time( $task_id, $time ) {
		$result = update_post_meta( $task_id, self::$meta_key_estimated_time, $time);
		
		$array_data = array(
			'estimate_start_date' => 0,
			'estimate_end_date' => 0,
			'planned_time' => $time,
			'real_start_date' => 0,
			'real_end_date' => 0,
			'elapsed_time' => 0,
		);
		
		update_post_meta( $task_id, self::$meta_key_task_planing, $array_data );
		
		return $result;
	}
	
	public static function get_estimated_time ( $task_id ) {
		$time = get_post_meta( $task_id, self::$meta_key_estimated_time, true );
		
		if( empty( $time ) ) $time = "00";
		
		return $time;
	}

	public static function add_default_setting( $task_id, $array_default_setting = null ) {
		if( 0 === $task_id )
			return false;
		
		if( empty( $array_default_setting ) ) {
			$array_default_setting = array(
				"user" => 1,
				"time" => 1,
			);
		}
		
		update_post_meta( $task_id, self::$meta_key_setting, $array_default_setting );
		
		return true;
	}

	public static function get_default_setting( $task_id ) {
		if( 0 === $task_id )
			return false;
		
		$meta = get_post_meta( $task_id, self::$meta_key_setting, true );
		
		/** Cannot be empty */
		if( empty( $meta ) ) {
			$meta['user'] = 1;
			$meta['time'] = 1;
		}
		
		return $meta;
	}

	public static function add_to_timeline ( $task_id, $args ) {
		$post_meta = self::get_timeline( $task_id );
		
		if( is_array( $post_meta ) )
			$post_meta[] = $args;
		else
			$post_meta[0] = $args;
		
		update_post_meta( $task_id, self::$meta_key_timeline, $post_meta );
	}
	
	public static function get_timeline( $task_id = 0 ) {
		/** One task */
		if( 0 !== $task_id )
			$post_meta = get_post_meta( $task_id, self::$meta_key_timeline, true );
		else {
			/** All task */
			$post_meta = null;
		}
		
		return $post_meta;
	}
}
