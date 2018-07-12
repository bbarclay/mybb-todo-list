<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 * @author     Your Name <email@example.com>
 */
class Mybb_Todo {


	protected $loader;
	protected $plugin_name;
	protected $version;



	public function __construct() {

		$this->plugin_name = 'Mybb_Todo';
		$this->version = '1.0.0';
		$this->load_dependencies();
		$this->set_locale();
		$this->initialize();
		$this->define_admin_hooks();
		$this->frontend_library(); 
		$this->define_public_hooks();

	}


	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mybb-todo-loader.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mybb-todo-i18n.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mybb-todo-functions.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mybb-todo-admin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mybb-todo-frontend.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-mybb-todo-public.php';

		$this->loader = new Mybb_Todo_Loader();

	}


	private function set_locale() {

		$plugin_i18n = new Mybb_Todo_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	private function initialize() {

		$plugin_admin = new Mybb_Todo_Functions( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_admin, 'mybb_custom_post_type' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'register_meta_box' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_suggest_task_meta_box_data' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'tasklink_meta_boxes' );
		$this->loader->add_action( 'save_post',  $plugin_admin, 'tasklink_save_meta_box' );

		$this->loader->add_filter( 'manage_mybb_todo_posts_columns', $plugin_admin, 'set_custom_edit_mybb_todo_columns' );
		$this->loader->add_action( 'manage_mybb_todo_posts_custom_column', $plugin_admin, 'custom_mybb_todo_column', 10, 2 );

	}	

	private function define_admin_hooks() {

		$plugin_admin = new Mybb_Todo_Admin( $this->get_plugin_name(), $this->get_version());

		//$this->loader->add_filter( 'set-screen-option', $plugin_admin, [ __CLASS__, 'set_screen' ], 10, 3 );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_mybb_subpage' );
		//$this->loader->add_action( 'load-$hookt', $plugin_admin, 'screen_option' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'settings_init' );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	private function frontend_library() {
		
		$plugin_frontend = new Mybb_Todo_Frontend();
		
	}


	private function define_public_hooks() {

		$plugin_public = new Mybb_Todo_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}


	public function get_loader() {
		return $this->loader;
	}


	public function get_version() {
		return $this->version;
	}

}
