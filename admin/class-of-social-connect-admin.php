<?php
/**
 * Ollie Ford & Co Social Connect.
 *
 * @package   Ollie Ford & Co Social Connect
 * @author    Rubén Madila (for Ollie Ford & Co) <ruben@ollieford.co.uk>
 * @license   GPL-2.0+
 * @link      http://www.ollieford.co.uk
 * @copyright 2014 Ollie Ford & Co
 */

/**
 * This Class is used to work with the
 * administrative side of the WordPress site.
 *
 * For public-facing functionality refer to `class-of-social-connect.php`
 *
 * @package Ollie Ford & Co Social Connect
 * @author  Rubén Madila (for Ollie Ford & Co) <ruben@ollieford.co.uk>
 */
class OF_Social_Connect_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    0.1.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    0.1.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     0.1.0
	 */
	private function __construct() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		/*
		 * Call $plugin_slug from public plugin class.
		 *
		 * @TODO:
		 *
		 * - Rename "Plugin_Name" to the name of your initial plugin class
		 *
		 */
		$plugin = of_social_connect::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		add_action( 'admin_init',  array( $this, 'register_of_social_connect_options') );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		/*
		 * Define custom functionality.
		 *
		 * Read more about actions and filters:
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( '@TODO', array( $this, 'action_method_name' ) );
		add_filter( '@TODO', array( $this, 'filter_method_name' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.1.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @TODO:
	 *
	 * - Rename "Plugin_Name" to the name your plugin
	 *
	 * @since     0.1.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), of_social_connect::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @TODO:
	 *
	 * - Rename "Plugin_Name" to the name your plugin
	 *
	 * @since     0.1.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), of_social_connect::VERSION );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    0.1.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 * @TODO:
		 *
		 * - Change 'Page Title' to the title of your plugin admin page
		 * - Change 'Menu Text' to the text for menu item for the plugin settings page
		 * - Change 'manage_options' to the capability you see fit
		 *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Ollie Ford & Co Social Connect', $this->plugin_slug ),
			__( 'OF Social Connect', $this->plugin_slug ),			
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    0.1.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    0.1.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	public function register_of_social_connect_options() {
	  add_settings_section(
		  'of_twitter_credentials_setting_section',
		  'Twitter API Credentials',
		  array( $this, 'of_twitter_credentials_section_callback_function' ),
		  $this->plugin_slug
	  );	  	
	  add_settings_field( 'of_twitter_api[key]', 'API key', array($this, 'of_text_input_callback_function'), $this->plugin_slug, 'of_twitter_credentials_setting_section', array( 'label_for' => 'of_twitter_api[key]', 'option_group' => 'of_twitter_api', 'index' => 'key' ) );
	  add_settings_field( 'of_twitter_api[secret]', 'API Secret', array($this, 'of_text_input_callback_function'), $this->plugin_slug, 'of_twitter_credentials_setting_section', array( 'label_for' => 'of_twitter_api[secret]', 'option_group' => 'of_twitter_api', 'index' => 'secret' ) );  	    
	  register_setting( $this->plugin_slug, 'of_twitter_api' );
	  
	  add_settings_section(
		  'of_instagram_credentials_setting_section',
		  'Instagram API Credentials',
		  array( $this, 'of_instagram_credentials_section_callback_function' ),
		  $this->plugin_slug
	  );	  	
	  add_settings_field( 'of_instagram_api[key]', 'API key', array($this, 'of_text_input_callback_function'), $this->plugin_slug, 'of_instagram_credentials_setting_section', array( 'label_for' => 'of_instagram_api[key]', 'option_group' => 'of_instagram_api', 'index' => 'key' ) );
	  add_settings_field( 'of_instagram_api[secret]', 'API Secret', array($this, 'of_text_input_callback_function'), $this->plugin_slug, 'of_instagram_credentials_setting_section', array( 'label_for' => 'of_instagram_api[secret]', 'option_group' => 'of_instagram_api', 'index' => 'secret' ) );  	    
	  register_setting( $this->plugin_slug, 'of_instagram_api' );

		add_settings_section(
			'of_facebook_credentials_setting_section',
			'Facebook API Credentials',
			array( $this, 'of_facebook_credentials_section_callback_function' ),
			$this->plugin_slug
		);
		add_settings_field( 'of_facebook_api[key]', 'App ID', array($this, 'of_text_input_callback_function'), $this->plugin_slug, 'of_facebook_credentials_setting_section', array( 'label_for' => 'of_facebook_api[key]', 'option_group' => 'of_facebook_api', 'index' => 'key' ) );
		add_settings_field( 'of_facebook_api[secret]', 'API Secret', array($this, 'of_text_input_callback_function'), $this->plugin_slug, 'of_facebook_credentials_setting_section', array( 'label_for' => 'of_facebook_api[secret]', 'option_group' => 'of_facebook_api', 'index' => 'secret' ) );
		register_setting( $this->plugin_slug, 'of_facebook_api' );

	}

	/**
	 * NOTE:     Actions are points in the execution of a page or process
	 *           lifecycle that WordPress fires.
	 *
	 *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    0.1.0
	 */
	public function action_method_name() {
		// @TODO: Define your action hook callback here
	}

	/**
	 * NOTE:     Filters are points of execution in which WordPress modifies data
	 *           before saving it or sending it to the browser.
	 *
	 *           Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    0.1.0
	 */
	public function filter_method_name() {
		// @TODO: Define your filter hook callback here
	}

	function of_twitter_credentials_section_callback_function($arg) {
	  // echo section intro text here
	  echo '<p><strong>Feel lost?</strong> Find out about your API key and API secret in <a href="https://apps.twitter.com">Twitter Application Management</a> website</p>';
	}

	function of_instagram_credentials_section_callback_function($arg) {
	  // echo section intro text here
	  echo '<p><strong>Feel lost?</strong> Find out about your API key and API secret in <a href="https://instagram.com/developer/?hl=en">Instagram Developer</a> website</p>';
	}

	function of_facebook_credentials_section_callback_function($arg) {
		// echo section intro text here
	}

	function of_text_input_callback_function($arg) {
			$id = $arg['label_for'];
			$option = get_option($arg['option_group']);
			$index = $arg['index'];
			echo '<input name="'.$id.'" type="text" id="'.$id.'" value="'.$option[$index].'" class="regular-text" mouseev="true" keyev="true">';
	}

}
