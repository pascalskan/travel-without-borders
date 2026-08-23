<?php
/**
 * Single post meta — Travel without Borders.
 *
 * Overrides the parent theme's part-meta.php. Two of the parent's three
 * items are deliberately gone:
 *
 *  - the author byline, which only ever read "admin"; posts are written in
 *    the company's voice, not by a named individual.
 *  - "Published in: <category>", which is not a useful cue on a single post.
 *
 * The date stays, and an "Updated" date appears whenever the post has been
 * edited on a later day than it was published. That is read from the post's
 * own modified date, so a revised article shows its currency without anyone
 * having to maintain the line by hand.
 *
 * The published label is "Originally published:" only when an Updated line is
 * actually shown, and plain "Published:" otherwise. "Originally" earns its
 * place by contrasting with a later revision; on a post that has never been
 * revised - a new article, for instance - it just reads oddly, implying a
 * history the post does not have.
 *
 * @package Ave Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$twb_published_day = get_the_time( 'Y-m-d' );
$twb_modified_day  = get_the_modified_time( 'Y-m-d' );
$twb_show_updated  = $twb_modified_day > $twb_published_day;
?>
<div class="post-meta twb-post-meta">

	<span class="posted-on">
		<span class="block text-uppercase ltr-sp-1"><?php
			echo esc_html( $twb_show_updated
				? __( 'Originally published:', 'ave' )
				: __( 'Published:', 'ave' ) );
		?></span>
		<time class="entry-date published" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
			<?php echo esc_html( get_the_date() ); ?>
		</time>
	</span>

	<?php if ( $twb_show_updated ) : ?>
	<span class="posted-updated">
		<span class="block text-uppercase ltr-sp-1"><?php esc_html_e( 'Updated:', 'ave' ); ?></span>
		<time class="entry-date updated" datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>">
			<?php echo esc_html( get_the_modified_date( 'F Y' ) ); ?>
		</time>
	</span>
	<?php endif; ?>

</div><!-- /.post-meta -->
