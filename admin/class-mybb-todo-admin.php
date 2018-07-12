<?php
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mybb-todo-list.php';

class Mybb_Todo_Admin {


	// class instance
	static $instance;

	// customer WP_List_Table object
	public $todo_obj;


	private $version;
	private $plugin_name;
	private $suggest;


	public function __construct( $plugin_name, $version ) {


		$this->plugin_name = $plugin_name;
		$this->version = $version;
	
		
	}

	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	public function add_mybb_subpage() {

    		add_submenu_page( 
				'edit.php?post_type=mybb_todo', 
				'Task Settings', 
				'Suggested List',
    			'manage_options', 
    			'mybb_suggested_list',
    			array(&$this, 'settings_suggested_subpage')
    		);

    		add_submenu_page( 
				'edit.php?post_type=mybb_todo', 
				'Task Settings', 
				'General Settings',
    			'manage_options', 
    			'mybb_todo_settings',
    			array(&$this, 'settings_todo_subpage')
    		);

    	
	}

	public function settings_suggested_subpage() {
		$this->todo_obj = new Mybb_Todo_List();
		require_once( plugin_dir_path(__FILE__)) . 'partials/mybbtodo-admin-suggested.php';

	}
	public function settings_todo_subpage() {

		require_once( plugin_dir_path(__FILE__)) . 'partials/mybbtodo-admin-display.php';

	}

