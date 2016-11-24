<h3>
	<span>
		<?php echo $calendar->getName(); ?> (<?php echo $calendar->getYear(); ?>)
	</span>
</h3>
<table class="wp-advent-admin-calendar">
	<?php for($i = 0; $i < 4; $i++): ?>
		<tr>
			<?php for($j = 1; $j <= 6; $j++):
				$daydate = $i * 6 + $j; ?>
				<?php $day = $calendar->getDay($daydate); ?>
				<td class="<?php echo ($day) ? 'sheet' : 'empty'; ?>">
					<?php echo $daydate; ?>
				</td>
			<?php endfor; ?>
		</tr>
	<?php endfor; ?>
</table>

<p class="meta-options">
	<label for="this_calendar_<?php echo $calendar->getId(); ?>" class="selectit">
		<input name="repair_add_to_calendar" type="radio" id="this_calendar_<?php echo $calendar->getId(); ?>" value="<?php echo $calendar->getId(); ?>">
		<?php _e('Add to this calendar','wp-advent'); ?>
	</label>
</p>
