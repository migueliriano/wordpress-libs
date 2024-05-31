<?php
//phpcs:disable WordPress.DB -- This whole class is custom DB handler.
declare( strict_types=1 );

namespace Lipe\Lib\Db;

/**
 * Custom database table interaction.
 *
 * @author Mat Lipe
 * @since  4.10.0
 *
 * @template COLUMNS of array<string, string|float|int|null>
 * @template FORMATS of array<string, "%d"|"%i"|"%f"|"%s">
 * // Partials must be passed in because \Partial<array> does not support templates.
 * @template PARTIALS of array<key-of<COLUMNS>, float|string|int|null>
 *
 * @phpstan-type MYSQL array<key-of<COLUMNS>, string>
 */
class Custom_Table {
	public const ORDER_ASC  = 'ASC';
	public const ORDER_DESC = 'DESC';

	/**
	 * Holds the database name with the prefix included.
	 *
	 * @var string
	 */
	protected readonly string $table;

	/**
	 * The last WHERE clause used in a query.
	 *
	 * @var string
	 */
	protected string $last_where = '';


	/**
	 * Set up the custom table for query interaction.
	 *
	 * @phpstan-param Table<FORMATS> $config
	 *
	 * @param Table                  $config - The table configuration.
	 */
	final protected function __construct(
		protected readonly Table $config
	) {
		global $wpdb;
		if ( \str_starts_with( $this->config->get_table(), $wpdb->prefix ) ) {
			$this->table = $this->config->get_table();
		} else {
			$this->table = $wpdb->prefix . $this->config->get_table();
		}
	}


	/**
	 * Get the name of the database table with the prefix included.
	 *
	 * @return string
	 */
	public function get_table(): string {
		return $this->table;
	}


	/**
	 * Get items from the database table.
	 *
	 * @phpstan-param PARTIALS             $where
	 * @phpstan-param key-of<COLUMNS>|null $order_by
	 * @phpstan-param self::ORDER_*        $order
	 *
	 * @param array                        $where    - Array of where column => value.
	 * @param ?int                         $count    - Number of rows to return.
	 * @param ?string                      $order_by - An ORDERBY column.
	 * @param string                       $order    - ORDERBY direction (ASC or DESC).
	 *
	 * @phpstan-return array<int, COLUMNS>
	 * @return array
	 */
	public function get( array $where = [], ?int $count = null, ?string $order_by = null, string $order = 'ASC' ): array {
		global $wpdb;
		$query = $this->get_select_query( [], $where, $count, $order_by, $order );
		$data = $wpdb->get_results( $query, 'ARRAY_A' );
		return \array_map( [ $this, 'format_values' ], $data );
	}


	/**
	 * Get a single item from the database table.
	 *
	 * @phpstan-param PARTIALS             $where
	 * @phpstan-param key-of<COLUMNS>|null $order_by
	 * @phpstan-param self::ORDER_*        $order
	 *
	 * @param array                        $where    - Array of where column => value.
	 * @param ?string                      $order_by - An ORDERBY column.
	 * @param string                       $order    - ORDERBY direction (ASC or DESC).
	 *
	 * @phpstan-return COLUMNS|null
	 * @return ?array
	 */
	public function get_one( array $where = [], ?string $order_by = null, string $order = 'ASC' ): ?array {
		global $wpdb;
		$query = $this->get_select_query( [], $where, 1, $order_by, $order );
		$data = $wpdb->get_row( $query, 'ARRAY_A' );
		if ( null !== $data ) {
			return $this->format_values( $data );
		}
		return null;
	}


	/**
	 * Get a paginated list of items.
	 *
	 * @phpstan-param PARTIALS             $where
	 * @phpstan-param key-of<COLUMNS>|null $order_by
	 * @phpstan-param self::ORDER_*        $order
	 *
	 * @param int                          $page     - The page number to retrieve.
	 * @param int                          $per_page - The number of items per a page.
	 * @param array                        $where    - Array of where column => value.
	 * @param ?string                      $order_by - An ORDERBY column.
	 * @param string                       $order    - ORDERBY direction (ASC or DESC).
	 *
	 * @phpstan-return array{
	 *     items: list<COLUMNS>,
	 *     total: int,
	 *     total_pages: int
	 * }
	 * @return array
	 */
	public function get_paginated( int $page, int $per_page, array $where = [], ?string $order_by = null, string $order = 'ASC' ): array {
		global $wpdb;
		$offset = null;
		if ( $page > 1 ) {
			$offset = ( $page - 1 ) * $per_page;
		}
		$query = $this->get_select_query( [], $where, $per_page, $order_by, $order, $offset );
		$items = $wpdb->get_results( $query, 'ARRAY_A' );
		$total = $wpdb->get_var( "SELECT COUNT(*) FROM `{$this->get_table()}` {$this->last_where}" );

		return [
			'items'       => \array_map( [ $this, 'format_values' ], $items ),
			'total'       => (int) $total,
			'total_pages' => (int) \ceil( $total / $per_page ),
		];
	}