	/**
	 * Screen options
	 */
	public function screen_option() {

		$option = 'per_page';
		$args   = [
			'label'   => 'Todo',
			'default' => 5,
			'option'  => 'todo_per_page'
		];

		add_screen_option( $option, $args );

		$this->todo_obj = new Mybb_Todo_List();
	}


	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	public function settings_init() {

		register_setting( 'mybbtodo-group', 'ontrabb_appid', array($this, 'sanitize_input_field') );
		register_setting( 'mybbtodo-group', 'ontrabb_appkey', array($this, 'sanitize_input_field') );

		register_setting( 'mybbtodo-group', 'mytask_option_name', array($this, 'sanitize_input_field') );
		register_setting( 'mybbtodo-group', 'completedtask_option_name', array($this, 'sanitize_input_field') );
		register_setting( 'mybbtodo-group', 'suggestedtask_option_name', array($this, 'sanitize_input_field') );
		register_setting( 'mybbtodo-group', 'addtask_option_name', array($this, 'sanitize_input_field') );

		register_setting( 'mybbtodo-group', 'completedtask_msg', array($this, 'sanitize_input_field') );
		register_setting( 'mybbtodo-group', 'addtask_msg', array($this, 'sanitize_input_field') );
		register_setting( 'mybbtodo-group', 'notaskname_msg' , array($this, 'sanitize_input_field'));
		register_setting( 'mybbtodo-group', 'notaskcontent_msg', array($this, 'sanitize_tcontent_msg') );
		register_setting( 'mybbtodo-group', 'suggested_task_completed', array($this, 'sanitize_suggested_task') );
		register_setting( 'mybbtodo-group', 'empty_task_suggested', array($this, 'sanitize_input_field') );
		register_setting( 'mybbtodo-group', 'empty_task_completed', array($this, 'sanitize_input_field') );
		register_setting( 'mybbtodo-group', 'notify_completed_task', array($this, 'sanitize_input_field') );
		register_setting( 'mybbtodo-group', 'added_suggested_task', array($this, 'sanitize_input_field') );

		register_setting( 'mybbtodo-group', 'top_notification', array($this, 'sanitize_tcontent_msg') );





		add_settings_section(
		    'mybbtodo-section',                   		
		    'Tab Settings',  						 
		    array( $this, 'settings_section_description'), 			 
		    'mybb_todo_settings'                          
		);

		add_settings_field(
		    'ontrabb-id',     
		    'Ontraport APP ID',      
		    array($this, 'settings_appID_callback'), 
		    'mybb_todo_settings',                    
		    'mybbtodo-section'               
		);


		add_settings_field(
		    'ontrabb-key',     
		    'Ontraport APP KEY',      
		    array($this, 'settings_appKEY_callback'), 
		    'mybb_todo_settings',                    
		    'mybbtodo-section'               
		);

	
		add_settings_field(
		    'settings-mytask-name',     
		    'Members Task Header',      
		    array($this, 'settings_mytask_callback'), 
		    'mybb_todo_settings',                    
		    'mybbtodo-section'               
		);

		add_settings_field(
		    'settings-completedtask-name',     
		    'Completed Task Name',      
		    array($this, 'settings_completedtask_callback'), 
		    'mybb_todo_settings',                    
		    'mybbtodo-section'               
		);

		add_settings_field(
		    'settings-suggestedtask-name',     
		    'Suggested Task Name',      
		    array($this, 'settings_suggestedtask_callback'), 
		    'mybb_todo_settings',                    
		    'mybbtodo-section'               
		);

		add_settings_field(
		    'settings-addtask-name',     
		    'Adding Task Name',      
		    array($this, 'settings_addtask_callback'), 
		    'mybb_todo_settings',                    
		    'mybbtodo-section'               
		);

		/* ==================================
			SETTING IN PENDING TAB
		=================================  */
		add_settings_section(
		    'pending-notification-section',                   		
		    'Pending Task',  						 
		    array( $this, 'pending_settings_description'), 			 
		    'mybb_todo_settings'                          
		);

		add_settings_field(
		    'settings-notaskcontent-msg',     
		    'If list in pending tab is empty',      
		    array($this, 'notaskcontent_msg'), 
		    'mybb_todo_settings',                    
		    'pending-notification-section'               
		);
		add_settings_field(
		    'settings-notaskcontent-msg',     
		    'If list in pending tab is empty',      
		    array($this, 'notaskcontent_msg'), 
		    'mybb_todo_settings',                    
		    'pending-notification-section'               
		);

		add_settings_field(
		    'settings-completing-task',     
		    'If all task has been marked completed',      
		    array($this, 'completed_task_msg'), 
		    'mybb_todo_settings',                    
		    'pending-notification-section'               
		);


		add_settings_field(
		    'settings-notaskname-msg',     
		    'If title of new task is empty',      
		    array($this, 'notaskname_msg'), 
		    'mybb_todo_settings',                    
		    'pending-notification-section'               
		);		
		
	


		add_settings_field(
		    'settings-adding-task',     
		    'If pending task is empty',      
		    array($this, 'add_task_msg'), 
		    'mybb_todo_settings',                    
		    'pending-notification-section'               
		);


		add_settings_field(
		    'notify-completed-task',     
		    'When a task is completed',      
		    array($this, 'notify_completed_task'), 
		    'mybb_todo_settings',                    
		    'pending-notification-section'               
		);	

		/* ==================================
			SETTINGS IN COMPLETED TAB
		=================================  */
		add_settings_section(
		    'completed-task-section',                   		
		    'Completed Task',  						 
		    array( $this, 'completed_task_description'), 			 
		    'mybb_todo_settings'                          
		);

		add_settings_field(
		    'settings-emptycompleted-task',     
		    'If list in completed tab is empty',      
		    array($this, 'empty_task_completed'), 
		    'mybb_todo_settings',                    
		    'completed-task-section'               
		);	



		/* ==================================
			SETTINGS IN SUGGESTED TAB
		=================================  */
		add_settings_section(
		    'suggested-task-section',                   		
		    'Suggested Task',  						 
		    array( $this, 'suggested_task_description'), 			 
		    'mybb_todo_settings'                          
		);

		add_settings_field(
		    'settings-emptysuggested-task',     
		    'If list in suggested tab is empty',      
		    array($this, 'empty_task_suggested'), 
		    'mybb_todo_settings',                    
		    'suggested-task-section'               
		);	

		add_settings_field(
		    'settings-suggested-task-msg',     
		    'If suggested tasks are completed',      
		    array($this, 'task_suggestion_complete'), 
		    'mybb_todo_settings',                    
		    'suggested-task-section'               
		);

		add_settings_field(
		    'settings-added-suggested-task',     
		    'If suggested task has been added',      
		    array($this, 'task_suggestion_added'), 
		    'mybb_todo_settings',                    
		    'suggested-task-section'               
		);


		add_settings_section(
		    'mybbtodo-setting-notification',                   		
		    'Tab Settings',  						 
		    array( $this, 'settings_section_notification'), 			 
		    'mybb_todo_settings'                          
		);

		add_settings_field(
		    'settings-welcome-message',     
		    'Welcome Message',      
		    array($this, 'task_welcome_message'), 
		    'mybb_todo_settings',                    
		    'mybbtodo-setting-notification'               
		);


	}



