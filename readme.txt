=== Hybrid Theme Bugfix Plugin ===
Contributors: Angelo Mandato
Tags: hybrid, theme, bug, fix
Requires at least: 2.0
Tested up to: 2.8.1
Stable tag: 0.1


== Description ==
Fixes a bug in the Hybrid Theme that causes problems for some plugins. The bug is with the page meta description logic in the Hybrid theme and can be reproduced by going to the Theme > Hybrid Settings and turning on/off the "Use the excerpt on single posts for your meta description?" setting. The bug is caused by the developer calling the `get_the_excerpt()` function outside of the content loop. This plugin prevents the get_the_excerpt() call by the Hybrid theme to negatively impact the performance of your blog as well as prevent plugins from filtering the content when they are not supposed to. It also uses an improved description for the Hybrid meta description tag, the description does not end with a [...] if there are more than 55 words in the blog post.

 = Details =

The developer of the Hybrid Theme is insistent that his use of the `get_the_excerpt()` outside of the post loop is acceptable. WordPress.org clearly states that calls to `the_excerpt()` and `the_content()` must be within the loop. A call to `get_the_excerpt()` relies upon calling `get_the_content()` as well as applies `the_content` filter when the excerpt of a blog post is empty. All of this logic takes place in the wp_trim_excerpt function in `wp-includes/formatting.php`. Plugins that use filters for `get_the_excerpt`, `the_excerpt`, `get_the_content` and `the_content` can be negatively impacted by the Hybrid theme.

The excerpt documentation: [the_excerpt](http://codex.wordpress.org/Template_Tags/the_excerpt)

The Hybrid developer insists that plugin developers should call the in_the_loop() to double check that the excerpt/content filter is inside the page's content loop. Though this fixes the problem at the plugin level, it would require already coded plugins to accommodate the code in the Hybrid theme. This plugin fixes the problem for plugins that are no longer actively developed or are unaware that they need to add code to their plugin to accommodate the Hybrid theme.

This plugin removes the hooks to get_the_excerpt during the init action and restores the hooks during the loop_start action. This is so the `get_the_excerpt` call that the Hybrid theme makes during the wp_head action does not call the plugin filters for `get_the_excerpt`. A temporary `get_the_excerpt` hook is added between these actions by this plugin which returns a better formatted excerpt for the Hybrid theme to use.

The excerpt provided to the Hybrid theme is better suited for the meta description tag. The logic in this plugin removes the [...] which is added by the `wp_trim_excerpt` function when there are more than 55 words in the excerpt. The additional [...] to the end of a meta description serves no purpose for SEO.

 
== Frequently Asked Questions ==

 = Why the plugin? =
 The developer of the Hybrid theme system is insistent that there is no bug with how his plugin obtains the excerpt for the meta description tag. In so many words the Hybrid theme developer told me he could care less about my plugin working with the Hybrid theme. Since he will not implement a fix, I've written this plugin to fix the bug for anyone having the same problem with plugins and their Hybrid theme.
 
 If/when the Hybrid Theme developer implements the patch (or one like it) documented below then this plugin will become obsolete.
 
 = What's the Patch for the Hybrid Theme? =
 
 To fix the bug in the Hybrid theme, edit the `library/functions/framework.php` file with your favorite text editor and go to line 185 (line number may change with future releases of the Hybird theme).

Replace:

`		$meta_desc = get_the_excerpt();`

With:

`
		$meta_desc = $post->post_excerpt;
		if( $meta_desc == '' )
		{
			$meta_desc = $post->post_content;

			$meta_desc = strip_shortcodes( $meta_desc );
			$meta_desc = str_replace(']]>', ']]&gt;', $meta_desc);
			$meta_desc = strip_tags($meta_desc);
			$excerpt_length = apply_filters('excerpt_length', 55);
			$words = explode(' ', $meta_desc, $excerpt_length + 1);
			if (count($words) > $excerpt_length) {
				array_pop($words);
				$meta_desc = implode(' ', $words);
			}
		}
`
You no longer need this plugin if you implement this patch to your Hybrid theme.


== Installation ==
1. Copy the entire directory from the downloaded zip file into the /wp-content/plugins/ folder.
2. Activate the "Hybrid Theme Bugfix" plugin in the Plugin Management page.
3. Test your site that everything still works as expected.


== Screenshots ==
No screenshots needed.


== Changelog ==

0.1 released on 07/18/2009
Initial release of Hybrid Theme Bugfix plugin


== Contributors ==
Angelo Mandato [The Blog of Angelo](http://angelo.mandato.com) - Plugin author

