<?php

/**
 * @author Jimmy Latour
 * @version 0.1
 * @abstract
 */
abstract class wpeo_response_controller_01 {
	var $json_response = array(
		'status' 	=> 'success',
		'version' 	=> '0.1',
		'code' 		=> '0x00',
		'message' 	=> '',
		'checksum' 	=> '',
		'count'		=> 0,
		'data'		=> null,
	);
	/**
	 * Output the @param $model 
	 * @param wpeo_entity_model_01 $model
	 * @return string | null
	 */
	public function output_json( $model ) {
		$this->json_response['checksum'] = md5( json_encode( $model ) );
		$this->json_response['count'] = count( $model );
		$this->json_response['data'] = $model;
	
// 		if( empty( $model ) )
// 			return null;
		
		return $this->json_response;
	}
}