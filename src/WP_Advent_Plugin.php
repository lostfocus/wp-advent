<?php
class WP_Advent_Plugin {
	protected $loader;
	protected $plugin_name;
	protected $version;
	protected $options;

	public function __construct() {

		$this->plugin_name = 'wp-advent';
		$this->version = '1.2.3';

		$this->options = get_option($this->plugin_name);

		$this->_load_dependencies();
		$this->_set_locale();
		$this->_define_admin_hooks();
		$this->_define_public_hooks();
		$this->_define_custom_types();
	}

	public function run(){
		$this->loader->run();
	}

	public function getOption($key){
		if(!$this->options){
			$this->options = array();
		}
		if(!is_array($this->options)){
			$tmpOption = $this->options;
			$this->options = array();
			$this->options['option'] = $tmpOption;
			unset($tmpOption);
		}
		if(isset($this->options[$key])){
			return $this->options[$key];
		}
		return false;
	}

	public function setOption($key,$value,$unique = false){
		if(!$this->options){
			$this->options = array();
		}
		if(!is_array($this->options)){
			$tmpOption = $this->options;
			$this->options = array();
			$this->options['option'] = $tmpOption;
			unset($tmpOption);
		}
		if(isset($this->options[$key])){
			if($unique){
				$this->options[$key] = $value;
			} else {
				if(is_array($this->options[$key])){
					$this->options[$key][] = $value;
				} else {
					$newOptions = array($this->options[$key],$value);
					$this->options[$key] = $newOptions;
				}
			}
		} else {
			$this->options[$key] = $value;
		}
		update_option($this->plugin_name,$this->options);
	}

	protected function _load_dependencies(){
		require_once plugin_dir_path( dirname( __FILE__ ) ).'src/WP_Advent_Plugin_Loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ).'src/WP_Advent_Plugin_Admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ).'src/WP_Advent_Plugin_Custom.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ).'src/WP_Advent_Plugin_Shortcode.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ).'src/WP_Advent_Plugin_i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ).'src/WP_Advent_Plugin_Calendar.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ).'src/WP_Advent_Plugin_Calendar_Admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ).'src/WP_Advent_Plugin_Calendar_Collection.php';
		$this->loader = new WP_Advent_Plugin_Loader();
	}
	protected function _set_locale(){
		$plugin_i18n = new WP_Advent_Plugin_i18n('wp-advent', $this->version, $this);

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}
	protected function _define_admin_hooks(){
		$plugin_admin = new WP_Advent_Plugin_Admin ( $this->plugin_name, $this->version, $this );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'menues' );
		$this->loader->add_action( 'wp_ajax_wp_advent_set_calendar_image', $plugin_admin, 'set_calendar_image' );
	}
	protected function _define_public_hooks(){
		$shortcode = new WP_Advent_Plugin_Shortcode ( $this->plugin_name, $this->version, $this );
		$this->loader->add_action( 'init', $shortcode, 'init' );
		$this->loader->add_filter( 'single_template', $shortcode, 'wp_advent_custom_template' );
	}

	protected function _define_custom_types(){
		$plugin_custom = new WP_Advent_Plugin_Custom ( $this->plugin_name, $this->version, $this );
		$this->loader->add_action( 'init', $plugin_custom, 'init' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_custom, 'enqueue_scripts' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_custom, 'add_meta_box',10,2 );
		$this->loader->add_action( 'save_post', $plugin_custom, 'wp_advent_save_repair_meta_box_content' );
	}
}
