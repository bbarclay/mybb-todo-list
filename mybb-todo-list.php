<?php 
/*
Plugin Name: Business Blueprint To Do
Version: 1.0
Description: My BB Todo List.
Author: July Cabigas
Author URI: my.businesslblueprint.com
Plugin URI: http://businesslblueprint.com.au
*/

/**
 * MyBB To-Do List Plugin Main File
 *
 * This plugin was based on the to-do plugin by Abstract Dimensions with a patch by WordPress by Example.
 * @package mybb-to-do-list
 * @version 1
 */

if ( ! defined( 'WPINC' ) ) {
	die('not allowed');
}


function activate_mybb_todo_list() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mybb-todo-activator.php';
	Mybb_Todo_Activator::activate();


}

function deactivate_mybb_todo_list() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mybb-todo-deactivator.php';
	Mybb_Todo_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_mybb_todo_list' );
register_deactivation_hook( __FILE__, 'deactivate_mybb_todo_list' );


require plugin_dir_path( __FILE__ ) . 'includes/class-mybb-todo.php';



function run_mybb_todo_list() {

	$plugin = new Mybb_Todo();
	$plugin->run();

}

run_mybb_todo_list();


