<?php
/**
 * Plugin Name:       WP Advent
 * Description:       Enables an Advent calendar on your WordPress blog
 * Version:           1.1.2
 * Author:            <a href="http://martinschneider.me/">Martin Schneider</a> & <a href="http://dominikschwind.com/">Dominik Schwind</a>
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ).'src/WP_Advent_Plugin_Maintainer.php';
require_once plugin_dir_path( __FILE__ ).'src/WP_Advent_Plugin.php';

function run_wp_advent_plugin() {

	$plugin = new WP_Advent_Plugin();
	$plugin->run();

}
run_wp_advent_plugin();