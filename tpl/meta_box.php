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
					<?php if($day && ($day->ID == $post->ID)): ?>
						<span style="font-weight: bold;"><?php echo $daydate; ?></span>
					<?php elseif($day): ?>
						<a href="<?php echo get_edit_post_link($day->ID); ?>"><?php echo $daydate; ?></a>
					<?php else: ?>
						<a href="<?php echo admin_url('tools.php?page=wp_advent_plugin&noheader=true&action=addsheet&calendar='.$calendar->getId().'&day='.$daydate); ?>"><?php echo $daydate; ?></a>
					<?php endif; ?>
				</td>
			<?php endfor; ?>
		</tr>
	<?php endfor; ?>
</table>