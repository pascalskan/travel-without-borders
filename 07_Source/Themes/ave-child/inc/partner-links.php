<?php
/**
 * TWB — Partner link click counter.
 *
 * WHY THIS EXISTS
 *
 * The client asked how many people click the CW Sports Travel link. Three
 * rounds of work tried to answer it through GA4 and could not: the GTM
 * container holds no GA4 tag, only four dead Universal Analytics tags whose
 * hits are relayed into GA4 by connected site tags, and that relay is what
 * suppresses Enhanced Measurement — so `link_domain` has never existed on the
 * property and the configured `cw_sports_travel_click` event cannot match.
 * Removing those tags needs container access we do not have.
 * See 01_Documentation/PARTNER_LINK_TRACKING.md.
 *
 * This counts the clicks on our own server instead, and so depends on nothing
 * outside this site. It has three advantages over the GA4 route beyond simply
 * working:
 *
 *  1. **It counts everyone.** GA4 only ever sees visitors who accept cookies,
 *     so every figure in that property is a floor, not a total. This is not.
 *  2. **No cookies, no consent question.** Nothing is stored about the
 *     visitor — see "What is recorded" below — so this is not analytics in the
 *     PECR sense and needs no banner interaction to work.
 *  3. **Nothing to configure.** No account, no tag, no container.
 *
 * The trade-off, which the client accepted: the browser's status bar shows
 * travelwithoutborders.co.uk/go/... rather than the partner's domain, and
 * there is no segmentation by device or country as GA4 would give.
 *
 * WHAT IS RECORDED
 *
 * A count, and the date. That is all. No IP address, no user agent, no
 * cookie, no identifier of any kind, so no visitor can be distinguished from
 * another. This deliberately measures **clicks, not people** — de-duplicating
 * into "people" would need a cookie or a stored IP hash, which would drag the
 * feature back under the consent rules it currently sits outside. The admin
 * screen is labelled accordingly.
 *
 * HOW THE LINK GETS ROUTED
 *
 * By filtering the rendered content rather than editing the page. The link
 * lives inside an `ld_fancy_heading` shortcode on the Football Camps page, so
 * it renders through `the_content`; a filter after `do_shortcode` (priority
 * 11) sees the finished anchor. Doing it this way means a WPBakery re-save
 * cannot silently switch the tracking off, which editing the href into the
 * page content would allow.
 *
 * ONLY registered destinations are ever redirected to, and the destination is
 * read from the table below — never from the request. A `/go/` endpoint that
 * accepted a target URL from a query string would be an **open redirect**, and
 * those get used for phishing under the borrowed credibility of the domain
 * they sit on.
 *
 * @package Ave Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bumped whenever the rewrite rule below changes shape, to trigger a flush.
 * Rewrite rules are stored in the database, so a rule added by a theme upload
 * does not exist until they are rebuilt — and there is no activation hook for
 * a theme that is already active. See twb_partner_links_maybe_flush().
 */
define( 'TWB_PARTNER_LINKS_RULES_VERSION', '1' );

/** Option holding the counts. Not autoloaded — only two screens ever read it. */
define( 'TWB_PARTNER_LINKS_OPTION', 'twb_partner_link_clicks' );

/**
 * The partners.
 *
 * `target` is both the canonical destination and the URL matched in content.
 * Matching the exact URL rather than merely the domain means a link to a
 * *different* page on the partner's site is left alone rather than being
 * quietly redirected somewhere the author did not intend. If the destination
 * ever changes, change it here — the link in the page can stay as it is.
 *
 * @return array<string,array<string,string>> Keyed by slug.
 */
function twb_partner_links_registry() {
	$partners = array(
		'cw-sports-travel' => array(
			'label'  => 'CW Sports Travel',
			'target' => 'https://cwsportstravel.com/clubs/',
			'where'  => 'Football Camps page, header text',
		),
	);

	/**
	 * Filter the partner registry.
	 *
	 * @param array $partners Keyed by slug.
	 */
	return apply_filters( 'twb_partner_links_registry', $partners );
}

/**
 * Normalise a URL for comparison: scheme-insensitive, trailing slash and
 * "www." insensitive. The link in the page and the value above should match
 * exactly today, but a WPBakery re-save or a hand edit can easily introduce a
 * trailing slash, and silently losing the count over that would be the worst
 * kind of failure here — invisible.
 *
 * @param string $url URL.
 * @return string
 */