	/* =======================================
 		Handle Sanitization of all Fields
	======================================  */
	 function sanitize_input_field( $input ) {
 		if(isset( $input )) {

			$clean = sanitize_text_field( $input );

			return $clean;
		}
		else {
			return false;
		}
 	}
 	function sanitize_suggested_task( $input ) {
 		if(isset( $input )) {

			$clean = sanitize_text_field( $input );

			return $clean;
		}
		else {
			return false;
		}
 	}
	function sanitize_tcontent_msg( $input ) {

		if(isset( $input )) {

			$allowed_html = array(
						    'a' => array(
						        'href' => array(),
						        'title' => array()
						    ),
						    'br' => array(),
						    'em' => array(),
						    'span' => array(),
						    'strong' => array(),
						 );

			$clean = wp_kses($input, $allowed_html);

			return $clean;
		}
		else {
			return false;
		}
	}

	function mybbTodo_sanitize( $input ) {
		return isset( $input ) ? true : false;
	}


	/* =======================================
 		Section Description
	======================================  */

	public function settings_section_description(){
	    echo wpautop( "Task Heading Settings" );
	}

	public function  pending_settings_description() {
		echo wpautop( "Customize Notification on pending task" );
	}

	public function suggested_task_description(){
	    echo wpautop( "Customize Notifications on suggested task" );
	}
	public function completed_task_description(){
	    echo wpautop( "Customize Notifications on completed task" );
	}
	public function settings_section_notification(){
	    echo wpautop( "Customizing Welcome Message" );
	}


	/* =======================================
 		Displaying Input fields
	======================================  */

	function task_welcome_message() {
		$task = get_option('top_notification');

		echo '<label for="ntxtmsg">' .
		       '<textarea id="ntxtmsg" name="top_notification" type="input">'. ( !empty($task) ? $task : '' ) .'</textarea>' .
		      '</label>';
	}

	function notify_completed_task() {
		$task = get_option('notify_completed_task');

		echo '<label for="ntc_msg">' .
		       '<input id="ntc_msg" name="notify_completed_task" class="regular-text" type="input" value="'. ( !empty($task) ? $task : '' )  .'" />' .
		      '</label>';
	}

    function empty_task_completed() {
		$task = get_option('empty_task_completed');

		echo '<label for="etc_msg">' .
		       '<input id="etc_msg" name="empty_task_completed" class="regular-text" type="input" value="'. ( !empty($task) ? $task : '' )  .'" />' .
		      '</label>';
	}

    function empty_task_suggested() {
		$task = sanitize_text_field(get_option('empty_task_suggested'));

		echo '<label for="est_msg">' .
		       '<input id="est_msg" name="empty_task_suggested" class="regular-text" type="input" value="'. ( !empty($task) ? $task : '' )  .'" />' .
		      '</label> <span></span>';
	}


    function task_suggestion_complete() {
		$task = sanitize_text_field(get_option('suggested_task_completed'));

		echo '<label for="st_msg">' .
		       '<input id="st_msg" name="suggested_task_completed" class="regular-text" type="input" value="'. ( !empty($task) ? $task : '' )  .'" />' .
		      '</label> <span>Show this message when suggested Tasks have been completed</span>';
	}


    function notaskcontent_msg() {
		$task = sanitize_text_field(get_option('notaskcontent_msg'));

		echo '<label for="notaskc_msg">' .
		       '<input id="notaskc_msg" name="notaskcontent_msg" class="regular-text" type="input" value="'. ( !empty($task) ? $task : '' )  .'" />' .
		      '</label> <span>Show this message when Task description is empty</span>';
	}

