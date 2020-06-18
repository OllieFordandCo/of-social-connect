
<?php
foreach($tweets as $tweet):
	$tweet_id = $tweet->id; ?>
    <div class="social-entry entry-tweet" data-timestamp="<?php echo strtotime($tweet->created_at); ?>">
        <div class="card-icon">
            <i class="fab fa-twitter"></i>
        </div>
        <?php
        if(property_exists($tweet->entities, 'media')) :
            foreach($tweet->entities->media as $media) : ?>
            <div class="social-entry-thumbnail">
                <img data-src="<?php echo $media->media_url_https; ?>" class="lazyload img-responsive" />
            </div>
        <?php endforeach;
        endif; ?>
    	<div id="tweet-<?php echo $tweet_id; ?>" class="tweet-content">
        	<?php
            $text = preg_replace('/\*\*\*(.*?)\*\*\*/', "<h3>$1</h3>",  $tweet->full_text);
            $text = preg_replace('/\*\*(.*?)\*\*/', "<h2>$1</h2>",  $tweet->full_text);
            $url = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';
            $text= preg_replace($url, '<a href="$0" target="_blank" title="$0">$0</a>', $text);
        	$text = preg_replace('#@(\w+)#', '<a target="_blank" href="https://twitter.com/$1">$0</a>', $text);
			$text = preg_replace('/#(\w+)/', '<a target="_blank" href="https://twitter.com/search?q=%23$1&src=hash">$0</a>', $text);
			echo $text; ?>
        </div>
		<div class="tweet-meta">
        	<span class="time-meta"><a href="https://twitter.com/<?php echo $tweet->user->screen_name; ?>/statuses/<?php echo $tweet_id; ?>" target="_blank"><?php echo OF_Social_Connect::time_ago(strtotime($tweet->created_at)); ?></a></span>
        </div>
        <div class="tweet-intent">
            <a href="https://twitter.com/intent/tweet?in_reply_to=<?php echo $tweet_id; ?>" class="in-reply-to" title="Reply" target="_blank"><i class="fas fa-reply"></i><span class="sr-only">Reply</span></a>
            <a href="https://twitter.com/intent/retweet?tweet_id=<?php echo $tweet_id; ?>" class="retweet" title="Retweet" target="_blank"><i class="fas fa-retweet"></i><span class="sr-only">Retweet</span></a>
            <a href="https://twitter.com/intent/favorite?tweet_id=<?php echo $tweet_id; ?>" class="favorite" title="Favorite" target="_blank"><i class="fas fa-heart"></i><span class="sr-only">Favorite</span></a>
        </div>
    </div>
<?php endforeach;
?>