function twb_partner_links_normalise( $url ) {
	$url = trim( (string) $url );
	$url = preg_replace( '#^https?://#i', '', $url );
	$url = preg_replace( '#^www\.#i', '', $url );
	return rtrim( strtolower( $url ), '/' );
}

/**
 * The public URL for a partner's counted link.
 *
 * @param string $slug Partner slug.
 * @return string
 */
function twb_partner_links_go_url( $slug ) {
	return home_url( '/go/' . $slug . '/' );
}

/* -------------------------------------------------------------------------
 * Routing
 * ---------------------------------------------------------------------- */

/**
 * Register /go/<slug>/.
 */
function twb_partner_links_add_rewrite() {
	add_rewrite_rule( '^go/([^/]+)/?$', 'index.php?twb_partner_go=$matches[1]', 'top' );
}
add_action( 'init', 'twb_partner_links_add_rewrite' );

/**
 * Make the rule's value readable from the query.
 *
 * @param array $vars Query vars.
 * @return array
 */
function twb_partner_links_query_var( $vars ) {
	$vars[] = 'twb_partner_go';
	return $vars;
}
add_filter( 'query_vars', 'twb_partner_links_query_var' );

/**
 * Rebuild the rewrite rules once after a deployment that changed them.
 *
 * Runs on `init` at a late priority so the rule above is registered first.
 * A version option rather than an activation hook, because the theme is
 * already active when it is replaced and `after_switch_theme` never fires.
 * flush_rewrite_rules() is expensive, so this must stay one-shot.
 */
function twb_partner_links_maybe_flush() {
	if ( get_option( 'twb_partner_links_rules_version' ) === TWB_PARTNER_LINKS_RULES_VERSION ) {
		return;
	}
	flush_rewrite_rules( false );
	update_option( 'twb_partner_links_rules_version', TWB_PARTNER_LINKS_RULES_VERSION, false );
}
add_action( 'init', 'twb_partner_links_maybe_flush', 99 );

/* -------------------------------------------------------------------------
 * Counting and redirecting
 * ---------------------------------------------------------------------- */

/**
 * Whether this request should be counted.
 *
 * The redirect happens either way — a visitor is never made to suffer for a
 * counting decision. This only decides whether the click is *recorded*.
 *
 * Excluded:
 *  - HEAD requests: link checkers and preview generators, not readers.
 *  - Prefetch and prerender: the browser guessing, not a person clicking.
 *    Chrome sends `Sec-Purpose: prefetch`; older builds and other engines use
 *    `Purpose` or `X-Purpose`. Without this the count inflates on its own.
 *  - Obvious bots by user agent. Deliberately a short list of the honest ones,
 *    which is all that is achievable: anything determined to look human will,
 *    and no amount of pattern matching fixes that.
 *  - Logged-in users who can edit pages, so testing the link does not pad the
 *    client's numbers.
 *
 * @return bool
 */
function twb_partner_links_should_count() {
	if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'GET' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
		return false;
	}

	foreach ( array( 'HTTP_SEC_PURPOSE', 'HTTP_PURPOSE', 'HTTP_X_PURPOSE', 'HTTP_X_MOZ' ) as $header ) {
		if ( ! empty( $_SERVER[ $header ] ) && preg_match( '/prefetch|preview|prerender/i', $_SERVER[ $header ] ) ) {
			return false;
		}
	}

	$agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
	if ( '' === $agent ) {
		return false;
	}
	if ( preg_match( '/bot|crawl|spider|slurp|facebookexternalhit|preview|monitor|curl|wget|python-requests|headless|lighthouse|pingdom|uptime/i', $agent ) ) {
		return false;
	}

	if ( is_user_logged_in() && current_user_can( 'edit_pages' ) ) {
		return false;
	}

	return true;
}

/**
 * Record one click.
 *
 * Read-modify-write on a single option. Two clicks landing in the same
 * millisecond could lose one; at this link's volume that is not worth a custom
 * table or a transient lock, and the number is a measure of interest rather
 * than an invoice. Recorded here so the limitation is known rather than
 * discovered.
 *
 * Daily rows are trimmed to two years, which keeps the option small enough to
 * stay comfortably inside a single row for the life of this site.
 *
 * @param string $slug Partner slug.
 */
