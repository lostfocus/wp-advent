<?php
class WP_Advent_Plugin_i18n {
	private $plugin_name;
	private $version;
	private $plugin;

	public function __construct( $plugin_name = 'wp-advent', $version, $plugin ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin = $plugin;

	}

	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			$this->plugin_name,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}

} 