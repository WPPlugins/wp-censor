<?php /*
Plugin Name: Censor
Plugin URI: http://censor.dcoda.co.uk/
Description: Versatile Censor Tool
Author: dcoda
Author URI: 
Version: 3.1.43
License: GPLv2 or later
*/
@require_once  dirname ( __FILE__ ) . '/library/wordpress/application.php';
if (class_exists("wv43v_application"))
{
	new wv43v_application ( __FILE__);
}