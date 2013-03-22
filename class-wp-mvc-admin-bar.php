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
 * Abstract methods, called in this order:
 *  - initialize();		admin-bar.php:34
 *  - add_menus();		admin-bar.php:35
 *  - render();
 */
class WP_MVC_Admin_Bar extends WP_Extend{

	/**
	 * @var WP_Admin_Bar
	 */
	private $wp_admin_bar;
	/**
	 * The current logged in user details. Defined in @link initialize()
	 * @var stdClass
	 */
	public $user;

	/**
	 * Extend wp class using WP_Extender::extend_filter()
	 * @uses WP_Extender
	 */
	function __construct( $extend=false ){

		//extend the bar class
		$this->extend_filter('wp_admin_bar_class', __CLASS__ );
	}

	/**
	 * Magic Method. Catch unkown methods and try parent.
	 * If method not found pass request on to parent to check $this->parent
	 * @param  [type] $method The method
	 * @param  [type] $args   Array of arguments
	 * @return mixed returns PARENT::parent->$method( $args ) if method is found or
	 * throws an exception if none
	 */
	function __call($method, $args){
		if(!method_exists($this, $method))
			return parent::__call($method, $args);
	}

	function debug($step=1){
		$trace = debug_backtrace();
		print_r($trace[$step]);
	}

	/**
	 * Abstract method. Called in @link admin-bar.php:35
	 * Loads actions and filters for the admin_menu
	 */
	public function add_menus(){
		return $this->parent->add_menus();
	}

	/**
	 * Construct flow.
	 * Acts as the construct. Is called from @link admin-bar.php:34
	 * @return string [description]
	 */
	public function initialize(){

		/**
		 * set user parameter
		 */
		$this->user = new stdClass;

		if ( is_user_logged_in() ) {
			/* Populate settings we need for the menu based on the current user. */
			$this->user->blogs = get_blogs_of_user( get_current_user_id() );
			if ( is_multisite() ) {
				$this->user->active_blog = get_active_blog_for_user( get_current_user_id() );
				$this->user->domain = empty( $this->user->active_blog ) ? user_admin_url() : trailingslashit( get_home_url( $this->user->active_blog->blog_id ) );
				$this->user->account_domain = $this->user->domain;
			} else {
				$this->user->active_blog = $this->user->blogs[get_current_blog_id()];
				$this->user->domain = trailingslashit( home_url() );
				$this->user->account_domain = $this->user->domain;
			}
		} //end set user parameter

		/**
		 * Actions hooks and filters
		 */
		;
		//end Actions hooks and filters
	}

	/**
	 * Abstract method.
	 * Display the view file
	 * @return [type] [description]
	 */
	public function render(){
		//return $this->parent->render();
	}
}

/**
 * Extends wordpress classes using anonymous functions.
 * Dependencies:
 * php < 5.3.0
 */
class WP_Extend{

	/** @var string The child class name */
	public $class_name;
	/** @var string The parent class anme */
	public $parent = false;

	function __call($method, $args){
		if(method_exists($this->parent, $method)){
			return call_user_func_array(array(&$this->parent, $method), $args);
		}
		throw new Exception("Method => " . __CLASS__ . ":$method not found");
		
	}

	/**
	 * Set the child class name using a filter.
	 * Stores child class name in global. Filter callback @link WP_Extend::get_class() 
	 * or if will set parent class name.
	 * @param  string $filter The filter that sets the class
	 * @param  string $class  The new class name
	 * @return void
	 */
	public function extend_filter($filter, $class_name){
		global $_wp_extend_modal;

		//if no global ar, then define it
		if(!is_array(@$_wp_extend_modal)){
			$_wp_extend_modal = array();
			if(!is_array(@$_wp_extend_modal[$class_name]))
				$_wp_extend_modal[$class_name] = '';
		}

		//else original class defined, so construct $parent
		else{
			$parent = $_wp_extend_modal[$class_name];
			$this->parent = new $parent();
		}

		//set params
		$this->class_name = $class_name;
		add_filter($filter, array($this, 'get_class'));
	}

	/**
	 * Filter/hook callback.
	 * @param  string $class The class name
	 * @return string        The set class name
	 */
	public function get_class( $class ){
		global $_wp_extend_modal;

		//if callback is from wp declaration, store class name to extend in global $var
		if($class != $this->class_name)
			$_wp_extend_modal[$this->class_name] = $class;

		//return set class name
		return $this->class_name;
	}
}