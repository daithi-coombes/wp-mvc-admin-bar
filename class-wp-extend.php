<?php
/**
 * Extends wordpress classes using anonymous functions.
 * Dependencies:
 * php < 5.3.0
 * @author daithi coombes
 * @package WP_Extend
 */
if(!class_exists('WP_Extend')):
	class WP_Extend{

		/** @var string The child class name */
		public $class_name;
		/** @var string The parent class anme */
		public $parent = false;

		function __call($method, $args){
			if(method_exists($this->parent, $method)){
				$res = call_user_func_array(array(&$this->parent, $method), $args);
				return $res;
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

		/**
		 * Use to call current method name in parent.
		 * Will automatically pass args send to child method if none set as parameter
		 * @param array An array of arguments to overrite params pass to child method
		 * @return mixed returns value from calling parent method with args
		 */
		public function parent( $args=false ){
			$trace = debug_backtrace()[1];
			if(!$args)
				$args = $trace['$args'];
			return call_user_func_array(array(&$this->parent, $trace['function']), args);
		}
	}
endif;