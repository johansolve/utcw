<?php if ( ! defined( 'ABSPATH' ) ) die();
/**
 * Ultimate Tag Cloud Widget
 * @author     Rickard Andersson <rickard@0x539.se>
 * @version    2.0
 * @license    GPLv2
 * @package    utcw
 * @subpackage test
 */

class UTCW_Test_Data extends WP_UnitTestCase {

	/**
	 * @var MockFactory
	 */
	private $mockFactory;

	function setUp() {
		$this->mockFactory = new MockFactory( $this );
	}

	function test_taxonomy() {
		$this->helper_query_contains( array( 'taxonomy' => 'category' ), "taxonomy = 'category'" );
	}

	function test_author() {
		$this->helper_query_contains( array( 'authors' => array( 1, 2, 3 ) ), 'post_author IN (1,2,3)' );
	}

	function test_single_post_type() {
		$this->helper_query_contains( array( 'post_type' => 'post' ), "post_type IN ('post')" );
	}

	function test_multiple_post_types() {
		$this->helper_query_contains( array( 'post_type' => array( 'post', 'page' ) ), "post_type IN ('post','page')" );
	}

	function test_post_status_not_authenticated() {
		$this->helper_query_contains( array(), "post_status = 'publish'" );
	}

	function test_post_status_authenticated() {
		$this->helper_query_contains( array(), "post_status IN ('publish','private')", 'authenticated' );
	}

	function test_tags_list_include() {
		$instance = array(
			'tags_list_type' => 'include',
			'tags_list'      => array( 1, 2, 3 ),
		);
		$this->helper_query_contains( $instance, 't.term_id IN (1,2,3)' );
	}

	function test_tags_list_exclude() {
		$instance = array(
			'tags_list_type' => 'exclude',
			'tags_list'      => array( 1, 2, 3 ),
		);
		$this->helper_query_contains( $instance, 't.term_id NOT IN (1,2,3)' );
	}

	function test_tags_list_taxonomy() {

		$utcw = $this->mockFactory->getUTCWMock();

		$utcw->expects( $this->any() )
			->method( 'check_term_taxonomy' )
			->will( $this->returnValue( false ) );

		$instance = array(
			'tags_list_type' => 'include',
			'tags_list'      => array( 1, 2, 3, 4, 5 ),
		);

		$db = $this->mockFactory->getWPDBMock();

		$db->expects( $this->once() )
			->method( 'get_results' )
			->will( $this->returnValue( array() ) )
			->with( new PHPUnit_Framework_Constraint_Not( $this->stringContains( 't.term_id IN (' ) ) );

		$config = new UTCW_Config( $instance, $utcw );
		$data   = new UTCW_Data( $config, $utcw, $db );
		$data->get_terms();
	}

	function test_days_old() {
		$this->helper_query_contains( array( 'days_old' => 1 ), "post_date > '" . date( 'Y-m-d', strtotime( 'yesterday' ) ) . "'" );
	}

	function test_minimum() {
		$this->helper_query_contains( array( 'minimum' => 5 ), 'HAVING count >= 5' );
	}

	function test_order_name() {
		$instance = array( 'order' => 'name', 'reverse' => false );
		$this->helper_query_contains( $instance, 'ORDER BY count DESC' );
		$this->helper_query_contains( $instance, ') AS subQuery' );
		$this->helper_query_contains( $instance, 'ORDER BY name ASC' );
	}

	function test_order_name_reverse() {
		$instance = array( 'order' => 'name', 'reverse' => true );
		$this->helper_query_contains( $instance, 'ORDER BY count DESC' );
		$this->helper_query_contains( $instance, ') AS subQuery' );
		$this->helper_query_contains( $instance, 'ORDER BY name DESC' );
	}

	function test_order_name_case_sensitive() {
		$instance = array( 'order' => 'name', 'case_sensitive' => true );
		$this->helper_query_contains( $instance, 'ORDER BY count DESC' );
		$this->helper_query_contains( $instance, ') AS subQuery' );
		$this->helper_query_contains( $instance, 'ORDER BY BINARY name ASC' );
	}

	function test_order_slug() {
		$instance = array( 'order' => 'slug', 'reverse' => false );
		$this->helper_query_contains( $instance, 'ORDER BY count DESC' );
		$this->helper_query_contains( $instance, ') AS subQuery' );
		$this->helper_query_contains( $instance, 'ORDER BY slug ASC' );
	}

	function test_order_slug_reverse() {
		$instance = array( 'order' => 'slug', 'reverse' => true );
		$this->helper_query_contains( $instance, 'ORDER BY count DESC' );
		$this->helper_query_contains( $instance, ') AS subQuery' );
		$this->helper_query_contains( $instance, 'ORDER BY slug DESC' );
	}

