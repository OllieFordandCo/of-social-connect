<?php 

use OAuth\OAuth1\Service\Twitter;
use OAuth\Common\Storage\WPDatabase;
use OAuth\Common\Consumer\Credentials;
use OAuth\ServiceFactory;

/**
 * Adds Foo_Widget widget.
 */
class OF_Twitter_Timeline extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'of_twitter_timeline', // Base ID
			__('Ollie Ford & Co Twitter Timeline', 'text_domain'), // Name
			array( 'classname'   => 'of_twitter_timeline', 'description' => __( 'Display your Twitter timeline', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		
		$title = apply_filters( 'widget_title', $instance['title'] );
		$no_tweets = $instance['no_tweets'];
    	$screenname = $instance['screenname'];

		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];

		$api_key = get_option('of_twitter_api_key');
		$api_secret = get_option('of_twitter_api_secret'); 
		
		if(!empty($api_key) && !empty($api_secret)) :
		
			$update = false;
			if ( ! ( $result = get_transient( $screenname.'_of_timeline_widget' ) ) ) {
				$update = true;
			};
			
			$screenname_changed = (isset($result['screenname'])) ? $result['screenname'] : $screenname;
			$no_tweets_changed = (isset($result['no_tweets'])) ? $result['no_tweets'] : $no_tweets;
			
			if($screenname_changed !== $screenname || $no_tweets_changed !== $no_tweets) {
				$update = true;
			}
			
			$message = 'Transient Call';
			
			if($update) {
				$message = 'Twitter Call';
				
				$storage = new WPDatabase();
			
				$credentials = new Credentials(
					$api_key,
					$api_secret,
					admin_url('options-general.php?page=of_twitter_connect')
				);
				$serviceFactory = new ServiceFactory();
				$twitterService = $serviceFactory->createService('twitter', $credentials, $storage);
				
				$result['screnname'] = $screenname;
				$result['no_tweets'] = $no_tweets;
				$result['tweets'] = json_decode($twitterService->request('statuses/user_timeline.json?screen_name='.$screenname.'&count='.$no_tweets));
				
				set_transient(  $screenname.'_of_timeline_widget', $result, 15 * MINUTE_IN_SECONDS );			
			}
				
				
			foreach($result['tweets'] as $tweet):
				echo '<hr>';
				echo $tweet->text;
			endforeach;		
			echo '<hr>';
			echo $message;		
		
		else:
		
			echo 'Please, setup the plugin before using the widget';
		
		endif;
		
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'text_domain' );
		}
		$screenname = isset($instance[ 'screenname' ]) ? $instance[ 'screenname' ] : 'madilaonline';
		$no_tweets = isset($instance[ 'no_tweets' ]) ? $instance[ 'no_tweets' ] : 4;
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'screenname' ); ?>"><?php _e( 'Screenname:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'screenname' ); ?>" name="<?php echo $this->get_field_name( 'screenname' ); ?>" type="text" value="<?php echo esc_attr( $screenname ); ?>">
		</p>  
		<p>
		<label for="<?php echo $this->get_field_id( 'no_tweets' ); ?>"><?php _e( 'Number of Tweets:' ); ?></label> 
		<select class="widefat" id="<?php echo $this->get_field_id( 'no_tweets' ); ?>" name="<?php echo $this->get_field_name( 'no_tweets' ); ?>">      	
            <option value="1"<?php echo ($no_tweets == 1) ? ' selected': ''; ?>>1</option>
            <option value="2"<?php echo ($no_tweets == 2) ? ' selected': ''; ?>>2</option>
            <option value="3"<?php echo ($no_tweets == 3) ? ' selected': ''; ?>>3</option>
            <option value="4"<?php echo ($no_tweets == 4) ? ' selected': ''; ?>>4</option>
            <option value="5"<?php echo ($no_tweets == 5) ? ' selected': ''; ?>>5</option>
            <option value="6"<?php echo ($no_tweets == 6) ? ' selected': ''; ?>>6</option>
        </select>
		</p>              
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['screenname'] = ( ! empty( $new_instance['screenname'] ) ) ? strip_tags( $new_instance['screenname'] ) : '';
		$instance['no_tweets'] = ( ! empty( $new_instance['no_tweets'] ) ) ? strip_tags( $new_instance['no_tweets'] ) : '';

		return $instance;
	}

} // class Foo_Widget