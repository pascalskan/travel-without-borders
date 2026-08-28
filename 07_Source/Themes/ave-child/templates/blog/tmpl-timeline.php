<?php
/**
 * Timeline blog listing card — Travel without Borders.
 *
 * Overrides the parent theme's templates/blog/tmpl-timeline.php, which is
 * loaded through locate_template() and so picks the child copy up first.
 *
 * Two changes from the parent.
 *
 * **The publication date is gone.** The posts are evergreen guides rather than
 * news, and a 2019 date on the card made current advice look stale before
 * anyone had read a word of it. The date is still recorded on the post itself
 * and still drives the ordering of this list — it is only removed from the card.
 *
 * **The thumbnail is requested at `large` rather than `liquid-timeline-blog`.**
 * The client reported the cards as blurred, and the cause was this template,
 * not the photographs. `liquid-timeline-blog` is a fixed 490x300 crop, so every
 * card was served a 490px image whatever was uploaded — while the card is
 * displayed 473px wide and therefore wants roughly 946px to be sharp on a 2x
 * screen. Seven of the eight originals are larger than 490px and were being
 * thrown away; two of them are over 1000px.
 *
 * `large` is asked for rather than `full` because it caps the download at
 * 1024px, which still clears the ~946px needed, and WordPress falls back to the
 * full file by itself when a `large` was never generated — which is what the six
 * smaller images get. Nothing needs regenerating.
 *
 * This is safe to do only because the visible image is a background, not the
 * `<img>`: the theme's `data-responsive-bg` script reads the img's `currentSrc`
 * (or its `data-src` under lazy loading) and paints it onto the figure, and the
 * `<img>` itself is `visibility: hidden`. Nothing can be stretched — had the
 * `<img>` been the visible element it would have been squashed, since it
 * carries `object-fit: fill`.
 *
 * **The cards no longer share a height, and that is deliberate.** The hidden
 * `<img>` is still in normal flow, so it is what gives the figure its height —
 * which means each card now takes the shape of its own photograph instead of
 * the uniform 490x300 every card used to be forced into. A 16:9 photograph
 * makes a 266px card, a 4:3 one makes 355px, and the square "7 Facts" image
 * makes a 473px square.
 *
 * The uniform height could be restored with `aspect-ratio` on the figure, but
 * only by cropping to fit, and the client's instruction was the opposite —
 * "we dont want the photos cropped at all they should show all the image as
 * best as possible" (2026-08-28). Letterboxing to a fixed box was tried and
 * rejected: it holds the height but puts black bars down the sides of the
 * portrait-ish images. The listing is a masonry grid, so it takes the varying
 * heights without leaving gaps.
 *
 * The cost is about 520KB more across the eight cards on this page. They are
 * lazy-loaded, so it is spread down the scroll rather than paid upfront. Two of
 * them are worth revisiting at source: `berlin-new.png` is a photograph saved as
 * a PNG (325KB) and `cd380ad3…` is a poorly compressed JPEG (277KB at 1024px).
 * Both would fall a long way with no visible loss.
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

<?php $this->entry_thumbnail( 'large' ) ?>

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
