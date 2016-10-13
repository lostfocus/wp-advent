<div class="wrap wp-advent-admin-wrapper">

	<h1><?php echo $taxonomy->labels->name; ?></h1>

	<?php // Neue Kalender anlegen ?>
	<h2><?php echo $taxonomy->labels->add_new_item; ?></h2>
	<form method="post">
		<label class="calendar-add-floater" for="new_wp_advent_plugin_calendar"><?php echo $taxonomy->labels->new_item_name_colon; ?><br>
			<input required type="text" id="new_wp_advent_plugin_calendar" name="new_wp_advent_plugin_calendar" /><br>
		</label>
		<label class="calendar-add-floater" for="new_wp_advent_plugin_calendar_year"><?php echo __('Year:'); ?><br>
			<select required name="new_wp_advent_plugin_calendar_year" id="new_wp_advent_plugin_calendar_year">
				<?php
					$year = (int)date("Y");
				?>
				<option value="<?php echo $year + 1; ?>"><?php echo $year + 1; ?></option>
				<option value="<?php echo $year; ?>" selected><?php echo $year; ?></option>
				<option value="<?php echo $year - 1; ?>"><?php echo $year - 1; ?></option>
			</select><br>
		</label>
		<div class="calendar-add-floater">
			<br/>
			<input type="hidden" name="ap_form" value="new">
			<?php wp_nonce_field( 'new_wp_advent_plugin_calendar' ); ?>
			<?php submit_button($taxonomy->labels->add_new_item); ?>
		</div>
		<br class="clear">
	</form>

	<?php // Alle Kalender ?>
	<?php if(count($calendars) > 0): ?>
		<h2 class="all-calendars-headline"><?php echo $taxonomy->labels->all_items; ?></h2>
		<div id="poststuff">
			<?php foreach($calendars as $calendar): ?>
				<h3><?php echo $calendar->getName(); ?> (<?php echo $calendar->getYear(); ?>)</h3>
				<div id="post-body" class="metabox-holder columns-2">

					<!-- main content -->
					<div id="post-body-content">

						<div class="meta-box-sortables ui-sortable">
							<div class="postbox">
								<h2><span><?php _e("Shortcode",$plugin_name); ?></span></h2>
								<div class="inside">
										<input class="large-text" type="text" readonly value='[adventcalendar calendar="<?php echo $calendar->getSlug(); ?>"]'>
								</div><!-- .inside -->
							</div><!-- .postbox -->
						</div><!-- .meta-box-sortables .ui-sortable -->

						<div class="meta-box-sortables ui-sortable">
							<div class="postbox">
								<h2><span><?php _e("Calendar cover image",$plugin_name); ?></span></h2>
								<div class="inside">
									<?php $image = $calendar->getImage(); ?>
									<?php if($image): ?>
										<img src="<?php echo wp_get_attachment_thumb_url($image); ?>" class="wp-advent-imagepreview" />
									<?php endif; ?>
									<p>
										<button
												id="wp_advent_plugin_add_image_<?php echo $calendar->getId(); ?>"
												data-calendar="<?php echo $calendar->getId(); ?>"
												class="button wp_advent_plugin_add_image jsonly">
											<?php echo __('Choose other image',$plugin_name); ?>
										</button>
									</p>
								</div><!-- .inside -->
							</div><!-- .postbox -->
						</div><!-- .meta-box-sortables .ui-sortable -->

					</div><!-- post-body-content -->

					<!-- sidebar -->
					<div id="postbox-container-1" class="postbox-container">
						<div class="meta-box-sortables">
							<div class="postbox">
								<h2><span><?php _e("Manage calendar",$plugin_name); ?></span></h2>
								<div class="inside">
									<table class="wp-advent-admin-calendar">
										<?php
											$order = $calendar->getOrder();
											for($i = 0; $i < 4; $i++): ?>
											<tr>
												<?php for($j = 0; $j <= 5; $j++):
													$daydatekey = $i * 6 + $j;
													$daydate = $order[$daydatekey];
													?>
													<?php $day = $calendar->getDay($daydate); ?>
													<td class="<?php echo ($day) ? 'sheet' : 'empty'; ?>">
														<?php if($day): ?>
															<a href="<?php echo get_edit_post_link($day->ID); ?>"><?php echo $daydate; ?></a>
														<?php else: ?>
															<a href="<?php echo admin_url('tools.php?page=wp_advent_plugin&noheader=true&action=addsheet&calendar='.$calendar->getId().'&day='.$daydate); ?>"><?php echo $daydate; ?></a>
														<?php endif; ?>
													</td>
												<?php endfor; ?>
											</tr>
										<?php endfor; ?>
									</table>
								</div><!-- .inside -->
							</div><!-- .postbox -->
						</div><!-- .meta-box-sortables -->

						<?php if(function_exists('update_term_meta')): ?>
							<div class="meta-box-sortables">
								<form method="post" class="wp-advent-randomize-form">
									<input type="hidden" name="ap_form" value="randomize" />
									<input type="hidden" name="calendar" value="<?php echo $calendar->getId(); ?>" />
									<?php wp_nonce_field( 'randomize_wp_advent_plugin_calendar' ); ?>
									<?php submit_button(__('Randomize days',$plugin_name),'randomize'); ?>
								</form>
							</div><!-- .meta-box-sortables -->
						<?php endif; ?>

						<div class="meta-box-sortables">
							<form method="post" class="wp-advent-delete-form">
								<input type="hidden" name="ap_form" value="delete" />
								<input type="hidden" name="calendar" value="<?php echo $calendar->getId(); ?>" />
								<?php wp_nonce_field( 'delete_wp_advent_plugin_calendar' ); ?>
								<?php submit_button($taxonomy->labels->delete_item,'delete'); ?>
							</form>
						</div><!-- .meta-box-sortables -->
					</div><!-- #postbox-container-1 .postbox-container -->
				</div><!-- #post-body .metabox-holder .columns-2 -->

				<br class="clear">
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<?php if($no_calender_sheet_query->post_count > 0): ?>
	<h3><?php _e('Sheets that somehow got lost',$plugin_name); ?></h3>
	<?php
		while ( $no_calender_sheet_query->have_posts() ) {
			$no_calender_sheet_query->the_post();
			echo '<li><a href="' . get_edit_post_link(get_the_ID()) . '">' . get_the_title(  );
			echo '</a>';
			echo '</li>';
		}
	?>
	<?php endif; ?>
</div>
