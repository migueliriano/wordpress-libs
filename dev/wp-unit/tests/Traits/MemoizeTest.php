<?php
/**
 * @author Mat Lipe
 * @since  March, 2019
 *
 */

namespace Lipe\Lib\Traits;

class MemoizeTestTrait {
	use Memoize;

	public function heavy_memo( ...$args ) {
		return $this->memoize( function( ...$passed ) {
			return [ $passed[0], microtime( true ), $passed[1] ?? null ];
		}, __METHOD__, ...$args );
	}


	public function heavy_once( ...$args ) {
		return $this->once( function( ...$passed ) {
			return [ $passed[0], microtime( true ), $passed[1] ?? null ];
		}, __METHOD__, ...$args );
	}


	public function heavy_persistent( ...$args ) {
		return $this->persistent( function( ...$passed ) {
			return [ $passed[0], microtime( true ), $passed[1] ?? null ];
		}, __METHOD__, 0, ...$args );
	}
}

class MemoizeTest extends \WP_UnitTestCase {
	/**
	 * @var MemoizeTestTrait
	 */
	private $trait;


	public function setUp() : void {
		$this->trait = new MemoizeTestTrait();

		parent::setUp();
	}


	public function test_memoize_method() : void {
		$first = $this->trait->heavy_memo( [ 'first' ] )[1];
		$this->assertEquals( [ 'first' ], $this->trait->heavy_memo( [ 'first' ] )[0] );
		$this->assertEquals( 'chair', $this->trait->heavy_memo( 'purse', 'chair' )[2] );

		$this->assertEquals( $first, $this->trait->heavy_memo( [ 'first' ] )[1] );
		$this->assertNotEquals( $first, $this->trait->heavy_memo( 'second' )[1] );
		$third = $this->trait->heavy_memo( [ 'third', 'one' ] )[1];
		$this->assertNotEquals( $first, $third );
		$this->assertEquals( $third, $this->trait->heavy_memo( [ 'third', 'one' ] )[1] );
		$this->assertEquals( $first, $this->trait->heavy_memo( [ 'first' ] )[1] );
	}


	public function test_once_method() : void {
		$first = $this->trait->heavy_once( 'purse', 'chair' );
		$this->assertEquals( 'chair', $first[2] );
		$this->assertEquals( $first, $this->trait->heavy_once( [ 'first' ] ) );
		$this->assertEquals( $first, $this->trait->heavy_once( 'second' ) );
		$third = $this->trait->heavy_once( [ 'third', 'one' ] );
		$this->assertEquals( $first, $third );

		$this->trait->clear_memoize_cache();
		$this->assertNotEquals( $first, $this->trait->heavy_once( [ 'first' ] ) );
	}


	public function test_persistent_method() : void {
		$first = $this->trait->heavy_persistent( [ 'purse' ] )[1];
		$this->assertEquals( [ 'purse' ], $this->trait->heavy_persistent( [ 'purse' ] )[0] );
		$this->assertEquals( 'chair', $this->trait->heavy_persistent( 'purse', 'chair' )[2] );

		$this->assertEquals( $first, $this->trait->heavy_persistent( [ 'purse' ] )[1] );
		$this->assertNotEquals( $first, $this->trait->heavy_persistent( 'second' )[1] );
		$third = $this->trait->heavy_persistent( [ 'third', 'one' ] )[1];
		$this->assertNotEquals( $first, $third );
		$this->assertEquals( $third, $this->trait->heavy_persistent( [ 'third', 'one' ] )[1] );
		$this->assertEquals( [ 'third', 'one' ], $this->trait->heavy_persistent( [ 'third', 'one' ] )[0] );
		$this->assertEquals( $first, $this->trait->heavy_persistent( [ 'purse' ] )[1] );

		$this->trait->clear_memoize_cache();
		$this->assertNotEquals( $third, $this->trait->heavy_persistent( [ 'third', 'one' ] )[1] );
		$this->assertNotEquals( $first, $this->trait->heavy_persistent( [ 'purse' ] )[1] );

		$first = $this->trait->heavy_persistent( [ 'purse' ] )[1];
		$this->assertEquals( $first, $this->trait->heavy_persistent( [ 'purse' ] )[1] );

		\wp_cache_flush();
		$this->assertNotEquals( $first, $this->trait->heavy_persistent( [ 'purse' ] )[1] );

		$other = new class {
			use Memoize;

			public function heavy_persistent( ...$args ) {
				return $this->persistent( function( ...$passed ) {
					return [ $passed[0], microtime( true ), $passed[1] ?? null ];
				}, __METHOD__, 0, ...$args );
			}
		};
		$second = $other->heavy_persistent( [] );
		$this->trait->clear_memoize_cache();
		$this->assertEquals( $second, $other->heavy_persistent( [] ), 'Caches are being cleared on other classes.' );
	}
}
