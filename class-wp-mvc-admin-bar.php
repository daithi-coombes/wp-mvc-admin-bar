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
	public $nodes = "woo hoo";

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
		add_action( 'wp_head', 'wp_admin_bar_header' );

		add_action( 'admin_head', 'wp_admin_bar_header' );

		if ( current_theme_supports( 'admin-bar' ) ) {
			$admin_bar_args = get_theme_support( 'admin-bar' ); // add_theme_support( 'admin-bar', array( 'callback' => '__return_false') );
			$header_callback = $admin_bar_args[0]['callback'];
		}

		if ( empty($header_callback) )
			$header_callback = '_admin_bar_bump_cb';

		add_action('wp_head', $header_callback);

		wp_enqueue_script( 'admin-bar' );
		wp_enqueue_style( 'admin-bar' );

		do_action( 'admin_bar_init' );
		//end Actions hooks and filters
	}

	/**
	 * Abstract method.
	 * Display the view file
	 * @return [type] [description]
	 */
	public function render(){
		return $this->parent->render();
	}
}