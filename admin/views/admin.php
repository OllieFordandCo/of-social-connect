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
use OAuth\Common\Exception\Exception;
use OAuth\ServiceFactory;
use OAuth\Common\Http\Uri\UriFactory;

$uriFactory = new UriFactory();
$currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
$currentUri->setQuery('');

?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
    
    <p>This plugin allows you to connect with your Social Networks</p>

    <div>
    <form method="post" action="options.php">
		<?php settings_fields('of_social_connect'); ?>
    	<?php do_settings_sections( 'of_social_connect' ); ?> 
		<?php submit_button( 'Save changes', 'primary', 'submit', true ); ?>
	</form>
	</div>
	<hr>
	<?php include_once(__DIR__ .'/twitter.php'); ?>
	<?php include_once(__DIR__ .'/instagram.php'); ?>
</div>