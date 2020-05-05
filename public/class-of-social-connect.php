<?php
/**
 * Ollie Ford & Co Social Connect.
 *
 *
 * @package   Ollie Ford & Co Social Connect
 * @author    Rubén Madila (for Ollie Ford & Co) <ruben@ollieford.co.uk>
 * @license   GPL-2.0+
 * @link      http://www.ollieford.co.uk
 * @copyright 2014 Ollie Ford & Co
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-plugin-name-admin.php`
 *
 * @TODO: Rename this class to a proper name for your plugin.
 *
 * @package Plugin_Name
 * @author  Your Name <email@example.com>
 */
class OF_Social_Connect {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   0.1.0
	 *
	 * @var     string
	 */
	const VERSION = '0.1.0';

	/**
	 *
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    0.1.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'of_social_connect';

	/**
	 * Instance of this class.
	 *
	 * @since    0.1.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     0.1.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		/* Define custom functionality.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( 'widgets_init', array( $this, 'of_social_connect_widget_init' ) );
		add_filter( '@TODO', array( $this, 'filter_method_name' ) );

		add_shortcode( 'timeline_widget', array($this, 'of_social_connect_timeline_shortcode') );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    0.1.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.1.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    0.1.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    0.1.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    0.1.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    0.1.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    0.1.0
	 */
	private static function single_activate() {
		// @TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    0.1.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.1.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles() {
		//wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {
		//wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}

	/**
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    0.1.0
	 */
	public function of_social_connect_widget_init() {
		
		$twitter_api = get_option('of_twitter_api');		
		$api_key = $twitter_api['key'];
		$api_secret = $twitter_api['secret'];
		
		if(!empty($api_key) && !empty($api_secret)) :		
			require_once( plugin_dir_path( __FILE__ ) . 'includes/twitter-widgets.php' );
			register_widget( 'OF_Twitter_Timeline' );	
		endif;

		$instagram_api = get_option('of_instagram_api');		
		$api_key = $instagram_api['key'];
		$api_secret = $instagram_api['secret'];
		
		if(!empty($api_key) && !empty($api_secret)) :		
			require_once( plugin_dir_path( __FILE__ ) . 'includes/instagram-widgets.php' );
			register_widget( 'OF_Instagram_Timeline' );	
		endif;
		
	}

	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *        Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    0.1.0
	 */
	public function filter_method_name() {
		// @TODO: Define your filter hook callback here
	}

	public static function retrieve_all_social() {


		//if (  $social = get_transient( 'ccc_retrieve_all_social_feeds' )  ) {
			$social = array();
			$insta = array();
			$twitter = array();
			$facebook = array();

			try {

				$feed = OF_Social_Connect::retrieve_facebook_posts('CCreative4', 8, true);
				if(isset($feed->data)) {
					foreach($feed->data as $asset) {
						$default_asset = new OF_Social_Asset();
						$asset = $default_asset->return_normalized_asset($asset, 'facebook');
						$facebook[$asset->date] = $asset;
					}
				}
				$social['facebook'] = $facebook;
			} catch(Exception $e) {
				$social['facebook'] = array();
			}


			try {

				$feed = OF_Social_Connect::retrieve_instagram_feed('6380557924', 2);

				foreach ($feed as $asset) {
					$default_asset = new OF_Social_Asset();
					$asset = $default_asset->return_normalized_asset($asset, 'instagram', true);
					$insta[$asset->date] = $asset;
				}
				$social['instagram'] = $insta;
			} catch(Exception $e) {
				$social['instagram'] = array();
			}

			try {

				$feed = OF_Social_Connect::retrieve_tweets('CCreative4', 5);

				foreach ($feed as $asset) {
					$default_asset = new OF_Social_Asset();
					$asset = $default_asset->return_normalized_asset($asset, 'twitter', true);
					$twitter[$asset->date] = $asset;
				}
				//echo '<div class="d-none">' . var_dump($feed) . '</div>';
				$social['twitter'] = $twitter;
			} catch(Exception $e) {
				$social['twitter'] = array();
			}

			//set_transient(  'ccc_retrieve_all_social_feeds', $social, 30 * MINUTE_IN_SECONDS );

		//};

		return $social;

	}


	/**
	 * Retrieve any tweets saves on the database.
	 *
	 * @since    0.1.0
	 */
	public static function retrieve_facebook_posts($user_id, $no_pics, $force_update = false) {

		//Check if user has already submitted the api key and secret
		$of_fb_api = get_option('of_facebook_api');


		$api_key = $of_fb_api['key'];
		$api_secret = $of_fb_api['secret'];

		if(!empty($api_key) && !empty($api_secret)) :

			$update = false;
			if ( ! ( $result = get_transient( 'of_facebook_timeline_widget' ) ) ) {
				$update = true;
			};

			$user_id_changed = (isset($result['user_id'])) ? $result['user_id'] : $user_id;
			$no_pics_changed = (isset($result['no_pics'])) ? $result['no_pics'] : $no_pics;

			if($force_update || $user_id_changed !== $user_id || $no_pics_changed !== $no_pics) {
				$update = true;
			}

			if($update) {

				$storage = new OAuth\Common\Storage\WPDatabase();
				$token = $storage->retrieveAccessToken('Facebook');


				$credentials = new OAuth\Common\Consumer\Credentials(
					$api_key,
					$api_secret,
					admin_url('options-general.php?page=of_social_connect&authorised_facebook=true')
				);


				$serviceFactory = new OAuth\ServiceFactory();
				$facebookService = $serviceFactory->createService('Facebook', $credentials, $storage);

				$result['user_id'] = $user_id;
				$result['no_pics'] = $no_pics;
				$result['facebook_posts'] = json_decode($facebookService->request('/v6.0/'.$user_id.'/posts?limit='.$no_pics.'&fields=id,created_time,story,message,full_picture,is_hidden,permalink_url&access_token='.$token->getAccessToken()));

				set_transient(  'of_facebook_timeline_widget', $result, 15 * MINUTE_IN_SECONDS );
			}

			return $result['facebook_posts'];

		else :

			return false;

		endif;

	}

    /**
     * Retrieve any tweets saves on the database.
     *
     * @since    0.1.0
     */
    public static function retrieve_instagram_feed($user_id, $no_pics, $force_update = false) {

        //Check if user has already submitted the api key and secret
        $of_fb_api = get_option('of_facebook_api');


        $api_key = $of_fb_api['key'];
        $api_secret = $of_fb_api['secret'];

        if(!empty($api_key) && !empty($api_secret)) :

            $update = false;
            if ( ! ( $result = get_transient( 'of_instagram_timeline_widget' ) ) ) {
                $update = true;
            };

            $user_id_changed = (isset($result['user_id'])) ? $result['user_id'] : $user_id;
            $no_pics_changed = (isset($result['no_pics'])) ? $result['no_pics'] : $no_pics;

            if($force_update || $user_id_changed !== $user_id || $no_pics_changed !== $no_pics) {
                $update = true;
            }

            if($update) {

                $storage = new OAuth\Common\Storage\WPDatabase();
                $token = $storage->retrieveAccessToken('Facebook');


                $credentials = new OAuth\Common\Consumer\Credentials(
                    $api_key,
                    $api_secret,
                    admin_url('options-general.php?page=of_social_connect&authorised_facebook=true')
                );


                $serviceFactory = new OAuth\ServiceFactory();
                $facebookService = $serviceFactory->createService('Facebook', $credentials, $storage);

                $result['user_id'] = $user_id;
                $result['no_pics'] = $no_pics;
                $result['instagram_feed'] = json_decode($facebookService->request('/v6.0/'.$user_id.'/media?limit='.$no_pics.'&fields=thumbnail_url,caption,id,media_type,username,permalink,media_url,like_count,shortcode&access_token='.$token->getAccessToken()));

                set_transient(  'of_instagram_timeline_widget', $result, 15 * MINUTE_IN_SECONDS );
            }

            return $result['instagram_feed'];

        else :

            return false;

        endif;

    }


    /**
	 * Retrieve any tweets saves on the database.
	 *
	 * @since    0.1.0
	 */
	public static function retrieve_old_instagram_feed($user_id, $no_pics, $force_update = false) {

		//Check if user has already submitted the api key and secret
		$of_instagram_api = get_option('of_instagram_api');

		
		$api_key = $of_instagram_api['key'];
		$api_secret = $of_instagram_api['secret'];

		if(!empty($api_key) && !empty($api_secret)) :
		
			$update = false;
			if ( ! ( $result = get_transient( 'of_instagram_timeline_widget' ) ) ) {
				$update = true;
			};

			$user_id_changed = (isset($result['user_id'])) ? $result['user_id'] : $user_id;
			$no_pics_changed = (isset($result['no_pics'])) ? $result['no_pics'] : $no_pics;
			
			if($force_update || $user_id_changed !== $user_id || $no_pics_changed !== $no_pics) {
				$update = true;
			}
			
			if($update) {
				
				$storage = new OAuth\Common\Storage\WPDatabase();

				$credentials = new OAuth\Common\Consumer\Credentials(
					$api_key,
					$api_secret,
					admin_url('options-general.php?page=of_social_connect&authorised_instagram=true')
				);
				$serviceFactory = new OAuth\ServiceFactory();
				$instagramService = $serviceFactory->createService('Instagram', $credentials, $storage);

				$result['user_id'] = $user_id;
				$result['no_pics'] = $no_pics;
				$result['instagram_feed'] = json_decode($instagramService->request('users/'.$user_id.'/media/recent/?count='.$no_pics));

				set_transient(  'of_instagram_timeline_widget', $result, 15 * MINUTE_IN_SECONDS );			
			}

			return (property_exists($result['instagram_feed'],'data')) ? $result['instagram_feed']->data : $result['instagram_feed'];
		
		else :
			
			return false;
			
		endif;

	}

	/**
	 * Retrieve any tweets saves on the database.
	 *
	 * @since    0.1.0
	 */
	public static function retrieve_tweets($default_screen_name, $no_tweets, $force_update = false) {

		//Check if user has already submitted the api key and secret
		$twitter_api = get_option('of_twitter_api');
		
		$api_key = $twitter_api['key'];
		$api_secret = $twitter_api['secret'];

		if(!empty($api_key) && !empty($api_secret)) :
		
			$update = false;
			
			if ( ! ( $result = get_transient( 'of_timeline_widget' ) ) ) {
				$update = true;
			};
			
			$default_screen_name_changed = (isset($result['default_screen_name'])) ? $result['default_screen_name'] : $default_screen_name;
			$no_tweets_changed = (isset($result['no_tweets'])) ? $result['no_tweets'] : $no_tweets;
			
			if($force_update || $default_screen_name_changed !== $default_screen_name || $no_tweets_changed !== $no_tweets) {
				$update = true;
			}
			
			if($update) {
				
				$storage = new OAuth\Common\Storage\WPDatabase();
			
				$credentials = new OAuth\Common\Consumer\Credentials(
					$api_key,
					$api_secret,
					admin_url('options-general.php?page=of_twitter_connect')
				);
				$serviceFactory = new OAuth\ServiceFactory();
				$twitterService = $serviceFactory->createService('twitter', $credentials, $storage);
				
				$result['default_screen_name'] = $default_screen_name;
				$result['no_tweets'] = $no_tweets;
				$result['tweets'] = json_decode($twitterService->request('statuses/user_timeline.json?screen_name='.$default_screen_name_changed.'&count='.$no_tweets.'&tweet_mode=extended&include_rts=true&exclude_replies=true'));
				
				set_transient( 'of_timeline_widget', $result, 15 * MINUTE_IN_SECONDS );			
			}

			return $result['tweets'];	
		
		else :
			
			return false;
			
		endif;	
	}
	
	/**
	 * Display time as {time} ago
	 * http://css-tricks.com/snippets/php/time-ago-function/
	 *
	 * @since    0.1.0
	 */
	public static function time_ago($time)
	{
	   $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
	   $lengths = array("60","60","24","7","4.35","12","10");
	
	   $now = time();
	
		   $difference     = $now - $time;
		   $tense         = "ago";
	
	   for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
		   $difference /= $lengths[$j];
	   }
	
	   $difference = round($difference);
	
	   if($difference != 1) {
		   $periods[$j].= "s";
	   }
	
	   return "$difference $periods[$j] ago";
	}