function twb_partner_links_record( $slug ) {
	$all = get_option( TWB_PARTNER_LINKS_OPTION, array() );
	if ( ! is_array( $all ) ) {
		$all = array();
	}

	$now   = current_time( 'mysql' );
	$today = current_time( 'Y-m-d' );

	if ( ! isset( $all[ $slug ] ) || ! is_array( $all[ $slug ] ) ) {
		$all[ $slug ] = array(
			'total' => 0,
			'first' => $now,
			'last'  => $now,
			'days'  => array(),
		);
	}

	$all[ $slug ]['total'] = (int) $all[ $slug ]['total'] + 1;
	$all[ $slug ]['last']  = $now;

	$days                    = isset( $all[ $slug ]['days'] ) && is_array( $all[ $slug ]['days'] ) ? $all[ $slug ]['days'] : array();
	$days[ $today ]          = isset( $days[ $today ] ) ? (int) $days[ $today ] + 1 : 1;
	$cutoff                  = gmdate( 'Y-m-d', strtotime( $today . ' -730 days' ) );
	foreach ( array_keys( $days ) as $date ) {
		if ( $date < $cutoff ) {
			unset( $days[ $date ] );
		}
	}
	ksort( $days );
	$all[ $slug ]['days'] = $days;

	update_option( TWB_PARTNER_LINKS_OPTION, $all, false );
}

/**
 * Handle /go/<slug>/ — count, then send the visitor on.
 *
 * **302, not 301, and that is not a detail.** A permanent redirect is cached
 * by the browser, so the second and every later click would go straight to the
 * partner without ever reaching this site — the counter would read 1 forever
 * and look broken rather than wrong. Search engines would also treat the URL
 * as consolidated. A temporary redirect is re-requested every time, which is
 * exactly what a counter needs.
 *
 * An unknown slug goes to the home page rather than 404ing: a stale printed or
 * shared link should still land somewhere useful.
 */
function twb_partner_links_handle() {
	$slug = get_query_var( 'twb_partner_go' );
	if ( ! $slug ) {
		return;
	}

	$slug     = sanitize_title( $slug );
	$partners = twb_partner_links_registry();

	if ( ! isset( $partners[ $slug ] ) ) {
		wp_redirect( home_url( '/' ), 302 );
		exit;
	}

	if ( twb_partner_links_should_count() ) {
		twb_partner_links_record( $slug );
	}

	// WP Rocket and any other page cache must not serve this from disk, or the
	// request never reaches PHP and nothing is counted. Redirects are not
	// normally cached, but this is cheap and removes the doubt.
	if ( ! defined( 'DONOTCACHEPAGE' ) ) {
		define( 'DONOTCACHEPAGE', true );
	}
	nocache_headers();

	// wp_redirect, not wp_safe_redirect: the destination is external by
	// definition. It is safe here because it comes from the registry above and
	// never from the request.
	wp_redirect( $partners[ $slug ]['target'], 302 );
	exit;
}
add_action( 'template_redirect', 'twb_partner_links_handle', 0 );

/* -------------------------------------------------------------------------
 * Rewriting the links in content
 * ---------------------------------------------------------------------- */

/**
 * Point any registered partner link at its counted URL.
 *
 * Priority 20 so it runs after do_shortcode (11) and therefore sees the anchor
 * WPBakery produced, not the shortcode that produces it.
 *
 * Only the href is touched: target, rel and the data attributes already on the
 * link are left exactly as they are.
 *
 * @param string $content Rendered content.
 * @return string
 */
function twb_partner_links_rewrite( $content ) {
	if ( is_admin() || false === strpos( $content, '<a ' ) ) {
		return $content;
	}

	$partners = twb_partner_links_registry();
	if ( empty( $partners ) ) {
		return $content;
	}

	$lookup = array();
	foreach ( $partners as $slug => $partner ) {
		$lookup[ twb_partner_links_normalise( $partner['target'] ) ] = $slug;
	}

	return preg_replace_callback(
		'/href=(["\'])(.*?)\1/i',
		function ( $m ) use ( $lookup ) {
			$key = twb_partner_links_normalise( html_entity_decode( $m[2], ENT_QUOTES, 'UTF-8' ) );
			if ( ! isset( $lookup[ $key ] ) ) {
				return $m[0];
			}
			return 'href=' . $m[1] . esc_url( twb_partner_links_go_url( $lookup[ $key ] ) ) . $m[1];
		},
		$content
	);
}
add_filter( 'the_content', 'twb_partner_links_rewrite', 20 );

/* -------------------------------------------------------------------------
 * Seeing the numbers
 * ---------------------------------------------------------------------- */

/**
 * Counts for one partner over the last N days.
 *
 * @param array $record Stored record.
 * @param int   $days   Window.
 * @return int
 */