	/**
	 * Get a single item by the ID.
	 *
	 * @param int $id - ID of the item to retrieve.
	 *
	 * @phpstan-return COLUMNS|null
	 * @return ?array
	 */
	public function get_by_id( int $id ): ?array {
		global $wpdb;
		$query = $this->get_select_query( [], [ $this->config->get_id_field() => $id ] );
		$data = $wpdb->get_row( $query, 'ARRAY_A' );
		if ( null !== $data ) {
			return $this->format_values( $data );
		}
		return null;
	}


	/**
	 * Add a row to the table
	 *
	 * @phpstan-param PARTIALS $columns
	 *
	 * @param array            $columns - column => value pairs to insert.
	 *
	 * @return ?int - insert id on success or null on failure.
	 */
	public function add( array $columns ): ?int {
		global $wpdb;
		unset( $columns[ $this->config->get_id_field() ] );
		$columns = $this->sort_columns( $columns );

		if ( false !== $wpdb->insert( $this->get_table(), $columns, $this->config->get_column_formats() ) ) {
			return $wpdb->insert_id;
		}
		return null;
	}


	/**
	 * Delete an item from the database by ID.
	 *
	 * @param int $id - The id of the item to delete.
	 */
	public function delete( int $id ): bool {
		global $wpdb;
		$deleted = $wpdb->delete( $this->get_table(), [ $this->config->get_id_field() => $id ], [ '%d' ] );
		return 1 === $deleted;
	}


	/**
	 * Delete items from the database based on the provided criteria.
	 *
	 * @phpstan-param PARTIALS $where
	 *
	 * @param array            $where - Array of where column => value.
	 *
	 * @return false|int - Number of rows deleted or false on failure.
	 */
	public function delete_where( array $where ): bool|int {
		global $wpdb;
		$formats = $this->get_formats( $where );
		return $wpdb->delete( $this->get_table(), $where, $formats );
	}


	/**
	 * Update an item in the database by ID.
	 *
	 * @phpstan-param PARTIALS $columns
	 *
	 * @param int              $id      - The id of the item to update.
	 * @param array            $columns - column => value pairs to update.
	 *
	 * @return bool - True if the row was updated.
	 */
	public function update( int $id, array $columns ): bool {
		global $wpdb;
		$updated = $wpdb->update( $this->get_table(), $columns, [ $this->config->get_id_field() => $id ], $this->get_formats( $columns ), [ '%d' ] );
		return 1 === $updated;
	}


	/**
	 * Update items in the database based on the provided criteria.
	 *
	 * @phpstan-param PARTIALS $where
	 * @phpstan-param PARTIALS $columns
	 *
	 * @param array            $where   - Array of where column => value.
	 * @param array            $columns - column => value pairs to update.
	 *
	 * @return false|int - Number of rows updated or false on failure.
	 */
	public function update_where( array $where, array $columns ): bool|int {
		global $wpdb;
		$formats = $this->get_formats( $columns );
		return $wpdb->update( $this->get_table(), $columns, $where, $formats, $this->get_formats( $where ) );
	}


	/**
	 * Replace an item in the database by ID.
	 *
	 * Works exactly like `add` except if an old row in the table
	 * has the same value in a unique index, the old row is deleted
	 * before the new row is added.
	 *
	 * @phpstan-param COLUMNS $columns
	 *
	 * @param int             $id      - ID of the item to replace.
	 * @param array           $columns - column => value pairs to replace.
	 *
	 * @return int|bool - The number of rows affected or false on failure.
	 */
	public function replace( int $id, array $columns ): int|bool {
		global $wpdb;
		$columns = $this->sort_columns( $columns );
		$columns[ $this->config->get_id_field() ] = $id;
		return $wpdb->replace( $this->get_table(), $columns, $this->get_formats( $columns ) );
	}


