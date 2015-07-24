<?php 

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
		$follow = '<a href="https://twitter.com/intent/follow?original_referer=http%3A%2F%2Fwildingsmusic.com&region=follow_link&screen_name='.$screenname.'&tw_p=followbutton" class="twitter-follow">Follow us</a>';
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $follow . $args['after_title'];

		if( $tweets = OF_Social_Connect::retrieve_tweets($screenname, $no_tweets) ) :	
						
			$user_template = locate_template( 'of-social-connect/twitter/widget-timeline.php' );
				
			if (!empty( $user_template )) :
					  
				include(locate_template( 'of-social-connect/twitter/widget-timeline.php'));
				
			else :			
			
				include( plugin_dir_path( __FILE__ ) . 'templates/widget-timeline.php' );
				
			endif;
		
		else :
		
			echo 'Please, authorise your twitter account before retrieving tweets.';
		
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
		$of_instagram_api = get_option('of_instagram_api');
		
		if(!empty($of_instagram_api['default_user_id'])) :
			$default_user_id = $of_instagram_api['default_user_id'];
		else :
			$default_screen_name = 'olliefordandco';
		endif;
		
		
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Instagram Timeline', 'text_domain' );
		}
		$screenname = isset($instance[ 'user_id' ]) ? $instance[ 'user_id' ] : $default_screen_name;
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
            <option value="7"<?php echo ($no_tweets == 7) ? ' selected': ''; ?>>7</option>
            <option value="8"<?php echo ($no_tweets == 8) ? ' selected': ''; ?>>8</option>
            <option value="9"<?php echo ($no_tweets == 9) ? ' selected': ''; ?>>9</option>            
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