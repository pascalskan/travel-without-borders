<?php
/**
 * Single post layout — Travel without Borders.
 *
 * A copy of the parent theme's templates/blog/single/default.php with three
 * deliberate changes. Everything else is kept byte-for-byte so the parent's
 * hooks, classes and post-type guards keep behaving identically.
 *
 *  1. The share row calls twb_blog_share() rather than ave-core's
 *     liquid_portfolio_share(). The plugin's function cannot be replaced from
 *     a child theme — plugins load before themes, so its own function_exists
 *     guard always wins — but the template that calls it can be overridden.
 *
 *  2. The author bio box is dropped. Posts have no named author.
 *
 *  3. The content keeps the theme's own .blog-single-content wrapper, which
 *     is what assets/css/blog.css is anchored on. The .twb-blog-article class
 *     below is retained only so existing markup does not change; it no longer
 *     carries any styling, because tying the typography to this one template
 *     left posts using other layouts (cover-spaced, modern) unstyled.
 *
 * @package Ave Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$twb_is_layout_post = in_array(
	get_post_type(),
	array( 'liquid-header', 'liquid-footer', 'liquid-mega-menu', 'ld-product-layout' ),
	true
);
?>
<article <?php liquid_helper()->attr( 'post', array( 'class' => 'blog-single' ) ); ?>>

	<div class="container">

		<div class="row">

			<?php do_action( 'liquid_start_single_post_container' ); ?>

			<?php get_template_part( 'templates/blog/single/part', 'media' ); ?>

			<?php if ( ! $twb_is_layout_post ) : ?>
				<div class="blog-single-details">

					<header class="entry-header blog-single-header">

						<?php the_title( '<h1 class="blog-single-title entry-title h2">', '</h1>' ); ?>

						<?php get_template_part( 'templates/blog/single/part', 'meta' ); ?>

						<?php get_template_part( 'templates/blog/single/part', 'extra' ); ?>

					</header><!-- /.blog-single-header -->

				</div><!-- /.blog-single-details -->
			<?php endif; ?>

				<div class="blog-single-content entry-content twb-blog-article">
				<?php
					the_content(
						sprintf(
							/* translators: %s: post title. */
							esc_html__( 'Continue reading %s', 'ave' ),
							the_title( '<span class="screen-reader-text">', '</span>', false )
						)
					);

					wp_link_pages(
						array(
							'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'ave' ) . '</span>',
							'after'       => '</div>',
							'link_before' => '<span>',
							'link_after'  => '</span>',
							'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'ave' ) . ' </span>%',
							'separator'   => '<span class="screen-reader-text">, </span>',
						)
					);
				?>
				</div><!-- /.blog-single-content entry-content -->

				<?php if ( ! $twb_is_layout_post ) : ?>
				<footer class="blog-single-footer entry-footer">
					<?php the_tags( '<span class="tags-links">', esc_html_x( ' ', 'Used between list items, there is a space', 'ave' ), '</span>' ); ?>
					<?php
					if ( function_exists( 'twb_blog_share' ) ) {
						twb_blog_share();
					}
					?>
				</footer><!-- /.blog-single-footer entry-footer -->
				<?php endif; ?>

				<?php liquid_render_post_nav(); ?>

			<?php do_action( 'liquid_end_single_post_container' ); ?>

			<?php do_action( 'liquid_single_post_sidebar' ); ?>

		</div><!-- /.row -->
	</div><!-- /.container -->

	<?php liquid_render_related_posts( get_post_type() ); ?>
	<?php
		// Comments are closed on posts (see inc/blog.php); this stays for any
		// other post type that reaches this template.
		if ( comments_open() || get_comments_number() ) :
			comments_template();
		endif;
	?>

</article><!-- /.blog-single -->
