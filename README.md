# WP Custom Object Plugin

_Note: I use "custom object" to refer to what WordPress calls a "custom post type". That's a stupid name for it because it's not a post, and it's confusing._

This is a custom object class to help you build a basic custom object really fast. It's built for developers who are building CMS-type applications on the WordPress platform.

## How to Use This Plugin

Download, install, and activate the plugin, which will give you access to a few new classes.

The main class is CustomObject, so you can create a new custom object by calling:

```php
<?php
	# Create new Super Villain object type
  $my_object = new CustomObject( 'super villain' );
?>
```

Easy, right?

You'll probably want to control a lot more than just _the name_ of your new object, so you can pass the constructor method two additional arrays, one for "options" and one for "labels".

### Options Array

Every argument listed in the WordPress Codex for register_post_type() applies here, spelled the same way. The only argument you can't use here is "labels", since they get passed in a separate array.

http://codex.wordpress.org/Function_Reference/register_post_type#Arguments

For example:

```php
<?php
	$options = array(
		'public' => true, # shortcut to enable a bunch of other arguments for public object types
		'menu_position' => 30, # controlling where the object's menus appear in the Admin Menu
		'supports' => array( 'title', 'editor', 'thumbnail' ) # what appears on the new/edit page
	);
?>
```

### Labels Array

This is the labels argument, separated into it's own array for clarity. I almost always leave this as a blank array and let the class fill in default values. The defaults are just what you would expect for each scenario.

_Note: The WordPress defaults here always use "post" everywhere, so these defaults incorporate your new object type's name._

If you have special labels you'd like to assign, you can. Like this:

```php
<?php
	$labels = array(
		'new_item' => "New Totally Rad Amazing Object",
		'not_found' => "We couldn't find any of these at all and boy did we look and look."
	);
?>
```

Your two new labels would be applied, but the class's defaults would take care of the rest.

Then be sure to include those options arrays in your original function call:

```php
<?php
	# Create new Super Villain object type
	$my_object = new CustomObject( 'super villain', $options, $labels );
?>
```