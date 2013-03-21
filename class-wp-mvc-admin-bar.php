<?php
/**
 * @package wp_mvc_admin_bar
 */


//require_once( ABSPATH . WPINC . "/class-wp-admin-bar.php" );

/**
 * Provide an mvc architecture for the WP_Admin_Class.
 *
 * Hooks into the WP_Admin_Class to set and get admin bar nodes.
 * Needs to hook in before init to set the WP_Admin_Class
 *
 * Dependencies:
 * php < 5.3.0
 * 
 * Abstract methods:
 *  - add_menus();
 *  - admin_bar_init();
 *  - filter_set_class();
 *  - initialize();
 *  - render();
 * @uses admin_bar_init hook
 * @uses add_admin_bar_menus hook
 */
class WP_MVC_Admin_Bar extends WP_Extend{

	/**
	 * @var WP_Admin_Bar
	 */
	private $wp_admin_bar;

	/**
	 * Hooks, actions and filters. @see @link initialize() for construct flow.
	 * @uses WP_Extender
	 */
	function __construct(){

		//extend the bar class
		WP_Extend::filter('wp_admin_bar_class', __CLASS__ );
	}

	function foo(){
		return __CLASS__;
	}

	function debug(){
		$trace = debug_backtrace();
		print "<pre>\n";
		if($trace[1]['class'])
			print $trace[1]['class'] . "::";
		print $trace[1]['function']."\n";
		print_r(func_get_args());
		print "</pre>";
		die();	
	}

	/**
	 * Abstract method. Called in @link admin-bar.php:35
	 * Loads actions and filters for the admin_menu
	 */
	public function __add_menus(){
		$this->debug('add_menus');
	}

	public function admin_bar_init(){
		$this->debug();
		global $wp_admin_bar;

		if(!is_object($wp_admin_bar))
			$admin_bar = new WP_Admin_Bar();

		$this->debug('admin_bar_init');
	}

	/**
	 * Construct flow.
	 * Acts as the construct. Is called from @link admin-bar.php:34
	 * @return string [description]
	 */
	public function initialize(){
		$this->debug('initialize');
		//load wp_admin_bar class
		//require_once ABSPATH . WPINC . '/class-wp-admin-bar.php';
		//$this->wp_admin_bar = new WP_Admin_Bar();
	}

	/**
	 * Abstract method.
	 * Display the view file
	 * @return [type] [description]
	 */
	public function __render(){
		$this->debug('render');
	}
}

/**
 * Extends wordpress classes using anonymous functions.
 * Dependencies:
 * php < 5.3.0
 */
class WP_Extend{

	public $class_name;

	/**
	 * Add new class to a filter
	 * @global WP_Extender $extender;
	 * @param  string $filter The filter that sets the class
	 * @param  string $class  The new class name
	 * @return string         The new class name
	 */
	public function filter($filter, $class_name){
		global $extender;
		$extender->class_name = $class_name;
		add_filter($filter, function($class){ global $extender; return $extender->class_name;});
	}
}
global $extender;
$extender = new WP_Extend();