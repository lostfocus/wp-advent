<?php
require_once plugin_dir_path( dirname( __FILE__ ) ).'src/WP_Advent_Plugin_Calendar_Admin.php';

class WP_Advent_Plugin_Calendar_Collection {
	private static $instance;

	private $calendars;
	private $calendar_ids;
	private $plugin;
	private $check_array;

	public static function getInstance()
	{
		if (null === static::$instance) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	protected function __construct()
	{
	}

	private function __clone() {}
	private function __wakeup() {}

	public function setPlugin($plugin){
		$this->plugin = $plugin;
	}

	public function getCalendars(){
		if(isset($this->calendars)) return $this->calendars;
		$this->_getCalendarsAndCalendarIds();
		return $this->calendars;
	}

	public function getCalendarIds(){
		if(isset($this->calendar_ids)) return $this->calendar_ids;
		$this->_getCalendarsAndCalendarIds();
		return $this->calendar_ids;
	}

	public function getCheckArray(){
		if(isset($this->check_array)) return $this->check_array;
		$this->_getCalendarsAndCalendarIds();
		return $this->check_array;
	}

	public function getCalendarById($id){
		if(isset($this->calendars) && isset($this->calendars[$id])){
			return $this->calendars[$id];
		}
		$this->_getCalendarsAndCalendarIds();
		if(isset($this->calendars) && isset($this->calendars[$id])){
			return $this->calendars[$id];
		}
		return false;
	}

	public function getCalendarsByYear($year){
		if(isset($this->check_array) && isset($this->check_array[$year])){
			$ids = array_keys($this->check_array[$year]);
			$calendars = array();
			foreach($ids as $id){
				$calendars[$id] = $this->getCalendarById($id);
			}
			return $calendars;
		}
		$this->_getCalendarsAndCalendarIds();
		if(isset($this->check_array) && isset($this->check_array[$year])){
			$ids = array_keys($this->check_array[$year]);
			$calendars = array();
			foreach($ids as $id){
				$calendars[$id] = $this->getCalendarById($id);
			}
			return $calendars;
		}
	}

	protected function _getCalendarsAndCalendarIds(){
		$calendar_metadatas = get_categories(array('taxonomy' => 'wp_advent_plugin_calendar','hide_empty' => 0));

		usort($calendar_metadatas,array($this,'_sortCalendars'));

		$calendar_images = $this->plugin->getOption('calendar_images');
		if(!$calendar_images){
			$calendar_images = array();
		}

		$this->calendar_ids = array();
		$this->calendars = array();
		foreach($calendar_metadatas as $calendar_metadata){
			$calendar = new WP_Advent_Plugin_Calendar_Admin();
			$calendar->setId($calendar_metadata->term_id);
			$calendar->setYear($calendar_metadata->description);
			$calendar->setName($calendar_metadata->name);
			$calendar->setSlug($calendar_metadata->slug);
			if(isset($calendar_images[$calendar->getId()])){
				// $image = wp_get_attachment_metadata($calendar_images[$calendar->getId()]);
				$image = $calendar_images[$calendar->getId()];
				$calendar->setImage($image);
			}

			if(function_exists('get_term_meta')){
				$calendar_order = get_term_meta($calendar_metadata->term_id,'calendar_order',true);
				$calendar->setOrder($calendar_order);
			}

			if(!isset($this->check_array[(int)$calendar_metadata->description])){
				$this->check_array[(int)$calendar_metadata->description] = array();
			}
			if(!isset($this->check_array[(int)$calendar_metadata->description][$calendar_metadata->term_id])){
				$this->check_array[(int)$calendar_metadata->description][$calendar_metadata->term_id] = array();
			}

			$args = array(
				'post_type'	=>	'wp_advent_sheet',
				'year'	=>	(int)$calendar_metadata->description,
				'tax_query' => array(
					array(
						'taxonomy' => 'wp_advent_plugin_calendar',
						'field'    => 'term_id',
						'terms'    => $calendar_metadata->term_id,
					),
				),
				'posts_per_page'	=>	-1,
			);
			$query = new WP_Query( $args );
			$query->get_posts();
			if($query->post_count > 0){
				foreach($query->posts as $post){
					$calendar->addPost($post);
					$this->check_array[(int)$calendar_metadata->description][$calendar_metadata->term_id][] = date("j",strtotime($post->post_date));

				}
			}
			$this->calendars[$calendar_metadata->term_id] = $calendar;

			$this->calendar_ids[] = $calendar_metadata->term_id;

		}
	}

	protected function _sortCalendars($a, $b){
		if ((int)$a->description == (int)$b->description) {
			return 0;
		}
		return ((int)$a->description < (int)$b->description) ? 1 : -1;
	}
}