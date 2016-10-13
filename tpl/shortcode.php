<?php
	$image = $calendar->getImage();

	if ($image) {
		$image_attributes = wp_get_attachment_image_src($image, 'large');
	}
	$order = $calendar->getOrder();
?>

<div id="wp-advent-wrapper">
	<ul id="wp-advent-list" class="wp-advent-cf" style="background-image: url(<?php echo $image_attributes[0]; ?>);">
	<?php for($i = 0; $i <= 23; $i++): ?>
		<?php
			$active = false;
			$daydate = $order[$i];

			$day = $calendar->getDay($daydate);

			if($day){
				$active = true;
			}
			// $day is a WordPress post object.
		?>
		<?php if ($active): ?>
			<li><a href="<?php echo get_permalink($day->ID) ?>" class="calendar-sheet"><?php echo $daydate; ?></a></li>
		<?php else: ?>
			<li><span><?php echo $daydate; ?></span></li>
		<?php endif; ?>
	<?php endfor; ?>
	</ul>
</div>
