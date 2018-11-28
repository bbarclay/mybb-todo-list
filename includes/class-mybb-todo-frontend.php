<?php


class Mybb_Todo_Frontend {


	private $plugin_name;
	private $version;
	private $task_completed;
	private $added_suggested_task;


	public function __construct( ) {

		$this->task_completed = sanitize_text_field( get_option('notify_completed_task') );
		$this->added_suggested_task = sanitize_text_field( get_option('added_suggested_task') );

		if( is_admin() ) {

			add_action("wp_ajax_nopriv_mybbtodo_addpost", array( $this, 'addpost' ));
	    	add_action("wp_ajax_mybbtodo_addpost", array( $this, 'addpost' ));

	    	add_action("wp_ajax_nopriv_mybbtodo_addsubpost", array( $this, 'addsubpost' ));
	    	add_action("wp_ajax_mybbtodo_addsubpost", array( $this, 'addsubpost' ));

			add_action("wp_ajax_nopriv_mybbtodo_editpost", array( $this, 'editpost' ));
	    	add_action("wp_ajax_mybbtodo_editpost", array( $this, 'editpost' ));

	        add_action("wp_ajax_nopriv_mybbtodo_updateMenuOrder", array( $this, 'updateMenuOrder' ));
	    	add_action("wp_ajax_mybbtodo_updateMenuOrder", array( $this, 'updateMenuOrder' ));

	    	add_action("wp_ajax_nopriv_mybbtodo_deletepost", array( $this, 'deletepost' ));
	    	add_action("wp_ajax_mybbtodo_deletepost", array( $this, 'deletepost' ));

	    	add_action("wp_ajax_nopriv_mybbtodo_completedpost", array( $this, 'completedpost' ));
	    	add_action("wp_ajax_mybbtodo_completedpost", array( $this, 'completedpost' ));

	    	add_action("wp_ajax_nopriv_mybbtodo_undoCompletedpost", array( $this, 'undoCompletedpost' ));
	    	add_action("wp_ajax_mybbtodo_undoCompletedpost", array( $this, 'undoCompletedpost' ));

	    	add_action("wp_ajax_nopriv_mybbtodo_getPostDetails", array( $this, 'getPostDetails' ));
	    	add_action("wp_ajax_mybbtodo_getPostDetails", array( $this, 'getPostDetails' ));

	    	add_action("wp_ajax_nopriv_mybbtodo_backSuggestedpost", array( $this, 'backSuggestedPost' ));
	    	add_action("wp_ajax_mybbtodo_backSuggestedpost", array( $this, 'backSuggestedPost' ));


	    	add_action("wp_ajax_nopriv_mybbtodo_moveSuggestedPost", array( $this, 'suggestedpost' ));
	    	add_action("wp_ajax_mybbtodo_moveSuggestedPost", array( $this, 'suggestedpost' ));


	    	add_action("wp_ajax_nopriv_mmybbtodo_removeSuggestedpost", array( $this, 'removeSuggestedpost' ));
	    	add_action("wp_ajax_mybbtodo_removeSuggestedpost", array( $this, 'removeSuggestedpost' ));

	    	add_action("wp_ajax_nopriv_mybbtodo_duedate", array( $this, 'duedate' ));
	    	add_action("wp_ajax_mybbtodo_duedate", array( $this, 'duedate' ));

	    	add_action("wp_ajax_nopriv_mybbtodo_getposts", array( $this, 'getposts' ));
	    	add_action("wp_ajax_mybbtodo_getposts", array( $this, 'getposts' ));


	    	add_action("wp_ajax_nopriv_getMember_posts", array( $this, 'getMember_posts' ));
	    	add_action("wp_ajax_getMember_posts", array( $this, 'getMember_posts' ));

	    	add_action("wp_ajax_nopriv_add_member_task", array( $this, 'add_member_task' ));
	    	add_action("wp_ajax_add_member_task", array( $this, 'add_member_task' ));


	    	add_action("wp_ajax_nopriv_mybbtodo_completedsubpost", array( $this, 'completedsubpost' ));
	    	add_action("wp_ajax_mybbtodo_completedsubpost", array( $this, 'completedsubpost' ));


	    	add_action("wp_ajax_nopriv_mybbtodo_deletesubtask", array( $this, 'deletesubtask' ));
	    	add_action("wp_ajax_mybbtodo_deletesubtask", array( $this, 'deletesubtask' ));

    	}
    	
	}

