<?php

namespace Lipe\Lib\Schema;

use Lipe\Lib\Traits\Version;

/**
 * Interact with custom Database Tables
 *
 * Set the necessary class constants in the child class like so
 *
 * protected const NAME = 'personal'; //table name without prefix (prefix is set during construct)
 * protected const ID_FIELD = 'personal_id';
 * protected const DB_VERSION = 1;
 *
 * protected const COLUMNS = []
 */
abstract class Db {
	use Version;

	public const NAME = __CLASS__;
	protected const ID_FIELD = __CLASS__;

	/**
	 *
	 */
	public const DB_VERSION = 1;

	/**
	 * Db columns with corresponding data type
	 * Used to sanitize queries
	 *
	 * @see     May exclude the primary key from this list if it is auto increment
	 * @see     Date should be added to this list even if default current timestamp
	 *
	 * @example array(
	 * 'user_id'      => "%d",
	 * 'content_id'   => "%s",
	 * 'content_type' => "%s",
	 * 'date'         => "%d"
	 * );
	 *
	 *
	 * @var array
	 */
	public const COLUMNS = [];

	/**
	 * Holds the database name with prefix included.
	 *
	 * @internal
	 *
	 * @var string
	 */
	protected $table;


	/**
	 * Db constructor.
	 *
	 * @see Db::NAME
	 */
	public function __construct() {
		if ( __CLASS__ === static::NAME ) {
			_doing_it_wrong( __METHOD__, 'The Db class requires a `static::NAME`.', '2.23.0' );
			return;
		}

		global $wpdb;
		$this->table = $wpdb->prefix . static::NAME;

		$this->run_for_version( [ $this, 'run_updates' ], $this->get_db_version( true ) );
	}


	/**
	 * Get the name of the database table with prefix included.
	 *
	 * @return string
	 */
	public function get_table() : string {
		return $this->table;
	}


	/**
	 * Retrieve data from this db table.
	 *
	 * Automatically maps the results to a single value or row if the
	 * `$count` is set to 1.
	 *
	 * @param array<string>|string $columns    - Array or CSV of columns we want to return.
	 *                                         Pass '*' to return all columns.
	 *
	 * @param array|int [$id_or_wheres] - Row id or array or where column => value.
	 *                                         Adding a % within the value will turn the query
	 *                                         into a `LIKE` query
	 *
	 * @param bool       [$count] - Number of rows to return.
	 *                                         If set to 1 this will return a single value
	 *                                         or row depending on the number of columns
	 *                                         set in `$columns`.
	 *
	 * @param string               $order_by   - An ORDERBY column and direction.
	 *                                         Optionally pass `ASC` or `DESC` after the
	 *                                         column to specify direction.
	 *
	 * @return array<object>|object|string|int
	 *
	 */
	public function get( $columns, $id_or_wheres = null, $count = null, $order_by = null ) {
		global $wpdb;

		if ( \is_array( $columns ) ) {
			$columns = implode( ',', $columns );
		}

		if ( is_numeric( $id_or_wheres ) ) {
			$id_or_wheres = [ $this->get_id_field( true ) => $id_or_wheres ];
			$count        = 1;
		}

		$sql = "SELECT $columns FROM {$this->get_table()}";

		if ( null !== $id_or_wheres ) {
			$wheres        = [];
			$values        = [];
			$where_formats = $this->get_formats( $id_or_wheres );
			foreach ( $id_or_wheres as $column => $value ) {
				if ( false !== strpos( $value, '%' ) ) {
					$wheres[ $column ] = "`$column` LIKE " . array_shift( $where_formats );
				} else {
					$wheres[ $column ] = "`$column` = " . array_shift( $where_formats );
				}
				$values[] = $value;
			}
			$where = ' WHERE ' . implode( ' AND ', $wheres );
			//phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
			$sql   .= $wpdb->prepare( $where, $values );
		}

		if ( null !== $order_by ) {
			$sql .= " ORDER BY $order_by";
		}

		if ( null !== $count ) {
			$sql .= " LIMIT $count";
		}


		if ( '*' === $columns || \substr_count( $columns, ',' ) > 1 ) {
			if ( 1 === $count ) {
				return $wpdb->get_row( $sql );
			}

			return $wpdb->get_results( $sql );

		}
		if ( 1 === $count ) {
			return $wpdb->get_var( $sql );
		}

		return $wpdb->get_col( $sql );
		//phpcs:enable WordPress.DB.PreparedSQL.NotPrepared

	}


	/**
	 * Get a row by it's id.
	 *
	 * @param int $id - Primary key value.
	 *
	 * @since 2.23.2
	 *
	 * @return object|null
	 */
	public function get_by_id( int $id ) {
		return $this->get( '*', $id, 1 );
	}


	/**
	 * Add a row to the table
	 *
	 * @param array $columns
	 *
	 * @see Db::COLUMNS
	 *
	 * @return int|bool - insert id on success or false
	 */
	public function add( array $columns ) {
		global $wpdb;

		$columns = $this->sort_columns( $columns );

		if ( $wpdb->insert( $this->get_table(), $columns, $this->get_columns( true ) ) ) {
			return $wpdb->insert_id;
		}

		return false;
	}

