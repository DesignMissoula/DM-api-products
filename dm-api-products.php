<?php
/*
Plugin Name: DM API Products
Plugin URI: http://designmissoula.com/
Description: This is not just a plugin, it symbolizes the hope of every page.
Author: Bradford Knowlton
Version: 1.0.1
Author URI: http://bradknowlton.com/
*/

// Initialize Settings
require_once(sprintf("%s/inc/settings.php", dirname(__FILE__)));


if( is_admin() )
    $my_settings_page = new MySettingsPage();