function twb_partner_links_sum_last_days( $record, $days ) {
	if ( empty( $record['days'] ) || ! is_array( $record['days'] ) ) {
		return 0;
	}
	$from  = current_time( 'Y-m-d' );
	$from  = gmdate( 'Y-m-d', strtotime( $from . ' -' . ( (int) $days - 1 ) . ' days' ) );
	$total = 0;
	foreach ( $record['days'] as $date => $count ) {
		if ( $date >= $from ) {
			$total += (int) $count;
		}
	}
	return $total;
}

/**
 * Reset one partner's count to zero.
 *
 * A real form POST to admin-post.php rather than a link, because a GET that
 * destroys data can be fired by anything that follows links on the page — a
 * prefetch, a link checker, a browser extension warming the cache. The nonce
 * would survive all of those; the visitor never intended any of them.
 *
 * Clears the record outright rather than zeroing the total, so the daily
 * history goes too. "Reset to zero" should not leave yesterday's rows behind.
 */
function twb_partner_links_handle_reset() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( esc_html__( 'You do not have permission to do that.', 'ave' ), 403 );
	}
	check_admin_referer( 'twb_partner_links_reset' );

	$slug     = isset( $_POST['slug'] ) ? sanitize_title( wp_unslash( $_POST['slug'] ) ) : '';
	$partners = twb_partner_links_registry();

	if ( isset( $partners[ $slug ] ) ) {
		$all = get_option( TWB_PARTNER_LINKS_OPTION, array() );
		if ( is_array( $all ) && isset( $all[ $slug ] ) ) {
			unset( $all[ $slug ] );
			update_option( TWB_PARTNER_LINKS_OPTION, $all, false );
		}
	}

	wp_safe_redirect( add_query_arg( 'twb_reset', '1', admin_url( 'tools.php?page=twb-partner-links' ) ) );
	exit;
}
add_action( 'admin_post_twb_partner_links_reset', 'twb_partner_links_handle_reset' );

/**
 * Add the admin screen. Under Tools rather than its own top-level menu — one
 * table of numbers does not earn a permanent place in the sidebar.
 */
function twb_partner_links_menu() {
	add_management_page(
		__( 'Partner Link Clicks', 'ave' ),
		__( 'Partner Link Clicks', 'ave' ),
		'edit_pages',
		'twb-partner-links',
		'twb_partner_links_render_page'
	);
}
add_action( 'admin_menu', 'twb_partner_links_menu' );

/**
 * Render the Tools screen.
 */
