<?php 
use OAuth\OAuth1\Service\Twitter;
use OAuth\Common\Storage\WPDatabase;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Exception\Exception;
use OAuth\ServiceFactory;
use OAuth\Common\Http\Uri\UriFactory;

$uriFactory = new UriFactory();
$currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
$currentUri->setQuery('');	
//Check if user has already submitted the api key and secret

	$twitter_api = get_option('of_twitter_api');

	$twitter_api_key = $twitter_api['key'];
	$twitter_api_secret = $twitter_api['secret'];
	
	$storage = new WPDatabase();
	
	if(isset($_GET['deauthorise_twitter'])) :
		
		$storage->clearAuthorizationState('Twitter');
		$storage->clearToken('Twitter');
	
	
	endif;
	
	
	$twitter_authorised = $storage->hasAccessToken('Twitter');
	    
	if(!empty($twitter_api_key) && !empty($twitter_api_secret)) :
	//Let's do our Twitter stuff here
		
		if(!$twitter_authorised) :
			
		// Setup the credentials for the requests
		$twitter_credentials = new Credentials(
			$twitter_api_key,
			$twitter_api_secret,
			$currentUri->getAbsoluteUri().'?page=of_social_connect'
		);
		$twitter_serviceFactory = new ServiceFactory();
		$twitterService = $twitter_serviceFactory->createService('Twitter', $twitter_credentials, $storage);
	
		
			try {
				
				$twitter_token = $twitterService->requestRequestToken();

				$twitter_authorize_url = $twitterService->getAuthorizationUri(array('oauth_token' => $twitter_token->getRequestToken()));
		
				echo '<a href="'.$twitter_authorize_url.'" class="button button-primary">Authorise your Twitter Account</a>';		
			
			} catch(Exception $exception) {
				
				echo $exception->getMessage();
				echo 'Make sure the callback url had a placeholder when you created the application.';
			}
			
		elseif(!empty($_GET['oauth_token'])):

			// Setup the credentials for the requests
			$twitter_credentials = new Credentials(
				$api_key,
				$api_secret,
				admin_url('options-general.php?page=of_social_connect&authorised=true')
			);
			$twitter_serviceFactory = new ServiceFactory();
			$twitterService = $twitter_serviceFactory->createService('Twitter', $twitter_credentials, $storage);

			$twitter_token = $storage->retrieveAccessToken('Twitter');
		
			// This was a callback request from twitter, get the token
			$twitterService->requestAccessToken(
				$_GET['oauth_token'],
				$_GET['oauth_verifier'],
				$twitter_token->getRequestTokenSecret()
			);		

			// Send a request now that we have access token
    		$twitter_verify_credentials = json_decode($twitterService->request('account/verify_credentials.json'));				

			$of_twitter_api = get_option('of_twitter_api');
			if (isset($of_twitter_api)
				&& is_array($of_twitter_api)
			) {
				$of_twitter_api['default_screen_name'] = $twitter_verify_credentials->screen_name;
				update_option('of_twitter_api', $of_twitter_api);
			}		
				 
			$url = admin_url('options-general.php?page=of_social_connect');
			$string = '<script type="text/javascript">';
			$string .= 'window.location = "' . $url . '"';
			$string .= '</script>';
			
			echo $string;
				
		else:
		
			echo '<p>You\'ve authorised your account on Twitter.</p>';
			
			$twitter_deauthorize_url = admin_url('options-general.php?page=of_social_connect&deauthorise_twitter=true');
	
			echo '<a href="'.$twitter_deauthorize_url.'" class="button button-primary">Deauthorise your Twitter Account</a>';
			
			echo '<p>You can start using the Widgets, Shortcodes and Functions included with this plugin.</p>';	
			
			echo '<hr>';

		
		endif;	    
		
	endif; ?>