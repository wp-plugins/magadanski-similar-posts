=== M Similar Posts ===
Contributors: magadanski_uchen
Donate link: http://buy-me-a-beer.magadanski.com/project/magadanski-similar-posts/
Tags: similar, posts, category
Requires at least: 3.0
Tested up to: 3.8
Stable tag: 1.0.6
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin lists similar to the current post based on the number of common categories.

== Description ==

This plugin adds a widget to WordPress that will list similar posts to the one being reviewed. The widget will only be rendered on a post single page, as otherwise it would not be relevant.

A shortcode is also available -- [magadanski-similar-posts]. There are several attributes available for the shortcode:

1. ID -- (_integer_) the ID of the post you'd like to get similar posts for. Default value: the current post ID.
1. post_type -- (_string_) the post type you'd like to query. Default value: "post".
1. taxonomy -- (_string_) the taxonomy based on which you'd like to get similar items. Default value: "category".
1. limit -- (_integer_) a maximum number of results you'd like to receive. Default value: 5.

The similarity is considered based on the number of common categories for this posts and the rest of the posts in your blog.

== Installation ==

Download and activate the plugin. Go to Widgets and add the Similar Posts widget to a sidebar of your choosing.

== Frequently Asked Questions ==

= How does the plugin determine whether two posts are similar? =

It checks the categories assigned to this and other posts. The most similar post is considered the one with the most common categories.

= Does this work for tags too? =

Yes, when inserting the widget you are allowed to chose between categories, tags or any custom taxonomy you have.

= Does this work for custom post types? =

Yes, this works for pages, posts or any custom post type you have.

= Does this check the post's content too? =

No, currently similarity is calculated only in by shared taxonomy terms (categories, tags, etc).

== Screenshots ==

1. The options for the plugin's widget.

== Changelog ==

= 1.0.6 =
Fix for cause preventing widget from rendering.

= 1.0.5 =
 * Added inline documentation and some code comments.
 * Plugin name rebranded from "Magadanski Similar Posts" to "M Similar Posts"
 * i18n, filters and function prefixes updated to match rebranding -- "msp" is used instead of "simposts"

The folder for the plugin files has been kept to "magadanski-similar-posts" to prevent plugin deactivation upon update.

Hopefully the available custom filters have not been widely spread, so this change will not affect any custom code.

= 1.0.4 =
Updated readme.

= 1.0.3 =
Added [magadanski-similar-posts] shortcode.

= 1.0.2 =
Added proper screenshots

= 1.0.1 =
Updated readme.

= 1.0 =
Plugin was released.