<?php
/**
 * @package WP Custom Objects
 * @version 1.1
 */
/*
Plugin Name: WP Custom Objects
Plugin URI: http://jasonthings.com/wordpress/wp-custom-objects
Description: Registers the custom object class so you can easily build custom objects from your functions.php file.
Author: Jason Rhodes
Version: 1.1
Author URI: http://jasonthings.com
*/

	define('WPCUSTOMOBJECTS_VERSION', '1.1.0');
	define('WPCUSTOMOBJECTS_PLUGIN_URL', plugin_dir_url( __FILE__ ));	
	
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
				add_action( 'init', array( &$this, 'create_object_type' ) );
				
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
			if ( !isset( $this->type ) || empty( $this->type ) ) {
				$this->error_array[] = "A new custom object needs a <em>type</em>, like 'book', 'restaurant', or 'person'.";
				return false;
			}
			else {
				$this->type = str_replace( " ", "_", $this->type );
				if ( substr( $this->type, -1) == "s" ) { $this->type = substr( $this->type, 0, -1); }
				$this->type = strtolower( $this->type );
			}
			
			// A couple other required setups once $this->type has been set
			if ( !isset( $this->singular_name ) && !!$this->type ) {
				$spaced_type = str_replace( "_", " ", $this->type );
				$this->singular_name = ucwords( $spaced_type );
			}
			if ( !isset( $this->plural_name ) && !!$this->singular_name ) {
				$this->plural_name = $this->singular_name . "s";
			}
			
			return true;
		
		} // end validate()
		
		
		public function setup_labels() {
		
			// I've found that I usually just want these labels to be what you'd expect
			// So I leave $labels an empty array and let this method do its magic.
			
			if ( !isset( $this->add_new ) ) {
				$this->add_new = "Add New " . $this->singular_name;
			}
			if ( !isset( $this->add_new_item ) ) {
				$this->add_new_item = "Add New " . $this->singular_name;
			}
			if ( !isset( $this->edit ) ) { 
				$this->edit = "Edit"; 
			}
			if ( !isset( $this->edit_item ) ) { 
				$this->edit_item = "Edit " . $this->singular_name; 
			}
			if ( !isset( $this->new_item ) ) { 
				$this->new_item = "New " . $this->singular_name; 
			}
			if ( !isset( $this->view ) ) { 
				$this->view = "View " . $this->singular_name . " Page"; 
			}
			if ( !isset( $this->view_item ) ) { 
				$this->view_item = "View " . $this->singular_name; 
			}
			if ( !isset( $this->search_items ) ) { 
				$this->search_items = "Search " . $this->plural_name; 
			}
			if ( !isset( $this->not_found ) ) {
				$this->not_found = "No matching " . strtolower( $this->plural_name ) . " found";
			}
			if ( !isset( $this->not_found_in_trash ) ) {
				$this->not_found_in_trash = "No " . strtolower( $this->plural_name ) . " found in Trash";
			}
			if ( !isset( $this->parent_item_colon ) ) {
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
		 * 		$event->setup_metabox( $metabox_options );
		 *
		 * Or something like that.
		 *
		 */
		
		public function setup_metabox( $options=array() ) {
			
			if ( !is_array( $options ) ) $options = array( $options );
			$mb = new MetaBox( $options, $this->type );
			return $mb;

		} // end setup_metabox()
		
		public function disable_addnew() {
			
			/* Use this function to disable the ability to add new objects.
			 * Once you've created a few, you may want to lock it down so your
			 * users can't create any additional objects of this kind.
			 */
			 
			 /* http://minimalbugs.com/questions/how-to-disable-add-new-post-in-particular-custom-post-types-wordpress */
			
			add_action( 'admin_menu', array( $this, 'disable_addnew_hide_submenu' ) );
			add_action( 'admin_head', array( $this, 'disable_addnew_hide_button' ) );
			add_action( 'admin_menu', array( $this, 'disable_addnew_permissions_redirect' ) );
			add_action( 'admin_init', array( $this, 'disable_addnew_show_notice' ) );
			
		}
				public function disable_addnew_hide_submenu() {
					global $submenu;
					unset($submenu['edit.php?post_type='. $this->type][10]);
				}
				
				public function disable_addnew_hide_button() {
					global $pagenow;
					if ( is_admin() ) {
				  	if ( $pagenow == 'edit.php' && $_GET['post_type'] == $this->type ) {
				      echo "<style type=\"text/css\">.add-new-h2{display: none;}</style>";
						}  
					}
				}
				
				public function disable_addnew_permissions_redirect() {
					$result = stripos( $_SERVER['REQUEST_URI'], 'post-new.php?post_type='. $this->type );
					if ( $result !== false ) {
						wp_redirect( get_option('siteurl') . '/wp-admin/index.php?'. $this->type . '_addnew_disabled=true' );
					}
				}
				
				public function disable_addnew_show_notice() {
					if ( $_GET[$this->type . '_addnew_disabled'] ) {
						add_action( 'admin_notices', array( $this, 'disable_addnew_admin_notice' ) );
					}
				}
				
				public function disable_addnew_admin_notice() {
					// use the class "error" for red notices, and "update" for yellow notices
					echo "<div id='permissions-warning' class='error fade'><p><strong>".__('Adding new ' . $this->plural_name . ' is currently disabled.')."</strong></p></div>";
				}

	
	} // end CustomObject class
	
	class MetaBox {
		
		public $options;
		public $type;
		private static $nonce_name = 'wp-custom-object-nonce';
		
		function __construct( $options = array(), $type ) {
			
			if ( !is_array( $options ) ) $options = array( $options );
			
			// Save the $options to the class so functions can use it later
			$this->options = $options;
			foreach ( $this->options as $k => $v ) {
				$this->$k = $v;
			}
			
			if ( !isset( $this->type ) ) $this->type = $type;
			
			// Set some defaults if params weren't passed
			$this->setup_options();		
			
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
			
		}
		
		function setup_options() {
		
			$allowed_context = array(
				'normal', 'advanced', 'side'
			);
			
			$allowed_priority = array(
				'high', 'core', 'default', 'low'
			);
			
			$this->id = isset( $this->id ) && !empty( $this->id ) ? $this->id : "metabox-" . time();
			
			$this->title = isset( $this->title ) && !empty( $this->title ) ? $this->title : ucwords( $this->type ) . " Meta Box";
			
			$this->context = isset( $this->context ) && in_array( $this->context, $allowed_context ) ? $this->context : 'advanced';
			
			$this->priority = isset( $this->priority ) && in_array( $this->priority, $allowed_priority ) ? $this->priority : 'default';
		
		}
		
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
		  $nonce_name = 'wp_custom_object_nonce';
		  $nonce_action = 'save_metabox_data';
		 
		 // wp_verify_nonce refused to work here, so I done busted it for now...
		
		  // TODO: use the array of fields from the wpco_metabox_content TODO item, and save each one individually
		  
		  if ( is_array( $this->fields ) ) {
		  	
		  	//update_post_meta( $id, '_super_test2', $this->fields );
		  	
		  	foreach ( $this->fields as $field ) {
					$fieldname = $field['name'];
		  		$varname = "_".$fieldname;
		  		if ( isset( $_POST[$fieldname] ) ) {
		  			update_post_meta( $id, $varname, $_POST[$fieldname] );
		  		}
		  	}
		  
		  }
		  
		} // end wpco_save_meta_box() method
		
		
		function wpco_metabox_content() {
			
			global $post;
			$id = get_the_ID();
			
			// Generate a semi-secure nonce name and action
			$nonce_action = 'save_metabox_data';
			$nonce_name = 'wp_custom_object_nonce';
			wp_nonce_field( $nonce_action, $nonce_name );
			
			if ( !!$this->description ) {
				echo "<p>" . $this->description . "</p>";
			}
			
			echo "<div style='padding: 10px 0;'>";
			
			if ( !$this->fields || !is_array( $this->fields ) ) { 
				echo "No content for this meta box.";
				return;
			}
			
			foreach ( $this->fields as $field ) {
			
				$name 	= !!$field['name'] 	? $field['name'] 	: false;
				if ( !$name ) continue;
				$label 	= !!$field['label'] ? $field['label'] : false;
				$type 	= !!$field['type'] 	? $field['type'] 	: 'text';
				$meta_value = get_post_meta( $id, "_" . $name, true );
				
				$value = $field['value'];
				if ( !!$meta_value ) {
					$value = $meta_value;
				}
				elseif ( !!$_POST[$name] ) { 
					$value = $_POST[$name]; 
				}
				
				switch ( $type ) {
					
					case false:
						echo "Field type not set.";
						break;
					
					case 'textarea':
						echo "<label for='{$name}' style='display: block; margin-bottom: 5px;'>{$label}</label>";
						echo "<textarea class='widefat' name='{$name}' id='{$name}'>{$value}</textarea>";
						break;
						
					case 'checkbox':
						echo "<p style='margin: 15px 0;'><input";
						if ( !!$value ) echo " checked";
						echo " type='checkbox' name='{$name}' id='{$name}' value='{$value}' />";
						echo "<label for='{$name}' style='margin-left: 8px;'>{$label}</label></p>";
						break;
						
					case 'radio':
						echo "<div style='margin: 15px 0;'>";
						echo "<p style='margin-bottom: 5px;'><label for='{$name}'>{$label}</label></p>";
						$i = 1;
						foreach ( $field['options'] as $opt ) {
							$opt_label = $opt[0];
							$opt_value = !!$opt[1] ? $opt[1] : $opt_label;
							echo "<p><input";
							if ( $opt_value == $value ) echo " checked";
							echo " type='radio' name='{$name}' id='{$name}-{$i}' value='{$opt_value}' />";
							echo " <label for='{$name}-{$i}'>{$opt_label}</label></p>";
							$i++;
						}
						echo "</div>";
						break;
					
					case 'select':
						if ( !!$label ) { echo "<label for='{$name}'>{$label}</label>"; }
						echo "<select class='widefat' style='margin: 15px 0;' name='{$name}' id='{$name}'>";
						echo "<option value=''>--select one--</option>";
						foreach ( $field['options'] as $opt ) {
							$opt_label = $opt[0];
							$opt_value = !!$opt[1] ? $opt[1] : $opt_label;
							echo "<option value='{$opt_value}'";
							if ( $value == $opt_value ) echo " selected";
							echo ">{$opt_label}</option>";
						}
						echo "</select>";
						break;
											
					case 'upload':
						add_action( 'admin_print_scripts', function () {
							wp_enqueue_script( 'media-upload' );
							wp_enqueue_script( 'thickbox' );
							wp_enqueue_script( 'enable-uploader' );
						});
						add_action( 'admin_print_styles', function () {
							wp_enqueue_style( 'thickbox' );
						});
						
						echo "<div class='upload'><input class='widefat upload-input' type='text' name='{$name}' id='{$name}' value='{$value}' />";
						echo "<input type='submit' class='upload-button' value='Choose File' />";
						echo "</div>";
						break;
						
					default:
						echo "<label for='{$name}' style='display: block; margin-bottom: 5px;'>{$label}</label>";
						echo "<input class='widefat' style='margin-bottom: 15px;' type='text' name='{$name}' id='{$name}'";
						if ( $value ) echo " value='{$value}'";
						echo " />";
						break;
						
				}
				
			}
			
			echo "</div>";
					
		} // end wpco_metabox_content() method
		
		
	}
	
	function wpco_register_scripts() {
		wp_register_script( 'enable-uploader', WPCUSTOMOBJECTS_PLUGIN_URL . '/enable-uploader.js', array( 'jquery' ) );
	}
	add_action( 'init', 'wpco_register_scripts' );
