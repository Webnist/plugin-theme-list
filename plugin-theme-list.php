<?php
/**
 * Plugin Name: Plugin Theme List
 * Plugin URI:  https://wordpress.org/plugins/plugin-theme-list/
 * Description:
 * Version:     0.1.0
 * Author:      Webnist
 * Author URI:  https://profiles.wordpress.org/webnist
 * License: GPLv2 or later
 * Text Domain: plugin_theme_list
 * Domain Path: /languages
 */

if ( ! class_exists( 'PluginThemeListAdmin' ) )
	require_once( dirname(__FILE__) . '/includes/admin.php' );

if ( ! class_exists( 'PluginThemeListPostType' ) )
	require_once( dirname(__FILE__) . '/includes/post-type.php' );

class PluginThemeListInit {

	public function __construct() {
		$this->basename    = dirname( plugin_basename(__FILE__) );
		$this->dir         = plugin_dir_path( __FILE__ );
		$this->url         = plugin_dir_url( __FILE__ );
		$headers           = array(
			'name'        => 'Plugin Name',
			'version'     => 'Version',
			'domain'      => 'Text Domain',
			'domain_path' => 'Domain Path',
		);
		$data              = get_file_data( __FILE__, $headers );
		$this->name        = $data['name'];
		$this->version     = $data['version'];
		$this->domain      = $data['domain'];
		$this->domain_path = $data['domain_path'];
		load_plugin_textdomain( $this->domain, false, $this->name . $this->domain_path );
	}
}
new PluginThemeListInit();
new PluginThemeListAdmin();
new PluginThemeListPostType();