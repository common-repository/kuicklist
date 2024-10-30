<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              kuick.co
 * @since             1.0.0
 * @package           KuickList
 *
 * @wordpress-plugin
 * Plugin Name:       KuickList
 * Plugin URI:        kuicklist.com
 * Description:       Discover The Cutting Edge Software That Turns Hordes of Visitors Into Raving Fans While Making It Easy For You To Profit!
 * Version:           1.0.11
 * Author:            Kuick
 * Author URI:        kuick.co
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       kuicklist
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */

$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'), false);
$plugin_version = $plugin_data['Version'];

define( 'KUICKLIST_PLUGIN_VERSION', $plugin_version );


// KuickList Plugin's name
if (!defined('KUICKLIST_PLUGIN_NAME')) {
    define('KUICKLIST_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));
}

// KuickList Plugin's directory path
if (!defined('KUICKLIST_PLUGIN_DIR')) {
    define('KUICKLIST_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . KUICKLIST_PLUGIN_NAME);
}

// KuickList API URL
if (!defined('KUICKLIST_API_URL')) {
    if (is_ssl()) {
    	define('KUICKLIST_API_URL', 'https://app.kuicklist.com/api/v1/');
  	} else {
    	define('KUICKLIST_API_URL', 'http://app.kuicklist.com/api/v1/');
  	}
}
  	

// KuickList App URL
if (!defined('KUICKLIST_APP_URL')) {
    if (is_ssl()) {
    	define('KUICKLIST_APP_URL', 'https://app.kuicklist.com/');
    } else {
    	define('KUICKLIST_APP_URL', 'http://app.kuicklist.com/');
    }
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-kuicklist-activator.php
 */
function activate_kuicklist() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kuicklist-activator.php';
	KuickList_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-kuicklist-deactivator.php
 */
function deactivate_kuicklist() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kuicklist-deactivator.php';
	KuickList_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_kuicklist' );
register_deactivation_hook( __FILE__, 'deactivate_kuicklist' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-kuicklist.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_kuicklist() {

	$plugin = new KuickList();
	$plugin->run();

}
run_kuicklist();
