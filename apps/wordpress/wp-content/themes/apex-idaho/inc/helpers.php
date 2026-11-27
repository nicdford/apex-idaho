<?php
/**
 * Small shared helpers.
 *
 * @package apex-idaho
 */

defined( 'ABSPATH' ) || exit;

/**
 * URL of a file inside the theme's assets directory.
 *
 * @param string $path Path relative to assets/.
 * @return string
 */
function apex_idaho_asset( $path ) {
	return APEX_IDAHO_URI . '/assets/' . ltrim( $path, '/' );
}

/**
 * Read a Carbon Fields post meta value.
 *
 * Carbon Fields stores simple fields as `_{name}` post meta, so when the
 * library is missing (Composer not run yet) the raw meta is still readable and
 * sponsor logos keep rendering.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Field name, without the leading underscore.
 * @return mixed
 */
function apex_idaho_cf_meta( $post_id, $key ) {
	if ( function_exists( 'carbon_get_post_meta' ) ) {
		return carbon_get_post_meta( $post_id, $key );
	}
	return get_post_meta( $post_id, '_' . $key, true );
}

/**
 * A theme mod saved under the old Beaver Builder child theme.
 *
 * Theme mods are stored per theme, so after switching themes get_theme_mod()
 * no longer sees the social URLs and logo that were configured in the BB
 * customizer. Read them from the old theme's option directly. A value saved
 * on this theme wins.
 *
 * @param string $name Theme mod name, e.g. fl-social-instagram.
 * @return string
 */
function apex_idaho_legacy_mod( $name ) {
	$own = get_theme_mod( $name );
	if ( $own ) {
		return (string) $own;
	}

	foreach ( array( 'bb-theme-child', 'bb-theme' ) as $stylesheet ) {
		$mods = get_option( 'theme_mods_' . $stylesheet );
		if ( is_array( $mods ) && ! empty( $mods[ $name ] ) ) {
			return (string) $mods[ $name ];
		}
	}

	return '';
}

/**
 * Social profile URLs, filterable.
 *
 * Instagram falls back to @apex_idaho, the handle the media rules ask
 * photographers to tag.
 *
 * @return array<string,string> service => URL, only services with a URL.
 */
function apex_idaho_social_urls() {
	$urls = array(
		'instagram' => apex_idaho_legacy_mod( 'fl-social-instagram' ),
		'facebook'  => apex_idaho_legacy_mod( 'fl-social-facebook' ),
		'youtube'   => apex_idaho_legacy_mod( 'fl-social-youtube' ),
		'tiktok'    => apex_idaho_legacy_mod( 'fl-social-tiktok' ),
	);

	if ( ! $urls['instagram'] ) {
		$urls['instagram'] = 'https://www.instagram.com/apex_idaho/';
	}

	return array_filter( (array) apply_filters( 'apex_idaho_social_urls', $urls ) );
}

/**
 * Markup for a core/social-links block listing the configured profiles.
 *
 * @param string $extra_class Extra class for the wrapper.
 * @return string Block markup.
 */
function apex_idaho_social_links_markup( $extra_class = '' ) {
	$items = '';
	foreach ( apex_idaho_social_urls() as $service => $url ) {
		$items .= sprintf(
			'<!-- wp:social-link {"url":"%1$s","service":"%2$s"} /-->',
			esc_url( $url ),
			esc_attr( $service )
		);
	}

	if ( ! $items ) {
		return '';
	}

	$class = trim( 'apex-social ' . $extra_class );

	return sprintf(
		'<!-- wp:social-links {"className":"%1$s","layout":{"type":"flex","flexWrap":"nowrap"}} --><ul class="wp-block-social-links %1$s">%2$s</ul><!-- /wp:social-links -->',
		esc_attr( $class ),
		$items
	);
}

/**
 * The logo image the old header hard-coded, when it exists on this install.
 *
 * @return string URL or ''.
 */
function apex_idaho_legacy_logo_url() {
	$relative = '/2023/07/APEX_Idaho_logo-2-300x77.png';
	$uploads  = wp_get_upload_dir();

	if ( ! empty( $uploads['basedir'] ) && file_exists( $uploads['basedir'] . $relative ) ) {
		return $uploads['baseurl'] . $relative;
	}

	$bb_logo = apex_idaho_legacy_mod( 'fl-logo-image' );
	return $bb_logo ? $bb_logo : '';
}

/**
 * Linked logo for places where the Site Logo block has nothing to show.
 *
 * Order: Site Logo set in the editor, the logo the old theme used, then a
 * typographic wordmark so the header never renders empty.
 *
 * @param string $context header|footer.
 * @return string HTML.
 */