	function test_order_slug_case_sensitive() {
		$instance = array( 'order' => 'slug', 'case_sensitive' => true );
		$this->helper_query_contains( $instance, 'ORDER BY count DESC' );
		$this->helper_query_contains( $instance, ') AS subQuery' );
		$this->helper_query_contains( $instance, 'ORDER BY BINARY slug ASC' );
	}

	function test_order_id() {
		$instance = array( 'order' => 'id', 'reverse' => false );
		$this->helper_query_contains( $instance, 'ORDER BY count DESC' );
		$this->helper_query_contains( $instance, ') AS subQuery' );
		$this->helper_query_contains( $instance, 'ORDER BY term_id ASC' );
	}

	function test_order_id_reverse() {
		$instance = array( 'order' => 'id', 'reverse' => true );
		$this->helper_query_contains( $instance, 'ORDER BY count DESC' );
		$this->helper_query_contains( $instance, ') AS subQuery' );
		$this->helper_query_contains( $instance, 'ORDER BY term_id DESC' );
	}

	function test_order_count() {
		$instance = array( 'order' => 'count', 'reverse' => false );
		$this->helper_query_contains( $instance, 'ORDER BY count DESC' );
		$this->helper_query_contains( $instance, ') AS subQuery' );
		$this->helper_query_contains( $instance, 'ORDER BY count ASC' );
	}

	function test_order_count_reverse() {
		$instance = array( 'order' => 'count', 'reverse' => true );
		$this->helper_query_contains( $instance, 'ORDER BY count DESC' );
	}

	/**
	 * @param $query_terms
	 *
	 * @dataProvider terms
	 */
	function test_order_color( $query_terms ) {
		$instance = array(
			'order'   => 'color',
			'reverse' => false,
			'color'   => 'random'
		);

		$terms = $this->helper_get_terms( $instance, $query_terms );

		$previous = array_shift( $terms );

		foreach ( $terms as $term ) {
			$this->assertGreaterThanOrEqual( 0, strcmp( $term->color, $previous->color ) );
			$previous = $term;
		}
	}

	/**
	 * @param $query_terms
	 *
	 * @dataProvider terms
	 */
	function test_order_color_reverse( $query_terms ) {
		$instance = array(
			'order'   => 'color',
			'reverse' => true,
			'color'   => 'random'
		);

		$terms    = $this->helper_get_terms( $instance, $query_terms );
		$previous = array_shift( $terms );

		foreach ( $terms as $term ) {
			$this->assertLessThanOrEqual( 0, strcmp( $term->color, $previous->color ) );
			$previous = $term;
		}
	}

	/**
	 * @param array $query_terms
	 *
	 * @dataProvider terms
	 */
	function test_size( $query_terms ) {
		$instance = array(
			'size_from' => 1,
			'size_to'   => 10,
		);

		$terms = $this->helper_get_terms( $instance, $query_terms );

		foreach ( $terms as $term ) {
			$this->assertLessThanOrEqual( 10, $term->size );
			$this->assertGreaterThanOrEqual( 1, $term->size );
		}
	}

	/**
	 * @param array $query_terms
	 *
	 * @dataProvider terms
	 */
	function test_color_random( $query_terms ) {
		$terms = $this->helper_get_terms( array( 'color' => 'random' ), $query_terms );

		foreach ( $terms as $term ) {
			$this->assertNotEmpty( $term->color );
		}
	}

	/**
	 * @param array $query_terms
	 *
	 * @dataProvider terms
	 */
	function test_color_set( $query_terms ) {
		$color_set = array( '#fafafa', '#bada55', '#123456' );

		$instance = array(
			'color'     => 'set',
			'color_set' => $color_set,
		);

		$terms = $this->helper_get_terms( $instance, $query_terms );

		foreach ( $terms as $term ) {
			$this->assertContains( $term->color, $color_set );
		}
	}

	/**
	 * @param array $query_terms
	 *
	 * @dataProvider terms
	 */
	function test_color_span_smallest_first( $query_terms ) {
		$instance = array(
			'color'           => 'span',
			'color_span_from' => '#222222',
			'color_span_to'   => '#333333',
		);

		$colors = new stdClass;

		$colors->red_from = $colors->green_from = $colors->blue_from = 0x22;
		$colors->red_to   = $colors->green_to = $colors->blue_to = 0x33;

		$this->helper_test_color_span( $instance, $colors, $query_terms );
	}

	/**
	 * @param array $query_terms
	 *
	 * @dataProvider terms
	 */
	function test_color_span_biggest_first( $query_terms ) {
		$instance = array(
			'color'           => 'span',
			'color_span_from' => '#333333',
			'color_span_to'   => '#222222',
		);

		$colors = new stdClass;

		$colors->red_from = $colors->green_from = $colors->blue_from = 0x33;
		$colors->red_to   = $colors->green_to = $colors->blue_to = 0x22;

		$this->helper_test_color_span( $instance, $colors, $query_terms );
	}

