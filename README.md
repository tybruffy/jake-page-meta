Jake Page Meta
===================

This is a Wordpress plugin used to create custom page titles and descriptions.

It is designed as a super simple, developer friendly alternative to some of the more robust WordPress SEO Plugins.  It allows the user the ability to set a custom page title and description for any single post or page (or custom post.)  It will then update the title and descripton fields of the site to match.  Additionally, it will output Open Graph data for the following:

* Site Title
* Content Type (og:type)
* Page Title
* Page Description
* Page Locale

It is meant to be developer friendly, and therefore it does not clobber the output of the `wp_title` filter.  Rather, it uses that filter unless a title has been set for a given post.  

A caveat to that is that at some point in time, this plugin has to run that filter to set the page title.  So *after* this plugin runs, any other `wp_title` filters could clobber the `<title>` tag, but will fail to clobber the open graph title.  In an effort to avoid this, this plugin runs the `wp_title` filter with a priority of 100.  Any plugin or script that runs with a lower priority (1-99) will have it's return value used by this plugin.  

If yoru script or a plugin *has* to run with a priortity higher than 100, you can use the `JPM/title` filter.  It is a drop in replacement for the `wp_title` filter to set the Open Graph title.  It passes identical data as the `wp_title` filter.  An example:

```php
function my_title_changer($title, $seperator) {
	return $title . " $seperator " . get_blog_info("name");
}

apply_filter("wp_title", "my_title_changer", 200, 2);
apply_filter("JPM/title", "my_title_changer", 200, 2);
```

Notes
--------------

This plugin is also kept in the Wordpress plugin repository.  Both versions will be kept up to date, though this repo will be used for development and testing.