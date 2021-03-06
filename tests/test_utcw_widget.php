<?php if ( ! defined( 'ABSPATH' ) ) die();
/**
 * Ultimate Tag Cloud Widget
 * @author     Rickard Andersson <rickard@0x539.se>
 * @version    2.0
 * @license    GPLv2
 * @package    utcw
 * @subpackage test
 */

class test_utcw_widget extends PHPUnit_Framework_TestCase {

	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	private $utcw;

	function setUp() {
		$this->utcw = $this->getMock( 'UTCW_Plugin', array(), array(), '', false );
	}

	function test_save_config() {

		$instance = array(
			'save_config'      => 'on',
			'save_config_name' => '__test',
		);

		$this->utcw->expects( $this->once() )
			->method( 'save_configuration' )
			->with( '__test' );

		$widget = new UTCW( $this->utcw );
		$widget->update( $instance, array() );
	}

	function test_load_config() {

		$instance = array(
			'load_config'      => 'on',
			'load_config_name' => '__test',
		);

		$this->utcw->expects( $this->once() )
			->method( 'load_configuration' )
			->with( '__test' );

		$widget = new UTCW( $this->utcw );
		$widget->update( $instance, array() );
	}

	function test_form_renders_html() {
		$this->expectOutputRegex( UTCW_TEST_HTML_REGEX );

		$widget = new UTCW();
		$widget->form( array() );
	}

	function test_widget_renders_html() {
		$this->expectOutputRegex( UTCW_TEST_HTML_REGEX );

		$widget = new UTCW();
		$widget->widget( array(), array() );
	}

	function test_widget_converts_empty_checkbox_values_to_false() {

		$widget = new UTCW();

		$instance = $widget->update( array(), array() );

		$this->assertEquals( false, $instance[ 'show_title_text' ] );
		$this->assertEquals( false, $instance[ 'show_title' ] );
		$this->assertEquals( false, $instance[ 'debug' ] );
		$this->assertEquals( false, $instance[ 'reverse' ] );
		$this->assertEquals( false, $instance[ 'case_sensitive' ] );
	}
}