function apex_idaho_logo_fallback_html( $context = 'header' ) {
	$home = esc_url( home_url( '/' ) );
	$name = get_bloginfo( 'name' );
	$url  = apex_idaho_legacy_logo_url();

	if ( $url ) {
		return sprintf(
			'<a href="%1$s" class="apex-logo apex-logo--%3$s" rel="home"><img src="%2$s" alt="%4$s" width="300" height="77" decoding="async"></a>',
			$home,
			esc_url( $url ),
			esc_attr( $context ),
			esc_attr( $name ? $name : 'APEX Idaho' )
		);
	}

	return sprintf(
		'<a href="%1$s" class="apex-wordmark apex-wordmark--%2$s" rel="home" aria-label="%3$s"><span class="apex-wordmark__apex">APEX</span><span class="apex-wordmark__idaho">Idaho</span></a>',
		$home,
		esc_attr( $context ),
		esc_attr( $name ? $name : 'APEX Idaho' )
	);
}

/**
 * Upcoming events from The Events Calendar, normalised for the theme's blocks.
 *
 * Returns an empty array when TEC is not active, so every caller has to handle
 * the "no events" state anyway.
 *
 * @param int    $count    How many.
 * @param string $category Optional tribe_events_cat slug.
 * @return array<int,array<string,mixed>>
 */
function apex_idaho_upcoming_events( $count = 3, $category = '' ) {
	$count = max( 1, min( 12, (int) $count ) );

	$events = apply_filters( 'apex_idaho_pre_upcoming_events', null, $count, $category );
	if ( is_array( $events ) ) {
		return $events;
	}

	if ( ! function_exists( 'tribe_get_events' ) ) {
		return array();
	}

	$args = array(
		'posts_per_page' => $count,
		'eventDisplay'   => 'list',
		'post_status'    => 'publish',
	);
	if ( $category ) {
		$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'tribe_events_cat',
				'field'    => 'slug',
				'terms'    => $category,
			),
		);
	}

	$posts  = tribe_get_events( $args );
	$events = array();

	foreach ( (array) $posts as $post ) {
		$post = get_post( $post );
		if ( ! $post ) {
			continue;
		}

		$start = function_exists( 'tribe_get_start_date' ) ? tribe_get_start_date( $post, false, 'Y-m-d H:i:s' ) : '';
		$end   = function_exists( 'tribe_get_end_date' ) ? tribe_get_end_date( $post, false, 'Y-m-d H:i:s' ) : '';

		$events[] = array(
			'id'    => $post->ID,
			'title' => get_the_title( $post ),
			'url'   => get_permalink( $post ),
			'start' => $start,
			'end'   => $end,
			'venue' => function_exists( 'tribe_get_venue' ) ? (string) tribe_get_venue( $post->ID ) : '',
			'city'  => function_exists( 'tribe_get_city' ) ? (string) tribe_get_city( $post->ID ) : '',
			'cost'  => function_exists( 'tribe_get_cost' ) ? (string) tribe_get_cost( $post->ID, true ) : '',
			'image' => get_the_post_thumbnail_url( $post, 'large' ),
		);
	}

	return $events;
}

/**
 * Event start in milliseconds since the epoch (for countdown scripts), resolved
 * in the site's timezone.
 *
 * @param string $datetime Y-m-d H:i:s in site time.
 * @return int 0 when unparseable.
 */
function apex_idaho_js_timestamp( $datetime ) {
	if ( ! $datetime ) {
		return 0;
	}
	try {
		$dt = new DateTime( $datetime, wp_timezone() );
		return $dt->getTimestamp() * 1000;
	} catch ( Exception $e ) {
		return 0;
	}
}

/**
 * "Jun 13", "Jun 26–27" or "Jun 30 – Jul 1".
 *
 * @param string $start Start datetime.
 * @param string $end   End datetime.
 * @return string
 */
function apex_idaho_short_date_range( $start, $end ) {
	$s = $start ? strtotime( $start ) : 0;
	if ( ! $s ) {
		return '';
	}
	$e = $end ? strtotime( $end ) : $s;

	if ( gmdate( 'Y-m-d', $s ) === gmdate( 'Y-m-d', $e ) ) {
		return date_i18n( 'M j', $s );
	}
	if ( gmdate( 'Y-n', $s ) === gmdate( 'Y-n', $e ) ) {
		return date_i18n( 'M j', $s ) . '–' . date_i18n( 'j', $e );
	}
	return date_i18n( 'M j', $s ) . ' – ' . date_i18n( 'M j', $e );
}
