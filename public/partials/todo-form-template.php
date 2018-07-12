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

		$args = array(
				 'author' => self::getuserID(),
				 'post_type'   => 'mybb_todo',
				 'post_status' => 'publish',
				 'orderby' => 'menu_order ID',
				 'order'   => 'ASC',	
				 'posts_per_page' => -1,
 				 'meta_query' => array(
 				 	'relation' => 'OR',
				        array(
				            'key' => 'status',
				            'compare' => 'NOT EXISTS'
				        ),
				        array(
				            'key' => 'status',
				            'value' => 'Pending',
				            'compare' => 'LIKE'
				        )
				    )

		);

		$query = new WP_Query($args);


		return $query;

	}
	public static function get_completedTodos() {

		$args = array(
				 'author' => self::getuserID(),
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
				 'meta_key' => '_suggest_task',                   
			     'meta_value' => 'true',                               
			     'meta_compare' => '=', 
				 'orderby' => 'menu_order ID',
				 'order'   => 'ASC',	
				 'posts_per_page' => -1,
				 'meta_query' => array(
				 	'relation' => 'OR',
				 		array(
								'relation' => 'AND',
								array(
							            'key' => '_user_id',
							            'compare' => 'NOT EXISTS'
							    ),
							    array(
							            'key' => 'removed_by_user_id',
							            'compare' => 'NOT EXISTS'
							    ),


				 		),
				 		array (
								'relation' => 'OR',
						 		array(
						            'key' => 'removed_by_user_id',
						            'value' => self::getuserID(),
						            'compare' => '!='
						        ),
						        array(
						            'key' => '_user_id',
						            'value' => self::getuserID(),
						            'compare' => '!='
						        )
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