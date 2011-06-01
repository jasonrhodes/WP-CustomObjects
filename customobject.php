<?php
/**
 * @package WP Custom Object
 * @version 1.0
 */
/*
Plugin Name: WP Custom Object
Plugin URI: http://jasonthings.com/wordpress/wp-custom-object
Description: Registers the custom object class so you can easily build custom objects from your functions.php file.
Author: Jason Rhodes
Version: 1.0
Author URI: http://jasonthings.com
*/

	define('WPCUSTOMOBJECT_VERSION', '1.0.0');
	define('WPCUSTOMOBJECT_PLUGIN_URL', plugin_dir_url( __FILE__ ));
	
	class CustomObject {
		
		public $type;
		public $error_array = array();
		public $register_options = array();
		public $metaboxes = 0;
		public $metabox_options = array();

		
		# Big thanks to the following:
		# http://w3prodigy.com/behind-wordpress/php-classes-wordpress-plugin/
		# Dave Rupert's custom post type boilerplate https://gist.github.com/848232
		
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
				// Setting up local class variables for convenience
				// TODO: Is this way too expensive for performance? We could easily swap it out.
				foreach ( $options as $key => $value ) {
					$this->$key = $value; 
				}
			}
			
			if ( !!$labels ) {	
				// Setting up local class variables for convenience
				// TODO: Is this way too expensive for performance? We could easily swap it out.	
				foreach ( $labels as $key => $value ) {
					$this->$key = $value;
				}		
			}
			
			if ( $this->validate() ) {
				
				// Empty the error array
				$this->error_array = array();
				
				// Register this object's create_object_type() method to the init action
				add_action( 'init', array( $this, 'create_object_type' ) );
				
				/*
					FYI This last action (and others below) was added using an array() with an object/method reference.
					This makes these classes so much more powerful, because each object instance
					can register itself, with its object properties, to an action, instead of creating
					a hundred silly functions in the global scope. No more create_object_type_1 functions necessary!
					
					For more info: https://twitter.com/#!/markjaquith/status/76038034440728576				
					Also, it's not necessary to use &$this, just $this works as of PHP 5. https://twitter.com/#!/mattwiebe/status/76040791725838336
				*/
				
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
		
			// I've found that I usually just want these labels to be what you'd expect
			// So I leave $labels an empty array and let this method do its magic.
			
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
		 *		$event = new CustomObject( $type, $options, $labels ); 
		 * 		$event->setup_meta_box( $meta_box_options );
		 *
		 * Or something like that.
		 *
		 */
	
		
		public function setup_meta_box( $options ) {
			
			$mb = new MetaBox( $options, $this->type );	

		} // end setup_meta_box()
		

	
	} // end CustomObject class
	
	
	class MetaBox {
	
		public $options;
		private $nonce_name;
		
		function __construct( $options, $type ) {
			
			if ( !$options ) return false;
			
			// Save the $options to the class so functions can use it later
			$this->options = $options;
			foreach ( $this->options as $k => $v ) {
				$this->$k = $v;
			}
			
			if ( !isset( $this->type ) ) $this->type = $type;
			
			
			// Register this meta box to a WP action
			if ( has_action( 'add_meta_boxes' ) ) {
				// WP 3.0+
				add_action( 'add_meta_boxes', array( $this, 'wpco_add_meta_box' ) );
			}
			else {
				// backwards compatible?
				add_action( 'admin_init', array( $this, 'wpco_add_meta_box' ), 1 );
			}
			
			// Make sure you save any data
			add_action( 'save_post', array( $this, 'wpco_save_meta_box' ) );
			
		} // end __construct() method
		
		function wpco_add_meta_box() {
		
			add_meta_box( $this->id, $this->title, array( $this, 'wpco_metabox_content' ), $this->type, $this->context, $this->priority );
		
		} // end wpco_add_meta_box() method
		
		function wpco_save_meta_box( $id ) {
		
			// Verify if this is an auto save routine. 
		  // If it is, our form has not been submitted, so we don't want to do anything
		  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		      return;
		
		  // Verify this came from the our screen and with proper authorization,
		  // because save_post can be triggered at other times
		  if ( !wp_verify_nonce( $_POST[$this->nonce_name], $this->nonce_action ) )
		      return;
		
		  // TODO: use the array of fields from the wpco_metabox_content TODO item, and save each one individually
		  
		
		} // end wpco_save_meta_box() method
		
		
		function wpco_metabox_content() {
			
			// Generate a semi-secure nonce name and action
			$this->nonce_name = 'nonce_name_' . time();
			$this->nonce_action = 'save_metabox_data_' . time();
			wp_nonce_field( $this->nonce_action, $this->nonce_name );
			
			echo isset( $this->html ) ? $this->html : '';
			
			// TODO: Swap out this 'html' var for an array of fields, and build the HTML here.
		
		} // end wpco_metabox_content() method
	
	} // end MetaBox class
	