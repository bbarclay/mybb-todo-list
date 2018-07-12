<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/public
 * @author     Your Name <email@example.com>
 */
class Mybb_Todo_Public {


	private $plugin_name;
	protected $template;
	private $version;
	private $opt_completed;
	public $client;
	public $consultant;
	/**
    * Default functions
    *
    * 
    */

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->opt_completed = sanitize_text_field(get_option('completedtask_msg'));



		//Use to query data from database
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/lib/todo-form-template.php';

		//Shortcode for front end list of tasks
		add_shortcode( 'frontendtodo', array( $this, 'display_frontend' ) );



		//Add Base PHP
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/lib/apiconnect.php';




		//Declaring API KEY AND ID
		$appid  = get_option( 'ontrabb_appid' );
		$appkey = get_option( 'ontrabb_appkey' );


		$ontraDetails = array(
		    'app_id' => $appid,
		    'app_key' => $appkey
		);

		$instance  = ontraconnect::connect($ontraDetails);
		$this->client = $instance->getData();


		//Get consultants members or any ontraport queries
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/lib/todo-consultant-template.php';

        

		//This shortcode show consultant's members task.
		//add_shortcode( 'consultantviewtodo', array( $this, 'display_members_tasks' ) );


	}


    /**
    * Adding shortcode to output consultant's member tasks
    *
    * @return display lists of members  and their tasks that belongs to consultants
    */
    public function display_members_tasks(  ) {

    		$consultant = new Mybb_Consultant( $this->client );

    		if(!is_user_logged_in()) {
    			return 'Please login to view this page';
    		} 
    		else {
      			$current_user = wp_get_current_user();
	  			$user_email   = $current_user->user_email;

	  			   if ($user_email != 'luke@businessblueprint.com' && $user_email != 'beau@businessblueprint.com' && $user_email != 'josh@businessblueprint.com' && $user_email != 'july@businessblueprint.com' && $user_email != 'dale@businessblueprint.com'   ) {
				   	    return 'This is a restricted page';
				   }
    		}
    		
			$this->atts = $atts;

			$atts = shortcode_atts( array(

				'title' => '',
				'completed' => 0

			), $this->atts, 'consultantviewtodo' );

		    ob_start();

			//Display 	
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/members-list-template.php';

			$content = ob_get_clean();
    		return $content;


    }



    /**
    * Adding shortcode to output
    *
    * @return display lists of tasks
    */
    public function display_frontend( $atts ) {

    		if(!is_user_logged_in()) {
    			return 'Please login to view your task list';
    		} 
    		
			$this->atts = $atts;

			$atts = shortcode_atts( array(

				'title' => '',
				'completed' => 0

			), $this->atts, 'frontendtodo' );

			$this->template = new Mybb_Template;
			
	    ob_start();
				
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/mybb-todo-display.php';

		$content = ob_get_clean();
    	return $content;


    }
   

    public function get_form() {
    	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/mybb-todo-form.php';
    }

    public function get_lists() {
    	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/mybb-todo-lists.php';
    }
  

    /**
    * Adding styles to the front end
    *
    * @return css files
    */
	public function enqueue_styles() {

       wp_enqueue_style( $this->plugin_name . '-font-awesome', plugin_dir_url( __FILE__ ) . 'css/font-awesome.css', array(), $this->version, 'all' );
	   wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mybbtodo-frontend-style.css', array(), $this->version, 'all' );
	

	}

    /**
    * Adding styles to the front end
    *
    * @return  script files
    */
	public function enqueue_scripts() {


		wp_enqueue_script( $this->plugin_name . '-jquery', plugin_dir_url( __FILE__ ) . 'js/jquery-1.12.4.js', array(), '1.12.4', false );
		wp_enqueue_script( $this->plugin_name . '-jquery-ui', plugin_dir_url( __FILE__ ) . 'js/jquery-ui.js', array( 'Mybb_Todo-jquery' ), $this->version, false );

		wp_enqueue_script( 'mybb-todoSortable', plugin_dir_url( __FILE__ ) . 'js/mybb-todo-sortable.js', array( 'Mybb_Todo-jquery' ), $this->version, false);
		wp_localize_script( 'mybb-todoSortable', 'sortable_ajax', array( 
        	'ajaxurl' => admin_url( 'admin-ajax.php' ), 
        	'security' => wp_create_nonce('todo_sort_nonce') 
		) );
		wp_enqueue_script( $this->plugin_name . 'validation', 'http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js', array( 'jquery' ) );

		wp_enqueue_script( $this->plugin_name . '-date-picker', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js', array( 'jquery' ) );


		wp_enqueue_script( 'mybb-todo', plugin_dir_url( __FILE__ ) . 'js/mybb-todo-public.js', array( 'Mybb_Todo-jquery' ), $this->version, true);


        wp_localize_script( 'mybb-todo', 'mybb_ajax', array( 
        	'ajaxurl' => admin_url( 'admin-ajax.php' ), 
        	'security' => wp_create_nonce('todo_post_once'),
        	'completed_msg' => $this->opt_completed,
        	'title_error' => sanitize_text_field(get_option('notaskname_msg')),
        	'content_msg' =>  sanitize_text_field(get_option('notaskcontent_msg')),
        	'completed_suggest' =>  sanitize_text_field(get_option('suggested_task_completed')),
        	'empty_suggest' =>  sanitize_text_field(get_option('empty_task_suggested'))


        ) );

	}

}
