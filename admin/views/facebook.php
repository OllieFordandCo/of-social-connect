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
$facebook_api = get_option('of_facebook_api');

$fb_api_key = $facebook_api['key'];
$fb_api_secret = $facebook_api['secret'];

$storage = new WPDatabase();
?>

<?php

if(isset($_GET['deauthorise_facebook'])) :

    $storage->clearAuthorizationState('Facebook');
    $storage->clearToken('Facebook');

endif;


$fb_authorised = $storage->hasAccessToken('Facebook');

if(!empty($fb_api_key) && !empty($fb_api_secret)) :
    //Let's do our Facebook stuff here

    // Setup the credentials for the requests
    $fb_credentials = new Credentials(
        $fb_api_key,
        $fb_api_secret,
        $currentUri->getAbsoluteUri().'?page=of_social_connect&authorised_facebook=true'
    );

    $fb_serviceFactory = new ServiceFactory();
    $facebookService = $fb_serviceFactory->createService('Facebook', $fb_credentials, $storage, array('public_profile'));

    if(!empty($_GET['code']) && isset($_GET['authorised_facebook'])) :

        // This was a callback request from Facebook, get the token
        $facebookService->requestAccessToken($_GET['code']);
        // Send a request with it
        $fb_result = json_decode($facebookService->request('/me'), true);


        // Show some of the resultant data
        echo 'Your unique facebook user id is: ' . $fb_result['data']['id'] . ' and your name is ' . $fb_result['data']['full_name'];

        $of_facebook_api = get_option('of_facebook_api');
        if (isset($of_facebook_api)
            && is_array($of_facebook_api)
        ) {
            $of_facebook_api['default_user_id'] = $fb_result['data']['id'];
            update_option('of_facebook_api', $of_facebook_api);
        }

        $url = admin_url('options-general.php?page=of_social_connect');
        $string = '<script type="text/javascript">';
        $string .= 'window.location = "' . $url . '"';
        $string .= '</script>';

        echo $string;

    elseif(!$fb_authorised) :

        try {

            $authorize_url = $facebookService->getAuthorizationUri();
            echo '<a href="'.$authorize_url.'" class="button button-primary">Authorise your Facebook Account</a>';

        } catch(Exception $exception) {

            echo $exception->getMessage();
        }

    else:

        echo '<p>You\'ve authorised your account on Facebook.</p>';

        $deauthorize_url = admin_url('options-general.php?page=of_social_connect&deauthorise_facebook=true');

        echo '<a href="'.$deauthorize_url.'" class="button button-primary">Deauthorise your Facebook Account</a>';

        echo '<p>You can start using the Widgets, Shortcodes and Functions included with this plugin.</p>';

        echo '<hr>';


    endif;

endif; ?>