	/**
	 * @param array $query_terms
	 *
	 * @dataProvider terms
	 */
	function test_color_span_letters( $query_terms ) {
		$instance = array(
			'color'           => 'span',
			'color_span_from' => '#bbbbbb',
			'color_span_to'   => '#dddddd',
		);

		$colors = new stdClass;

		$colors->red_from = $colors->green_from = $colors->blue_from = 0xbb;
		$colors->red_to   = $colors->green_to = $colors->blue_to = 0xdd;

		$this->helper_test_color_span( $instance, $colors, $query_terms );
	}

	/**
	 * @param array $query_terms
	 *
	 * @dataProvider terms
	 */
	function test_color_span( $query_terms ) {
		$instance = array(
			'color'           => 'span',
			'color_span_from' => '#D010EB',
			'color_span_to'   => '#999999',
		);

		$colors = new stdClass;

		$colors->red_from   = 0xd0;
		$colors->green_from = 0x10;
		$colors->blue_from  = 0xeb;

		$colors->red_to   = 0x99;
		$colors->green_to = 0x99;
		$colors->blue_to  = 0x99;

		$this->helper_test_color_span( $instance, $colors, $query_terms );
	}

	/**
	 * @param array $query_terms
	 *
	 * @dataProvider terms
	 *
	 * @link https://github.com/rickard2/utcw/issues/2
	 * @link https://0x539.se/wordpress/ultimate-tag-cloud-widget/#comment-567130628
	 */
	function test_color_span_inverse( $query_terms ) {

		$instance = array(
			'color'           => 'span',
			'color_span_from' => '#00ffff',
			'color_span_to'   => '#ff0000',
		);

		$colors = new stdClass;

		$colors->red_from   = 0x00;
		$colors->green_from = 0xff;
		$colors->blue_from  = 0xff;

		$colors->red_to   = 0xff;
		$colors->green_to = 0x00;
		$colors->blue_to  = 0x00;

		$this->helper_test_color_span( $instance, $colors, $query_terms );
	}

	/**
	 * @param array $query_terms
	 *
	 * @dataProvider terms
	 */
	function test_color_span_below_ten( $query_terms ) {
		$instance = array(
			'color'           => 'span',
			'color_span_from' => '#010101',
			'color_span_to'   => '#020202',
		);

		$colors = new stdClass;

		$colors->red_from   = 0x01;
		$colors->green_from = 0x01;
		$colors->blue_from  = 0x01;

		$colors->red_to   = 0x02;
		$colors->green_to = 0x02;
		$colors->blue_to  = 0x02;

		$this->helper_test_color_span( $instance, $colors, $query_terms );
	}

	function helper_test_color_span( $instance, $colors, $query_terms ) {
		$terms = $this->helper_get_terms( $instance, $query_terms );

		foreach ( $terms as $term ) {

			$matches = preg_match_all( "/[0-9a-f]{2}/i", $term->color, $rgb_matches );

			$this->assertEquals( 3, $matches, 'Term color should always be in six digit hexadecimal value colors' );

			list( $red, $green, $blue ) = array_map( 'hexdec', $rgb_matches[ 0 ] );

			$red_from = min( $colors->red_to, $colors->red_from );
			$red_to   = max( $colors->red_to, $colors->red_from );

			$this->assertLessThanOrEqual( $red_to, $red );
			$this->assertGreaterThanOrEqual( $red_from, $red );

			$green_from = min( $colors->green_to, $colors->green_from );
			$green_to   = max( $colors->green_to, $colors->green_from );

			$this->assertLessThanOrEqual( $green_to, $green );
			$this->assertGreaterThanOrEqual( $green_from, $green );

			$blue_from = min( $colors->blue_to, $colors->blue_from );
			$blue_to   = max( $colors->blue_to, $colors->blue_from );

			$this->assertLessThanOrEqual( $blue_to, $blue );
			$this->assertGreaterThanOrEqual( $blue_from, $blue );
		}
	}

	function helper_get_terms( $instance, $query_terms ) {
		$dp = new DataProvider( $this );
		return $dp->get_terms( $instance, $query_terms );
	}

	function helper_query_contains( $instance, $string, $authenticated = false ) {
		$utcw = $authenticated ? $this->mockFactory->getUTCWAuthenticated() : $this->mockFactory->getUTCWNotAuthenticated();

		$utcw->expects( $this->any() )
			->method( 'check_term_taxonomy' )
			->will( $this->returnValue( true ) )
			->with( $this->anything(), $this->stringContains( 'post_tag' ) );

		$config = new UTCW_Config( $instance, $utcw );
		$db     = $this->mockFactory->getWPDBMock();

		$db->expects( $this->once() )
			->method( 'get_results' )
			->will( $this->returnValue( array() ) )
			->with( $this->stringContains( $string ) );

		$data = new UTCW_Data( $config, $utcw, $db );
		$data->get_terms();
	}

	function terms() {
		$dp = new DataProvider( $this );
		return $dp->termsProvider();
	}
}
