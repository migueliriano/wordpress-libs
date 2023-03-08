<?php
declare( strict_types=1 );

namespace Lipe\Lib\Post_Type;

use Lipe\Lib\Query\Args_Abstract;

/**
 * A fluent interface for the `register_post_status` function in WordPress.
 *
 * @author Mat Lipe
 * @since  4.0.0
 *
 * @link   https://developer.wordpress.org/reference/functions/register_post_status/
 *
 */
class Register_Post_Status extends Args_Abstract {
	/**
	 * A descriptive name for the post status marked for translation.
	 *
	 * Defaults to value of `$post_status`.
	 *
	 * @var string
	 */
	public string $label;

	/**
	 * Nooped plural text from `_n_noop()` to provide the singular
	 * and plural forms of the label for counts.
	 *
	 * Default array of `$label`, twice.
	 *
	 * @phpstan-var array{
	 *     0: string,
	 *     1: string,
	 *     singular: string,
	 *     plural: string,
	 *     context: string|null,
	 *     domain: string|null,
	 * }
	 *
	 * @var array
	 */
	public array $label_count;

	/**
	 * Whether to exclude posts with this post status from search results.
	 *
	 * Default is value of `$internal`.
	 *
	 * @var bool
	 */
	public bool $exclude_from_search;

	/**
	 * Whether posts of this status should be shown in the front end of the site.
	 *
	 * Default false.
	 *
	 * @var bool
	 */
	public bool $public;

	/**
	 * Whether the status is for internal use only.
	 *
	 * Default false.
	 *
	 * @var bool
	 */
	public bool $internal;

	/**
	 * Whether posts with this status should be protected.
	 *
	 * Default false.
	 *
	 * @var bool
	 */
	public bool $protected;

	/**
	 * Whether posts with this status should be private.
	 *
	 * Default false.
	 *
	 * @var bool
	 */
	public bool $private;

	/**
	 * Whether posts with this status should be publicly-queryable.
	 *
	 * Default is value of `$public`.
	 *
	 * @var bool
	 */
	public bool $publicly_queryable;

	/**
	 * Whether to include posts in the edit listing for their post type.
	 *
	 * Default is the opposite value of `$internal`.
	 *
	 * @var bool
	 */
	public bool $show_in_admin_all_list;

	/**
	 * Show in the list of statuses with post counts at the top of the edit listings.
	 * e.g. All (12) | Published (9) | My Custom Status (2)
	 *
	 * Default is the opposite value of `$internal`.
	 *
	 * @var bool
	 */
	public bool $show_in_admin_status_list;

	/**
	 * Whether the post has a floating creation date.
	 *
	 * Default `false`.
	 *
	 * @var bool
	 */
	public bool $date_floating;
}
