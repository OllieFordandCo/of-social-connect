
	<?php foreach($instagram_feed->data as $data) {
		if($data->media_type === 'IMAGE') : ?>
		<li class="social-entry entry-medium entry-instagram" data-timestamp="<?php echo strtotime($data->timestamp); ?>">
			<a class="instagram-image" target="_blank" href="<?php echo $data->permalink; ?>">
				<?php echo '<img src="'.$data->media_url.'" class="img-responsive">'; ?>
			<?php if(isset($data->caption)) : ?>
          <div class="media-body">
            <i class="fas fa-heart"></i>
          </div>
            </a>
			<?php endif; ?>
		</li>
	<?php 
		endif;
	}; ?>
