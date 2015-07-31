<?php

/**
 * @author Jimmy Latour
 * @version 0.1
 * @abstract
 */
abstract class wpeo_entity_model_01 {
	public function __construct( $object ) {
		if ( is_object( $object ) && $this->object_type == get_class( $object ) ) {
			foreach( $this->model as $field_name => $field_def ) {
				$this->$field_name = !empty( $field_def[ 'default' ]) ? $field_def[ 'default' ] : null;
				if( isset( $object->$field_def['bdd'] ) && $this->check_type ( $field_def['type'], $object->$field_def['bdd'] ) ) {
					$this->$field_name = $object->$field_def['bdd'];
				}
			}
		}
		else {
			foreach( $this->model as $field_name => $field_def ) {
				$this->$field_name = !empty( $field_def[ 'default' ]) ? $field_def[ 'default' ] : null;
				if( array_key_exists( $field_name, $object ) && $this->check_type ( $field_def['type'], $object[ $field_name ] ) ) {
					$this->$field_name = $object[ $field_name ];
				}
			}
		}
	}	
	
	public function update(){
		$this->id = wp_update_post( $this->do_wp_object() );
	}
	
	public function delete(){
		return wp_delete_post( $this->id );
	}
	
	protected function check_type( $type, $value ) {
		switch( $type ) {
			case 'integer':
				return is_int( $value );
				break;
			case 'string':
				return is_string( $value ); 
				break;
			case 'bool':
				return is_bool( $value );
				break;
			case 'float':
				return is_float( $value );
				break;
			default:
				return false; 
				break;
		}

		return false;
	}

}