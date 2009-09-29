<?php
/*
Plugin Name: Fix Disappearing Content in Themes
Plugin URI: http://www.wordpress.org/extend/plugins/hybrid-bugfix/
Description: Fix Disappearing Content in Themes plugin fixes a bug found in some themes such as Hybrid based Themes where content and features added by plugins do not appear in the page's body area.
Version: 0.1.1
Author: Angelo Mandato
Author URI: http://angelo.mandato.com/

License: GPL (http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt)
*/

add_action('loop_start', 		'hybrid_bugfix_loop_start' );

function hybrid_bugfix_loop_start()
{
	remove_filter('get_the_excerpt', 'hybrid_bugfix_get_the_excerpt');
	// we need to re-add all get_the_excerpt filters to the filters array...
	hybrid_bugfix_restore_filters('get_the_excerpt');
}

add_action('init', 'hybrid_bugfix_init', 10000000);

function hybrid_bugfix_init()
{
	// We need to remove all of the get_the_excerpt filter calls
	hybrid_bugfix_backup_filters('get_the_excerpt');
	
	add_filter('get_the_excerpt', 'hybrid_bugfix_get_the_excerpt');
}

function hybrid_bugfix_get_the_excerpt($text)
{
	if( $text == '' )
	{
		global $post;
		$text = $post->post_content;

		$text = strip_shortcodes( $text );
		$text = str_replace(']]>', ']]&gt;', $text);
		$text = strip_tags($text);
		$excerpt_length = apply_filters('excerpt_length', 55);
		$words = explode(' ', $text, $excerpt_length + 1);
		if (count($words) > $excerpt_length) {
			array_pop($words);
			$text = implode(' ', $words);
		}
	}
	return $text;
}

function hybrid_bugfix_backup_filters($tag)
{
	global $wp_filter, $merged_filters, $g_hybrid_bugfix_filter_backup;
	
	if( isset($wp_filter[$tag]) && count($wp_filter[$tag]) > 0 )
	{
		$g_hybrid_bugfix_filter_backup[$tag] = $wp_filter[$tag];
		unset($wp_filter[$tag]);

		// We need WordPress to re-sort the filters so they are executed in the correct priority
		if( isset($merged_filters[$tag]) )
			unset($merged_filters[$tag]);
	}
}

function hybrid_bugfix_restore_filters($tag)
{
	global $wp_filter, $merged_filters, $g_hybrid_bugfix_filter_backup;
	
	if( isset($g_hybrid_bugfix_filter_backup[$tag]) )
	{
		$wp_filter[$tag] = $g_hybrid_bugfix_filter_backup[$tag];
		unset($g_hybrid_bugfix_filter_backup[$tag]);
		
		// We need WordPress to re-sort the filters so they are executed in the correct priority
		if( isset($merged_filters[$tag]) )
			unset($merged_filters[$tag]);
		return true; // Filters were restored
	}
	return false; // no filters restored
}

?>