function twb_partner_links_render_page() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	$partners = twb_partner_links_registry();
	$all      = get_option( TWB_PARTNER_LINKS_OPTION, array() );
	if ( ! is_array( $all ) ) {
		$all = array();
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Partner Link Clicks', 'ave' ); ?></h1>

		<?php if ( ! empty( $_GET['twb_reset'] ) ) : ?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Count reset to zero.', 'ave' ); ?></p>
			</div>
		<?php endif; ?>
		<p class="description" style="max-width:46em">
			<?php esc_html_e( 'Counted on this website rather than in Google Analytics, so these numbers include every visitor — including those who decline cookies. Nothing is recorded about who clicked: no cookie, no IP address, no identifier. These are clicks, not people, so one person clicking twice counts twice.', 'ave' ); ?>
		</p>

		<table class="widefat striped" style="max-width:70em">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Link', 'ave' ); ?></th>
					<th style="text-align:right"><?php esc_html_e( 'Today', 'ave' ); ?></th>
					<th style="text-align:right"><?php esc_html_e( 'Last 7 days', 'ave' ); ?></th>
					<th style="text-align:right"><?php esc_html_e( 'Last 30 days', 'ave' ); ?></th>
					<th style="text-align:right"><?php esc_html_e( 'All time', 'ave' ); ?></th>
					<th><?php esc_html_e( 'Most recent click', 'ave' ); ?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ( $partners as $slug => $partner ) : ?>
				<?php
				$record = isset( $all[ $slug ] ) ? $all[ $slug ] : array( 'total' => 0, 'last' => '', 'days' => array() );
				$last   = ! empty( $record['last'] ) ? mysql2date( get_option( 'date_format' ) . ', ' . get_option( 'time_format' ), $record['last'] ) : '—';
				?>
				<tr>
					<td>
						<strong><?php echo esc_html( $partner['label'] ); ?></strong><br />
						<span class="description">
							<?php echo esc_html( $partner['where'] ); ?><br />
							<?php esc_html_e( 'Goes to:', 'ave' ); ?>
							<code><?php echo esc_html( $partner['target'] ); ?></code>
						</span>
					</td>
					<td style="text-align:right;font-size:15px"><?php echo esc_html( number_format_i18n( twb_partner_links_sum_last_days( $record, 1 ) ) ); ?></td>
					<td style="text-align:right;font-size:15px"><?php echo esc_html( number_format_i18n( twb_partner_links_sum_last_days( $record, 7 ) ) ); ?></td>
					<td style="text-align:right;font-size:15px"><?php echo esc_html( number_format_i18n( twb_partner_links_sum_last_days( $record, 30 ) ) ); ?></td>
					<td style="text-align:right;font-size:17px"><strong><?php echo esc_html( number_format_i18n( (int) $record['total'] ) ); ?></strong></td>
					<td><?php echo esc_html( $last ); ?></td>
					<td style="text-align:right">
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin:0">
							<?php wp_nonce_field( 'twb_partner_links_reset' ); ?>
							<input type="hidden" name="action" value="twb_partner_links_reset" />
							<input type="hidden" name="slug" value="<?php echo esc_attr( $slug ); ?>" />
							<button type="submit" class="button button-small"
								onclick="return confirm(<?php echo esc_attr( wp_json_encode( __( 'Reset this count to zero? The daily history is cleared too and this cannot be undone.', 'ave' ) ) ); ?>);">
								<?php esc_html_e( 'Reset to zero', 'ave' ); ?>
							</button>
						</form>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<?php
		foreach ( $partners as $slug => $partner ) :
			$record = isset( $all[ $slug ] ) ? $all[ $slug ] : array();
			if ( empty( $record['days'] ) ) {
				continue;
			}
			$days = array_slice( $record['days'], -30, 30, true );
			$max  = max( array_map( 'intval', $days ) );
			?>
			<h2><?php echo esc_html( sprintf( /* translators: %s: partner name */ __( '%s — last 30 days with clicks', 'ave' ), $partner['label'] ) ); ?></h2>
			<table class="widefat striped" style="max-width:40em">
				<tbody>
				<?php foreach ( array_reverse( $days, true ) as $date => $count ) : ?>
					<tr>
						<td style="width:12em"><?php echo esc_html( mysql2date( get_option( 'date_format' ), $date . ' 00:00:00' ) ); ?></td>
						<td style="width:4em;text-align:right"><strong><?php echo esc_html( number_format_i18n( (int) $count ) ); ?></strong></td>
						<td>
							<span style="display:inline-block;height:12px;border-radius:2px;background:#1e5630;width:<?php echo esc_attr( max( 2, round( ( (int) $count / max( 1, $max ) ) * 100 ) ) ); ?>%"></span>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * A dashboard widget as well, because a number nobody looks at is not an
 * answer. Tools is where the detail lives; this is so it is seen.
 */
function twb_partner_links_dashboard_widget() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}
	wp_add_dashboard_widget(
		'twb_partner_links',
		__( 'Partner Link Clicks', 'ave' ),
		'twb_partner_links_render_widget'
	);
}
add_action( 'wp_dashboard_setup', 'twb_partner_links_dashboard_widget' );

/**
 * Render the dashboard widget.
 */
function twb_partner_links_render_widget() {
	$partners = twb_partner_links_registry();
	$all      = get_option( TWB_PARTNER_LINKS_OPTION, array() );
	if ( ! is_array( $all ) ) {
		$all = array();
	}
	echo '<table class="widefat" style="border:0">';
	foreach ( $partners as $slug => $partner ) {
		$record = isset( $all[ $slug ] ) ? $all[ $slug ] : array( 'total' => 0, 'days' => array() );
		printf(
			'<tr><td style="padding-left:0"><strong>%1$s</strong><br /><span class="description">%2$s</span></td><td style="text-align:right;font-size:20px;padding-right:0"><strong>%3$s</strong></td></tr>',
			esc_html( $partner['label'] ),
			esc_html( sprintf( /* translators: %s: number of clicks */ __( '%s in the last 30 days', 'ave' ), number_format_i18n( twb_partner_links_sum_last_days( $record, 30 ) ) ) ),
			esc_html( number_format_i18n( (int) $record['total'] ) )
		);
	}
	echo '</table>';
	printf(
		'<p style="margin-bottom:0"><a href="%1$s">%2$s</a></p>',
		esc_url( admin_url( 'tools.php?page=twb-partner-links' ) ),
		esc_html__( 'See the detail', 'ave' )
	);
}
