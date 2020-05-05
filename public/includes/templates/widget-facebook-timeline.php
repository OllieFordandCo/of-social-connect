
<?php
foreach($facebook_posts->data as $data) {
    if(!$data->is_hidden) :
   ?>
        <?php
        $message = preg_replace("/\*\*\*(.*?)\*\*\*/", "<h3>$1</h3>",  $data->message);
        $word_count = str_word_count($data->message);
        $class = ($word_count > 60) ? 'large' : 'medium';
   ?>
        <li class="social-entry entry-facebook entry-<?php echo $class; ?>">
            <div class="card-icon">
                <i class="fab fa-facebook-f"></i>
            </div>
           <div class="social-entry-thumbnail">
               <?php echo '<img src="'.$data->full_picture.'" class="img-responsive">'; ?>
           </div>
            <div class="entry-content">
                <?php echo $message; ?>
            </div>
            <div class="tweet-meta">
                <span class="time-meta"><a href="https://twitter.com/<?php //echo $tweet->user->screen_name; ?>/statuses/<?php echo  $data->ID; ?>" target="_blank"><?php echo OF_Social_Connect::time_ago(strtotime($data->created_at)); ?></a></span>
            </div>
            <div class="tweet-intent">
                <a href="https://twitter.com/intent/tweet?in_reply_to=<?php echo $data->ID; ?>" class="in-reply-to" title="Reply" target="_blank"><i class="fas fa-reply"></i><span class="sr-only">Reply</span></a>
                <a href="https://twitter.com/intent/retweet?tweet_id=<?php echo  $data->ID; ?>" class="retweet" title="Retweet" target="_blank"><i class="fas fa-retweet"></i><span class="sr-only">Retweet</span></a>
                <a href="https://twitter.com/intent/favorite?tweet_id=<?php echo  $data->ID; ?>" class="favorite" title="Favorite" target="_blank"><i class="fas fa-heart"></i><span class="sr-only">Favorite</span></a>
            </div>
        </li>
    <?php
    endif;
}; ?>
