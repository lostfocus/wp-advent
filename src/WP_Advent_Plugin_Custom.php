<?php
class WP_Advent_Plugin_Custom {
	private $plugin_name;
	private $version;
	private $plugin;

	public function __construct( $plugin_name, $version, $plugin ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin = $plugin;

	}

	public function init(){
		global $wp_rewrite;

		$labels = array(
			'name'               => _x( 'Sheets', 'post type general name', $this->plugin_name ),
			'singular_name'      => _x( 'Sheet', 'post type singular name', $this->plugin_name ),
			'menu_name'          => _x( 'Sheets', 'admin menu', $this->plugin_name ),
			'name_admin_bar'     => _x( 'Sheet', 'add new on admin bar', $this->plugin_name ),
			'add_new'            => _x( 'Add New', 'sheet', $this->plugin_name ),
			'add_new_item'       => __( 'Add New Sheet', $this->plugin_name ),
			'new_item'           => __( 'New Sheet', $this->plugin_name ),
			'edit_item'          => __( 'Edit Sheet', $this->plugin_name ),
			'view_item'          => __( 'View Sheet', $this->plugin_name ),
			'all_items'          => __( 'All Sheets', $this->plugin_name ),
			'search_items'       => __( 'Search Sheets', $this->plugin_name ),
			'parent_item_colon'  => null,
			'not_found'          => __( 'No sheets found.', $this->plugin_name ),
			'not_found_in_trash' => __( 'No sheets found in Trash.', $this->plugin_name )
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'sheet' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments','revisions' )
		);

		register_post_type( 'wp_advent_sheet', $args );

		$labels = array(
			'name'              => _x( 'Calendars', 'taxonomy general name', $this->plugin_name ),
			'singular_name'     => _x( 'Calendar', 'taxonomy singular name', $this->plugin_name ),
			'search_items'      => __( 'Search Calendars', $this->plugin_name ),
			'popular_items'      => __( 'Popular Calendars', $this->plugin_name ),
			'all_items'         => __( 'All Calendars', $this->plugin_name ),
			'edit_item'         => __( 'Edit Calendar', $this->plugin_name ),
			'view_item'			=> __( 'View Calendar', $this->plugin_name ),
			'delete_item'			=> __( 'Delete Calendar', $this->plugin_name ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'update_item'       => __( 'Update Calendar', $this->plugin_name ),
			'add_new_item'      => __( 'Add New Calendar', $this->plugin_name ),
			'new_item_name'     => __( 'New Calendar Name', $this->plugin_name ),
			'new_item_name_colon'     => __( 'New Calendar Name:', $this->plugin_name ),
			'menu_name'         => __( 'Calendar', $this->plugin_name ),
			'separate_items_with_commas' => __( 'Separate calendars with commas', $this->plugin_name ),
			'add_or_remove_items'        => __( 'Add or remove calendars', $this->plugin_name ),
			'choose_from_most_used'      => null,
			'not_found'                  => __( 'No calendars found.', $this->plugin_name ),
		);

		$args = array(
			'hierarchical'          => false,
			'labels'                => $labels,
			'show_ui'               => false,
			'show_admin_column'     => false,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'calendar' ),
		);

		register_taxonomy( 'wp_advent_plugin_calendar', array('wp_advent_sheet'), $args );

		$wp_rewrite->flush_rules( false );
	}

	public function enqueue_scripts(){
		if(get_post_type() == 'wp_advent_sheet'){
			?>
			<style type="text/css">
				a.page-title-action {
					display: none;
				}
			</style>
			<?php
		}
	}

	public function add_meta_box($post_type,$post)
	{
		if($post_type != "wp_advent_sheet") return;
		$calendar = wp_get_object_terms($post->ID,'wp_advent_plugin_calendar');
		if(!$calendar || is_wp_error($calendar) || (count($calendar) < 1)) return;
		add_meta_box(
			'wp_advent_meta_box',
			_x( 'Calendar', 'taxonomy singular name', $this->plugin_name ),
			array( $this, 'render_meta_box_content' ),
			$post_type,
			'side'
		);
	}

	public function render_meta_box_content($post){
		$calendar_metadata = wp_get_object_terms($post->ID,'wp_advent_plugin_calendar');
		if(!$calendar_metadata || is_wp_error($calendar_metadata) || (count($calendar_metadata) < 1)) return;
		$calendar_metadata = $calendar_metadata[0];
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
		);
		$query = new WP_Query( $args );
		$query->get_posts();
		if($query->post_count > 0){
			foreach($query->posts as $sheet){
				$calendar->addPost($sheet);
			}
		}
		require_once plugin_dir_path( dirname( __FILE__ ) ).'tpl/meta_box.php';

	}
}