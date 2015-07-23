<div class="instagram-feed">
	<?php foreach($instagram_feed->data as $data) { 
		if($data->type == 'image') : ?>
		<div class="insta-item">
			<a class="instagram-image" target="_blank" href="<?php echo $data->link; ?>">
				<?php echo '<img src="'.$data->images->standard_resolution->url.'" width="'.$data->images->standard_resolution->width.'" height="'.$data->images->standard_resolution->height.'" class="img-responsive">'; ?>			
			</a>
			<?php if(isset($data->caption)) : ?>
			<div class="media">
			  <div class="media-left">
			    <a href="https://instagram.com/<?php echo $data->caption->from->username; ?>" class="insta-avatar">
			      <img class="media-object img-responsive" src="<?php echo $data->caption->from->profile_picture; ?>" alt="<?php echo $data->caption->from->username; ?>">
			    </a>
			  </div>
			  <div class="media-body">
			    <h4 class="media-heading"><a href="<?php echo 'https://instagram.com/'.$data->caption->from->username; ?>" target="_blank"><?php echo $data->caption->from->username; ?></a></h4>
			    <h5 class="media-heading timeago"><?php echo OF_Social_Connect::time_ago($data->caption->created_time); ?></h5>
	        	<?php 
	        	$text = preg_replace('#@(\w+)#', '<a target="_blank" href="https://instagram.com/$1">$0</a>', $data->caption->text); ?>				    
			    <?php echo wpautop($text); ?>
			  </div>
			</div>					
			<?php endif; ?>
		</div>	
	<?php 
		endif;
	}; ?>
</div>