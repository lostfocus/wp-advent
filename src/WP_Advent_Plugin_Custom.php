<?php
class WP_Advent_Plugin_Custom {
	private $plugin_name;
	private $version;
	private $plugin;

	public function __construct( $plugin_name = 'wp-advent', $version, $plugin ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin = $plugin;

	}

	public function init(){
		global $wp_rewrite;

		$labels = array(
			'name'               => _x( 'Sheets', 'post type general name', 'wp-advent' ),
			'singular_name'      => _x( 'Sheet', 'post type singular name', 'wp-advent' ),
			'menu_name'          => _x( 'Sheets', 'admin menu', 'wp-advent' ),
			'name_admin_bar'     => _x( 'Sheet', 'add new on admin bar', 'wp-advent' ),
			'add_new'            => _x( 'Add New', 'sheet', 'wp-advent' ),
			'add_new_item'       => __( 'Add New Sheet', 'wp-advent' ),
			'new_item'           => __( 'New Sheet', 'wp-advent' ),
			'edit_item'          => __( 'Edit Sheet', 'wp-advent' ),
			'view_item'          => __( 'View Sheet', 'wp-advent' ),
			'all_items'          => __( 'All Sheets', 'wp-advent' ),
			'search_items'       => __( 'Search Sheets', 'wp-advent' ),
			'parent_item_colon'  => null,
			'not_found'          => __( 'No sheets found.', 'wp-advent' ),
			'not_found_in_trash' => __( 'No sheets found in Trash.', 'wp-advent' )
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'sheet' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments','revisions', 'custom-fields' )
		);

		register_post_type( 'wp_advent_sheet', $args );

		$labels = array(
			'name'              => _x( 'Calendars', 'taxonomy general name', 'wp-advent' ),
			'singular_name'     => _x( 'Calendar', 'taxonomy singular name', 'wp-advent' ),
			'search_items'      => __( 'Search Calendars', 'wp-advent' ),
			'popular_items'      => __( 'Popular Calendars', 'wp-advent' ),
			'all_items'         => __( 'All Calendars', 'wp-advent' ),
			'edit_item'         => __( 'Edit Calendar', 'wp-advent' ),
			'view_item'			=> __( 'View Calendar', 'wp-advent' ),
			'delete_item'			=> __( 'Delete Calendar', 'wp-advent' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'update_item'       => __( 'Update Calendar', 'wp-advent' ),
			'add_new_item'      => __( 'Add New Calendar', 'wp-advent' ),
			'new_item_name'     => __( 'New Calendar Name', 'wp-advent' ),
			'new_item_name_colon'     => __( 'New Calendar Name:', 'wp-advent' ),
			'menu_name'         => __( 'Calendar', 'wp-advent' ),
			'separate_items_with_commas' => __( 'Separate calendars with commas', 'wp-advent' ),
			'add_or_remove_items'        => __( 'Add or remove calendars', 'wp-advent' ),
			'choose_from_most_used'      => null,
			'not_found'                  => __( 'No calendars found.', 'wp-advent' ),
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
		if(!$calendar || is_wp_error($calendar) || (count($calendar) < 1)) {
			add_meta_box(
				'wp_advent_repair_meta_box',
				__( 'Add to Calendar', 'wp-advent' ),
				array( $this, 'render_repair_meta_box_content' ),
				$post_type,
				'side'
			);
		} else {
			add_meta_box(
				'wp_advent_meta_box',
				_x( 'Calendar', 'taxonomy singular name', 'wp-advent' ),
				array( $this, 'render_meta_box_content' ),
				$post_type,
				'side'
			);
		}
	}

	public function render_meta_box_content($post){
		$calendar_metadata = wp_get_object_terms($post->ID,'wp_advent_plugin_calendar');
		if(!$calendar_metadata || is_wp_error($calendar_metadata) || (count($calendar_metadata) < 1)) {
			return $this->render_repair_meta_box_content($post);
		}
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
			'posts_per_page'	=>	-1,
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

	public function render_repair_meta_box_content($post){
		$useTsToFix = true;
		$useTitleToFix = true;

		// If the post is set to any kind of december, we'll assume it is set to the december it should appear at.
		$ts = strtotime($post->post_date_gmt);
		if(date("m",$ts) != '12'){
			$useTsToFix = false;
		}

		// Apparently some people thought writing the calendar date into the title is the way to go.
		$titlets = strtotime($post->post_title);
		if(($titlets == 0) ||(date("m",$titlets) != '12')){
			$useTitleToFix = false;
		}

		if(!$useTsToFix && !$useTitleToFix){
			_e('We could not find a calendar for this sheet. Try to change the date of this post to the day you want the sheet to appear.','wp-advent');
			return;
		}

		$calendar_collection = WP_Advent_Plugin_Calendar_Collection::getInstance();
		$calendar_collection->setPlugin($this->plugin);
		$check_array = $calendar_collection->getCheckArray();

		$possible_calendars = array();

		if($useTsToFix){
			$postYear = date("Y",$ts);
			$postDay = date("j",$ts);
			if(isset($check_array[$postYear])){
				foreach($check_array[$postYear] as $calendar_id => $usedDays){
					if(!in_array($postDay,$usedDays)){
						$possible_calendars[] = $calendar_id;
					}
				}
			}
		} elseif($useTitleToFix) {
			$postYear = date("Y",$titlets);
			$postDay = date("j",$titlets);
			if(isset($check_array[$postYear])){
				foreach($check_array[$postYear] as $calendar_id => $usedDays){
					if(!in_array($postDay,$usedDays)){
						$possible_calendars[] = $calendar_id;
					}
				}
			}
		}

		if(count($possible_calendars) < 1){
			_e('We could not find a calendar for this sheet. Try to change the date of this post to the day you want the sheet to appear.','wp-advent');
			return;
		}

		wp_nonce_field( 'wp_advent_save_repair_meta_box_content', 'render_repair_meta_box_content_nonce' );

		foreach($possible_calendars as $calendar_id){
			$calendar = $calendar_collection->getCalendarById($calendar_id);
			require plugin_dir_path( dirname( __FILE__ ) ).'tpl/meta_box_repair.php';
		}
		?>
		<hr>
		<p class="meta-options">
			<label for="no_calendar_at_all" class="selectit">
				<input name="repair_add_to_calendar" type="radio" id="no_calendar_at_all" value="0">
				<?php _e('None of these calendars','wp-advent'); ?>
			</label>
		</p>
		<?php
	}

	public function wp_advent_save_repair_meta_box_content($post_id){
		if ( ! isset( $_POST['render_repair_meta_box_content_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['render_repair_meta_box_content_nonce'], 'wp_advent_save_repair_meta_box_content' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['repair_add_to_calendar'] ) ) {
			return;
		}

		$calendar_id = (int)$_POST['repair_add_to_calendar'];

		$calendar_collection = WP_Advent_Plugin_Calendar_Collection::getInstance();
		$calendar_collection->setPlugin($this->plugin);

		$calendar = $calendar_collection->getCalendarById($calendar_id);

		$post = get_post($post_id);
		$useTsToFix = true;
		$useTitleToFix = true;

		// If the post is set to any kind of december, we'll assume it is set to the december it should appear at.
		$ts = strtotime($post->post_date_gmt);
		if(date("m",$ts) != '12'){
			$useTsToFix = false;
		}

		// Apparently some people thought writing the calendar date into the title is the way to go.
		$titlets = strtotime($post->post_title);
		if(($titlets == 0) ||(date("m",$titlets) != '12')){
			$useTitleToFix = false;
		}

		if(!$useTsToFix && !$useTitleToFix){
			return;
		}

		$datestring = sprintf("d-12-%s 00:00:01",$calendar->getYear());

		if($useTsToFix){
			$newPostDate = strtotime(date($datestring,$ts));
			$post->post_date = date("Y-m-d H:i:s",$newPostDate);
			unset($post->post_date_gmt);
			$post->edit_date = true;
			// wp_update_post($post);
		} elseif($useTitleToFix){
			$newPostDate = strtotime(date($datestring,$titlets));
			$post->post_date = date("Y-m-d H:i:s",$newPostDate);
			unset($post->post_date_gmt);
			$post->edit_date = true;
		}

		remove_action( 'save_post', array($this, 'wp_advent_save_repair_meta_box_content'));
		wp_update_post($post);
		add_action( 'save_post', array($this, 'wp_advent_save_repair_meta_box_content'));

		return wp_set_post_terms( $post_id , array( (int)$_POST['repair_add_to_calendar']), 'wp_advent_plugin_calendar' );
	}
}
