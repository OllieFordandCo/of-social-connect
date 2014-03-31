<ul class="tweet-list">
<?php
foreach($tweets as $tweet):
	$tweet_id = $tweet->id; ?>
    <li class="tweet">
    	<div id="tweet-<?php echo $tweet_id; ?>" class="tweet-content">
        	<?php echo $tweet->text; ?>
        </div>
		<div class="tweet-meta">
        	<span class="time-meta"><a href="http://twitter.com/<?php echo $tweet->user->screen_name; ?>/statuses/<?php echo $tweet_id; ?>" target="_blank"><?php echo OF_Social_Connect::time_ago($tweet->created_at); ?></a></span>       
        	<span class="from-meta">from <?php echo $tweet->source; ?></span>
        </div> 
        <div class="tweet-intent">
            <a href="http://twitter.com/intent/tweet?in_reply_to=<?php echo $tweet_id; ?>" class="in-reply-to" title="Reply" target="_blank"><span class="sr-only">Reply</span></a>
            <a href="http://twitter.com/intent/retweet?tweet_id=<?php echo $tweet_id; ?>" class="retweet" title="Retweet" target="_blank"><span class="sr-only">Retweet</span></a>
            <a href="http://twitter.com/intent/favorite?tweet_id=<?php echo $tweet_id; ?>" class="favorite" title="Favorite" target="_blank"><span class="sr-only">Favorite</span></a>
        </div>
    </li>
<?php endforeach;	
?>
</ul>