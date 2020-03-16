<?php if(isset($feed)) : ?>
<ul class="facebook-list">
<?php
foreach($feed as $fb_post):
    $fb_post_id = $fb_post->id; ?>
    <li class="of-tweet">
    	<div id="ofc-fb-post<?php echo $fb_post_id; ?>" class="tweet-content">
        	<?php
        	$text = preg_replace('#@(\w+)#', '<a target="_blank" href="https://twitter.com/$1">$0</a>', $tweet->full_text);
			$text = preg_replace('/#(\w+)/', '<a target="_blank" href="https://twitter.com/search?q=%23$1&src=hash">$0</a>', $text);
			echo $text; ?>
        </div>
		<div class="fb-post-meta">
        	<span class="time-meta"><a href="https://twitter.com/<?php echo $fb_post->user->name; ?>/statuses/<?php echo $tweet_id; ?>" target="_blank"><?php echo OF_Social_Connect::time_ago(strtotime($tweet->created_at)); ?></a></span>
        	<?php /*<span class="from-meta">from <?php echo $tweet->source; ?></span>*/ ?>
        </div>
    </li>
<?php endforeach;
?>
</ul>
<?php endif; ?>