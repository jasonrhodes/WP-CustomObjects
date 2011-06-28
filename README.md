## WP Custom Object Plugin

_Note: I use "custom object" to refer to what WordPress calls a "custom post type". That's a stupid name for it because it's not a post, and it's confusing._

This is a custom object class to help you build a basic custom object really fast. It's built for developers who are building CMS-type applications on the WordPress platform.

### How to Use This Plugin

Download, install, and activate the plugin, which will give you access to a few new classes.

The main class is CustomObject, so you can create a new custom object by calling:

```php
$my_object = new CustomObject( 'super villain' );
```

But since you'll probably want to control a lot more than just the name of your new object, you can pass the constructor method two additional arrays, one for "options" and one for "labels".

#### Options

