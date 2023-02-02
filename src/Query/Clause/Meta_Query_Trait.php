<?php
declare( strict_types=1 );

namespace Lipe\Lib\Query\Clause;

trait Meta_Query_Trait {

	/**
	 * Meta query generated by `meta_query()`.
	 *
	 * @see self::meta_query()
	 *
	 * @var array
	 */
	public array $meta_query;


	/**
	 * Generate the `meta_query` clauses.
	 *
	 * @link https://developer.wordpress.org/reference/classes/wp_query/#custom-field-post-meta-parameters
	 *
	 * @return Meta_Query
	 */
	public function meta_query() : Meta_Query {
		$query = new Meta_Query();
		$this->clauses[] = $query;
		return $query;
	}
}
