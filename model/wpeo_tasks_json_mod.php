<?php

class wpeo_tasks_json_mod {
	private $state = 'success';
	private $version = '0.1';
	private $code = 0;
	private $message = 'Success message';
	private $checksum = '';
	private $count = 0;
	private $object_id = 0;
	private $data = array();
	
	public function __construct( $data = "" ) {
		if( ! empty ( $data ) ) {
			if( ! empty ( $data['state'] ) ) $this->state = $data['state'];
			if( ! empty ( $data['version'] ) ) $this->version = $data['version'];
			if( ! empty ( $data['code'] ) ) $this->code = $data['code'];
			if( ! empty ( $data['message'] ) ) $this->message = $data['message'];
			if( ! empty ( $data['data'] ) ) $this->data = $data['data'];
		}
	}
	
	public function output_json() {
		header('Content-Type: application/json');
		return json_encode($this->get_json_data());
	}
	
	public function output_html() {
		$res_html = __( 'Nothing to display', 'wpeotasks-i18n' );
		
		if( !empty( $this->data ) && !empty( $this->data['html'] ) ) {
			$res_html = "";
			if( 1 == count( $this->data['html'] ) )
				$res_html = $this->data['html'];
			else {
				foreach( $this->data['html'] as $html ) {
					$res_html .= $html;
				}
			}
		}
		
		return $res_html;
	}
	
	public function setState( $state, $message = "" ) {
		$this->state = $state;
		
		if( ! empty ( $message ) )
			$this->setMessage( $message );
		
		
	}
	
	public function setVersion( $version ) {
		$this->version = $version;
	}
	
	public function setCode( $code ) {
		$this->code = $code;
	}
	
	public function setMessage( $message ) {
		$this->message = $message;
		
		wpeologs_ctr::log_datas_in_files('wpeo_project',
			array(
				'object_id' => $this->object_id,
				'message' => $this->message,
			), 
			$this->code
		);
	}
	
	public function setObjectId ( $object_id ) {
		$this->object_id = $object_id;
	}
	
	public function setData ( $array, $append = true ) {
		if($append) 
			$this->data = array_merge($this->data, $array);
		else
			$this->data = $array;
	}
	
	private function get_json_data() {
		$var = get_object_vars($this);
		foreach($var as &$value){
			if(is_object($value) && method_exists($value,'getJsonData')){
				$value = $value->getJsonData();
			}
		}
		return $var;
	}
	
	public function check_have_error() {
		return ($this->state == 'success') ? 0 : 1;
	}
};