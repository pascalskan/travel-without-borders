<?php
/**
 * Timeline blog listing card — Travel without Borders.
 *
 * Overrides the parent theme's templates/blog/tmpl-timeline.php, which is
 * loaded through locate_template() and so picks the child copy up first.
 *
 * The only change is that the publication date is gone. The posts are evergreen
 * guides rather than news, and a 2019 date on the card made current advice look
 * stale before anyone had read a word of it. The date is still recorded on the
 * post itself and still drives the ordering of this list — it is only removed
 * from the card.
 *
 * The <time> element carried the `published updated` microformat classes, so it
 * also fed hentry metadata. Nothing else on the site consumes that, and the
 * single-post view still publishes a proper `entry-date published` time through
 * templates/blog/single/part-meta.php, so the machine-readable date survives
 * where it matters.
 *
 * @package Ave Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$format = get_post_format();

?>
<div class="liquid-lp-details">
	<?php $this->entry_tags( 'bordered text-uppercase size-sm ltr-sp-1' ) ?>
</div><!-- /.liquid-lp-details -->

<?php $this->entry_thumbnail( 'liquid-timeline-blog' ) ?>

<a href="<?php the_permalink() ?>" class="liquid-overlay-link"><?php the_title(); ?></a>

<header class="liquid-lp-header">
	<?php $this->entry_title( 'font-weight-bold h3 size-sm' ); ?>
</header>

<?php $this->entry_content(); ?>

<footer class="liquid-lp-footer">
	<a href="<?php the_permalink(); ?>" class="btn btn-naked text-uppercase ltr-sp-1 size-sm font-weight-bold liquid-lp-read-more">
		<span>
			<span class="btn-line btn-line-before"></span>
			<span class="btn-txt"><?php esc_html_e( 'Continue Reading', 'ave' ); ?></span>
			<span class="btn-line btn-line-after"></span>
		</span>
	</a>
</footer>
