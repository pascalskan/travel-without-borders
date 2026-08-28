<?php
/**
 * Related posts card — Travel without Borders.
 *
 * Overrides the parent theme's templates/related-post.php, which is loaded
 * through locate_template() and so picks the child copy up first.
 *
 * The only change is the removal of the category list that the parent prints
 * under each card title:
 *
 *     a <ul class="related-post-categories"> wrapping a single <li> that
 *     printed liquid_get_category()
 *
 * Every post on this site sits in the single "Germany Travel Guide" category,
 * so that markup rendered the same label on all eight posts, twice per page,
 * each one linking to /category/germany-travel-guide/ — an archive nobody
 * designed. It inherits a stock header image that belongs to no article and
 * lists the posts in a layout unrelated to the blog index, which is how a
 * reader following it ends up somewhere that looks like a different website.
 * The client asked for that page to disappear (2026-08-26).
 *
 * Removing the link here is what makes it disappear from the site itself. The
 * archive URL is separately 301'd to /blog/ by the Redirection plugin, which
 * covers anyone arriving from a search result or an old bookmark; see
 * 05_Deployment/LIVE_SITE_CHANGES.md. The category is left in place on the
 * posts because Yoast and the WordPress admin both rely on it — it is only its
 * display that is dropped.
 *
 * Kept otherwise byte-for-byte in step with the parent so the two can be
 * diffed after a theme update.
 *
 * @package ave-child
 */

//get value from options
if ( class_exists( 'Liquid_Elementor_Addons' ) ){
	$page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );
	$page_settings_model = $page_settings_manager->get_model( get_the_ID() );

	$related_style = $page_settings_model->get_settings( 'post_style' );
	$related_style = $related_style ? $related_style : liquid_helper()->get_option( 'post-related-style' );
} else {
	$related_style = liquid_helper()->get_option( 'post-related-style' );
}

$col = '3';
if( '2' === $number_of_posts ) {
	$col = '6';
}
elseif( '3' === $number_of_posts ) {
	$col = '4';
}

?>
<div class="related-posts">

	<?php if( 'cover' === $related_style ) : ?>
	
		<div class="row">
			
			<?php if( !empty( $heading ) ) { ?>
				<div class="col-md-12">
					<h3 class="related-posts-title text-left"><?php echo esc_html( $heading ) ?></h3>
				</div><!-- /.col-md-12 -->
			<?php } ?>
			
			<?php while( $related_posts->have_posts() ): $related_posts->the_post(); ?>
				<div class="col-md-<?php echo esc_attr( $col ) ?> col-sm-12">
					
					<article class="related-post related-post-alt">

						<a href="<?php the_permalink() ?>" class="liquid-overlay-link"></a>
						<?php $thumb_url = wp_get_attachment_image_url( get_post_thumbnail_id(), 'liquid-rounded-blog' ); ?>
						<figure class="related-post-image" data-responsive-bg="true">
							<?php liquid_the_post_thumbnail( 'liquid-rounded-blog', '', false ); ?>
						</figure><!-- /.related-post-image -->
						<header class="related-post-header">
							<div class="related-post-date">
							<?php
								$time_string = '<time class="published updated" datetime="%1$s">%2$s</time>';
								printf( $time_string,
									esc_attr( get_the_date( 'c' ) ),
									get_the_date()
								);
							?>
							</div><!-- /.related-post-date -->
							<?php the_title( sprintf( '<h2 class="related-post-title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ) ?>
						</header>

					</article><!-- /.related-post -->
					
				</div><!-- /.col-md-6 col-sm-12 -->
			<?php endwhile; ?>
			
		</div><!-- /.row -->
						
	<?php else : ?>
	
		<div class="container">
			<div class="row">

			<?php if( !empty( $heading ) ) { ?>	
				<div class="col-md-12">
					<h3 class="related-posts-title"><?php echo esc_html( $heading ) ?></h3>
				</div><!-- /.col-md-12 -->
			<?php } ?>

			<?php while( $related_posts->have_posts() ): $related_posts->the_post(); ?>
				<div class="col-lg-<?php echo esc_attr( $col ) ?> col-md-6 col-sm-12">
	
					<article class="related-post">
						<a href="<?php the_permalink() ?>" class="liquid-overlay-link"></a>

						<figure class="related-post-image">
							<?php liquid_the_post_thumbnail( 'liquid-rounded-blog', '', false ); ?>
						</figure><!-- /.related-post-image -->

						<header class="related-post-header">
							<?php the_title( sprintf( '<h2 class="related-post-title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ) ?>
						</header>

					</article><!-- /.related-post -->
		
				</div><!-- /.col-lg-3 col-md-6 col-sm-12 -->
			<?php endwhile; ?>
	
			</div><!-- /.row -->
		</div><!-- /.container -->
	
	<?php endif; ?>

</div><!-- /.related-posts -->
<?php wp_reset_postdata();
