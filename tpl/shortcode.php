<?php
$image = $calendar->getImage();

if ( $image ) {
	$image_attributes = wp_get_attachment_image_src( $image, 'large' );
}
$order = $calendar->getOrder();
?>

<div id="wp-advent-wrapper" class="year-<?php echo $calendar->getYear(); ?> calendar-<?php echo $calendar->getSlug(); ?>">
    <ul id="wp-advent-calendar" style="background-image: url(<?php echo $image_attributes[0]; ?>);">
		<?php for ( $i = 0; $i <= 23; $i ++ ): ?>
			<?php
			$active  = false;
			$daydate = $order[ $i ];
			$day     = $calendar->getDay( $daydate );

			if ( $day ) {
				$active = true;
			}
			// $day is a WordPress post object.
			?>
			<?php if ( $active ): ?>

				<?php if ( get_post_custom_values( 'wp_advent_image_id', $day->ID ) ) :
					$image_id = get_post_custom_values( 'wp_advent_image_id', $day->ID )[0];
					$image_url = wp_get_attachment_image_src( $image_id, 'large' )[0];
					?>
                    <li>
                        <a href="<?php echo $image_url; ?>" class="calendar-sheet js-calendar-sheet">
							<?php echo $daydate; ?>
                        </a>
                    </li>
				<?php elseif ( get_post_custom_values( 'wp_advent_video_url', $day->ID )[0] ) :
					$video_url = get_post_custom_values( 'wp_advent_video_url', $day->ID )[0];
					?>
                    <li>
                        <a href="<?php echo $video_url; ?>" class="calendar-sheet js-calendar-sheet">
							<?php echo $daydate; ?>
                        </a>
                    </li>
				<?php else : ?>
                    <li class="day-<?php echo $day->ID; ?> status-<?php echo $day->post_status; ?>"><a href="<?php echo get_permalink( $day->ID ) ?>"
                           class="calendar-sheet js-calendar-sheet"><?php echo $daydate; ?></a></li>
				<?php endif; ?>
			<?php else: ?>
                <li><span><?php echo $daydate; ?></span></li>
			<?php endif; ?>
		<?php endfor; ?>
    </ul>
</div>