	// [bartag foo="foo-value"]
	function of_social_connect_timeline_shortcode( $atts ) {
        $atts = shortcode_atts( array(
			'screen_name' => '',
			'limit' => 5,
		), $atts );

		ob_start();

		?>
        <ul class="social-feed-list">
        <?php

		if( $facebook_posts = $this->retrieve_facebook_posts('thingsmadepublic', $atts['limit'])) :

            $user_template = locate_template( 'social/facebook-widget.php' );

            if (!empty( $user_template )) :

                include(locate_template( 'of-social-connect/templates/widget-facebook-timeline.php'));

            else :

                include( plugin_dir_path( __FILE__ ) . 'includes/templates/widget-facebook-timeline.php' );

            endif;

        endif;

        if( $instagram_feed = $this->retrieve_instagram_feed('17841404958562995', $atts['limit'])) :

            $user_template = locate_template( 'social/instagram-widget.php' );

            if (!empty( $user_template )) :

                include(locate_template( 'of-social-connect/templates/widget-instagram-timeline.php'));

            else :

                include( plugin_dir_path( __FILE__ ) . 'includes/templates/widget-instagram-timeline.php' );

            endif;

        endif;
		
		if( $tweets = $this->retrieve_tweets($atts['screen_name'], $atts['limit']) ) :
						
			$user_template = locate_template( 'social/twitter-widget.php' );

			if (!empty( $user_template )) :
					  
				include(locate_template( 'of-social-connect/twitter/widget-timeline.php'));
				
			else :			
			
				include( plugin_dir_path( __FILE__ ) . 'includes/templates/widget-timeline.php' );
				
			endif;
		
		else :
		
			echo 'Please, authorise your twitter account before retrieving tweets.';
		
		endif;

        ?>
        </ul>
        <?php
		$template = ob_get_clean();
		
		return $template;
	}

}
