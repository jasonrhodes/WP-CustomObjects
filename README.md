# WP-CustomObjects For Developers (A WordPress Plugin)

First things first: I call them "custom objects" instead of "custom post types". Cause, you know, they're _not posts_.

This is a custom object class to help you build a basic custom object really fast. It's built for developers who are building CMS-type applications on the WordPress platform.

PS _I've submitted to get this in the WordPress SVN repo, too. We'll see._

## How to Use This Plugin

Download, install, and activate the plugin, which will give you access to a few new classes.

The main class is CustomObject, so you can create a new custom object by calling:

```php
<?php
	# Create new Super Villain object type
  $co_super_villains = new CustomObject( 'super villain' );
?>
```

Easy, right?

But in case you want to control more than just _the name_ of your new object, you can pass the object a set of options and labels, too.

### Setting options for your object

Every argument listed in the WordPress Codex for register_post_type() applies here, spelled the same way. The only argument you can't use here is "labels", since they get passed in a separate labels array.

http://codex.wordpress.org/Function_Reference/register_post_type#Arguments

For example:

```php
<?php
	$villain_options = array(
		'public' => true, # shortcut to enable a bunch of other arguments for public object types
		'menu_position' => 30, # controlling where the object's menus appear in the Admin Menu
		'supports' => array( 'title', 'editor', 'thumbnail' ) # what appears on the new/edit page
	);
?>
```

### Setting labels for your object

This is the labels argument, separated into it's own array for clarity. I almost always leave this as a blank array and let the class fill in default values. The defaults are just what you would expect for each scenario.

_Note: The WordPress defaults use "post" in almost every label, but our defaults incorporate your new object type's name instead._

If you have a couple of special labels you'd like to designate, just do this:

```php
<?php
	$labels = array(
		'new_item' => "New Horrible Terror Striking Villain Person With Evil Moustache",
		'not_found' => "We couldn't find any SUPER villains, only just regular villains"
	);
?>
```

Your two new labels would be applied, but the plugin defaults would take care of the rest.

Just be sure to include the options and labels arrays in your original function call:
  
```php
<?php
	# Create new Super Villain object type
	$my_object = new CustomObject( 'super villain', $villain_options, $villain_labels );
```

## Metaboxes

Maybe you've just gotten a new custom object set up and you feel like a goddess among men. Then you remember that the new object is probably pretty much the same as a post, and you just wasted your time on it, and why did you even start building this site in the first place, and did your father ever really love you?

Calm down. You can use this sweet plugin to create metaboxes, too. That's why we saved the object type in a variable, remember? Time to put that thing to work.

### Using $object->setup_metabox()

To set up a generic metabox for your new object type, use the variable you set when you created the new CustomObject. In our example, we used $super_villain

```php
<?php 
  $super_villain->setup_metabox( $villain_mb_options ); 
?>
```

Just like before, we pass this function an options array. Here are the available options:

1. **ID** (string)

    The HTML id value to assign to this metabox div in the admin section
    
    Default: 'metabox-' . time()

1. **title** (string)

    Title value across the top of the metabox
    
    Default: Custom Type . "Metabox"

1. **context** (string)
options: normal | advanced | side

    Determines in which section of the page the box will appear

    * Normal = Right below the content.
    * Advanced = Further below the Normal section.
    * Side = in the narrower side section, somewhere under the "Publish/Update" box

1. **priority** (string)
options: high | core | default | low

    Determines where in the section the box will appear

1. **description** (string)

    Short description appears under the title of the box.

1. **fields** (array)

    Each item in this array is another array which includes:

    * name
    * label
    * type
    * options (if needed for something like a select box or radio group)

## Future plans

For my own sake, I'll be adding the ability to add custom taxonomies to a new object type, and I want to include static versions of the metabox and taxonomy methods so I can use them to build stuff for posts and pages, too.

If you have _any_ suggestions, leave them in the [Issues](/jasonrhodes/WP-CustomObjects/issues/) section here on GitHub.

**Oh, and hit me up on Twitter if you have any questions. [@rhodesjason](http://twitter.com/rhodesjason)**

## Summary

If all of that isn't enough to get you started, there's also an ```example-functions.php``` file in the plugin folder that walks you through another example.

Have fun building custom objects and conquering the universe.
