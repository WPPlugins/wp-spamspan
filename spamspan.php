<?php
/*
Plugin Name: SpamSpan
Plugin URI: http://wordpress.org/extend/plugins/wp-spamspan/
Description: Implements <a href="http://www.spamspan.com/">Spam Span</a> protection (level 3) for email addresses that appear in text. With javascript disabled, the email addresses appear obfuscated. With javascript enabled, they appear as clickable links.
Version: 1.2
Author: Chip Rosenthal
Author URI: http://www.unicom.com/
License: http://unlicense.org/
*/

/*
 * This is free and unencumbered software released into the public domain.
 * 
 * Anyone is free to copy, modify, publish, use, compile, sell, or
 * distribute this software, either in source code form or as a compiled
 * binary, for any purpose, commercial or non-commercial, and by any
 * means.
 *
 * In jurisdictions that recognize copyright laws, the author or authors
 * of this software dedicate any and all copyright interest in the
 * software to the public domain. We make this dedication for the benefit
 * of the public at large and to the detriment of our heirs and
 * successors. We intend this dedication to be an overt act of
 * relinquishment in perpetuity of all present and future rights to this
 * software under copyright law.
 *
 * IMPORTANT - This license does not apply to spamspan.js, which is
 * licensed separately.
 */

/*****************************************************************************
 *
 * Initialization
 *
 ****************************************************************************/

require_once("lib/SpamSpan.php");

# Ensure the "spamspan_priority" option setting exists.
define('SPAMSPAN_PRIORITY_DEFAULT', 9);
add_option('spamspan_priority', SPAMSPAN_PRIORITY_DEFAULT);

add_action('init', 'spamspan_init');

function spamspan_init() {
	wp_enqueue_script('spamspan', plugins_url('spamspan.js', __FILE__));
}


/*****************************************************************************
 *
 * Rendering Hooks
 *
 ****************************************************************************/

$prio = intval(get_option('spamspan_priority'));
if ($prio < 1 || $prio > 9999) {
	$prio = SPAMSPAN_PRIORITY_DEFAULT;
	update_option('spamspan_priority', $prio);
}

add_filter('the_content', 'SpamSpan::filter_html', $prio);
add_filter('the_content_rss', 'SpamSpan::filter_html', $prio);

add_filter('the_excerpt', 'SpamSpan::filter_html', $prio);
add_filter('the_excerpt_rss', 'SpamSpan::filter_html', $prio);

add_filter('the_author_email', 'SpamSpan::filter_text', $prio);

add_filter('comment_text', 'SpamSpan::filter_html', $prio);
add_filter('comment_text_rss', 'SpamSpan::filter_html', $prio);

add_filter('comment_excerpt', 'SpamSpan::filter_html', $prio);

add_filter('comment_author_email', 'SpamSpan::filter_text', $prio);


/*****************************************************************************
 *
 * Administration
 *
 ****************************************************************************/

add_action('admin_init', 'spamspan_admin_init');
add_action('admin_menu', 'spamspan_menu');

function spamspan_admin_init() {
	register_setting('spamspan_options', 'spamspan_priority', 'intval');
}

function spamspan_menu() {
	add_submenu_page('plugins.php', 'Spam Span Options', 'Spam Span', 'manage_options', 'spamspan-menu', 'spamspan_options');
}

function spamspan_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
?>

	<div class="wrap">
	<h2>Spam Span Options</h2>
	<form method="post" action="options.php">
	<?php settings_fields('spamspan_options'); ?>
	<table class="form-table">
	<tr valign="top">
	<th scope="row">Priority</th>
	<td><input type="text" name="spamspan_priority" value="<?php echo get_option('spamspan_priority'); ?>" /></td>
	<td class="description">Set the numeric priority of this filter. Lower priority filters run earlier. The default filter priority is 10. The standard value for "Spam Span" is 9. Adjusts this up or down only if you run into conflicts with other filters.</td>
	</table>
	<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
	</form>
	</div>
	</div>

<?php
}