	/**
	 * Retrieve a `SELECT` query based on the provided criteria.
	 *
	 * @phpstan-param array<key-of<COLUMNS>> $columns
	 * @phpstan-param PARTIALS               $where
	 * @phpstan-param key-of<COLUMNS>|null   $order_by
	 * @phpstan-param self::ORDER_*          $order
	 *
	 * @param array<string>                  $columns   Array of columns we want to return.
	 *                                                  Any empty array will return all columns.
	 * @param array                          $where     Array of where column => value.
	 *                                                  Adding a % within the value will turn the
	 *                                                  query into a `LIKE` query.
	 * @param ?int                           $count     Number of rows to return.
	 *                                                  Null will return all.
	 * @param ?string                        $order_by  An ORDERBY column.
	 * @param string                         $order     - ORDERBY direction (ASC or DESC).
	 * @param ?int                           $offset    Number of rows to skip.
	 *
	 * @return string
	 */
	public function get_select_query( array $columns, array $where, ?int $count = null, ?string $order_by = null, string $order = 'ASC', ?int $offset = null ): string {
		global $wpdb;
		if ( 0 === \count( $columns ) ) {
			$db_columns = '*';
		} else {
			$db_columns = \implode( ', ', \array_map( fn( string $column ) => "`{$column}`", $columns ) );
		}

		$sql = "SELECT $db_columns FROM `{$this->get_table()}`";
		$this->last_where = '';

		if ( \count( $where ) > 0 ) {
			$db_wheres = [];
			$values = [];
			$where_formats = $this->get_formats( $where );

			foreach ( $where as $column => $value ) {
				if ( null === $value ) {
					$db_wheres[ $column ] = "`$column` IS NULL";
					continue;
				}

				if ( \str_contains( (string) $value, '%' ) ) {
					$db_wheres[ $column ] = "`$column` LIKE " . \array_shift( $where_formats );
				} else {
					$db_wheres[ $column ] = "`$column` = " . \array_shift( $where_formats );
				}
				$values[] = $value;
			}
			// Allow filtering or adding of custom WHERE clauses.
			[ $db_wheres, $values ] = apply_filters_ref_array( 'lipe/lib/schema/db/get/wheres', [
				[ $db_wheres, $values ],
				$where,
				$columns,
				$this,
			] );
			if ( \count( $db_wheres ) > 0 ) {
				$where_clause = ' WHERE ' . \implode( ' AND ', $db_wheres );
				$this->last_where = $wpdb->prepare( $where_clause, $values );
				$sql .= $this->last_where;
			}
		}
		if ( null !== $order_by ) {
			$sql .= " ORDER BY `$order_by`";
			if ( 'ASC' !== $order ) {
				$sql .= " $order";
			}
		} elseif ( 'ASC' !== $order ) {
			$sql .= " ORDER BY `{$this->config->get_id_field()}` $order";
		}
		if ( null !== $count ) {
			$sql .= " LIMIT $count";
		}
		if ( null !== $offset ) {
			$sql .= " OFFSET $offset";
		}
		return $sql;
	}


	/**
	 * Translates the values using `\sprintf` based on the column type.
	 *
	 * All values coming from the db are `string`, so we need to format them
	 * back to the original type.
	 *
	 * @phpstan-param array<key-of<COLUMNS>, string|null> $values
	 *
	 * @param array                                       $values - Values to format.
	 *
	 * @phpstan-return COLUMNS
	 * @return array
	 */
	protected function format_values( array $values ): array {
		$formatted = [];
		$columns = $this->config->get_column_formats();
		foreach ( $values as $key => $value ) {
			if ( isset( $columns[ $key ] ) && \is_string( $value ) ) {
				$formatted[ $key ] = match ( $columns[ $key ] ) {
					'%i',
					'%d'    => (int) $value,
					'%f'    => (float) $value,
					default => $value,
				};
			}
		}
		return $formatted;
	}


	/**
	 * Sorts columns for use with query sanitization.
	 *
	 * @phpstan-param PARTIALS $columns
	 *
	 * @param array            $columns - Columns to sort.
	 *
	 * @phpstan-return COLUMNS
	 * @return array
	 */
	protected function sort_columns( array $columns ): array {
		$clean = [];
		foreach ( $this->config->get_column_formats() as $column => $type ) {
			if ( \array_key_exists( $column, $columns ) ) {
				$clean[ $column ] = $columns[ $column ];
				// Because we usually let MySQL handle default dates.
			} elseif ( 'date' !== $column ) {
				$clean[ $column ] = null;
			}
		}
		return $clean;
	}


	/**
	 * Get the sprintf style formats matching an array of columns
	 *
	 * @link https://www.php.net/manual/en/function.sprintf.php
	 *
	 * @template T of key-of<COLUMNS>
	 * @template K of array<T, value-of<COLUMNS>|null>
	 *
	 * @phpstan-param K $columns
	 *
	 * @param array     $columns - Columns to retrieve formats for.
	 *
	 * @phpstan-return array<T, "%d"|"%i"|"%f"|"%s">
	 * @return array
	 */
	protected function get_formats( array $columns ): array {
		$id_field = $this->config->get_id_field();
		$all_columns = $this->config->get_column_formats();
		$formats = [];
		foreach ( $columns as $column => $value ) {
			if ( $id_field === $column ) {
				$formats[ $column ] = '%d';
			} elseif ( isset( $all_columns[ $column ] ) ) {
				$formats[ $column ] = $all_columns[ $column ];
			}
		}
		return $formats;
	}


	/**
	 * Register a table for use with the Custom_Table class.
	 *
	 * @phpstan-param Table<FORMATS> $config
	 *
	 * @param Table                  $config - The table configuration.
	 *
	 * @return static
	 */
	public static function factory( Table $config ): static {
		return new static( $config );
	}
}
