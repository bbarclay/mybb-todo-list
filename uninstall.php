<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

//Remove all option related to the plugin
delete_option('mytask_option_name');
delete_option('completedtask_option_name');
delete_option('suggestedtask_option_name');
delete_option('addtask_option_name');
delete_option('completedtask_msg');
delete_option('addtask_msg');
delete_option('notaskname_msg');
delete_option('notaskcontent_msg');
delete_option('suggested_task_completed');
delete_option('empty_task_suggested');
delete_option('empty_task_completed');
delete_option('notify_completed_task');

//Remove all post meta related to the plugin
delete_post_meta_by_key( '_user_id' );
delete_post_meta_by_key( 'status' );
delete_post_meta_by_key( '_suggest_task' );
delete_post_meta_by_key( 'date_completed' );
delete_post_meta_by_key( 'removed_by_user_id' );
delete_post_meta_by_key( 'suggested_id' );


// Using where formatting.
global $wpdb;
$posts_table = $wpdb->posts;

$wpdb->query("DELETE FROM " . $posts_table . " WHERE post_type = 'mybb_todo'"); 
