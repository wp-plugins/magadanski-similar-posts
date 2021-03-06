=== M Similar Posts ===
Contributors: magadanski_uchen
Donate link: http://buy-me-a-beer.magadanski.com/project/magadanski-similar-posts/
Tags: similar, posts, category
Requires at least: 3.8
Tested up to: 4.2.1
Stable tag: 1.2.1
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

No, currently similarity is calculated only by shared taxonomy terms (categories, tags, etc).

= Is the plugin offered in my language? =

The plugin is originally distributed in English and is also translated in Spanish and Bulgarian. In case you need it in some other language I would gladly accept your assistance in internationalizing it. There are only 6 sentences and terms that need to be translated for any language.

== Screenshots ==

1. The options for the plugin's widget.

== Changelog ==

= 1.2.1 =
Bugfix: added missing return statement for public functions.

This is an absolutely safe to install update -- it won't break any of the existing functionality and you are encouraged to do so.

= 1.2 =
Dev: The following filters have been added for some control over the generated list markup:

`msp_similar_posts_list_tag` -- by default this is `ul` but you can set it to `ol`.

There are two more similar filters for this: `msp_similar_posts_shortcode_tag` and `msp_similar_posts_widget_tag`. As you may guess by the name, those allow to set the tag for only either the shortcode markup or the widget markup.

These secondary filters are executed after the main `msp_similar_posts_list_tag`, so they can overwrite the value.

`msp_similar_posts_list_classes` -- this allows you to add/edit/remove classes for the list tag. By default only one class is present: "msp-list".

There are two secondary filters for this as well: `msp_similar_posts_shortcode_classes` and `msp_similar_posts_widget_classes`.

= 1.1.6 =
Dev: fix for notice -- missing "global" keyword for variable.

= 1.1.5 =
Fix for widget bug to allow support for custom post types.

= 1.1.4 =
Fix for improper regular expression for request SQL query modification.

= 1.1.3 =
Dev: added `msp_get_similar_posts()` shortcut function.

= 1.1.2 =
Bugfix for showing the plugin widget on pages if necessary.

Fix for other potential issues.

= 1.1.1 =
Internationalization updates.

Feel free to submit patches for updating Spanish translation or adding a new one.

= 1.1 =
Added [WPBakery Visual Composer](http://vc.wpbakery.com/) integration.

Fix: `set_similar_id` uses current post's ID only if `$id` argument is 0 AND `similar_id` property is 0 too.

Dev: externalized `get_post_types` and `get_taxonomies` methods for `Magadanski_Similar_Posts_Widget` widget as global helper functions prefixed `msp_`:

 * `msp_get_post_types`
 * `msp_get_taxonomies`

= 1.0.7 =
Tested compatibility with WordPress 3.8 -- no issues registered.

Added Spanish localization for the plugin thanks to Andrew Kurtis from [WebHostingHub](http://www.webhostinghub.com/)

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