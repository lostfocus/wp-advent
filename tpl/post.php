<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/style.css" />

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<?php the_content(); ?>
<?php endwhile; endif; ?>
