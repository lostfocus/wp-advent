<div class="wrap">

	<h2><?php echo $taxonomy->labels->name; ?></h2>

	<?php // Neue Kalender anlegen ?>
	<h3><?php echo $taxonomy->labels->add_new_item; ?></h3>
	<form method="post">
		<label for="new_wp_advent_plugin_calendar"><?php echo $taxonomy->labels->new_item_name_colon; ?><br>
			<input required type="text" id="new_wp_advent_plugin_calendar" name="new_wp_advent_plugin_calendar" /><br>
		</label>
		<label for="new_wp_advent_plugin_calendar_year"><?php echo __('Year:'); ?><br>
			<select required name="new_wp_advent_plugin_calendar_year" id="new_wp_advent_plugin_calendar_year">
				<?php
					$year = (int)date("Y");
				?>
				<option value="<?php echo $year + 1; ?>"><?php echo $year + 1; ?></option>
				<option value="<?php echo $year; ?>" selected><?php echo $year; ?></option>
				<option value="<?php echo $year - 1; ?>"><?php echo $year - 1; ?></option>
			</select><br>
		</label>
		<input type="hidden" name="ap_form" value="new">
		<?php wp_nonce_field( 'new_wp_advent_plugin_calendar' ); ?>
		<?php submit_button($taxonomy->labels->add_new_item); ?>
	</form>

	<?php // Alle Kalender ?>
	<?php if(count($calendars) > 0): ?>
		<h3><?php echo $taxonomy->labels->all_items; ?></h3>
		<div id="poststuff">
			<div class="postbox-container">
				<div class="meta-box-sortables ui-sortable">

					<?php foreach($calendars as $calendar): ?>
						<div class="postbox closed">
							<!-- Zum Umschalten klicken -->
							<div class="handlediv" title="<?php _e("Click to switch",$plugin_name); ?>"><br></div>
							<h3>
								<span>
									<?php echo $calendar->getName(); ?> (<?php echo $calendar->getYear(); ?>)
								</span>
							</h3>
							<div class="inside">
								<div class="wp-advent-admin-floater-left">
									<p>
										<strong><?php _e("Shortcode",$plugin_name); ?></strong>
									</p>
									<p>
										<input class="wp-advent-shortcode-output" type="text" readonly value='[adventcalendar calendar="<?php echo $calendar->getSlug(); ?>"]'>
									</p>
									<p> <!-- Kalender verwalten -->
										<strong><?php _e("Manage calendar",$plugin_name); ?></strong>
									</p>
									<table class="wp-advent-admin-calendar">
										<?php for($i = 0; $i < 4; $i++): ?>
											<tr>
												<?php for($j = 1; $j <= 6; $j++):
													$daydate = $i * 6 + $j; ?>
													<?php $day = $calendar->getDay($daydate); ?>
													<td class="<?php echo ($day) ? 'sheet' : 'empty'; ?>">
														<a href="<?php echo admin_url('tools.php?page=wp_advent_plugin&noheader=true&action=addsheet&calendar='.$calendar->getId().'&day='.$daydate); ?>"><?php echo $daydate; ?></a>
													</td>
												<?php endfor; ?>
											</tr>
										<?php endfor; ?>
									</table>
								</div>

								<div class="wp-advent-admin-floater-right">
									<!-- Kalenderbild -->
									<p><strong><?php _e("Calendar cover image",$plugin_name); ?></strong></p>
									<?php $image = $calendar->getImage(); ?>
									<?php if($image): ?>
										<img src="<?php echo wp_get_attachment_thumb_url($image); ?>" class="wp-advent-imagepreview" />
										<p>
											<button
												id="wp_advent_plugin_add_image_<?php echo $calendar->getId(); ?>"
												data-calendar="<?php echo $calendar->getId(); ?>"
												class="button wp_advent_plugin_add_image jsonly">
												<?php echo __('Choose other image',$plugin_name); ?>
											</button>
										</p>
									<?php else : ?>
										<p>
											<button
												id="wp_advent_plugin_add_image_<?php echo $calendar->getId(); ?>"
												data-calendar="<?php echo $calendar->getId(); ?>"
												class="button wp_advent_plugin_add_image jsonly">
												<?php echo __('Choose other image',$plugin_name); ?>
											</button>
										</p>
									<?php endif; ?>
								</div>
								<div class="clear">
									<strong><?php echo $taxonomy->labels->delete_item ?></strong>
									<form method="post" class="wp-advent-delete-form">
										<input type="hidden" name="ap_form" value="delete">
										<input type="hidden" name="calendar" value="<?php echo $calendar->getId(); ?>">
										<?php wp_nonce_field( 'delete_wp_advent_plugin_calendar' ); ?>
										<?php submit_button($taxonomy->labels->delete_item,'delete'); ?>
									</form>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>