<?php

// Extending WP list table
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Mybb_Todo_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Suggest', 'mybb-todo' ), 
			'plural'   => __( 'Suggests', 'mybb-todo' ), 
			'ajax'     => false

		] );

	}


	/**
	 * Retrieve customer’s data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */

	public static function get_todo( $per_page = 20, $page_number = 1 ) {

	  global $wpdb;

	  $sql = "SELECT {$wpdb->prefix}posts.* FROM {$wpdb->prefix}posts, {$wpdb->prefix}postmeta WHERE {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id AND post_type = 'mybb_todo' AND {$wpdb->prefix}postmeta.meta_key = '_suggest_task'";


	  if ( ! empty( $_REQUEST['orderby'] ) ) {
	    $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
	    $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
	  }

	  $sql .= " LIMIT $per_page";

	  $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


	  $result = $wpdb->get_results( $sql, 'ARRAY_A' );

	  return $result;
	}


	/**
	 * Delete a customer record.
	 *
	 * @param int $id customer ID
	 */
	public static function delete_todo( $id ) {
	  global $wpdb;

	  $wpdb->delete(
	    "{$wpdb->prefix}posts",
	    [ 'ID' => $id ],
	    [ '%d' ]
	  );
	}

	/**
	* Returns the count of records in the database.
	*
	* @return null|string
	*/
	public static function record_count() {
		global $wpdb;

		// $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_type = 'mybb_todo'";

 $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}posts, {$wpdb->prefix}postmeta WHERE {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id AND post_type = 'mybb_todo' AND {$wpdb->prefix}postmeta.meta_key = '_suggest_task'";

		return $wpdb->get_var( $sql );
	}

	/** Text displayed when no customer data is available */
	public function no_items() {
	  _e( 'No task avaliable.', 'mybb_todo' );
	}



	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'title':
				return $item['post_title'];
			case 'task_added':
				$count = $this->total_added_task($item['ID']);
				return $count;
			case 'task_completed':
				$count = $this->total_completed_task($item['ID']);
				return $count;
			case 'author':
				$author = get_user_meta($item['post_author']);

				return $author['first_name'][0] . ' ' . $author['1ast_name'][0];
			case 'date':
				$date = date_create($item['post_date']);
				$date = date_format($date,"Y/m/d");
				return "Published <br><abbr title='" . $item['post_date'] . "'>". $date ."</abbr>";
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	private function total_added_task($id) {
		  global $wpdb;

		   $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}postmeta WHERE {$wpdb->prefix}postmeta.post_id = $id AND {$wpdb->prefix}postmeta.meta_key = '_user_id' ";


		return $wpdb->get_var( $sql );
	}

	private function total_completed_task($id) {
		  global $wpdb;

		   //$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}postmeta WHERE {$wpdb->prefix}postmeta.post_id = $id AND {$wpdb->prefix}postmeta.meta_key = 'suggested_id' ";

		  $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}postmeta WHERE {$wpdb->prefix}postmeta.meta_key = 'completed_suggest' AND {$wpdb->prefix}postmeta.meta_value = $id ";


		return $wpdb->get_var( $sql );
	}
	
	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
		);
	}

	/**
	* Method for name column
	*
	* @param array $item an array of DB data
	*
	* @return string
	*/
	function column_name( $item ) {

	  // create a nonce
	  $delete_nonce = wp_create_nonce( 'mybb_delete_todo' );

	  $title = '<strong>' . $item['title'] . '</strong>';

	  $actions = [
	    'delete' => sprintf( '<a href="?page=%s&action=%s&post=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['ID'] ), $delete_nonce )
	  ];

	  return $title . $this->row_actions( $actions );
	}


	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
	  $columns = array(
	  	'cb'        	  => '<input type="checkbox" />',
		'title'     	  => 'Title',
		'task_added'      => 'Total members added this task',
		'task_completed'  => 'Total members completed this task',
		'author'      	  => 'Author',
		'date'      	  => 'Posted on'
	  );
      
	  return $columns;
	}

	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
	  $sortable_columns = array(
	    'title' => array( 'title', true )
	
	  );

	  return $sortable_columns;
	}


	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
	  $actions = [
	    'bulk-delete' => 'Delete'
	  ];

	  return $actions;
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

	  // $this->_column_headers = $this->get_column_info();
		$this->_column_headers = array( 
		    $this->get_columns(), 
		    array(), //hidden columns if applicable 
		    $this->get_sortable_columns()
		);

	  /** Process bulk action */
	  $this->process_bulk_action();

	  $per_page     = $this->get_items_per_page( 'todo_per_page', 10 );
	  $current_page = $this->get_pagenum();
	  $total_items  = self::record_count();

	  $this->set_pagination_args( [
	    'total_items' => $total_items, //WE have to calculate the total number of items
	    'per_page'    => $per_page //WE have to determine how many items to show on a page
	  ] );


	  $this->items = self::get_todo( $per_page, $current_page );
	}

	public function process_bulk_action() {

	  //Detect when a bulk action is being triggered...
	  if ( 'delete' === $this->current_action() ) {

	    // In our file that handles the request, verify the nonce.
	    $nonce = esc_attr( $_REQUEST['_wpnonce'] );

	    if ( ! wp_verify_nonce( $nonce, 'mybb_delete_todo' ) ) {
	      die( 'Go get a life script kiddies' );
	    }
	    else {
	      self::delete_todo( absint( $_GET['post'] ) );

	      wp_redirect( esc_url( add_query_arg() ) );
	      exit;
	    }

	  }

	  // If the delete bulk action is triggered
	  if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
	       || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
	  ) {

	    $delete_ids = esc_sql( $_POST['bulk-delete'] );

	    // loop over the array of record IDs and delete them
	    foreach ( $delete_ids as $id ) {
	      self::delete_todo( $id );

	    }

	    wp_redirect( esc_url( add_query_arg() ) );
	    exit;
	  }
	}

}
