<?php
/*
Plugin Name: Sending form data to url (test task)
Description: The plugin sends the form data to a specific URL and outputs the result. Configure the plugin before use.
Version: 1.0.0
Author: Vlad Korneev
Text Domain: sfdtu
*/
define( 'SFDTU_OPTION_GROUP', 'sfdtu-group' );
define( 'SFDTU_OPTION', 'sfdtu_option' );
define( 'SFDTU_SETTINGS', 'sfdtu_settings' );
define( 'SFDTU_ADMIN_PAGE', 'sfdtu' );
define( 'SFDTU_TEXT_DOMAIN', 'sfdtu' );
define( 'SFDTU_PATH_PLUGIN', plugin_dir_path( __FILE__ ) );
define( 'SFDTU_URL_PLUGIN', plugin_dir_url( __FILE__ ) );
define( 'SFDTU_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once( SFDTU_PATH_PLUGIN . 'class.SFDTUSettingsPage.php' );
$settings_page = new SFDTUSettingsPage();

require_once( SFDTU_PATH_PLUGIN . 'class.SFDTUBasicLogic.php' );
$basic_logic = new SFDTUBasicLogic();