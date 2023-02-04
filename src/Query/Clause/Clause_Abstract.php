<?php
declare( strict_types=1 );

namespace Lipe\Lib\Query\Clause;

use Lipe\Lib\Query\Args;

/**
 * Shared methods and interface for various query clauses.
 *
 * @author Mat Lipe
 * @since  4.0.0
 *
 */
abstract class Clause_Abstract {
	/**
	 * Meta query clauses.
	 *
	 * @var array
	 */
	protected array $clauses = [];

	/**
	 * Nested clauses.
	 *
	 * @var static[]
	 */
	protected array $nested = [];

	/**
	 * Parent clause if within a nested clause.
	 *
	 * @var static|null
	 */
	protected ?Clause_Abstract $parent_clause = null;


	/**
	 * Flatten the finished clauses into a query argument property.
	 *
	 * @interal
	 *
	 * @param Args|mixed $args_class - Args class, which supports properties this method will assign.
	 *
	 * @return void
	 */
	abstract public function flatten( $args_class ) : void;


	/**
	 * @param static|null $parent_clause - The parent clause if we are within a nested clause.
	 */
	final public function __construct( ?Clause_Abstract $parent_clause = null ) {
		$this->parent_clause = $parent_clause;
	}


	/**
	 * Set the relation of the clauses.
	 *
	 * Defaults to 'AND'.
	 *
	 * @phpstan-param 'AND'|'OR' $relation
	 *
	 * @param string $relation
	 *
	 * @return static
	 */
	public function relation( string $relation = 'AND' ) : Clause_Abstract {
		$this->clauses['relation'] = $relation;

		return $this;
	}


	/**
	 * Generate a sub level query for nested queries.
	 *
	 * @phpstan-param 'AND'|'OR' $relation
	 *
	 * @param string $relation
	 *
	 * @return static
	 */
	public function nested_clause( string $relation = 'AND' ) : Clause_Abstract {
		if ( empty( $this->clauses['relation'] ) ) {
			$this->relation();
		}
		$sub = new static( $this );
		$this->nested[] = $sub;
		$sub->relation( $relation );

		return $sub;
	}


	/**
	 * Switch out of a nested clause back to the original parent.
	 *
	 * @throws \LogicException - If we are not in a nested class.
	 *
	 * @return static
	 */
	public function parent_clause() : Clause_Abstract {
		if ( null === $this->parent_clause ) {
			throw new \LogicException( 'You cannot switch to a parent clause if you are not already nested.' );
		}
		return $this->parent_clause;
	}


	/**
	 * Loop through the nested clauses and append them to
	 * the clause array at the correct level.
	 *
	 * @param array                  $clauses
	 * @param static|Clause_Abstract $level
	 *
	 * @return void
	 */
	protected function extract_nested( array &$clauses, Clause_Abstract $level ) : void {
		if ( ! empty( $level->nested ) ) {
			foreach ( $level->nested as $nested ) {
				$clauses[] = $nested->clauses;
				$this->extract_nested( $clauses[ \array_key_last( $clauses ) ], $nested );
			}
		}
	}
}
