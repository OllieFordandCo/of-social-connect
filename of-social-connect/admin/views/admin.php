<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Plugin_Name
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 */

use OAuth\OAuth1\Service\Twitter;
use OAuth\Common\Storage\WPDatabase;
use OAuth\Common\Consumer\Credentials;
use OAuth\ServiceFactory;

?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<!-- @TODO: Provide markup for your options page here. -->
    
    <p>This plugin allows you to connect with Twitter</p>
    
	<? //Check if user has already submitted the api key and secret
	$twitter_api = get_option('of_twitter_api');

	$api_key = $twitter_api['key'];
	$api_secret = $twitter_api['secret'];
	
	$storage = new WPDatabase();
	?>
    <div>
    <form method="post" action="options.php">
		<?php settings_fields('of_social_connect'); ?>
    	<?php do_settings_sections( 'of_social_connect' ); ?> 
		<?php submit_button( 'Save changes', 'primary', 'submit', true ); ?>
	</form>
	</div>

    <hr>
    <?php 

	if(isset($_GET['deauthorise'])) :
		
		$storage->clearAuthorizationState('Twitter');
		$storage->clearToken('Twitter');
	
	
	endif;
	
	
	$authorised = $storage->hasAccessToken('Twitter');
	    
	if(!empty($api_key) && !empty($api_secret)) :
	//Let's do our Twitter stuff here
		
		if(!$authorised) :
			
		// Setup the credentials for the requests
		$credentials = new Credentials(
			$api_key,
			$api_secret,
			admin_url('options-general.php?page=of_twitter_connect&authorised=true')
		);
		$serviceFactory = new ServiceFactory();
		$twitterService = $serviceFactory->createService('Twitter', $credentials, $storage);
			
		$token = $twitterService->requestRequestToken();
		
		$authorize_url = $twitterService->getAuthorizationUri(array('oauth_token' => $token->getRequestToken()));

		echo '<a href="'.$authorize_url.'" class="button button-primary">Authorise your Twitter Account</a>';		
	
		elseif(!empty($_GET['oauth_token'])):

			// Setup the credentials for the requests
			$credentials = new Credentials(
				$api_key,
				$api_secret,
				admin_url('options-general.php?page=of_twitter_connect&authorised=true')
			);
			$serviceFactory = new ServiceFactory();
			$twitterService = $serviceFactory->createService('Twitter', $credentials, $storage);

			$token = $storage->retrieveAccessToken('Twitter');
		
			// This was a callback request from twitter, get the token
			$twitterService->requestAccessToken(
				$_GET['oauth_token'],
				$_GET['oauth_verifier'],
				$token->getRequestTokenSecret()
			);		
				 
			$url = admin_url('options-general.php?page=of_twitter_connect');
			$string = '<script type="text/javascript">';
			$string .= 'window.location = "' . $url . '"';
			$string .= '</script>';
			
			echo $string;
				
		else:
		
			echo '<p>You\'ve authorised your account on Twitter.</p>';
			
			$deauthorize_url = admin_url('options-general.php?page=of_twitter_connect&deauthorise=true');
	
			echo '<a href="'.$deauthorize_url.'" class="button button-primary">Deauthorise your Twitter Account</a>';	
			
			echo '<hr>';

		
		endif;	    
		
	endif;
	if(!empty($api_key) && !empty($api_secret) && $authorised):
	
		$credentials = new Credentials(
			$api_key,
			$api_secret,
			admin_url('options-general.php?page=of_twitter_connect')
		);
		$serviceFactory = new ServiceFactory();
		$twitterService = $serviceFactory->createService('Twitter', $credentials, $storage);
				 
   		// Send a request now that we have access token
    	$result = json_decode($twitterService->request('statuses/user_timeline.json?screen_name=andresfulla'));
	
		echo 'result: <pre>' . print_r($result, true) . '</pre>';	
	
	endif; ?>
</div>