<?php

/**
 * @author Jimmy Latour
 * @version 0.1
 */
class wpeo_task_controller_01 extends wpeo_response_controller_01 {
	/**
	 * Add filter for callback the callback_register_route function
	 */
	public function __construct() {
		add_filter( 'json_endpoints', array( &$this, 'callback_register_route' ) );
	}

	public function callback_register_route( $array_route ) {
		/** Get */
		$array_route['/0.1/get/task'] = array(
			array( array( $this, 'get' ), WP_JSON_Server::READABLE | WP_JSON_Server::ACCEPT_JSON )
		);
		$array_route['/0.1/get/task/(?P<id>\d+)'] = array(
			array( array( $this, 'get' ), WP_JSON_Server::READABLE |  WP_JSON_Server::ACCEPT_JSON )
		);

		/** Post */
		$array_route['/0.1/post/task'] = array(
			array( array( $this, 'post' ), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
		);
		
		/** Delete */
		$array_route['/0.1/delete/task/(?P<id>\d+)'] = array(
				array( array( $this, 'delete' ), WP_JSON_Server::DELETABLE | WP_JSON_Server::ACCEPT_JSON ),
		);

		return $array_route;
	}

	public function get( $id = 0 ) {
		$model_array_task = array();

		if( empty( $id ) ) {
			/** Get array task */
			$array_task = get_posts( array ( "post_type" => wpeo_tasks_mod::$post_type, "posts_per_page" => -1, ) );

			if( !empty( $array_task ) ) {
				foreach( $array_task as $task ) {
					$model_array_task[] = new wpeo_task_model_01( $task );
				}
			}
		}
		else {
			/** Get the task by $id */
			$task = get_post( $id );

			if( !empty( $task ) ) {
				$model_array_task = new wpeo_task_model_01( $task );
			}
		}

		return $this->output_json( $model_array_task );
	}

	public function post( $data ) {
		$model = new wpeo_task_model_01( $data['data'] );
 		$model->update();

		return $this->output_json( $model );
	}
	
	public function delete( $id ) {
		$model = new wpeo_task_model_01();
		$model->id = $id;
		$model->delete();
		
		return $this->output_json( $model );
	}
}

new wpeo_task_controller_01();