	/**
	*  When completing a subtask
	*
	*
	*/
	function completedsubpost() {

		if ( !is_user_logged_in() ){
				wp_send_json_error('you are not allowed');
		}
		$success_security = check_ajax_referer( 'subtask_post_nonce', 'security' );

	
				
	        $id = (int)$_POST['id'];

			$post_id = update_post_meta( $id, 'completed_subtask', 'completed');


			if(!$post_id) {
					wp_send_json_error(array(
	      			'message' => 'Error submitting post'
	    		));
			}
			else {
				wp_send_json_success(array(
	      			'message' => 'Subtask completed',
	      			'completed' => true

	    		));
			}
					
	}


	function getMember_posts() {

		if ( !is_user_logged_in() ){
			wp_send_json_error('you are not allowed');
		}
		
		$success_security = check_ajax_referer( 'todo_post_once', 'security' );

 		global $wpdb;

 		if(!isset($_POST['user_id'])) {
 			wp_send_json_error(array(
 					'message' => 'Invalid Id'
 				));
 		}

 		$author_id = (int)$_POST['user_id'];


		$query = $wpdb->get_results(

			$wpdb->prepare(
			"
					    SELECT $wpdb->posts.* 
					    FROM $wpdb->posts, $wpdb->postmeta
					    WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 
					    AND $wpdb->postmeta.meta_key = 'status' 
					    AND $wpdb->posts.post_status = 'publish' 
					    AND $wpdb->posts.post_type = 'mybb_todo'
					    AND $wpdb->posts.post_author = %s
					    ORDER BY $wpdb->posts.post_date DESC

			", $author_id)
			

			);

  

			if($query) {

				$task = array();
				$count = 0;

				foreach($query as $row) {
					$count++;

					$task[$count]['ID'] = $row->ID;
					$task[$count]['post_title'] = $row->post_title;
					$task[$count]['post_content'] = $row->post_content;
					$task[$count]['status'] = get_post_meta($row->ID, 'status', true);

					$has_consultant = get_post_meta($row->ID, 'consultant', true);

					if( $has_consultant ) {
						$task[$count]['is_consultant'] = 'c-added';
					}
					else {
						$task[$count]['is_consultant'] = '';
					} 

				}
				wp_send_json_success(array(
				      		'message' => 'retrieve member\'s post',
				      		'query' => $task,
				      		'total' => $count
				));
					
			}
			else {

				wp_send_json_error(array(
	      			'message' => 'No Entry Found',
	      			'query' => false,
	    		));
			}
			exit;
						
	}

	/**
	*  Get Subtask of the members 
	*  @return posted data
	*/
	function getposts () {

			 global $wpdb;

				if ( !is_user_logged_in() ){
						wp_send_json_error('you are not allowed');
				}


				if (!isset( $_POST['id'] ) ) {

						wp_send_json_error(array(
			      			'message' => 'Error checking post'
			    		));
				}
				$success_security = check_ajax_referer( 'todo_post_once', 'security' );
					
				

					$id =  intval( $_POST['id'] );



					$querystr = $wpdb->prepare("
					    SELECT 		$wpdb->posts.* 
					    FROM 		$wpdb->posts, $wpdb->postmeta
					    WHERE 		$wpdb->posts.ID = $wpdb->postmeta.post_id	
					    			AND $wpdb->postmeta.meta_key = 'subtask_from'  
					    			AND $wpdb->postmeta.meta_value = %d
					    			AND $wpdb->posts.post_status = 'publish' 
					    			AND $wpdb->posts.post_type = 'mybb_todo'
					    			ORDER BY $wpdb->posts.post_date DESC
					", $id);

					 $query = $wpdb->get_results($querystr, OBJECT);


					 $count = 0;
					 $arr = array();

					 foreach($query as $row) {
					 	$count++;

					 		$is_completed = get_post_meta( $row->ID, 'completed_subtask', true);



						 	$arr[$count]['ID'] = $row->ID;
						 	$arr[$count]['title'] = $row->post_title;
						 	$arr[$count]['content'] = $row->post_content;
						 	$arr[$count]['is_complete'] = $is_completed;

					 	

					 }
				

				if(!$query) {
						wp_send_json_error(array(
		      			'message' => 'No subtask',
		      			'has_subtask' => false,
		    		));
				}
				else {


					wp_send_json_success(array(
			      		'message' => 'successfully retrieve data',
			 			'has_subtask' => true,
			      		'tasks' => $arr,
			      		'id' => $id

		    		));
				}
				exit;

	}

	/**
	*  Adding task to BB member for consultant page  
	*  @return posted data
	*/
	function add_member_task() {

				if ( !is_user_logged_in() ){
						wp_send_json_error('you are not allowed');
				}

				if ( !isset( $_POST['post_nonce_field'] ) || ! wp_verify_nonce( $_POST['post_nonce_field'], 'task_post_nonce' ) ) {

					  wp_send_json_error(array(
			      			'message' => 'Nonce verification failed'
			    		));

				}  else {  

				if (!isset( $_POST['title'] ) || !isset( $_POST['content'] ) && !isset( $_POST['id'] )) {

						wp_send_json_error(array(
			      			'message' => 'Error checking post'
			    		));

				}
					$post_id    = (int)$_POST['id'];
		            $post_title =  sanitize_text_field($_POST['title']);
		            $post_link = sanitize_text_field( $_POST['link']);
		            $post_content = $_POST['content'];


						$allowedHtml = array(
						    'a' => array(
						    	'target' => array(),
						        'href' => array(),
						        'title' => array(),
						        
						    ),
						    'br' => array(),
						    'em' => array(),
						    'strong' => array(),
						);

     					$content = wp_kses($post_content, $allowed_html);
     					$link = wp_kses($post_link, $allowed_html);


                    $content = $this->convert_link($content);
					$task_link = $this->convert_link($link);

				    $post_information = array(
				    	'post_author' => $post_id,	
					    'post_title' => $post_title,
					    'post_content' => $content,
					    'post_type' => 'mybb_todo',
					    'post_status' => 'publish'
					);
					 
					$post_id = wp_insert_post( $post_information );

					//Add Pending
					update_post_meta( $post_id, 'status', 'pending');

					$current_user = wp_get_current_user();

					$user_id = $current_user->ID;
					
					update_post_meta( $post_id, 'consultant', $user_id);

					if( !empty($task_link) ) {
						$url = update_post_meta( $post_id, 'my_task_link', $task_link);

					}

					if(!$post_id) {
							wp_send_json_error(array(
			      			'message' => 'Error submitting post'
			    		));
					}
					else {
						$date = current_time( 'mysql' );
						$date = date_create($date);
						$formatted_date = date_format($date,"D M j");


						wp_send_json_success(array(
			      		'message' => 'New task has been added',
			      		'id' => $post_id,
			      		'title' => $post_title,
			      		'content' => $content,
			      		'link' => get_post_meta((int)$post_id, 'my_task_link'),
			      		'date' => $formatted_date
			    		));
					}

			}			
	}

	function addpost() {

				if ( !is_user_logged_in() ){
						wp_send_json_error('you are not allowed');
				}

				if ( !isset( $_POST['post_nonce_field'] ) || ! wp_verify_nonce( $_POST['post_nonce_field'], 'task_post_nonce' ) ) {

					  wp_send_json_error(array(
			      			'message' => 'Nonce verification failed'
			    		));

				}    

				if (!isset( $_POST['title'] ) || !isset( $_POST['content'] )) {

						wp_send_json_error(array(
			      			'message' => 'Error checking post'
			    		));

				}
		            $post_title =  sanitize_text_field($_POST['title']);

		            if( $_POST['link'] )  {
		            	$post_link = sanitize_text_field( $_POST['link']);
		        	}

		            $post_content = $_POST['content'];
		            $menu_order = (int)$_POST['menu_order'] + 1;

			

						$allowedHtml = array(
						    'a' => array(
						    	'target' => array(),
						        'href' => array(),
						        'title' => array(),
						        
						    ),
						    'br' => array(),
						    'em' => array(),
						    'strong' => array(),
						);

     					$content = wp_kses($post_content, $allowed_html);
     					$link = wp_kses($post_link, $allowed_html);


                    $content = $this->convert_link($content);
					$task_link = $this->convert_link($link);

				    $post_information = array(
					    'post_title' => $post_title,
					    'post_content' => $content,
					    'post_type' => 'mybb_todo',
					    'menu_order' => $menu_order,
					    'post_status' => 'publish'
					);
					 
					$post_id = wp_insert_post( $post_information );

					update_post_meta( $post_id, 'status', 'pending');

					if( !empty($task_link) ) {
						$url = update_post_meta( $post_id, 'my_task_link', $task_link);
					}

					if(!$post_id) {
							wp_send_json_error(array(
			      			'message' => 'Error submitting post'
			    		));
					}
					else {
						$date = current_time( 'mysql' );
						$date = date_create($date);
						$formatted_date = date_format($date,"D M j");

						wp_send_json_success(array(
				      		'message' => 'New task has been added',
				      		'id' => $post_id,
				      		'title' => $post_title,
				      		'content' => $content,
				      		'link' => get_post_meta((int)$post_id, 'my_task_link'),
				      		'menu_order' => $menu_order,
				      		'date' => $formatted_date
			    		));
					}

					exit;
					
	}

	function addsubpost() {

				if ( !is_user_logged_in() ){
						wp_send_json_error('you are not allowed');
				}

				if ( !isset( $_POST['post_nonce_field'] ) || ! wp_verify_nonce( $_POST['post_nonce_field'], 'subtask_post_nonce' ) ) {

					  wp_send_json_error(array(
			      			'message' => 'Nonce verification failed'
			    		));

				} else { 

				if (!isset( $_POST['title'] ) || !isset( $_POST['content'] )) {

						wp_send_json_error(array(
			      			'message' => 'Error checking post'
			    		));

				}
					$parent_id = $_POST['id'];

		            $post_title =  sanitize_text_field($_POST['title']);
		            $post_content = $_POST['content'];
		            $status = 'pending';
					$date =	current_time( 'mysql' );


						$allowedHtml = array(
						    'a' => array(
						    	'target' => array(),
						        'href' => array(),
						        'title' => array(),
						        
						    ),
						    'br' => array(),
						    'em' => array(),
						    'strong' => array(),
						);

     					$content = wp_kses($post_content, $allowed_html);
     					//$link = wp_kses($post_link, $allowed_html);


                    $content = $this->convert_link($content);
					//$task_link = $this->convert_link($link);

				    $post_information = array(
					    'post_title' => $post_title,
					    'post_content' => $content,
					    'post_type' => 'mybb_todo',
					    'post_status' => 'publish'
					);
					 
					$post_id = wp_insert_post( $post_information  );
					update_post_meta( $post_id, 'subtask_from', $parent_id);



					if(!$post_id) {
							wp_send_json_error(array(
			      			'message' => 'Error submitting post'
			    		));
					}
					else {
						$date = current_time( 'mysql' );
						$date = date_create($date);
						$formatted_date = date_format($date,"D M j");


						wp_send_json_success(array(
				      		'message' => 'New subtask has been added',
				      		'id' => $post_id,
				      		'title' => $post_title,
				      		'content' => $content,
				      		'status' => 'pending',
				      		'date' => $formatted_date
			    		));
					}
		}						
	}

	function editpost() {

				if ( !is_user_logged_in() ){
						wp_send_json_error('you are not allowed');
				}

				if (!isset( $_POST['title'] ) || !isset( $_POST['id'] ) ) {

						wp_send_json_error(array(
			      			'message' => 'Error checking post'
			    		));

				}
				$success_security = check_ajax_referer( 'todo_post_once', 'security' );
					

		            $post_title = sanitize_text_field( $_POST['title'] );
		            $post_link = sanitize_text_field( $_POST['link'] );
		            $post_content = $_POST['content'];
		            $post_id =  $_POST['id'];


						$allowedHtml = array(
						    'a' => array(
						    	'target' => array(),
						        'href' => array(),
						        'title' => array(),
						        
						    ),
						    'br' => array(),
						    'em' => array(),
						    'strong' => array(),
						);

     					$clean_post = wp_kses($post_content, $allowed_html);
     					$clean_link = wp_kses($post_link, $allowed_html);


						$content = $this->convert_link( $clean_post );
						$sanitize_link = $this->convert_link( $clean_link );

				    $post_array = array(
				    	'ID' => (int)$post_id,
					    'post_title' => $post_title,
					    'post_content' => $content
					);
					 
					$post_id = wp_update_post( $post_array);

					if( !empty($sanitize_link) ) {
						$link_id = update_post_meta((int)$post_id, 'my_task_link', $sanitize_link );
					}
					else {
						$link_id = update_post_meta((int)$post_id, 'my_task_link', '' );
					}

	

					if(!$post_id) {
							wp_send_json_error(array(
			      			'message' => 'Error submitting post'
			    		));
					}
					else {
						$db_link = get_post_meta((int)$post_id, 'my_task_link', true); 

						wp_send_json_success(array(
			      		'message' => 'To Do List has been successfully edited',
			      		'title' => $post_title,
			      		'link' => $db_link,
			      		'content' => $content
			    		));
					}
				wp_die();	
						
	}

	function completedpost() {

				if ( !is_user_logged_in() ){
						wp_send_json_error('you are not allowed');
				}
				$success_security = check_ajax_referer( 'todo_post_once', 'security' );

				

					if (!isset( $_POST['title'] ) && !isset( $_POST['content'] )) {

							wp_send_json_error(array(
				      			'message' => 'Error checking post'
				    		));

					}
						
			            $id = (int)$_POST['id'];
			            $title = sanitize_text_field( $_POST['title'] );
			            $link = $_POST['link'] ;
			            $only_list = sanitize_text_field(  $_POST['only_list'] );
			            $content = $_POST['content'];
			         


						$allowedHtml = array(
							    'a' => array(
							    	'target' => array(),
							        'href' => array(),
							        'title' => array(),
							        
							    ),
							    'br' => array(),
							    'em' => array(),
							    'strong' => array(),
						);

	     				$clean_post = wp_kses($content, $allowed_html);
	     				$sanitize_link = wp_kses($link, $allowed_html);

						$content = $this->convert_link( $clean_post );
						$clean_link = $this->convert_link( $sanitize_link );



			         	$date = current_time( 'mysql' );

						$post_id = update_post_meta( $id, 'status', 'completed');

	 					$user_id = get_current_user_id();					
	 					$suggested_id = get_post_meta($id, 'suggested_id', true);

						if(!empty($suggested_id)) {
							update_post_meta($id, 'completed_suggest', $suggested_id );
						}
						update_post_meta( $id, 'date_completed', $date);


						$date = date_create($date);
						$formatted_date = date_format($date,"D M j");

						if(!$post_id) {
								wp_send_json_error(array(
				      			'message' => 'Error submitting post'
				    		));
						}
						else {
							wp_send_json_success(array(
					      		'message' => $this->task_completed,
					      		'id' => $id,
					      		'title'   => $title,
					      		'only_list' => $only_list,
					      		'link' => $clean_link,
					      		'content' => $content,
					      		'date_completed' => $date
				    		));
						}
					
				exit;		
	}

	function undoCompletedpost() {

				if ( !is_user_logged_in() ){
						wp_send_json_error('you are not allowed');
				}


				if (!isset( $_POST['title'] ) && !isset( $_POST['id'] ) &&  !isset( $_POST['last_menu'] ) ) {

						wp_send_json_error(array(
			      			'message' => 'Error checking post'
			    		));
				}

				$success_security = check_ajax_referer( 'todo_post_once', 'security' );
					
				
					
		            $id = $_POST['id'];
					$title = sanitize_text_field( $_POST['title'] );
					//$link = $_POST['link'];
					$last_menu = $_POST['last_menu'];
		         
					 
					$post_id = update_post_meta( $id, 'status', 'Pending');

				    $post_array = array(
				    	'ID' => (int)$id,
					    'post_title' => $title,
					    'menu_order' => (int)$last_menu
					);
					 
					$queried_post = get_post(absint($id));
					$date = $queried_post->post_date;
					$content = $queried_post->post_content;


					$date = date_create($date);
					$formatted_date = date_format($date,"D M j");

					$post_id = wp_update_post( $post_array);


					$link = get_post_meta((int)$id, 'my_task_link', true);

 					$suggested_id = get_post_meta((int)$id, 'completed_suggest', true);
 					

 					if(!$task_link) {
 						$task_link = '';
 					}
					if(!empty($suggested_id)) {
						delete_post_meta((int)$id, 'completed_suggest');
					}

					if(!$post_id) {
							wp_send_json_error(array(
			      			'message' => 'Error submitting post'
			    		));
					}
					else {
						wp_send_json_success(array(
				      		'message' => 'Task has been added back to your list',
				      		'id' => $id,
				      		'title' => $title,
				      		'date' => $formatted_date,
				      		'link'     => $link,
				      		'content' => $content
			    		));
					}
			exit;						
	}

	function backSuggestedPost() {

		if ( !is_user_logged_in() ){
			wp_send_json_error('you are not allowed');
		}

		if (!isset( $_POST['id'] ) ) {

			wp_send_json_error(array(
      			'message' => 'Error checking post'
    		));
		}

		$success_security = check_ajax_referer( 'todo_post_once', 'security' );

        $post_id = (int)$_POST['id'];
     	$user_id = get_current_user_id();
        $suggested_id = get_post_meta( $post_id, 'suggested_id', true );

        if($user_id) {
        	$deleted = delete_post_meta( $suggested_id, '_user_id', $user_id );
    	}
    	//delete subtask and task from the user's list
		$status = wp_delete_post( absint($post_id), true); 

		delete_post_meta((int)$post_id, 'completed_subtask');
		delete_post_meta((int)$post_id, 'subtask_from');

		if( !$suggested_id && !$deleted ) {

			wp_send_json_error(array(
      			'message' => 'Error retrieving post'
    		));

		}
		else {

			wp_send_json_success(array(
	      		'message' => 'successfully retrieve data',
	      		'suggested_id' => $suggested_id,
	      		'user' => $user_id
    		));

		}
	
		exit;

	}
	function getPostDetails() {

				if ( !is_user_logged_in() ){
						wp_send_json_error('you are not allowed');
				}


				if (!isset( $_POST['id'] ) ) {

						wp_send_json_error(array(
			      			'message' => 'Error checking post'
			    		));

				}
				$success_security = check_ajax_referer( 'todo_post_once', 'security' );
					
				

	            $post_id = (int)$_POST['id'];
	         
				
				$queried_post = get_post(absint($post_id));
				$title = sanitize_text_field( $queried_post->post_title );
				$content = sanitize_textarea_field( $queried_post->post_content );

				$link = get_post_meta($post_id, 'my_task_link', true);



				$link = wp_strip_all_tags($link);

				if(!$queried_post) {
						wp_send_json_error(array(
		      			'message' => 'Error retrieving post'
		    		));
				}
				else {
					wp_send_json_success(array(
			      		'message' => 'successfully retrieve data',
			      		'id' => $id,
			      		'title' => $title,
			      		'link' => $link,
			      		'content' => $content
		    		));
				}
			
			exit;
	}
	function updateMenuOrder() {
				

			if ( !is_user_logged_in() ){
					wp_send_json_error('you are not allowed');
			}
		

			if ( !isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'todo_sort_nonce' ) ) {

					wp_send_json_error(array(
					'message' => 'Nonce verification failed'
					));

			}
			


            $order = $_POST['order'];
            $count = 0;

            foreach($order as $item_id) {

            	$post = array(
            		 	  'ID' => (int)$item_id,
            		 	  'menu_order' => $count,

            		);

            	wp_update_post( $post );

            	$count++;

            }
      		 				
	}
	function duedate() {
		if ( !is_user_logged_in() ){
				wp_send_json_error('you are not allowed');
		}
		if (!isset( $_POST['id'] ) && !isset( $_POST['due']) ) {
					wp_send_json_error(array(
		      			'message' => 'Error checking post'
		    		));
		}

		$success_security = check_ajax_referer( 'todo_post_once', 'security' );
					
        $post_id = $_POST['id'];
        $due_date = sanitize_text_field( $_POST['due'] );

        $status = update_post_meta((int)$post_id, 'due_date', $due_date );

			if(!$status) {
					wp_send_json_error(array(
	      			'message' => 'Error submitting post'
	    		));
			}
			else {
				wp_send_json_success(array(
	      		'message' => 'Due date is added'
	    		));
			}
		exit;	

	}

	function deletesubtask() {
		

			if ( !is_user_logged_in() ){
					wp_send_json_error('you are not allowed');
			}
			if (!isset( $_POST['id'] ) ) {
				wp_send_json_error(array(
	      			'message' => 'Error checking post'
	    		));
			}

			$success_security = check_ajax_referer( 'todo_post_once', 'security' );
			

            $post_id = $_POST['id'];
			 
			$status = wp_delete_post( absint($post_id), true); 

			delete_post_meta((int)$post_id, 'completed_subtask');
			delete_post_meta((int)$post_id, 'subtask_from');


			if(!$status) {
					wp_send_json_error(array(
	      			'message' => 'Error submitting post'
	    		));
			}
			else {
				wp_send_json_success(array(
	      		'message' => 'New To Do has been deleted'
	    		));
			}
			
			exit;

	}

	function deletepost() {
		

			if ( !is_user_logged_in() ){
					wp_send_json_error('you are not allowed');
			}
			if (!isset( $_POST['id'] ) ) {

						wp_send_json_error(array(
			      			'message' => 'Error checking post'
			    		));

			}

			$success_security = check_ajax_referer( 'todo_post_once', 'security' );
				
			

	            $post_id = $_POST['id'];


				 
				$status = wp_delete_post( absint($post_id), true); 

				delete_post_meta((int)$post_id, 'status');
				delete_post_meta((int)$post_id, 'date_completed');


				if(!$status) {
						wp_send_json_error(array(
		      			'message' => 'Error submitting post'
		    		));
				}
				else {
					wp_send_json_success(array(
		      		'message' => 'New To Do has been deleted'
		    		));
				}
			
			exit;
				 			
	}

	function removeSuggestedpost() {
		
			if ( !is_user_logged_in() ){
					wp_send_json_error('you are not allowed');
			}

			$success_security = check_ajax_referer( 'todo_post_once', 'security' );
				

			
            $post_id = $_POST['id'];

            $user_id = get_current_user_id();

			$status = update_post_meta((int)$post_id, '_user_id', $user_id );
			

			if(!$status) {
					wp_send_json_error(array(
	      			'message' => 'Error submitting post'
	    		));
			}
			else {
				wp_send_json_success(array(
	      		'message' => 'Suggested Task has been removed'
	    		));
			}

			exit;				 
				
	}

	function suggestedpost() {

		if ( !is_user_logged_in() ){
				wp_send_json_error('you are not allowed');
		}
		$success_security = check_ajax_referer( 'todo_post_once', 'security' );
			
		

        $id = (int)$_POST['id'];
        $title = sanitize_text_field( $_POST['title'] );
        $link = sanitize_text_field( $_POST['link'] );
        $menu_order = sanitize_text_field( $_POST['menu_order'] );
        $only_list = sanitize_text_field(  $_POST['only_list'] );
        $content = $_POST['content'];
     		
 		$allowedHtml = array(
		    'a' => array(
		    	'target' => array(),
		        'href' => array(),
		        'title' => array(),
		        
		    ),
		    'br' => array(),
		    'em' => array(),
		    'strong' => array(),
		);

			$content = wp_kses($content, $allowed_html);
			$link = wp_kses($link, $allowed_html);

        $content = $this->convert_link($content);
        $link = $this->convert_link($link);

	    $post_information = array(
		    'post_title' => $title,
		    'post_content' => $content,
		    'post_type' => 'mybb_todo',
		    'menu_order' => (int)$menu_order,
		    'post_status' => 'publish'
		);
		 
		$post_id = wp_insert_post( $post_information );


		$user_id = get_current_user_id();

		update_post_meta( $post_id, 'status', 'pending');

		add_post_meta( $id, '_user_id', $user_id );

		update_post_meta( $post_id, 'suggested_id', $id );

		if( !empty($link) ) {
			update_post_meta( $post_id, 'my_task_link', $link );
		}

		$date = date_create($date);
		$formatted_date = date_format($date,"D M j");



		if(!$post_id) {
				wp_send_json_error(array(
      			'message' => 'Error submitting post'
    		));
		}
		else {
			wp_send_json_success(array(
      		'message' => $this->added_suggested_task,
      		'id' => $post_id,
      		'title'   => $title,
      		'only_list' => $only_list,
      		'link' => $link,
      		'content' => $content,
      		'date' => $formatted_date,
      		'menu_order' => $menu_order
    		));
		}
		exit;
						
	}

	private function allowed_html($string) {


		$allowedHtml = array(
						    'a' => array(
						        'href' => array(),
						        'title' => array(),
						        'target' => array()
						    ),
						    'br' => array(),
						    'em' => array(),
						    'strong' => array(),
						);

     	return wp_kses($string, $allowedHtml);

	}
	
	private function convert_link($text) {
		// The Regular Expression filter
		$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";


		// Check if there is a url in the text
		if(preg_match($reg_exUrl, $text, $url)) {

		       // make the urls hyper links
       			return preg_replace($reg_exUrl, '<a href="'.$url[0].'" rel="nofollow" target="_blank">'.$url[0].'</a>', $text);
		} else {

		       // if no urls in the text just return the text
		       return $text;

		}
	}

  
}
