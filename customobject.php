<?php
/**
 * @package WP Custom Object
 * @version 1.0
 */
/*
Plugin Name: WP Custom Object
Plugin URI: http://jasonthings.com/wordpress/wp-custom-object
Description: Creates a WP Custom Object class to quickly get a custom object up and running (otherwise known as "custom post type")
Author: Jason Rhodes
Version: 1.0
Author URI: http://jasonthings.com
*/

	define('WPCUSTOMOBJECT_VERSION', '1.0.0');
	define('WPCUSTOMOBJECT_PLUGIN_URL', plugin_dir_url( __FILE__ ));

	function roundtable_init() {
		// Do nothing for now...
	}
	
	class CustomObject {
		
		public $type;
		
		public $error_array = array();
		public $register_options = array();
		public $meta_box_options = array();
		public $meta_fields_array = array();
		public $columns_array = array();
	
		# credit: http://w3prodigy.com/behind-wordpress/php-classes-wordpress-plugin/
		# credit: Dave Rupert's custom post type boilerplate https://gist.github.com/848232
		# Old versions of PHP will now call the __construct method
		function CustomObject( $type, $options, $labels ) {
			$this->__construct( $type, $options, $labels );
		}
		
		public function __construct( $type, $options = array(), $labels = array() ) {
			
			// Pass in all of your options in the options array, labels in the labels array -- duh
			// 
			// For a complete list of available options, see: http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
			
			$this->type = $type;
			
			if ( !!$options ) { 	
				$this->register_options = $options;
				foreach ( $options as $key => $value ) {
					$this->$key = $value; 
				}
			}
			
			if ( !!$labels ) {		
				foreach ( $labels as $key => $value ) {
					$this->$key = $value;
				}		
			}
			
			if ( $this->validate() ) {
				
				$this->error_array = array(); // empty the error array
				add_action( 'init', array( &$this, 'create_object_type' ) );
				
			}
		
			else { return $this->error_array; }
			
		} // end __construct()
		
		
		public function validate() {
			
			// If no type has been set, we can't create this object
			// Set an error message in error_array and be done with it
			if ( !$this->type ) {
				$this->error_array[] = "A new custom object needs a <em>type</em>, like 'book', 'restaurant', or 'person'.";
				return false;
			}
			else {
				$this->type = strtolower( $this->type );
			}
			
			// A couple other required setups once $this->type has been set
			if ( !$this->singular_name && !!$this->type ) {
				$this->singular_name = ucwords( $this->type );
			}
			if ( !$this->plural_name && !!$this->singular_name ) {
				$this->plural_name = $this->singular_name . "s";
			}
			
			return true;
		
		} // end validate()
		
		
		public function setup_labels() {
			
			if ( !$this->add_new ) {
				$this->add_new = "Add New " . $this->singular_name;
			}
			if ( !$this->add_new_item ) {
				$this->add_new_item = "Add New " . $this->singular_name;
			}
			if ( !$this->edit ) { 
				$this->edit = "Edit"; 
			}
			if ( !$this->edit_item ) { 
				$this->edit_item = "Edit " . $this->singular_name; 
			}
			if ( !$this->new_item ) { 
				$this->new_item = "New " . $this->singular_name; 
			}
			if ( !$this->view ) { 
				$this->view = "View " . $this->singular_name . " Page"; 
			}
			if ( !$this->view_item ) { 
				$this->view_item = "View " . $this->singular_name; 
			}
			if ( !$this->search_items ) { 
				$this->search_items = "Search " . $this->plural_name; 
			}
			if ( !$this->not_found ) {
				$this->not_found = "No matching " . strtolower( $this->plural_name ) . " found";
			}
			if ( !$this->not_found_in_trash ) {
				$this->not_found_in_trash = "No " . strtolower( $this->plural_name ) . " found in Trash";
			}
			if ( !$this->parent_item_colon ) {
				$this->parent_item_colon = "Parent " . $this->singular_name;
			}
			
			$this->register_options['labels'] = array(
				'name' => __( $this->plural_name ),
				'singular_name' => __( $this->singular_name ),
				'add_new' => __( $this->add_new ),
				'add_new_item' => __( $this->add_new_item ),
				'edit' => __( $this->edit ),
				'edit_item' => __( $this->edit_item ),
				'new_item' => __( $this->new_item ),
				'view' => __( $this->view ),
				'view_item' => __( $this->view_item ),
				'search_items' => __( $this->search_items ),
				'not_found' => __( $this->not_found ),
				'not_found_in_trash' => __( $this->not_found_in_trash ),
				'parent_item_colon' => __( $this->parent_item_colon ),
			);
		
		} // end setup_labels()
	
	
		public function create_object_type() {
		
			$this->setup_labels();
			register_post_type( $this->type, $this->register_options );
		
		} // end create_object_type()


		/* 
		 * And that's it! Your new object type is set up and you don't *need* to do anything else.
		 * However...
		 * 
		 * At this point, your new object looks... exactly like a post. So that's kind of dumb.
		 * In order to add value to the new object type, we need to do a few of these other things.
		 *
		 * To do that, call these methods after you've initialized your new object type, like:
		 * 		
		 *		$event = new CustomObject( $options ); 
		 * 		$event->setup_meta_box( $meta_box_options );
		 *
		 * Or something like that.
		 *
		 */
	
		
		public function setup_meta_box( $options ) {
		
			//add_meta_box
		
		} // end setup_meta_box()
	
	
	} // end CustomObject class
