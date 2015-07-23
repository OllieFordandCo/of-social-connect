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
	$instagram_api = get_option('of_instagram_api');

	$ig_api_key = $instagram_api['key'];
	$ig_api_secret = $instagram_api['secret'];
	
	$storage = new WPDatabase();
	?>
	
    <?php 
	
	if(isset($_GET['deauthorise_instagram'])) :
		
		$storage->clearAuthorizationState('Instagram');
		$storage->clearToken('Instagram');	
	
	endif;
	
	
	$ig_authorised = $storage->hasAccessToken('Instagram');
	    
	if(!empty($ig_api_key) && !empty($ig_api_secret)) :
	//Let's do our Instagram stuff here

		$ig_scopes = array('basic', 'comments', 'relationships', 'likes');
		// Setup the credentials for the requests
		$ig_credentials = new Credentials(
			$ig_api_key,
			$ig_api_secret,
			$currentUri->getAbsoluteUri().'?page=of_social_connect&authorised_instagram=true'
		);

		$ig_serviceFactory = new ServiceFactory();
		$instagramService = $ig_serviceFactory->createService('Instagram', $ig_credentials, $storage, $ig_scopes);	
						
		if(!empty($_GET['code']) && isset($_GET['authorised_instagram'])):
			
			echo 'Hello';
		    // This was a callback request from Instagram, get the token
		    $instagramService->requestAccessToken($_GET['code']);
		    // Send a request with it
		    $ig_result = json_decode($instagramService->request('users/self'), true);

			
    		// Show some of the resultant data
   			echo 'Your unique instagram user id is: ' . $ig_result['data']['id'] . ' and your name is ' . $ig_result['data']['full_name'];			

			$of_instagram_api = get_option('of_instagram_api');
			if (isset($of_instagram_api)
				&& is_array($of_instagram_api)
			) {
				$of_instagram_api['default_user_id'] = $ig_result['data']['id'];
				update_option('of_instagram_api', $of_instagram_api);
			}		
				 
			$url = admin_url('options-general.php?page=of_social_connect');
			$string = '<script type="text/javascript">';
			$string .= 'window.location = "' . $url . '"';
			$string .= '</script>';
			
			echo $string;

		elseif(!$ig_authorised) :
			
			try {
								
				$authorize_url = $instagramService->getAuthorizationUri();	
				echo '<a href="'.$authorize_url.'" class="button button-primary">Authorise your Instagram Account</a>';		
			
			} catch(Exception $exception) {
				
				echo $exception->getMessage();
			}
				
		else:
		
			echo '<p>You\'ve authorised your account on Instagram.</p>';
			
			$deauthorize_url = admin_url('options-general.php?page=of_social_connect&deauthorise_instagram=true');
	
			echo '<a href="'.$deauthorize_url.'" class="button button-primary">Deauthorise your Instagram Account</a>';
			
			echo '<p>You can start using the Widgets, Shortcodes and Functions included with this plugin.</p>';	
			
			echo '<hr>';

		
		endif;	    
		
	endif; ?>
