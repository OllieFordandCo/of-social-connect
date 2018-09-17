<?php
 /**
 * Adds Foo_Widget widget.
 */
class OF_Instagram_Timeline extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'of_instagram_timeline', // Base ID
			__('Ollie Ford & Co Instagram Timeline', 'text_domain'), // Name
			array( 'classname'   => 'of_instagram_timeline', 'description' => __( 'Display your Instagram timeline', 'text_domain' ), ) // Args
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
		$no_pics = $instance['no_pics'];
    	$user_id = $instance['user_id'];

		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];

		if( $instagram_feed = OF_Social_Connect::retrieve_instagram_feed($user_id, $no_pics) ) :	
						
			$user_template = locate_template( 'social/instagram-widget.php' );
				
			if (!empty( $user_template )) :
					  
				include(locate_template( 'social/instagram-widget.php' ));
				
			else :			
			
				include( plugin_dir_path( __FILE__ ) . 'templates/widget-instagram-timeline.php' );
				
			endif;
		
		else :
		
			echo 'Please, authorise your instagram account before retrieving your photos.';
		
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
			$default_user_id = 'olliefordandco';
		endif;
		
		
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Instagram Timeline', 'text_domain' );
		}
		$user_id = isset($instance[ 'user_id' ]) ? $instance[ 'user_id' ] : $default_user_id;
		$no_pics = isset($instance[ 'no_pics' ]) ? $instance[ 'no_pics' ] : 6;
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'user_id' ); ?>"><?php _e( 'Instagram ID:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'user_id' ); ?>" name="<?php echo $this->get_field_name( 'user_id' ); ?>" type="text" value="<?php echo esc_attr( $user_id ); ?>">
		</p>  
		<p>
		<label for="<?php echo $this->get_field_id( 'no_pics' ); ?>"><?php _e( 'Number of Tweets:' ); ?></label> 
		<select class="widefat" id="<?php echo $this->get_field_id( 'no_pics' ); ?>" name="<?php echo $this->get_field_name( 'no_pics' ); ?>">      	
            <option value="1"<?php echo ($no_pics == 1) ? ' selected': ''; ?>>1</option>
            <option value="2"<?php echo ($no_pics == 2) ? ' selected': ''; ?>>2</option>
            <option value="3"<?php echo ($no_pics == 3) ? ' selected': ''; ?>>3</option>
            <option value="4"<?php echo ($no_pics == 4) ? ' selected': ''; ?>>4</option>
            <option value="5"<?php echo ($no_pics == 5) ? ' selected': ''; ?>>5</option>
            <option value="6"<?php echo ($no_pics == 6) ? ' selected': ''; ?>>6</option>
            <option value="7"<?php echo ($no_pics == 7) ? ' selected': ''; ?>>7</option>
            <option value="8"<?php echo ($no_pics == 8) ? ' selected': ''; ?>>8</option>
            <option value="9"<?php echo ($no_pics == 9) ? ' selected': ''; ?>>9</option>              
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
		$instance['user_id'] = ( ! empty( $new_instance['user_id'] ) ) ? strip_tags( $new_instance['user_id'] ) : '';
		$instance['no_pics'] = ( ! empty( $new_instance['no_pics'] ) ) ? strip_tags( $new_instance['no_pics'] ) : '';

		return $instance;
	}

} // class Foo_Widget