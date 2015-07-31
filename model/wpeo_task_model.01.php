<?php

/**
 * @author Jimmy Latour
 * @version 0.1
 */
class wpeo_task_model_01 extends wpeo_entity_model_01 {

	// TABLE_NAME
	protected $object_type = "WP_Post";
	private $table = 'posts';
	protected $model = array(
		'id' => array(
			'type'		=> 'integer',
			'bdd'		=> 'ID',
		),
		'name' => array(
			'type'	=> 'string',
			'bdd'	=> 'post_title',
		),
		'description' => array(
			'type'	=> 'string',
			'bdd'	=> 'post_content',
		),
		'status' => array(
			'type'	=> 'string',
			'bdd'	=> 'post_status',
			'default' => 'publish',
		),
		'type' => array(
			'type'	=> 'string',
			'bdd'	=> 'post_type',
			'default' => 'wpeo-tasks',
		),
	);
	
	/**
	 * JOR of object ( version )
	 * @var array ( decimal, )
	*/
	var $JOR = array(
		"version" => 0.1,
	);

	public function __construct( $object = array() ) {
		parent::__construct( $object );

// 		$this->element['user'] = array( $this->fill_user( $object->ID ) );
// 		$this->element['planing'] = array( $this->fill_planing( $object->ID ) );
// 		$this->element['planing'] = wp_parse_args( $this->fill_planing( $object->ID ),
// 			array(
// 				'estimate_start_date' => '',
// 				'estimate_end_date' => '',
// 				'planed_time' => '',
// 				'real_start_date' => '',
// 				'real_end_date' => '',
// 				'elapsed_time' => '',
// 			)
// 		);

	}


	public function do_wp_object() {
		$post = array();

		foreach( $this->model as $field_name => $field_def ) {
			$post[ $field_def[ 'bdd' ] ] = $this->$field_name;
		}

		return $post;
	}

	public function fill_user( $id ) {
		$post_meta = get_post_meta( $id, class_exists( 'wpeo_user_ctr' ) && !wpeo_user_mod::$meta_key, true );

		return $post_meta;
	}

	public function fill_planing( $id ) {
		$post_meta = get_post_meta( $id, wpeo_tasks_mod::$meta_key_task_planing, true );

		return $post_meta;
	}

}
