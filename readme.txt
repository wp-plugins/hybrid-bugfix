=== Fix Disappearing Content in Themes ===
Contributors: Angelo Mandato
Donate link:
Tags: hybrid, theme, hybrid theme, bug, fix, disappearing, content, disappear, missing, lost
Requires at least: 2.0
Tested up to: 2.8.4
Stable tag: 0.1.1


== Description ==
Fix Disappearing Content in Themes plugin fixes a bug found in some themes such as Hybrid based Themes where content and features added by plugins do not appear in the page's body area.

If your site does not display content/features added by plugins, this plugin may fix the problem.

This plugin prevents plugins from adding content to the page until after the `have_posts()` call is made by the theme.

 = Bug Details =
The problem occurs with themes that call `the_excerpt()`, `get_the_excerpt()`, `the_content()`, or `get_the_content()` functions in places such as in the HTML head area, header area of the page, or in a sidebar to display a short description of the page's content. Because the function call is made outside of the main content area, plugins will add their content prematurely and thus not appear in the page.

= Technical Explanation of Fix =
This plugin removes the hooks to `get_the_excerpt()` during the init action and restores the hooks during the `loop_start` action. This is so the `get_the_excerpt` calls made outside of the content loop do not call the plugin filters for `get_the_excerpt`. A temporary `get_the_excerpt` hook is added between these actions by this plugin which returns a better formatted excerpt for the theme to use.

The excerpt provided to the theme is better suited for excerpt use either in the page body or as content in a header meta description tag. The logic in this plugin removes the [...] which is added by WordPress when there are more than 55 words in the excerpt.

== Frequently Asked Questions ==

 = Why the plugin? =
 The developer of the Hybrid theme system did not agree with me that he should not be calling `get_the_excerpt()` outside of the content loop. The theme developer was un-cooperative and in so many words told me he could care less about my plugin working with the Hybrid theme. Left with solving the problem in my own plugin, I coded the solution found in this plugin. After testing, I discovered this plugin also fixed the same problem with other plugins and the Hybird Theme system. Since this code could fix problems for other plugins as well as mine, I decided to release it as a separate plugin.
 
 = Why is this a bug in themes? =
 WordPress.org clearly states that calls to `the_excerpt()` and `the_content()` must be within the loop. A call to `get_the_excerpt()` relies upon calling `get_the_content()` as well as applies `the_content` filter when the excerpt of a blog post is empty. All of this logic takes place in the `wp_trim_excerpt()` function in `wp-includes/formatting.php`. Plugins that use filters for `get_the_excerpt`, `the_excerpt`, `get_the_content` and    `the_content` can be negatively impacted by themes that incorrectly call these functions outside of the content loop.

The excerpt documentation: [the_excerpt](http://codex.wordpress.org/Template_Tags/the_excerpt)

 = I developed a theme where I call `get_the_excerpt()` outside of the content loop. Is there another way I can get the excerpt without messing up the output of plugins? =
 
Yes. First, do not call `the_excerpt()`, `get_the_excerpt()`, `the_content()` or `get_the_content()` outside of the content loop. If you do need the excerpt, the most efficient way to get the excerpt or the content is to obtain it directly from the global $post object. The advantage of obtaining the content or the excerpt from the global post object is that you do not have to worry about WordPress cutting the string length at 55 words, adding an undesired [...] to the end of the excerpt, and you can brag that your theme is efficient in that it does not require WordPress to iterate through additional filters which can be process intensive and use precious system memory.

Below is an example function you can add to your theme's functions.php and call when you want to obtain the excerpt outside of the content loop.

`
function mythemename_get_the_excerpt($excerpt_word_count=55)
{
    global $post;
    $excerpt = $post->post_excerpt;
    if( $excerpt == '' )
    {
        $excerpt = $post->post_content;
        $excerpt = strip_shortcodes( $excerpt );
        $excerpt = str_replace(']]>', ']]&gt;', $excerpt);
        $excerpt = strip_tags($excerpt);
    }
    $words = explode(' ', $excerpt, $excerpt_word_count + 1);
    if (count($words) > $excerpt_length) {
        array_pop($words);
        $excerpt = implode(' ', $words);
    }
    return $excerpt;
}
`

The description may be obtained simply by referring to the $post object.

`
 echo $post->post_content;
`

== Installation ==
1. Copy the entire directory from the downloaded zip file into the /wp-content/plugins/ folder.
2. Activate the "Fix Disappearing Content in Themes" plugin in the Plugin Management page.
3. Test your site that everything still works as expected.


== Screenshots ==
No screenshots needed.


== Changelog ==

= 0.1.1 =
* Released on 09/28/2009
* Renamed the plugin to 'Fix Disappearing Content in Themes' plugin since this plugin fixes the bug in other themes as well as the Hybrid Theme system.

= 0.1 =
* Released on 07/18/2009
* Initial release of Hybrid Theme Bugfix plugin


== Contributors ==
Angelo Mandato [The Blog of Angelo](http://angelo.mandato.com) - Plugin author

