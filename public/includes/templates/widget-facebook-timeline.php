
<?php
foreach($facebook_posts->data as $data) {
    if(!$data->is_hidden) :
   ?>
        <?php
        $message = preg_replace("/\*\*\*(.*?)\*\*\*/", "<h3>$1</h3>",  $data->message);
        $message = preg_replace("/\*\*(.*?)\*\*/", "<h2>$1</h2>",  $message);
        $url = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';
        $message= preg_replace($url, '<a href="$0" target="_blank" title="$0">$0</a>', $message);
        $word_count = str_word_count($data->message);
        $class = ($word_count > 60) ? 'large' : 'medium';
        $picture = (property_exists($data, 'full_picture') && !empty($data->full_picture)) ? $data->full_picture : false;
        $class .= ($picture) ? ' has-thumbnail' : ' no-thumbnail' ;
   ?>
        <li class="social-entry entry-facebook entry-<?php echo $class; ?>" data-timestamp="<?php echo strtotime($data->created_time); ?>">
           <div class="social-entry-thumbnail">
               <?php echo '<img data-src="'.$data->full_picture.'" class="lazyload img-responsive">'; ?>
           </div>
            <div class="entry-content">
                <?php echo $message; ?>
            </div>
            <div class="tweet-meta">
                <span class="time-meta"><a href="https://twitter.com/<?php //echo $tweet->user->screen_name; ?>/statuses/<?php echo  $data->ID; ?>" target="_blank"><?php echo OF_Social_Connect::time_ago(strtotime($data->created_time)); ?></a></span>
            </div>
            <div class="card-icon">
                <i class="fab fa-facebook-f"></i>
            </div>
        </li>
    <?php
    endif;
}; ?>
