<?php
/**
 * Generate Form Template
 *
 * 
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/public
 * @author     Your Name <email@example.com>
 */


Class Mybb_Template {

	protected $error;

	private static $userID;

	public function __construct() {


	}


	public static function get_todos() {

 		global $wpdb;



		$current_user = wp_get_current_user();
		$id = $current_user->ID;
		$meta_key = 'status';
		$meta_value = 'pending';
		$post_type = 'mybb_todo';

		$query = $wpdb->get_results(  

			$wpdb->prepare("

				 SELECT 	posts.* 
				 FROM 		$wpdb->posts posts
				 LEFT JOIN  $wpdb->postmeta meta
				 	   		ON posts.ID = meta.post_id
				 WHERE      meta.meta_key = %s
				            AND  meta.meta_value = %s
				            AND posts.post_type = %s
				            AND posts.post_author = %s
				 ORDER BY   posts.menu_order, posts.ID      

				", $meta_key, $meta_value, $post_type, $id )

			);

		//$query = new WP_Query($args);
		return $query;
	}
	public static function get_completedTodos() {

		$user_id = get_current_user_id();

		$args = array(
				 'author' => $user_id,
				 'post_type'   => 'mybb_todo',
				 'post_status' => 'publish',
				 'meta_key' => 'status',                   
			     'meta_value' => 'completed',                               
			     'meta_compare' => '=', 
				 'orderby' => 'menu_order ID',
				 'order'   => 'ASC',	
				 'posts_per_page' => -1,


		);

		$query = new WP_Query($args);


		return $query;

	}

	public static function get_suggestedTodos() {

		$args = array(
				 'post_type'   => 'mybb_todo',
				 'post_status' => 'publish',
				 'orderby' => 'menu_order ID',
				 'order'   => 'ASC',	
				 'posts_per_page' => -1,
				 'meta_query' => array(
				 	'relation' => 'AND',
				 			array(
						            'key' => '_suggest_task',
						            'value' => 'true',
						            'compare' => '='
						     )
				 			)

		);

		$query = new WP_Query($args);

		return $query;





	}

	public static function get_list_message() {

		$msg = get_option('addtask_msg');

		echo '<p class="notification-msg">' . $msg . '</p>';
	}

	public static function task_notification($task) {

		switch($task) {

			case "pending":
				$response = sanitize_text_field(get_option('addtask_msg') );
			   break;
			case "completed":
				$response = sanitize_text_field(get_option('empty_task_completed') );
			   break;

			case "suggested":
				$response = sanitize_text_field(get_option('empty_task_suggested') );
			   break;   

			default:
				$response = 'Hello';   
		}
		echo  '<p class="notification-msg">' . $response . '</p>';
	}

	private function getUserID()
    {
        $user_id = get_current_user_id();

        return $user_id;
    }
}