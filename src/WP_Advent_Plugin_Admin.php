<?php
class WP_Advent_Plugin_Admin {
	private $plugin_name;
	private $version;
	private $plugin;

	public function __construct( $plugin_name, $version, $plugin ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin = $plugin;

	}

	public function menues(){
		add_management_page(
			__('Advent Calendar',$this->plugin_name),
			__('Advent Calendar',$this->plugin_name),
			'edit_posts',
			'wp_advent_plugin',
			array($this, 'management_page')
		);
	}

	public function enqueue_scripts(){
		wp_enqueue_media();
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( dirname( __FILE__ )) . 'js/WP_Advent_Plugin_Admin.js', array( 'jquery' ), $this->version, false );

		$translation_array = array(
			'are_you_sure'	=>	__('Are you sure?',$this->plugin_name),
			'choose_image'	=>	__('Choose Image',$this->plugin_name)
		);
		wp_localize_script( $this->plugin_name, 'wpadventplugin', $translation_array );

		wp_enqueue_style($this->plugin_name, plugin_dir_url( dirname( __FILE__ )) . 'css/WP_Advent_Plugin_Admin.css', false, $this->version, false);
	}

	public function set_calendar_image(){
		$calendar_images = $this->plugin->getOption('calendar_images');
		if(!$calendar_images){
			$calendar_images = array();
		}
		$calendar = (int)$_POST['calendar'];
		$image = (int)$_POST['image'];
		$calendar_images[$calendar] = $image;
		$this->plugin->setOption('calendar_images',$calendar_images, true);
		echo 1; die();
	}

	public function management_page(){
		if(isset($_POST['submit']) && isset($_POST['ap_form']) && ($_POST['ap_form'] == 'new')){
			check_admin_referer( 'new_wp_advent_plugin_calendar' );
			$calendar_data = array(
				'cat_name'	=>	sanitize_text_field($_POST['new_wp_advent_plugin_calendar']),
				'category_description'	=>	(int)$_POST['new_wp_advent_plugin_calendar_year'],
				'taxonomy'	=>	'wp_advent_plugin_calendar'
			);
			$my_cat_id = wp_insert_category($calendar_data);
			unset($calendar_data);
		}

		if(isset($_POST['submit']) && isset($_POST['ap_form']) && ($_POST['ap_form'] == 'delete')){
			check_admin_referer( 'delete_wp_advent_plugin_calendar' );
			$category = get_term($_POST['calendar'],'wp_advent_plugin_calendar');
			$args = array(
				'post_type'	=>	'wp_advent_sheet',
				'tax_query' => array(
					array(
						'taxonomy' => 'wp_advent_plugin_calendar',
						'field'    => 'term_id',
						'terms'    => $category->term_id,
					),
				),
			);
			$query = new WP_Query( $args );
			$query->get_posts();

			if(count($query->posts) > 0){
				foreach($query->posts as $post){
					wp_delete_post($post->ID);
				}
			}

			wp_delete_term($category->term_id,'wp_advent_plugin_calendar');
		}

		if(isset($_GET['action']) && ($_GET['action'] == 'addsheet')) {
			$category = get_term($_GET['calendar'],'wp_advent_plugin_calendar');
			if($category){
				$args = array(
					'post_type'	=>	'wp_advent_sheet',
					'year'	=>	(int)$category->description,
					'day'	=>	(int)$_GET['day'],
					'tax_query' => array(
						array(
							'taxonomy' => 'wp_advent_plugin_calendar',
							'field'    => 'term_id',
							'terms'    => $category->term_id,
						),
					),
				);
				$query = new WP_Query( $args );
				$query->get_posts();
				if($query->post_count > 0){
					$post_id = $query->posts[0]->ID;
					$url = admin_url('post.php?post='.$post_id.'&action=edit');
					wp_redirect($url);
				} else {
					$date = strtotime(sprintf("%s-12-%s 00:00:01",(int)$category->description,(int)$_GET['day']));
					$post = array(
						'post_title'	=>	date("d.m.Y",$date),
						'post_type'	=>	'wp_advent_sheet',
						'post_date'	=>	date("Y-m-d H:i:s",$date),
						'post_status'	=>	'future'
					);
					$post_id = wp_insert_post($post);

					wp_set_post_terms( $post_id, array( (int)$category->term_id), 'wp_advent_plugin_calendar' );
					$url = get_edit_post_link($post_id,'edit');
					wp_redirect($url);
				}
			}
		}

		$calendar_collection = WP_Advent_Plugin_Calendar_Collection::getInstance();
		$calendar_collection->setPlugin($this->plugin);


		$calendar_ids = $calendar_collection->getCalendarIds();
		$calendars = $calendar_collection->getCalendars();

		$taxonomy = get_taxonomy('wp_advent_plugin_calendar');
		$plugin_name = $this->plugin_name;

		$args = array(
			'post_type'	=>	'wp_advent_sheet',
			'orderby' => 'post_date',
			'order'   => 'ASC',
			'tax_query' => array(
				array(
					'taxonomy' => 'wp_advent_plugin_calendar',
					'field'    => 'term_id',
					'terms'    => $calendar_ids,
					'operator' => 'NOT IN',
				),
			),
		);
		$no_calender_sheet_query = new WP_Query( $args );
		$no_calender_sheet_query->get_posts();

		require_once plugin_dir_path( dirname( __FILE__ ) ).'tpl/management_page.php';
	}

	protected function _sortCalendars($a, $b){
		if ((int)$a->description == (int)$b->description) {
			return 0;
		}
		return ((int)$a->description < (int)$b->description) ? 1 : -1;
	}
}