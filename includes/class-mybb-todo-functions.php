<?php 

Class Mybb_Todo_Functions {



	public function __construct() {

	}

    public function mybb_custom_post_type() {
			$labels = array(
				'name'               => __( 'Members Task', 'mybb-todo' ),
				'singular_name'      => __( 'BB Todo Task', 'mybb-todo' ),
				'add_new'			 => __( 'Make a Task', 'mybb-todo' ),
				'add_new_item'			 => __( 'Suggest a Task', 'mybb-todo' )
			);

			$args = array(
				'labels'             => $labels,
		        'description'        => __( 'Add MY BB TO DO', 'mybb-todo' ),
				'public'             => false,
				'register_meta_box_cb' => array(&$this, 'register_meta_box'),
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => false,
				'rewrite'            => array( 'slug' => 'mybbtodo' ),
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array( 'title', 'editor', 'author' )
			);
	 
			register_post_type( 'mybb_todo', $args );

	}


	public function tasklink_meta_boxes() {
	    add_meta_box( 'task-link-id', __( 'BB Task Link', 'juggernut' ), array(&$this, 'tasklink_callback'), 'mybb_todo' );
	}

	public function tasklink_callback( $post) {

		 wp_nonce_field( 'global_notice_nonce', 'global_notice_nonce' );

		 $link = get_post_meta( $post->ID, 'my_task_link', true );

		 $output = '<input type="text" class="full-width-field" name="my_task_link"  value="' . $link . '" />';

		 echo $output;

	}

	public function tasklink_save_meta_box( $post_id ) {

	    // Save logic goes here. Don't forget to include nonce checks!
	    if ( ! isset( $_POST['global_notice_nonce'] ) ) {
	        return;
	    }

	    if ( ! wp_verify_nonce( $_POST['global_notice_nonce'], 'global_notice_nonce' ) ) {
	        return;
	    }

	    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	        return;
	    }


	    if ( isset( $_POST['post_type'] ) && 'mybb_todo' == $_POST['post_type'] ) {

	        if ( ! current_user_can( 'edit_page', $post_id ) ) {
	            return;
	        }

	    }
	    else {

	        if ( ! current_user_can( 'edit_post', $post_id ) ) {
	            return;
	        }
	    }
	    if ( ! isset( $_POST['my_task_link'] ) ) {
	        return;
	    }

	    $my_data = sanitize_text_field( $_POST['my_task_link'] );

	    update_post_meta( $post_id, 'my_task_link', $my_data );

	}


	public function register_meta_box() {
		
	    add_meta_box( 'suggested-task', __( 'Suggested Task', 'mybb-todo' ), array(&$this, 'metabox_callback'), 'mybb_todo' );

	}


	public function metabox_callback( $post) {

		wp_nonce_field( 'global_notice_nonce', 'global_notice_nonce' );

		$value = get_post_meta( $post->ID, '_suggest_task', true );

		$output .= '<input type="checkbox" name="suggest_task"  value="true" checked/>';

		echo $output;

	}

	function save_suggest_task_meta_box_data( $post_id ) {


	    if ( ! isset( $_POST['global_notice_nonce'] ) ) {
	        return;
	    }

	    if ( ! wp_verify_nonce( $_POST['global_notice_nonce'], 'global_notice_nonce' ) ) {
	        return;
	    }

	    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	        return;
	    }


	    if ( isset( $_POST['post_type'] ) && 'mybb_todo' == $_POST['post_type'] ) {

	        if ( ! current_user_can( 'edit_page', $post_id ) ) {
	            return;
	        }

	    }
	    else {

	        if ( ! current_user_can( 'edit_post', $post_id ) ) {
	            return;
	        }
	    }

	    if ( ! isset( $_POST['suggest_task'] ) ) {
	        return;
	    }

	    $my_data = sanitize_text_field( $_POST['suggest_task'] );

	    update_post_meta( $post_id, '_suggest_task', $my_data );
	}


	/* ==============================
	 CALLBACKS
	============================== */

	function set_custom_edit_mybb_todo_columns($columns) {
		$new = array();
	    unset( $columns['pilotpress'] );
	    $status = __( 'Status', 'mybb_todo' );

	    foreach($columns as $key => $value) {
	    	if($key == 'author') {
	    		$new['status'] = $status;
	    	}

	    	$new[$key] = $value;
	    }

	    return $new;
	}

	function custom_mybb_todo_column( $column, $post_id ) {
	    switch ( $column ) {

	        case 'status' :
	            $status = get_post_meta( $post_id , 'status' ,  true );
	            if (empty($status))
	            	echo 'Pending';
	            else if (!empty($status) && is_string( $status ) )
	                echo $status;
	            else
	                _e( 'Unable to get status', 'mybb_todo' );
	            break;

	        case 'publisher' :
	            echo get_post_meta( $post_id , 'publisher' , true ); 
	            break;

	    }
	}


}