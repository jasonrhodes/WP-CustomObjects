<?php

/********************************************************
 * The following code would go into your theme's functions.php file,
 * or into a separate plugin file you create for yourself.
 *
 ********************************************************/

/* Check if our plugin is on. If it is, the custom object class will exist. */
if ( class_exists( 'CustomObject' ) ) :

/* 'Type' is the name for your new custom object.
 * It can be written as 'client groups' or 'client_groups', and
 * all the default labels and names will be set correctly.
 */
$co_type = 'client groups';

/* Other options can go in the $co_options array, using the same syntax found
 * in the Codex here: http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
 *
 * Note: I'm not including any labels, so the object inherits all default values. Most
 * of the time, that should be enough. If you need to adjust a label, create
 * something like 'add_new' => 'Add a really awesome new thing!' to the $co_labels array.
 */
$co_options = array( 'public' => true );
$co_labels = array();

/* This is the syntax for creating each custom object you create */
$client_groups = new CustomObject( $co_type, $co_options, $co_labels );

/* That's actually it. If you stop here, you'll have a fully functional
 * custom object that appears in your admin menu (if you have the options
 * set correctly, obviously).
 *
 * Most times, though, you want to customize the object a little further.
 * One way to customize is to add metaboxes to accept custom fields. You can
 * do this in a couple ways, even using this plugin. But if you're creating 
 * a metabox just for your new object and you're not doing anything too
 * fancy with it, you can run the $custom_object->setup_metabox() function.
 *
 * Here's how:
 */

/* In the metabox, you'll be accepting values in a form. You can set up the
 * form fields easily using the $mb_fields array. I'll include more information
 * on how these are setup later, but for now you can look at this example and
 * figure out how the different form fields are handled.
 */
$mb_fields = array(
	array(
		'name' => 'client_name',
		'label' => 'Client Name'
	),
	array(
		'name' => 'client_type',
		'label' => 'Choose a type: ',
		'type' => 'select',
		'options' => array(
			array( 'First type', 'first' ),
			array( 'Second type', 'second_type' )
		)
	),
	array(
		'name' => 'long-thing',
		'label' => "Write something longer here, I guess?",
		'type' => 'textarea'
	),
	array(
		'name' => 'my-checkbox1',
		'label' => 'Hope this layout is right',
		'type' => 'checkbox',
		'value' => 'layout-right'
	),
	array(
		'name' => 'my-radio-group',
		'label' => 'A set of radio buttons',
		'type' => 'radio',
		'options' => array(
			array( 'First radio', 'first-radio' ),
			array( 'Second button', 'second-button' ),
			array( 'The third', 'the-third' )
		)
	)
);

/* After setting up your fields, you create an $mb_options array for
 * all of the other options like ID, Title, Description, etc.
 *
 * Make sure you include your $mb_fields array here too.
 */
$mb_options = array(
	'id' => 'client-details-metabox',
	'title' => 'Client Details',
	'description' => 'Put some details about this client group here.',
	'context' => 'side',
	'priority' => 'low',
	'fields' => $mb_fields
);

/* One quick call to the setup_metabox method, on your new custom object,
 * and you will have a working metabox running. 
 */
$client_groups->setup_metabox( $mb_options );

/* If you would like to disable adding new objects for this object type,
 * you can use this function to take care of the multiple actions
 * needed to achieve this. 
 * Based on: http://minimalbugs.com/questions/how-to-disable-add-new-post-in-particular-custom-post-types-wordpress
 */
//$client_groups->disable_addnew();


/* DON'T ADD ANYMORE WP CUSTOM OBJECTS CALLS AFTER THIS */
/* Close that if statement when we checked if the class exists! */
endif; 

/* That's it for now! */