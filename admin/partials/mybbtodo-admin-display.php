<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap task-wrap">
   <h2>General Settings</h2>

   <form method="post" action="options.php">
	    <?php settings_fields( 'mybbtodo-group' ); ?>
	    <?php do_settings_sections( 'mybb_todo_settings' ); ?>

 		<p>Use this shortcode : [frontendtodo]</p>
     <?php submit_button(); ?>
    </form>
</div>