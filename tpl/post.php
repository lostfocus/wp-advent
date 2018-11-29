<link rel="stylesheet" href="<?php bloginfo( 'stylesheet_url' ); ?>" />

<?php wp_print_styles(); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<?php the_content(); ?>
<?php endwhile; endif; ?>
