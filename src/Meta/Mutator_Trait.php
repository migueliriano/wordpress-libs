<?php

namespace Lipe\Lib\Meta;

/**
 * Meta interaction support for Object Traits which use the Meta Repo.
 *
 * Also provided \ArrayAccess modifiers if the class which uses this
 * implements \ArrayAccess.
 *
 * If you don't want array keys to be able to modify data, then simply
 * do not give not implement \ArrayAccess on the class and they will
 * not work
 *
 * All methods will manipulate data in the database directly.
 *
 * @author Mat Lipe
 * @since  2.5.0
 *
 */
trait Mutator_Trait {
	/**
	 * Get the object id.
	 *
	 * @example post_id, term_id, user_id, comment_id, <custom>
	 *
	 * @return mixed
	 */
	abstract public function get_id();


	/**
	 * Get the type of meta that is stored for this object.
	 *
	 * @example 'post','user','comment','term',<custom>
	 *
	 * @return string
	 */
	abstract public function get_meta_type() : string;


	/**
	 * Get a value of this post's meta field
	 * using the meta repo to map the appropriate data type.
	 *
	 * @param string $key
	 * @param mixed  $default
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function get_meta( string $key, $default = null ) {
		$value = Repo::instance()->get_value( $this->get_id(), $key, $this->get_meta_type() );
		if ( null !== $default && empty( $value ) ) {
			return $default;
		}

		return $value;
	}


	/**
	 * Update a value of this post's meta field
	 * using the meta repo to map the appropriate data type.
	 *
	 * @param string $key
	 * @param mixed  $value
	 *
	 *
	 * @return void
	 */
	public function update_meta( string $key, $value ) : void {
		Repo::instance()->update_value( $this->get_id(), $key, $value, $this->get_meta_type() );
	}


	/**
	 * Delete the value of this post's meta field
	 * using the meta repo to map the appropriate data type.
	 *
	 * @param string $key
	 *
	 * @return void
	 */
	public function delete_meta( string $key ) : void {
		Repo::instance()->delete_value( $this->get_id(), $key, $this->get_meta_type() );
	}


	public function offsetGet( $field_id ) {
		return $this->get_meta( $field_id );
	}


	public function offsetSet( $field_id, $value ) : void {
		$this->update_meta( $field_id, $value );
	}


	public function offsetUnset( $field_id ) : void {
		$this->delete_meta( $field_id );
	}


	public function offsetExists( $field_id ) : bool {
		return ! empty( $this->get_meta( $field_id ) );
	}


}