	/**
	 * Delete a row from the database
	 *
	 * @param int|array $id_or_wheres - row id or array or column => values to use as where
	 * @since 1.6.1
	 *
	 * @return int|false
	 */
	public function delete( $id_or_wheres ) {
		global $wpdb;

		if ( is_numeric( $id_or_wheres ) ) {
			$id_or_wheres = [ $this->get_id_field( true ) => $id_or_wheres ];
		}

		$formats = $this->get_formats( $id_or_wheres );

		return $wpdb->delete( $this->get_table(), $id_or_wheres, $formats );

	}


	/**
	 *
	 * @param int|array $id_or_wheres - row id or array or column => values to use as where
	 * @param array     $columns      - data to change
	 *
	 * @see Db::COLUMNS
	 *
	 * @return int|bool - number of rows updated or false on error
	 */
	public function update( $id_or_wheres, array $columns ) {
		global $wpdb;

		if ( is_numeric( $id_or_wheres ) ) {
			$id_or_wheres = [ $this->get_id_field( true ) => $id_or_wheres ];
		}

		$column_formats = $this->get_formats( $columns );

		$formats = $this->get_formats( $id_or_wheres );

		return $wpdb->update( $this->get_table(), $columns, $id_or_wheres, $column_formats, $formats );

	}


	/**
	 * Get the sprintf style formats matching an array of columns
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	protected function get_formats( array $columns ) : array {
		$formats = [];
		foreach ( $columns as $column => $value ) {
			if ( $column === $this->get_id_field( true ) ) {
				$formats[] = '%d';
			} elseif ( ! empty( $this->get_columns( true )[ $column ] ) ) {
				$formats[] = $this->get_columns( true )[ $column ];
			} else {
				$formats[] = '%s';
			}
		}

		return $formats;
	}


	/**
	 * Sorts columns to match $this->columns for use with query sanitization
	 *
	 * @param array $columns
	 *
	 * @uses Db::COLUMNS
	 *
	 * @return array
	 */
	protected function sort_columns( array $columns ) : array {
		$clean = [];

		foreach ( $this->get_columns( true ) as $column => $type ) {
			if ( array_key_exists( $column, $columns ) ) {
				$clean[ $column ] = $columns[ $column ];
				//because we usually let mysql handle default dates
			} elseif ( 'date' !== $column ) {
				$clean[ $column ] = null;
			}
		}

		return $clean;
	}


	/**
	 * Deprecated in favor of using the constant directly.
	 *
	 * @deprecated
	 */
	public function get_id_field( $internal = false ) : string {
		if ( ! $internal ) {
			_deprecated_function( __METHOD__, '2.24.0', 'static::ID_FIELD' );
		}

		if ( __CLASS__ === static::ID_FIELD ) {
			_deprecated_argument( 'id_field', '2.23.0', 'Using a variable for id field is deprecated. Use the `static::ID_FIELD)` const.' );

			return isset( $this->id_field ) ? $this->id_field : '';
		}

		return static::ID_FIELD;
	}


	/**
	 * Deprecated in favor of using the constant directly.
	 *
	 * @deprecated
	 */
	public function get_columns( $internal = false ) : array {
		if ( ! $internal ) {
			_deprecated_function( __METHOD__, '2.24.0', 'static::COLUMNS' );
		}
		if ( empty( static::COLUMNS ) ) {
			_deprecated_argument( 'columns', '2.23.0', 'Using a variable for columns is deprecated. Use the `static::COLUMNS` const.' );
			return isset( $this->columns ) ? $this->columns : [];
		}
		return static::COLUMNS;
	}


	/**
	 * Deprecated in favor of using the constant directly.
	 *
	 * @deprecated
	 */
	protected function get_db_version( $internal = false ) : string {
		if ( ! $internal ) {
			_deprecated_function( __METHOD__, '2.24.0', 'static::DB_VERSION' );
		}

		if ( empty( static::DB_VERSION ) ) {
			_deprecated_argument( 'db_version', '2.23.0', 'Using a variable for db version is deprecated. Use the `static::DB_VERSION` const.' );
			return isset( $this->db_version ) ? $this->db_version : '1';
		}

		return static::DB_VERSION;
	}


	/**
	 * Run specified updates based on db version and update the option to match.
	 *
	 * @return void
	 */
	protected function run_updates() : void {
		$this->create_table();
		if ( method_exists( $this, 'update_table' ) ) {
			$this->update_table();
		}
	}


	/** Table creation *************************** */

	/**
	 *
	 * Create the custom db table
	 *
	 * @example
	 * require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	 *
	 * $sql = "CREATE TABLE IF NOT EXISTS " . $this->table . " (
	 * uid bigint(50) NOT NULL AUTO_INCREMENT,
	 * user_id bigint(20) NOT NULL,
	 * content_id varchar(100) NOT NULL,
	 * content_type varchar(100) NOT NULL,
	 * date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,,
	 * PRIMARY KEY  (uid),
	 * KEY user_id (user_id),
	 * KEY content_id (content_id),
	 * KEY content_type (content_type)
	 * );";
	 *
	 * dbDelta( $sql );
	 *
	 * @notice dbDelta expects the "CREATE" etc. to be capitalized
	 *         and will not run if not capitalized.
	 *
	 * @return void
	 */
	abstract protected function create_table() : void;

}