	function notaskname_msg() {
		$task = sanitize_text_field(get_option('notaskname_msg'));

		echo '<label for="adtask_msg">' .
		       '<input id="adtask_msg" name="notaskname_msg" class="regular-text" type="input" value="'. ( !empty($task) ? $task : '' )  .'" />' .
		      '</label> <span>Show error message when title is empty</span>';
	}

	function add_task_msg() {
		$task = get_option('addtask_msg');

		echo '<label for="adtask_msg">' .
		       '<input id="adtask_msg" name="addtask_msg" class="regular-text" type="input" value="'. ( !empty($task) ? $task : '' )  .'" />' .
		      '</label> <span>This message will pop up when task list is empty</span>';
	}


	function completed_task_msg() {
		$task = get_option('completedtask_msg');

		echo '<label for="ctask_msg">' .
		       '<input id="ctask_msg" name="completedtask_msg" class="regular-text" type="input" value="'. ( !empty($task) ? $task : '' )  .'" />' .
		      '</label>';
	}

	function settings_addtask_callback() {
	     $task = get_option( 'addtask_option_name' ); ?>
	    <label for="addtask">
	        <input id="addtask" class="regular-text" type="input" value="<?php  echo ( !empty($task)  ? $task : '' );  ?>" name="addtask_option_name" />
	    </label>
	    <?php
	}

	function settings_mytask_callback() {
	    ?>
	    <?php $task = get_option( 'mytask_option_name' ); ?>
	    <label for="mytask">
	        <input id="mytask" class="regular-text" type="input" value="<?php  echo ( !empty($task)  ? $task : '' );  ?>" name="mytask_option_name" />
	    </label>
	    <?php
	}

    function settings_completedtask_callback() {
	    ?>
	    <?php $task = get_option( 'completedtask_option_name' ); ?>
	    <label for="completedtask">
	        <input id="completedtask" class="regular-text" type="input" value="<?php  echo ( !empty($task)  ? $task : '' );  ?>" name="completedtask_option_name" />
	    </label>
	    <?php
	}

	function settings_suggestedtask_callback() {
	    ?>
	    <?php $task = get_option( 'suggestedtask_option_name' ); ?>
	    <label for="suggestedtask">
	        <input id="suggestedtask" class="regular-text" type="input" value="<?php  echo ( !empty($task)  ? $task : '' );  ?>" name="suggestedtask_option_name" />
	    </label>
	    <?php
	}
	function task_suggestion_added() {
	    ?>
	    <?php $task = get_option( 'added_suggested_task' ); ?>
	    <label for="added_suggested_task">
	        <input id="added_suggested_task" class="regular-text" type="input" value="<?php  echo ( !empty($task)  ? $task : '' );  ?>" name="added_suggested_task" />
	    </label>
	    <?php
	}

	function settings_appID_callback() {
	    ?>
	    <?php $task = get_option( 'ontrabb_appid' ); ?>
	    <label for="ontrabb_appid">
	        <input id="ontrabb_appid" class="regular-text" type="input" value="<?php  echo ( !empty($task)  ? $task : '' );  ?>" name="ontrabb_appid" />
	    </label>
	    <?php
	}

   	function settings_appKEY_callback() {
	    ?>
	    <?php $task = get_option( 'ontrabb_appkey' ); ?>
	    <label for="ontrabb_appkey">
	        <input id="ontrabb_appkey" class="regular-text" type="input" value="<?php  echo ( !empty($task)  ? $task : '' );  ?>" name="ontrabb_appkey" />
	    </label>
	    <?php
	}

	public function enqueue_styles() {

		global $typenow;

		if($typenow == 'mybb_todo')	{
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mybbtodo-admin.css', array(), $this->version, 'all' );
		}

	}


	public function enqueue_scripts() {

		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plugin-name-admin.js', array( 'jquery' ), $this->version, false );

	}

    
}

