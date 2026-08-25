<?php
/**
 * Template Name: Luau Party
 *
 * Landing page for the APEX Idaho Luau Party at Magic Valley Speedway.
 *
 * Ticket sales are rendered from the Event Tickets attached to THIS post, so the
 * page that uses this template is also the ticket host. The tier cards below are
 * built from the live ticket objects; the actual purchase form is the stock
 * Event Tickets module, so cart/checkout behaviour is untouched.
 *
 * Note: this template deliberately does not call the_content(). Event Tickets
 * auto-injects its form into the_content on non-event post types, and calling it
 * here would render the purchase form twice.
 */

$luau_post_id = get_the_ID();

/**
 * Renders "September 5–6", "September 5", or "September 30 – October 1"
 * depending on how the range falls.
 */
if ( ! function_exists( 'apex_luau_format_date_range' ) ) {
	function apex_luau_format_date_range( $start, $end ) {
		$s = $start ? strtotime( $start ) : 0;
		if ( ! $s ) {
			return '';
		}
		$e = $end ? strtotime( $end ) : $s;

		if ( gmdate( 'Y-m-d', $s ) === gmdate( 'Y-m-d', $e ) ) {
			return date_i18n( 'F j', $s );
		}
		if ( gmdate( 'Y-n', $s ) === gmdate( 'Y-n', $e ) ) {
			return date_i18n( 'F j', $s ) . '–' . date_i18n( 'j', $e );
		}
		return date_i18n( 'F j', $s ) . ' – ' . date_i18n( 'F j', $e );
	}
}

/**
 * Event particulars. When this post is an Events Calendar event the real event
 * data is the source of truth, so the countdown and date labels cannot drift
 * from what is actually on sale. The literals are only fallbacks, and every
 * value stays filterable.
 */
$luau_ev_start = '';
$luau_ev_end   = '';
if ( function_exists( 'tribe_get_start_date' ) && 'tribe_events' === get_post_type( $luau_post_id ) ) {
	$luau_ev_start = tribe_get_start_date( $luau_post_id, false, 'Y-m-d H:i:s' );
	$luau_ev_end   = tribe_get_end_date( $luau_post_id, false, 'Y-m-d H:i:s' );
}

$luau_venue_default = 'Magic Valley Speedway';
if ( function_exists( 'tribe_get_venue' ) ) {
	$luau_tec_venue = tribe_get_venue( $luau_post_id );
	if ( $luau_tec_venue ) {
		$luau_venue_default = $luau_tec_venue;
	}
}

$luau_start   = apply_filters( 'apex_luau_start_date', $luau_ev_start ? $luau_ev_start : '2026-09-05 08:00:00' );
$luau_dates   = apply_filters( 'apex_luau_date_label', $luau_ev_start ? apex_luau_format_date_range( $luau_ev_start, $luau_ev_end ) : 'September 5–6' );
$luau_year    = apply_filters( 'apex_luau_year_label', $luau_ev_start ? date_i18n( 'Y', strtotime( $luau_ev_start ) ) : '2026' );
$luau_venue   = apply_filters( 'apex_luau_venue', $luau_venue_default );
$luau_city    = apply_filters( 'apex_luau_venue_city', 'Twin Falls, Idaho' );
$luau_map_url = apply_filters( 'apex_luau_map_url', 'https://maps.google.com/?q=Magic+Valley+Speedway+Twin+Falls+Idaho' );

// Countdown target, resolved in site time then handed to JS as a UTC timestamp.
$luau_start_ts = 0;
try {
	$luau_start_dt = new DateTime( $luau_start, wp_timezone() );
	$luau_start_ts = $luau_start_dt->getTimestamp() * 1000;
} catch ( Exception $e ) {
	$luau_start_ts = 0;
}

/**
 * Live ticket objects for this post, if Event Tickets is active.
 */
if ( ! function_exists( 'apex_luau_get_tickets' ) ) {
	function apex_luau_get_tickets( $post_id ) {
		if ( ! class_exists( 'Tribe__Tickets__Tickets' ) ) {
			return array();
		}
		$tickets = Tribe__Tickets__Tickets::get_all_event_tickets( $post_id );
		return is_array( $tickets ) ? $tickets : array();
	}
}

/**
 * The stock Event Tickets purchase module for this post.
 */
if ( ! function_exists( 'apex_luau_tickets_module' ) ) {
	function apex_luau_tickets_module( $post_id ) {
		if ( shortcode_exists( 'tribe_tickets' ) ) {
			return do_shortcode( sprintf( '[tribe_tickets post_id="%d"]', absint( $post_id ) ) );
		}
		return '';
	}
}

/**
 * Price label for a ticket, falling back gracefully if Event Tickets' currency
 * helper is unavailable.
 */
if ( ! function_exists( 'apex_luau_ticket_price' ) ) {
	function apex_luau_ticket_price( $ticket ) {
		$price = isset( $ticket->price ) ? $ticket->price : '';

		if ( '' === $price || null === $price ) {
			return '';
		}

		if ( 0 == $price ) {
			return 'Free';
		}

		if ( function_exists( 'tribe_format_currency' ) ) {
			return tribe_format_currency( $price, $ticket->ID );
		}

		return '$' . number_format_i18n( (float) $price, 2 );
	}
}

/**
 * Remaining-stock label, or '' when the ticket is unlimited.
 */
if ( ! function_exists( 'apex_luau_ticket_stock_label' ) ) {
	function apex_luau_ticket_stock_label( $ticket ) {
		if ( ! method_exists( $ticket, 'available' ) ) {
			return '';
		}

		$available = $ticket->available();

		// -1 means unlimited in Event Tickets.
		if ( -1 === $available || '' === $available || null === $available ) {
			return '';
		}

		$available = (int) $available;

		if ( $available <= 0 ) {
			return 'Sold out';
		}

		if ( $available <= 25 ) {
			return sprintf( 'Only %d left', $available );
		}

		return sprintf( '%d available', $available );
	}
}

$luau_tickets = apex_luau_get_tickets( $luau_post_id );

/**
 * Shown only when Event Tickets returns nothing for this page (not configured
 * yet, or sales not published). Once tickets exist in Event Tickets, the live
 * objects win and this array is ignored entirely.
 */
$luau_fallback_tiers = apply_filters(
	'apex_luau_fallback_tiers',
	array(
		array(
			'name'  => 'Driver Entry',
			'price' => '$300',
			'desc'  => 'Two days on track. Includes camping, food and drinks.',
		),
		array(
			'name'  => '2-Day Pit Pass',
			'price' => '$50',
			'desc'  => 'Both days in the pits. Includes camping, food and drinks.',
		),
	)
);
$luau_module  = apex_luau_tickets_module( $luau_post_id );

// Gallery: images attached to this page, otherwise the polaroids render as
// neon placeholders so the layout never collapses.
$luau_gallery = array();
if ( has_post_thumbnail( $luau_post_id ) ) {
	$luau_gallery[] = get_post_thumbnail_id( $luau_post_id );
}
foreach ( (array) get_attached_media( 'image', $luau_post_id ) as $luau_attachment ) {
	if ( ! in_array( $luau_attachment->ID, $luau_gallery, true ) ) {
		$luau_gallery[] = $luau_attachment->ID;
	}
}
$luau_gallery = array_slice( $luau_gallery, 0, 3 );

get_header();
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Barlow+Condensed:ital,wght@0,400;0,600;0,700;1,700&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">

<style>
/* ============================================================================
   LUAU PARTY — scoped entirely under .luau-page so nothing leaks into the
   Beaver Builder theme or other templates.
   ========================================================================= */
.luau-page {
  --ink:        #05000c;
  --ink-2:      #0d0118;
  --magenta:    #ff2bd6;
  --magenta-dp: #e8197d;
  --lime:       #7bff4d;
  --violet:     #8b3cff;
  --cyan:       #35e8ff;
  --paper:      #fdfdfd;

  --glow-magenta: 0 0 4px #fff, 0 0 12px var(--magenta), 0 0 34px var(--magenta), 0 0 70px rgba(255,43,214,.55);
  --glow-lime:    0 0 4px #eaffe2, 0 0 12px var(--lime), 0 0 32px var(--lime), 0 0 64px rgba(123,255,77,.45);
  --glow-violet:  0 0 10px var(--violet), 0 0 30px rgba(139,60,255,.6);

  position: relative;
  isolation: isolate;
  background: var(--ink);
  color: #fff;
  font-family: 'Barlow Condensed', system-ui, sans-serif;
  overflow-x: clip;
}
.luau-page *,
.luau-page *::before,
.luau-page *::after { box-sizing: border-box; }

.luau-page h1,
.luau-page h2,
.luau-page h3 { font-family: 'Archivo Black', Impact, sans-serif; margin: 0; line-height: .92; }
.luau-page p  { margin: 0; }
.luau-page a  { text-decoration: none; }
/* <figure> and <ul> carry UA margins that break the flex maths below. */
.luau-page figure, .luau-page figcaption, .luau-page ul { margin: 0; padding: 0; }

.luau-script { font-family: 'Dancing Script', cursive; font-weight: 700; }

/* ── Shared section furniture ─────────────────────────────────────────── */
.luau-section { position: relative; padding: 6.5rem 1.5rem; }
.luau-wrap    { max-width: 1180px; margin: 0 auto; position: relative; z-index: 2; }

.luau-eyebrow {
  font-weight: 700;
  letter-spacing: .42em;
  text-transform: uppercase;
  font-size: .82rem;
  color: var(--lime);
  text-shadow: 0 0 10px rgba(123,255,77,.8);
  margin-bottom: 1.1rem;
}

.luau-h2 {
  font-size: clamp(2.6rem, 7vw, 5.2rem);
  letter-spacing: -.01em;
  text-transform: uppercase;
  color: #fff;
  text-shadow: var(--glow-magenta);
}

.luau-lede {
  font-size: clamp(1.1rem, 2.2vw, 1.4rem);
  line-height: 1.55;
  color: rgba(255,255,255,.68);
  max-width: 58ch;
}

/* ── Backdrop: aurora blooms + neon grid floor + starfield ─────────────── */
.luau-aurora {
  position: absolute; inset: 0;
  overflow: hidden; z-index: 0; pointer-events: none;
}
.luau-bloom {
  position: absolute;
  border-radius: 50%;
  filter: blur(90px);
  opacity: .5;
  will-change: transform;
}
.luau-bloom--a { width: 46vw; height: 46vw; left: -12vw; top: -8vw;  background: var(--magenta); animation: luau-drift 19s ease-in-out infinite; }
.luau-bloom--b { width: 40vw; height: 40vw; right: -10vw; top: 12vw; background: var(--violet);  animation: luau-drift 23s ease-in-out infinite reverse; }
.luau-bloom--c { width: 34vw; height: 34vw; left: 28vw; bottom: -14vw; background: #1d7a3a; opacity: .38; animation: luau-drift 27s ease-in-out infinite; }

@keyframes luau-drift {
  0%, 100% { transform: translate3d(0,0,0) scale(1); }
  33%      { transform: translate3d(6vw,4vw,0) scale(1.14); }
  66%      { transform: translate3d(-4vw,7vw,0) scale(.9); }
}

/* Perspective grid floor, scrolling toward the viewer. */
.luau-grid-floor {
  position: absolute;
  left: -25%; right: -25%; bottom: 0;
  height: 46vh;
  z-index: 1;
  pointer-events: none;
  perspective: 320px;
  perspective-origin: 50% 0%;
  mask-image: linear-gradient(to top, #000 8%, transparent 92%);
  -webkit-mask-image: linear-gradient(to top, #000 8%, transparent 92%);
}
.luau-grid-floor::before {
  content: '';
  position: absolute; inset: -100% 0 0;
  transform: rotateX(72deg);
  transform-origin: 50% 100%;
  background-image:
    linear-gradient(to right,  rgba(255,43,214,.55) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(123,255,77,.42) 1px, transparent 1px);
  background-size: 62px 62px;
  animation: luau-grid-run 3.2s linear infinite;
}
@keyframes luau-grid-run {
  to { background-position: 0 62px, 0 62px; }
}

/* Drifting sparkles. */
.luau-sparks { position: absolute; inset: 0; z-index: 1; pointer-events: none; overflow: hidden; }
.luau-spark {
  position: absolute;
  width: 3px; height: 3px;
  border-radius: 50%;
  background: #fff;
  box-shadow: 0 0 8px 2px rgba(255,255,255,.85);
  opacity: 0;
  animation: luau-rise linear infinite;
}
@keyframes luau-rise {
  0%       { opacity: 0; transform: translateY(0) scale(.6); }
  12%      { opacity: 1; }
  85%      { opacity: .9; }
  100%     { opacity: 0; transform: translateY(-78vh) scale(1.3); }
}

/* ── Neon SVG line art ────────────────────────────────────────────────── */
.luau-neon-svg { overflow: visible; }
.luau-neon-svg .stroke {
  fill: none;
  stroke-linecap: round;
  stroke-linejoin: round;
  stroke-width: 6;
  filter: drop-shadow(0 0 3px currentColor) drop-shadow(0 0 12px currentColor) drop-shadow(0 0 30px currentColor);
  stroke: currentColor;
}
.luau-neon-svg .fill {
  fill: currentColor;
  filter: drop-shadow(0 0 8px currentColor);
}

/* Flamingo: strokes draw themselves in, then hold with a slow neon breathe. */
.luau-flamingo {
  position: absolute;
  left: clamp(-5rem, -2vw, 0rem);
  top: 52%;
  transform: translateY(-50%);
  width: clamp(180px, 26vw, 380px);
  color: var(--magenta);
  z-index: 1;
  pointer-events: none;
  animation: luau-breathe 5.5s ease-in-out infinite;
}
.luau-flamingo .stroke {
  /* Every path declares pathLength="1000", so one dash length covers any
     geometry — no repeating-dash gaps regardless of the real curve length. */
  stroke-dasharray: 1000;
  stroke-dashoffset: 1000;
  /* Each path carries its own animation-delay inline so the bird draws itself
     in anatomical order: silhouette, wing, legs, eye. */
  animation: luau-draw 2.2s cubic-bezier(.45,.05,.3,1) forwards;
}
/* The silhouette is one long contour; give it a slightly softer stroke than the
   detail lines so the bird doesn't turn into a solid magenta blob. */
.luau-flamingo .stroke--body { stroke-width: 5.5; }
.luau-flamingo .stroke--thin { stroke-width: 4.5; }
.luau-flamingo .fill { opacity: 0; animation: luau-pop .5s 2.1s ease forwards; }

@keyframes luau-draw { to { stroke-dashoffset: 0; } }
@keyframes luau-pop  { to { opacity: 1; } }
@keyframes luau-breathe {
  0%, 100% { opacity: 1; }
  50%      { opacity: .82; }
}

/* Palm trees stand on the horizon rather than floating in the corners, so
   they anchor at the bottom edge and sway from the base of the trunk. */
.luau-palm {
  position: absolute;
  bottom: -2vh;
  width: clamp(170px, 27vw, 400px);
  z-index: 1;
  pointer-events: none;
  transform-origin: bottom center;
  color: var(--lime);
}
.luau-palm--left  { left: -5vw;  animation: luau-sway 9s ease-in-out infinite; }
.luau-palm--right { right: -5vw; transform: scaleX(-1); animation: luau-sway 11.5s ease-in-out infinite reverse; }
.luau-palm--far   { right: 19vw; width: clamp(110px, 15vw, 215px); opacity: .4; animation: luau-sway 13s ease-in-out infinite; }

/* `rotate` is its own property, so the sway composes with the scaleX(-1)
   mirror on the right-hand tree instead of overwriting it. */
@keyframes luau-sway {
  0%, 100% { rotate: -1.6deg; }
  50%      { rotate: 2.2deg; }
}

/* ══ HERO ═══════════════════════════════════════════════════════════════ */
.luau-hero {
  position: relative;
  min-height: min(100svh, 940px);
  display: grid;
  place-items: center;
  padding: clamp(5rem, 12vh, 9rem) 1.5rem clamp(4rem, 9vh, 7rem);
  text-align: center;
  overflow: hidden;
  border-bottom: 1px solid rgba(255,43,214,.28);
}

.luau-hero__inner { position: relative; z-index: 3; max-width: 960px; }

.luau-presents {
  font-weight: 700;
  letter-spacing: .5em;
  text-transform: uppercase;
  font-size: clamp(.7rem, 1.5vw, .95rem);
  color: rgba(255,255,255,.72);
  margin-bottom: clamp(.75rem, 2vw, 1.4rem);
}

/* The word LUAU — the loudest thing on the page. */
.luau-title {
  font-size: clamp(4.6rem, 20vw, 15rem);
  letter-spacing: -.02em;
  text-transform: uppercase;
  color: #fff;
  text-shadow: var(--glow-magenta);
  animation: luau-flicker 7s 2.2s infinite;
}
@keyframes luau-flicker {
  0%, 42%, 44.6%, 46%, 49.4%, 100% { opacity: 1;   text-shadow: var(--glow-magenta); }
  43.4%, 45.4%, 48%                { opacity: .58; text-shadow: 0 0 4px #fff, 0 0 10px var(--magenta); }
}

/* "Party" script, overlapped onto LUAU exactly like the poster. */
.luau-party {
  display: block;
  font-size: clamp(2.9rem, 11.5vw, 8.4rem);
  line-height: .8;
  color: #d9ffcc;
  text-shadow: var(--glow-lime);
  margin-top: clamp(-2.4rem, -5.5vw, -1rem);
  margin-left: clamp(1rem, 8vw, 6rem);
  rotate: -5deg;
  animation: luau-buzz 4.5s ease-in-out infinite;
}
@keyframes luau-buzz {
  0%, 100% { transform: translateY(0)     rotate(0deg); }
  50%      { transform: translateY(-7px)  rotate(1.2deg); }
}

.luau-meta {
  margin-top: clamp(2rem, 5vw, 3.4rem);
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
  gap: .6rem 1.4rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .16em;
  font-size: clamp(1rem, 2.6vw, 1.6rem);
}
.luau-meta__date { color: #fff; text-shadow: 0 0 14px rgba(255,255,255,.5); }
.luau-meta__dot  { color: var(--magenta); }
.luau-meta__venue { color: rgba(255,255,255,.78); }

/* ── Countdown ─────────────────────────────────────────────────────────── */
.luau-countdown {
  margin: clamp(2rem, 5vw, 3rem) auto 0;
  display: flex;
  justify-content: center;
  gap: clamp(.5rem, 1.6vw, 1.1rem);
  flex-wrap: wrap;
}
.luau-cd {
  min-width: clamp(66px, 15vw, 104px);
  padding: .85rem .6rem .6rem;
  border: 1px solid rgba(255,43,214,.5);
  border-radius: 14px;
  background: rgba(255,43,214,.07);
  backdrop-filter: blur(6px);
  box-shadow: inset 0 0 24px rgba(255,43,214,.18), 0 0 22px rgba(255,43,214,.16);
}
.luau-cd__num {
  display: block;
  font-family: 'Archivo Black', Impact, sans-serif;
  font-size: clamp(1.5rem, 4.6vw, 2.5rem);
  line-height: 1;
  color: #fff;
  text-shadow: 0 0 12px var(--magenta), 0 0 30px rgba(255,43,214,.6);
  font-variant-numeric: tabular-nums;
}
.luau-cd__label {
  display: block;
  margin-top: .4rem;
  font-size: .68rem;
  font-weight: 700;
  letter-spacing: .24em;
  text-transform: uppercase;
  color: rgba(255,255,255,.55);
}
.luau-cd--live {
  border-color: rgba(123,255,77,.6);
  background: rgba(123,255,77,.09);
  box-shadow: inset 0 0 24px rgba(123,255,77,.2), 0 0 26px rgba(123,255,77,.22);
}

/* ── Buttons ──────────────────────────────────────────────────────────── */
.luau-cta-row {
  margin-top: clamp(2.2rem, 5vw, 3.2rem);
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: .9rem;
}

.luau-btn {
  --btn-glow: var(--magenta);
  position: relative;
  display: inline-flex;
  align-items: center;
  gap: .6rem;
  padding: 1.05rem 2.5rem;
  border: 0;
  border-radius: 999px;
  font-family: 'Barlow Condensed', sans-serif;
  font-weight: 700;
  font-size: 1.12rem;
  letter-spacing: .18em;
  text-transform: uppercase;
  cursor: pointer;
  overflow: hidden;
  transition: transform .28s cubic-bezier(.2,.8,.2,1), box-shadow .28s ease, color .28s ease;
}

.luau-btn--solid {
  color: #14000d;
  background: linear-gradient(115deg, var(--magenta) 0%, #ff7ae3 46%, var(--lime) 100%);
  background-size: 220% 100%;
  box-shadow: 0 0 26px rgba(255,43,214,.5), 0 10px 34px rgba(0,0,0,.55);
  animation: luau-shift 6s ease infinite;
}
@keyframes luau-shift {
  0%, 100% { background-position:   0% 50%; }
  50%      { background-position: 100% 50%; }
}
.luau-btn--solid:hover,
.luau-btn--solid:focus-visible {
  transform: translateY(-3px) scale(1.03);
  box-shadow: 0 0 44px rgba(255,43,214,.8), 0 16px 44px rgba(0,0,0,.6);
}

.luau-btn--ghost {
  color: #eaffe2;
  background: transparent;
  border: 1px solid rgba(123,255,77,.65);
  box-shadow: inset 0 0 20px rgba(123,255,77,.12), 0 0 20px rgba(123,255,77,.16);
}
.luau-btn--ghost:hover,
.luau-btn--ghost:focus-visible {
  transform: translateY(-3px);
  color: #05000c;
  background: var(--lime);
  box-shadow: 0 0 38px rgba(123,255,77,.7);
}

/* Sheen sweep on hover. */
.luau-btn::after {
  content: '';
  position: absolute; inset: 0;
  background: linear-gradient(105deg, transparent 38%, rgba(255,255,255,.55) 50%, transparent 62%);
  transform: translateX(-120%);
  transition: transform .7s cubic-bezier(.2,.8,.2,1);
}
.luau-btn:hover::after,
.luau-btn:focus-visible::after { transform: translateX(120%); }

/* Scroll hint. */
.luau-scroll-hint {
  position: absolute;
  left: 50%; bottom: 1.4rem;
  translate: -50% 0;
  z-index: 3;
  font-size: .7rem;
  font-weight: 700;
  letter-spacing: .3em;
  text-transform: uppercase;
  color: rgba(255,255,255,.42);
  display: grid;
  justify-items: center;
  gap: .5rem;
}
.luau-scroll-hint span:last-child {
  width: 1px; height: 34px;
  background: linear-gradient(to bottom, var(--magenta), transparent);
  animation: luau-hint 2s ease-in-out infinite;
}
@keyframes luau-hint {
  0%, 100% { transform: scaleY(.4); opacity: .4; transform-origin: top; }
  50%      { transform: scaleY(1);  opacity: 1;  transform-origin: top; }
}

/* ══ MARQUEE ════════════════════════════════════════════════════════════ */
.luau-marquee {
  position: relative;
  z-index: 4;
  display: flex;
  overflow: hidden;
  padding: .9rem 0;
  background: linear-gradient(90deg, var(--magenta-dp), var(--violet) 50%, var(--magenta-dp));
  border-block: 1px solid rgba(255,255,255,.22);
  box-shadow: 0 0 34px rgba(255,43,214,.45);
  user-select: none;
}
.luau-marquee__track {
  display: flex;
  flex-shrink: 0;
  gap: 2.6rem;
  padding-right: 2.6rem;
  white-space: nowrap;
  animation: luau-marquee 26s linear infinite;
}
.luau-marquee:hover .luau-marquee__track { animation-play-state: paused; }
.luau-marquee__item {
  font-weight: 700;
  letter-spacing: .26em;
  text-transform: uppercase;
  font-size: clamp(.8rem, 1.7vw, 1.02rem);
  color: #fff;
  text-shadow: 0 0 12px rgba(0,0,0,.45);
}
.luau-marquee__item::before { content: '★'; color: var(--lime); margin-right: 2.6rem; }
@keyframes luau-marquee {
  to { transform: translateX(-100%); }
}

/* ══ PERKS ══════════════════════════════════════════════════════════════ */
.luau-perks-grid {
  margin-top: 3.5rem;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.25rem;
}
.luau-perk {
  position: relative;
  padding: 2.2rem 1.7rem;
  border-radius: 20px;
  border: 1px solid rgba(255,255,255,.12);
  background: linear-gradient(160deg, rgba(255,255,255,.07), rgba(255,255,255,.02));
  overflow: hidden;
  transition: transform .4s cubic-bezier(.2,.8,.2,1), border-color .4s ease, box-shadow .4s ease;
}
.luau-perk::before {
  content: '';
  position: absolute;
  inset: -1px;
  border-radius: inherit;
  padding: 1px;
  background: linear-gradient(140deg, var(--magenta), transparent 42%, transparent 58%, var(--lime));
  -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite: xor;
  mask-composite: exclude;
  opacity: 0;
  transition: opacity .4s ease;
}
.luau-perk:hover {
  transform: translateY(-8px);
  border-color: transparent;
  box-shadow: 0 22px 50px rgba(0,0,0,.6), 0 0 34px rgba(255,43,214,.22);
}
.luau-perk:hover::before { opacity: 1; }

.luau-perk__icon {
  width: 52px; height: 52px;
  margin-bottom: 1.25rem;
  color: var(--lime);
}
.luau-perk:nth-child(even) .luau-perk__icon { color: var(--magenta); }
.luau-perk__icon svg { width: 100%; height: 100%; overflow: visible; }
.luau-perk__icon .stroke { stroke-width: 5.5; }

.luau-perk__title {
  font-family: 'Archivo Black', Impact, sans-serif;
  font-size: 1.32rem;
  text-transform: uppercase;
  letter-spacing: .01em;
  color: #fff;
  margin-bottom: .6rem;
}
.luau-perk__body {
  font-size: 1.05rem;
  line-height: 1.55;
  color: rgba(255,255,255,.62);
}

/* ── House rule notice ────────────────────────────────────────────────
   Deliberately not a perk card: it sits under the grid in magenta rather
   than the lime used for things you get, so it reads as a condition.    */
.luau-note {
  margin-top: 1.6rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
  padding: 1.15rem 1.4rem;
  border-radius: 16px;
  border: 1px solid rgba(255,43,214,.55);
  background: rgba(255,43,214,.08);
  box-shadow: inset 0 0 32px rgba(255,43,214,.12);
  font-size: 1.1rem;
  line-height: 1.5;
  color: rgba(255,255,255,.86);
}
.luau-note__tag {
  flex: none;
  padding: .34rem .85rem;
  border-radius: 999px;
  background: var(--magenta);
  color: #14000d;
  font-weight: 700;
  font-size: .74rem;
  letter-spacing: .2em;
  text-transform: uppercase;
  box-shadow: 0 0 18px rgba(255,43,214,.55);
}

/* ══ TICKETS ════════════════════════════════════════════════════════════ */
.luau-tickets { background: var(--ink-2); border-block: 1px solid rgba(255,43,214,.24); }

.luau-tiers {
  margin-top: 3.5rem;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
  gap: 1.4rem;
}
.luau-tier {
  position: relative;
  display: flex;
  flex-direction: column;
  padding: 2.1rem 1.8rem;
  border-radius: 22px;
  border: 1px solid rgba(255,43,214,.38);
  background: linear-gradient(170deg, rgba(255,43,214,.11), rgba(139,60,255,.05) 55%, transparent);
  box-shadow: inset 0 1px 0 rgba(255,255,255,.1);
  transition: transform .4s cubic-bezier(.2,.8,.2,1), box-shadow .4s ease, border-color .4s ease;
}
.luau-tier:hover {
  transform: translateY(-6px);
  border-color: var(--magenta);
  box-shadow: 0 24px 54px rgba(0,0,0,.6), 0 0 40px rgba(255,43,214,.3);
}
.luau-tier--out { opacity: .5; }
.luau-tier--out:hover { transform: none; }

.luau-tier__name {
  font-family: 'Archivo Black', Impact, sans-serif;
  font-size: 1.22rem;
  text-transform: uppercase;
  color: #fff;
  margin-bottom: .85rem;
}
.luau-tier__price {
  font-family: 'Archivo Black', Impact, sans-serif;
  font-size: clamp(2.1rem, 5vw, 2.9rem);
  line-height: 1;
  color: var(--lime);
  text-shadow: 0 0 16px rgba(123,255,77,.6);
  margin-bottom: .9rem;
}
.luau-tier__desc {
  font-size: 1.02rem;
  line-height: 1.5;
  color: rgba(255,255,255,.6);
  margin-bottom: 1.1rem;
}
.luau-tier__stock {
  margin-top: auto;
  align-self: flex-start;
  padding: .34rem .8rem;
  border-radius: 999px;
  font-size: .74rem;
  font-weight: 700;
  letter-spacing: .18em;
  text-transform: uppercase;
  border: 1px solid rgba(255,255,255,.24);
  color: rgba(255,255,255,.72);
}
.luau-tier__stock--low {
  color: #05000c;
  background: var(--lime);
  border-color: transparent;
  box-shadow: 0 0 20px rgba(123,255,77,.6);
  animation: luau-pulse 2s ease-in-out infinite;
}
.luau-tier__stock--out {
  color: #fff;
  background: rgba(255,255,255,.14);
}
@keyframes luau-pulse {
  0%, 100% { box-shadow: 0 0 16px rgba(123,255,77,.45); }
  50%      { box-shadow: 0 0 30px rgba(123,255,77,.9); }
}

/* The stock Event Tickets module, restyled to sit on the dark page. */
.luau-module {
  margin-top: 3.5rem;
  padding: clamp(1.4rem, 4vw, 2.6rem);
  border-radius: 24px;
  border: 1px solid rgba(255,255,255,.14);
  background: rgba(255,255,255,.045);
  backdrop-filter: blur(10px);
  box-shadow: 0 26px 60px rgba(0,0,0,.5);
}
.luau-module__label {
  font-weight: 700;
  letter-spacing: .3em;
  text-transform: uppercase;
  font-size: .76rem;
  color: var(--lime);
  margin-bottom: 1.5rem;
}
/* ── Event Tickets, re-themed for the dark page ────────────────────────
   Event Tickets ships a light theme: the form carries a hard white background
   and near-black type. Recolouring the text alone is what produced washed-out
   text on a white card, so the background has to be neutralised first. These
   use !important deliberately — they override a third-party plugin's own
   stylesheet, and the class names below are Event Tickets' public block
   classes. If a future ET release renames them, the module simply reverts to
   its stock light styling rather than breaking.                            */
.luau-module .tribe-tickets__tickets-form,
.luau-module .tribe-tickets__tickets-wrapper,
.luau-module .tribe-tickets__notice,
.luau-module .tribe-tickets__tickets-footer {
  background: transparent !important;
  border-color: rgba(255,255,255,.14) !important;
}
.luau-module .tribe-tickets__tickets-form { border: 0 !important; padding: 0 !important; }

/* Our own "Choose your tickets" label already sits above this. */
.luau-module .tribe-tickets__tickets-title { display: none !important; }

.luau-module .tribe-common,
.luau-module .tribe-common h1, .luau-module .tribe-common h2,
.luau-module .tribe-common h3, .luau-module .tribe-common h4,
.luau-module .tribe-common p,  .luau-module .tribe-common span,
.luau-module .tribe-common div, .luau-module .tribe-common label { color: #f4f2f8; }

.luau-module .tribe-tickets__tickets-item { border-color: rgba(255,255,255,.14) !important; }

.luau-module .tribe-tickets__tickets-item-content-title {
  color: #fff !important;
  font-weight: 700;
  letter-spacing: .01em;
}
.luau-module .tribe-tickets__tickets-item-details-content,
.luau-module .tribe-tickets__tickets-item-details-content * {
  color: rgba(255,255,255,.66) !important;
}

/* Price in the same lime as the tier cards above, so the two read as one set. */
.luau-module .tribe-tickets__tickets-item-extra-price,
.luau-module .tribe-tickets__tickets-item-extra-price *,
.luau-module .tribe-amount,
.luau-module .tribe-currency-symbol,
.luau-module .tribe-currency-prefix {
  color: var(--lime) !important;
}
.luau-module .tribe-tickets__tickets-item-extra-available {
  color: rgba(255,255,255,.5) !important;
}

/* Quantity stepper: pill, magenta controls, legible number. */
.luau-module .tribe-tickets__tickets-item-quantity {
  background: rgba(255,255,255,.06) !important;
  border: 1px solid rgba(255,43,214,.45) !important;
  border-radius: 999px !important;
}
.luau-module .tribe-tickets__tickets-item-quantity-number-input,
.luau-module .tribe-tickets__tickets-item-quantity-number input,
.luau-module input[type="number"],
.luau-module input[type="text"],
.luau-module input[type="email"],
.luau-module select,
.luau-module textarea {
  color: #fff !important;
  background: transparent !important;
  border: 0 !important;
  -moz-appearance: textfield;
}
.luau-module select option { color: #111; }
.luau-module .tribe-tickets__tickets-item-quantity-add,
.luau-module .tribe-tickets__tickets-item-quantity-remove {
  color: var(--magenta) !important;
  background: transparent !important;
  border: 0 !important;
  font-weight: 700;
}
.luau-module .tribe-tickets__tickets-item-quantity-add:hover,
.luau-module .tribe-tickets__tickets-item-quantity-remove:hover { color: #fff !important; }

.luau-module .tribe-tickets__tickets-footer-quantity-label,
.luau-module .tribe-tickets__tickets-footer-total-label {
  color: rgba(255,255,255,.6) !important;
}
.luau-module .tribe-tickets__tickets-footer-quantity-number,
.luau-module .tribe-tickets__tickets-footer-total-wrap { color: #fff !important; }

/* Buy button matched to .luau-btn--solid, with a deliberate disabled state
   (it ships disabled until a quantity is chosen). */
.luau-module .tribe-common-c-btn,
.luau-module .tribe-tickets__tickets-buy {
  background: linear-gradient(115deg, var(--magenta) 0%, #ff7ae3 46%, var(--lime) 100%) !important;
  color: #14000d !important;
  border: 0 !important;
  border-radius: 999px !important;
  padding: .9rem 2.1rem !important;
  font-weight: 700 !important;
  font-size: 1rem !important;
  letter-spacing: .16em;
  text-transform: uppercase;
  box-shadow: 0 0 22px rgba(255,43,214,.45);
  transition: transform .25s ease, box-shadow .25s ease;
}
.luau-module .tribe-common-c-btn:hover:not(:disabled),
.luau-module .tribe-tickets__tickets-buy:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 0 38px rgba(255,43,214,.75);
}
.luau-module .tribe-common-c-btn:disabled,
.luau-module .tribe-tickets__tickets-buy:disabled {
  background: rgba(255,255,255,.1) !important;
  color: rgba(255,255,255,.45) !important;
  box-shadow: none;
  cursor: not-allowed;
}

.luau-module .tribe-common a:not(.tribe-common-c-btn) { color: var(--lime); }

.luau-module__fallback {
  padding: 1.4rem;
  border: 1px dashed rgba(255,43,214,.6);
  border-radius: 14px;
  color: rgba(255,255,255,.75);
  font-size: 1.02rem;
  line-height: 1.55;
}

/* ══ GALLERY (poster polaroids) ═════════════════════════════════════════ */
.luau-polaroids {
  margin-top: 3.5rem;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  flex-wrap: wrap;
  gap: clamp(1rem, 3vw, 2.4rem);
  perspective: 1200px;
}
.luau-polaroid {
  position: relative;
  width: clamp(210px, 27vw, 310px);
  padding: 14px 14px 52px;
  background: var(--paper);
  border-radius: 3px;
  box-shadow: 0 26px 54px rgba(0,0,0,.62);
  transition: transform .5s cubic-bezier(.2,.8,.2,1), box-shadow .5s ease;
}
.luau-polaroid:nth-child(1) { rotate: -6deg; }
.luau-polaroid:nth-child(2) { rotate:  2.5deg; translate: 0 -22px; }
.luau-polaroid:nth-child(3) { rotate:  6.5deg; }
.luau-polaroid:hover {
  rotate: 0deg;
  transform: translateY(-14px) scale(1.045);
  box-shadow: 0 34px 70px rgba(0,0,0,.7), 0 0 40px rgba(255,43,214,.32);
  z-index: 5;
}
.luau-polaroid__img {
  display: block;
  width: 100%;
  aspect-ratio: 4 / 3;
  object-fit: cover;
  background: linear-gradient(135deg, var(--violet), var(--magenta) 55%, #1d7a3a);
}
.luau-polaroid__cap {
  position: absolute;
  left: 0; right: 0; bottom: 16px;
  text-align: center;
  font-family: 'Dancing Script', cursive;
  font-weight: 700;
  font-size: 1.3rem;
  color: #2a1b2b;
}
/* Plumeria bloom tucked on the corner, like the flyer. */
.luau-polaroid__bloom {
  position: absolute;
  top: -20px; right: -16px;
  width: 56px; height: 56px;
  animation: luau-sway 6s ease-in-out infinite;
}

/* ══ SCHEDULE ═══════════════════════════════════════════════════════════ */
.luau-days {
  margin-top: 3.5rem;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
}
.luau-day {
  padding: 2.2rem 1.9rem;
  border-radius: 22px;
  border: 1px solid rgba(255,255,255,.12);
  background: linear-gradient(165deg, rgba(255,255,255,.06), transparent);
}
.luau-day__label {
  font-weight: 700;
  letter-spacing: .3em;
  text-transform: uppercase;
  font-size: .76rem;
  color: var(--magenta);
  margin-bottom: .5rem;
}
.luau-day__name {
  font-family: 'Archivo Black', Impact, sans-serif;
  font-size: 2rem;
  text-transform: uppercase;
  color: #fff;
  margin-bottom: 1.6rem;
}
.luau-slots { list-style: none; margin: 0; padding: 0; }
.luau-slot {
  display: grid;
  grid-template-columns: auto 1fr;
  gap: 1rem;
  padding: .85rem 0;
  border-top: 1px solid rgba(255,255,255,.1);
  align-items: baseline;
}
.luau-slot__time {
  font-weight: 700;
  font-size: .95rem;
  letter-spacing: .1em;
  color: var(--lime);
  white-space: nowrap;
}
.luau-slot__what { font-size: 1.08rem; color: rgba(255,255,255,.78); }

/* ══ VENUE ══════════════════════════════════════════════════════════════ */
.luau-venue-card {
  margin-top: 3rem;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 2rem;
  padding: clamp(1.8rem, 4vw, 3rem);
  border-radius: 24px;
  border: 1px solid rgba(123,255,77,.34);
  background: linear-gradient(120deg, rgba(123,255,77,.09), transparent 60%);
}

/* ══ FINAL CTA ══════════════════════════════════════════════════════════ */
.luau-final {
  position: relative;
  text-align: center;
  overflow: hidden;
  padding: clamp(6rem, 14vh, 9rem) 1.5rem;
  border-top: 1px solid rgba(255,43,214,.3);
}
/* Outside the hero the palms are background texture, not subject matter:
   at full strength the crown washes over ticket prices and headlines. */
.luau-tickets .luau-palm,
.luau-final .luau-palm { opacity: .24; }
.luau-tickets .luau-palm--left { left: -11vw; }

.luau-final__title {
  position: relative;
  z-index: 2;
  font-size: clamp(3rem, 12vw, 8rem);
  text-transform: uppercase;
  color: #fff;
  text-shadow: var(--glow-magenta);
}

/* ══ SCROLL REVEAL ══════════════════════════════════════════════════════ */
.luau-reveal {
  opacity: 0;
  transform: translateY(44px);
  transition: opacity .85s cubic-bezier(.2,.8,.2,1), transform .85s cubic-bezier(.2,.8,.2,1);
  transition-delay: var(--d, 0ms);
}
.luau-reveal.is-in { opacity: 1; transform: none; }

/* ══ REDUCED MOTION ═════════════════════════════════════════════════════
   Everything above degrades to a static, fully legible page.               */
@media (prefers-reduced-motion: reduce) {
  .luau-page *,
  .luau-page *::before,
  .luau-page *::after {
    animation: none !important;
    transition-duration: .01ms !important;
  }
  .luau-page .luau-reveal { opacity: 1; transform: none; }
  .luau-page .luau-flamingo .stroke { stroke-dashoffset: 0; }
  .luau-page .luau-flamingo .fill  { opacity: 1; }
}

/* ══ RESPONSIVE ═════════════════════════════════════════════════════════ */
@media (max-width: 860px) {
  .luau-section { padding: 4.5rem 1.25rem; }

  /* The bird has to yield to the countdown on narrow screens. `animation:none`
     is required as well as `opacity` — luau-breathe animates opacity, and a
     running animation outranks the declared value. */
  .luau-flamingo {
    animation: none;
    opacity: .18;
    width: 190px;
    left: -3.5rem;
    top: 44%;
  }

  /* Less of the script tucked into LUAU, so both stay readable small. */
  .luau-party {
    margin-top: -.75rem;
    margin-left: 0;
  }

  .luau-polaroid:nth-child(n) { rotate: 0deg; translate: 0; }
  .luau-venue-card { flex-direction: column; align-items: flex-start; text-align: left; }
}
</style>

<?php
/**
 * Reusable neon line-art pieces. Kept as functions so the same vector can be
 * dropped into several sections without duplicating path data.
 */
/**
 * One pinnate palm frond as a single path: an arcing spine plus leaflets
 * stepped along it. This is the detail that stops a palm reading as a
 * cannabis leaf — cannabis leaflets radiate from a single point, palm
 * leaflets run the length of a rib and sweep back toward the tip.
 *
 * Everything is emitted as one path (spine + leaflet subpaths) so each frond
 * costs a single element and a single neon drop-shadow filter, rather than
 * twenty.
 */
if ( ! function_exists( 'apex_luau_frond_path' ) ) {
	function apex_luau_frond_path( $scale = 1.0 ) {
		$p = array(
			array( 0, 0 ),
			array( 34 * $scale, -18 * $scale ),
			array( 68 * $scale, -14 * $scale ),
			array( 92 * $scale,  26 * $scale ),
		);

		$point = function ( $t ) use ( $p ) {
			$m = 1 - $t;
			return array(
				$m*$m*$m*$p[0][0] + 3*$m*$m*$t*$p[1][0] + 3*$m*$t*$t*$p[2][0] + $t*$t*$t*$p[3][0],
				$m*$m*$m*$p[0][1] + 3*$m*$m*$t*$p[1][1] + 3*$m*$t*$t*$p[2][1] + $t*$t*$t*$p[3][1],
			);
		};
		$tangent = function ( $t ) use ( $p ) {
			$m = 1 - $t;
			return array(
				3*$m*$m*($p[1][0]-$p[0][0]) + 6*$m*$t*($p[2][0]-$p[1][0]) + 3*$t*$t*($p[3][0]-$p[2][0]),
				3*$m*$m*($p[1][1]-$p[0][1]) + 6*$m*$t*($p[2][1]-$p[1][1]) + 3*$t*$t*($p[3][1]-$p[2][1]),
			);
		};

		$d = sprintf(
			'M 0,0 C %.1f,%.1f %.1f,%.1f %.1f,%.1f',
			$p[1][0], $p[1][1], $p[2][0], $p[2][1], $p[3][0], $p[3][1]
		);

		for ( $i = 0; $i < 9; $i++ ) {
			$t = 0.10 + $i * 0.095;
			list( $x, $y )   = $point( $t );
			list( $tx, $ty ) = $tangent( $t );

			$mag = sqrt( $tx * $tx + $ty * $ty );
			if ( $mag < 0.001 ) {
				continue;
			}
			$tx /= $mag;
			$ty /= $mag;

			$leaf  = ( 17 - 9 * $t ) * $scale;   // leaflets shorten toward the tip
			$sweep = 0.5 * $leaf;                // and rake back along the rib

			foreach ( array( 1, -1 ) as $side ) {
				$d .= sprintf(
					' M %.1f,%.1f L %.1f,%.1f',
					$x,
					$y,
					$x + ( -$ty ) * $leaf * $side - $tx * $sweep,
					$y + (  $tx ) * $leaf * $side - $ty * $sweep
				);
			}
		}

		return $d;
	}
}

if ( ! function_exists( 'apex_luau_palm_svg' ) ) {
	function apex_luau_palm_svg() {
		// Uneven angles and lengths — a symmetrical crown is the other half of
		// why the old shape read as a cannabis leaf.
		$fronds = array(
			array( -208, 0.88 ),
			array( -164, 1.02 ),
			array( -128, 0.92 ),
			array(  -88, 1.08 ),
			array(  -50, 0.95 ),
			array(  -12, 1.00 ),
			array(   30, 0.84 ),
		);

		$out = '<svg class="luau-neon-svg" viewBox="0 0 200 320" aria-hidden="true" focusable="false">';

		// Trunk, leaning as it rises.
		$out .= '<path class="stroke" d="M 112,320 C 108,262 98,198 86,140" stroke-width="7" />';

		// Bark rings.
		foreach ( array( array( 103, 282 ), array( 99, 242 ), array( 93, 202 ), array( 87, 168 ) ) as $ring ) {
			$out .= sprintf(
				'<path class="stroke" d="M %d,%d q 7,5 14,-1" stroke-width="3" />',
				$ring[0],
				$ring[1]
			);
		}

		foreach ( $fronds as $frond ) {
			$out .= sprintf(
				'<path class="stroke" d="%s" transform="translate(86,136) rotate(%d)" stroke-width="2.6" />',
				apex_luau_frond_path( $frond[1] ),
				$frond[0]
			);
		}

		// Coconuts.
		$out .= '<circle class="fill" cx="95" cy="149" r="5" /><circle class="fill" cx="78" cy="153" r="4.5" />';

		return $out . '</svg>';
	}
}

if ( ! function_exists( 'apex_luau_bloom_svg' ) ) {
	function apex_luau_bloom_svg() {
		$out = '<svg viewBox="0 0 100 100" aria-hidden="true" focusable="false">';
		for ( $i = 0; $i < 5; $i++ ) {
			$angle = $i * 72;
			$out  .= sprintf(
				'<ellipse cx="50" cy="27" rx="15" ry="23" fill="#fffdf4" stroke="#f0e4c8" stroke-width="1.5" transform="rotate(%d 50 50)" />',
				$angle
			);
		}
		return $out . '<circle cx="50" cy="50" r="9" fill="#ffd24a" /></svg>';
	}
}
?>

<div class="luau-page">

  <!-- ═══════════════════════════════════════════════════════════════════
       HERO
  ════════════════════════════════════════════════════════════════════ -->
  <header class="luau-hero">

    <div class="luau-aurora" aria-hidden="true">
      <span class="luau-bloom luau-bloom--a"></span>
      <span class="luau-bloom luau-bloom--b"></span>
      <span class="luau-bloom luau-bloom--c"></span>
    </div>

    <div class="luau-grid-floor" aria-hidden="true"></div>

    <div class="luau-sparks" aria-hidden="true">
      <?php
      // Sparkles rising through the hero. Seeded per-render so the field never
      // looks mechanically even.
      for ( $i = 0; $i < 26; $i++ ) :
      	printf(
      		'<span class="luau-spark" style="left:%d%%;bottom:%d%%;animation-duration:%ss;animation-delay:%ss;"></span>',
      		wp_rand( 1, 99 ),
      		wp_rand( 0, 30 ),
      		number_format( wp_rand( 70, 165 ) / 10, 1 ),
      		number_format( wp_rand( 0, 140 ) / 10, 1 )
      	);
      endfor;
      ?>
    </div>

    <!-- Neon flamingo, drawn stroke by stroke on load -->
    <div class="luau-flamingo" aria-hidden="true">
      <svg class="luau-neon-svg" viewBox="0 0 260 470" focusable="false">
        <!--
          Traced clockwise from the crown: down the back of the head, down the
          concave side of the S-neck, over the body and tail, under the belly,
          up the breast, back up the convex side of the neck, then out around
          the beak to the crown. Three details do the heavy lifting and are
          worth preserving in any edit: the neck is a true S (two inflections,
          not a single arc) held to a slender ~17u width, the beak is deep at
          the base and hooks almost straight down at the tip, and the tail is a
          broad wedge rather than a spike. Lose those and it reads as a stork.
        -->
        <path class="stroke stroke--body" pathLength="1000" style="animation-delay:.15s" d="
          M 168,38
          C 186,40 194,52 193,66
          C 192,80 182,88 170,96
          C 158,110 142,132 144,156
          C 148,180 170,192 174,216
          C 178,238 168,250 160,260
          C 184,254 206,262 216,280
          C 228,268 240,254 254,240
          C 246,264 238,286 230,306
          C 232,326 220,342 198,350
          C 174,360 144,358 126,344
          C 112,333 106,316 110,300
          C 114,284 126,274 138,266
          C 150,252 160,232 157,210
          C 153,188 126,180 126,154
          C 124,126 140,104 152,92
          C 151,86 151,81 152,78
          C 142,84 132,86 124,88
          C 113,98 106,106 100,116
          C 104,100 109,84 115,66
          C 127,54 139,46 151,42
          C 157,39 162,38 168,38
          Z" />
        <!-- folded wing -->
        <path class="stroke stroke--thin" pathLength="1000" style="animation-delay:1.5s"
              d="M 168,292 C 190,282 210,292 220,310" />
        <!-- the one leg it stands on -->
        <path class="stroke stroke--thin" pathLength="1000" style="animation-delay:1.7s"
              d="M 162,348 C 158,378 156,412 162,446" />
        <path class="stroke stroke--thin" pathLength="1000" style="animation-delay:1.95s"
              d="M 146,450 L 182,450" />
        <circle class="fill" cx="176" cy="58" r="4.5" />
      </svg>
    </div>

    <div class="luau-palm luau-palm--right" aria-hidden="true"><?php echo apex_luau_palm_svg(); ?></div>
    <div class="luau-palm luau-palm--far" aria-hidden="true"><?php echo apex_luau_palm_svg(); ?></div>

    <div class="luau-hero__inner">
      <p class="luau-presents">APEX Idaho Presents</p>

      <h1>
        <span class="luau-title">Luau</span>
        <span class="luau-party luau-script">Party</span>
      </h1>

      <div class="luau-meta">
        <span class="luau-meta__date"><?php echo esc_html( $luau_dates ); ?></span>
        <span class="luau-meta__dot" aria-hidden="true">◆</span>
        <span class="luau-meta__venue"><?php echo esc_html( $luau_venue ); ?></span>
      </div>

      <?php if ( $luau_start_ts ) : ?>
      <div class="luau-countdown" data-luau-countdown data-luau-target="<?php echo esc_attr( $luau_start_ts ); ?>" role="timer" aria-label="Time remaining until the Luau Party">
        <div class="luau-cd"><span class="luau-cd__num" data-luau-days>--</span><span class="luau-cd__label">Days</span></div>
        <div class="luau-cd"><span class="luau-cd__num" data-luau-hours>--</span><span class="luau-cd__label">Hours</span></div>
        <div class="luau-cd"><span class="luau-cd__num" data-luau-mins>--</span><span class="luau-cd__label">Minutes</span></div>
        <div class="luau-cd"><span class="luau-cd__num" data-luau-secs>--</span><span class="luau-cd__label">Seconds</span></div>
      </div>
      <?php endif; ?>

      <div class="luau-cta-row">
        <a class="luau-btn luau-btn--solid" href="#luau-tickets">Get Tickets</a>
        <a class="luau-btn luau-btn--ghost" href="#luau-included">What's Included</a>
      </div>
    </div>

    <div class="luau-scroll-hint" aria-hidden="true">
      <span>Scroll</span>
      <span></span>
    </div>
  </header>

  <!-- ═══════════════════════════════════════════════════════════════════
       MARQUEE
  ════════════════════════════════════════════════════════════════════ -->
  <div class="luau-marquee" aria-hidden="true">
    <?php
    $luau_marquee = array(
    	'Food &amp; Drinks Included',
    	'Camping Friday, Saturday &amp; Sunday',
    	'Two Days of Track Action',
    	'Hawaiian Shirts Required',
    	$luau_venue,
    	$luau_dates . ', ' . $luau_year,
    );
    // Two identical tracks so the loop is seamless.
    for ( $t = 0; $t < 2; $t++ ) :
    	echo '<div class="luau-marquee__track">';
    	foreach ( $luau_marquee as $luau_item ) {
    		echo '<span class="luau-marquee__item">' . wp_kses_post( $luau_item ) . '</span>';
    	}
    	echo '</div>';
    endfor;
    ?>
  </div>

  <!-- ═══════════════════════════════════════════════════════════════════
       WHAT'S INCLUDED
  ════════════════════════════════════════════════════════════════════ -->
  <section class="luau-section" id="luau-included">
    <div class="luau-aurora" aria-hidden="true">
      <span class="luau-bloom luau-bloom--b" style="opacity:.22"></span>
    </div>

    <div class="luau-wrap">
      <p class="luau-eyebrow luau-reveal">The Whole Weekend</p>
      <h2 class="luau-h2 luau-reveal" style="--d:80ms">Entry Covers It</h2>
      <p class="luau-lede luau-reveal" style="--d:160ms;margin-top:1.25rem;">
        One ticket, two days, zero nickel-and-diming. Roll in Friday, park up, and stay
        for the whole thing — the food, the drinks and the campsite are already yours.
      </p>

      <div class="luau-perks-grid">
        <?php
        $luau_perks = array(
        	array(
        		'title' => 'Food &amp; Drinks Included',
        		'body'  => 'Hot food off the grill and non-alcoholic drinks all weekend, bundled into your entry. No wristband upsells, no cash-only line.',
        		'icon'  => '<path class="stroke" d="M44,46 L54,116 L86,116 L96,46" /><path class="stroke" d="M36,46 L104,46" /><path class="stroke" d="M78,46 L88,20" />',
        	),
        	array(
        		'title' => 'Camping Fri&ndash;Sun',
        		'body'  => 'Friday, Saturday and Sunday nights are all open. Tent or rig &mdash; pitch up in the infield and you are already at the track when the gates open.',
        		'icon'  => '<path class="stroke" d="M70,18 L20,114 L120,114 Z" /><path class="stroke" d="M70,18 L70,114" /><path class="stroke" d="M70,72 L48,114" />',
        	),
        	array(
        		'title' => 'Two Days of Track',
        		'body'  => 'Tandems, smoke and door-to-door runs across Saturday and Sunday at Magic Valley Speedway.',
        		'icon'  => '<path class="stroke" d="M70,18 a52,52 0 1,0 0.1,0" /><path class="stroke" d="M70,54 a16,16 0 1,0 0.1,0" /><path class="stroke" d="M70,18 L70,38" /><path class="stroke" d="M38,88 L56,78" /><path class="stroke" d="M102,88 L84,78" />',
        	),
        	array(
        		'title' => 'Hawaiian Shirts Required',
        		'body'  => 'Not a suggestion. No shirt, no luau &mdash; and the loudest one in the paddock earns the respect of everyone who sees it.',
        		'icon'  => '<path class="stroke" d="M44,34 L26,46 L36,62 L46,54 L46,114 L94,114 L94,54 L104,62 L114,46 L96,34 L82,34 L70,52 L58,34 Z" />',
        	),
        );

        foreach ( $luau_perks as $luau_i => $luau_perk ) :
        	printf(
        		'<article class="luau-perk luau-reveal" style="--d:%dms">
        			<div class="luau-perk__icon"><svg class="luau-neon-svg" viewBox="0 0 140 140" aria-hidden="true" focusable="false">%s</svg></div>
        			<h3 class="luau-perk__title">%s</h3>
        			<p class="luau-perk__body">%s</p>
        		</article>',
        		$luau_i * 110,
        		$luau_perk['icon'],
        		wp_kses_post( $luau_perk['title'] ),
        		wp_kses_post( $luau_perk['body'] )
        	);
        endforeach;
        ?>
      </div>

      <p class="luau-note luau-reveal" style="--d:480ms">
        <span class="luau-note__tag">House Rule</span>
        <span><strong>No alcohol allowed</strong> &mdash; anywhere on the property, including the paddock and the campground. Everything else is on us.</span>
      </p>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════════════════════════════
       TICKETS — Event Tickets attached to this page
  ════════════════════════════════════════════════════════════════════ -->
  <section class="luau-section luau-tickets" id="luau-tickets">
    <div class="luau-palm luau-palm--left" aria-hidden="true"><?php echo apex_luau_palm_svg(); ?></div>

    <div class="luau-wrap">
      <p class="luau-eyebrow luau-reveal">Tickets</p>
      <h2 class="luau-h2 luau-reveal" style="--d:80ms">Grab Your Spot</h2>
      <p class="luau-lede luau-reveal" style="--d:160ms;margin-top:1.25rem;">
        Capacity is capped by the infield, not by us — when a tier sells out it stays out.
      </p>

      <?php if ( ! empty( $luau_tickets ) ) : ?>
      <div class="luau-tiers">
        <?php
        foreach ( $luau_tickets as $luau_i => $luau_ticket ) :
        	$luau_stock = apex_luau_ticket_stock_label( $luau_ticket );
        	$luau_out   = ( 'Sold out' === $luau_stock );
        	$luau_low   = ( 0 === strpos( (string) $luau_stock, 'Only ' ) );
        	$luau_price = apex_luau_ticket_price( $luau_ticket );
        	$luau_desc  = isset( $luau_ticket->description ) ? wp_strip_all_tags( $luau_ticket->description ) : '';
        	?>
        	<article class="luau-tier luau-reveal<?php echo $luau_out ? ' luau-tier--out' : ''; ?>" style="--d:<?php echo (int) ( $luau_i * 100 ); ?>ms">
        		<h3 class="luau-tier__name"><?php echo esc_html( $luau_ticket->name ); ?></h3>

        		<?php if ( '' !== $luau_price ) : ?>
        			<p class="luau-tier__price"><?php echo esc_html( $luau_price ); ?></p>
        		<?php endif; ?>

        		<?php if ( '' !== $luau_desc ) : ?>
        			<p class="luau-tier__desc"><?php echo esc_html( $luau_desc ); ?></p>
        		<?php endif; ?>

        		<?php if ( '' !== $luau_stock ) : ?>
        			<span class="luau-tier__stock<?php echo $luau_out ? ' luau-tier__stock--out' : ( $luau_low ? ' luau-tier__stock--low' : '' ); ?>">
        				<?php echo esc_html( $luau_stock ); ?>
        			</span>
        		<?php endif; ?>
        	</article>
        <?php endforeach; ?>
      </div>
      <?php elseif ( ! empty( $luau_fallback_tiers ) ) : ?>
      <div class="luau-tiers">
        <?php foreach ( $luau_fallback_tiers as $luau_i => $luau_tier ) : ?>
        	<article class="luau-tier luau-reveal" style="--d:<?php echo (int) ( $luau_i * 100 ); ?>ms">
        		<h3 class="luau-tier__name"><?php echo esc_html( $luau_tier['name'] ); ?></h3>
        		<p class="luau-tier__price"><?php echo esc_html( $luau_tier['price'] ); ?></p>
        		<p class="luau-tier__desc"><?php echo esc_html( $luau_tier['desc'] ); ?></p>
        	</article>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- Stock Event Tickets purchase module: cart, ARF and checkout unchanged -->
      <div class="luau-module luau-reveal" style="--d:120ms">
        <p class="luau-module__label">Choose your tickets</p>
        <?php
        if ( '' !== $luau_module ) {
        	echo $luau_module; // phpcs:ignore WordPress.Security.EscapeOutput -- Event Tickets output.
        } elseif ( current_user_can( 'edit_posts' ) ) {
        	echo '<div class="luau-module__fallback"><strong>Editor note:</strong> no Event Tickets module rendered for this page. '
        		. 'Check that Event Tickets is active and that tickets are attached to this page '
        		. '(Tickets &rarr; Settings &rarr; Ticket-able post types must include Pages).</div>';
        } else {
        	echo '<div class="luau-module__fallback">Ticket sales open shortly — check back soon.</div>';
        }
        ?>
      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════════════════════════════
       GALLERY — poster polaroids
  ════════════════════════════════════════════════════════════════════ -->
  <section class="luau-section">
    <div class="luau-wrap" style="text-align:center;">
      <p class="luau-eyebrow luau-reveal">Last Year</p>
      <h2 class="luau-h2 luau-reveal" style="--d:80ms">Suns Out, Guns Out</h2>

      <div class="luau-polaroids">
        <?php
        $luau_caps = array( 'the crew', 'tandem run', 'pit vibes' );
        for ( $luau_i = 0; $luau_i < 3; $luau_i++ ) :
        	$luau_att = isset( $luau_gallery[ $luau_i ] ) ? $luau_gallery[ $luau_i ] : 0;
        	?>
        	<figure class="luau-polaroid luau-reveal" style="--d:<?php echo (int) ( $luau_i * 130 ); ?>ms">
        		<div class="luau-polaroid__bloom" aria-hidden="true"><?php echo apex_luau_bloom_svg(); ?></div>
        		<?php
        		if ( $luau_att ) {
        			echo wp_get_attachment_image(
        				$luau_att,
        				'large',
        				false,
        				array(
        					'class'   => 'luau-polaroid__img',
        					'loading' => 'lazy',
        				)
        			);
        		} else {
        			// No attachment yet — the gradient placeholder keeps the layout intact.
        			echo '<div class="luau-polaroid__img" role="img" aria-label="Photo coming soon"></div>';
        		}
        		?>
        		<figcaption class="luau-polaroid__cap"><?php echo esc_html( $luau_caps[ $luau_i ] ); ?></figcaption>
        	</figure>
        <?php endfor; ?>
      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════════════════════════════
       WEEKEND SCHEDULE
  ════════════════════════════════════════════════════════════════════ -->
  <section class="luau-section" style="background:var(--ink-2);border-block:1px solid rgba(123,255,77,.2);">
    <div class="luau-wrap">
      <p class="luau-eyebrow luau-reveal">Run Sheet</p>
      <h2 class="luau-h2 luau-reveal" style="--d:80ms">The Weekend</h2>

      <div class="luau-days">
        <?php
        // Both track days run the same shape and share one vocabulary:
        // Drivers meeting / Track hot / Lunch break / Track shutdown.
        // Friday is a roll-in day, so it keeps its own wording.
        $luau_track_day = array(
        	array( '8:00 AM',  'Drivers meeting' ),
        	array( '8:30 AM',  'Track hot' ),
        	array( '12:00 PM', 'Lunch break' ),
        	array( '2:00 PM',  'Track hot' ),
        	array( '4:00 PM',  'Track shutdown' ),
        );

        $luau_schedule = array(
        	array(
        		'label' => 'Roll In',
        		'name'  => 'Friday',
        		'slots' => array(
        			array( '5:00 PM', 'Gates open &mdash; camping' ),
        			array( '7:30 PM', 'Shindig with friends' ),
        		),
        	),
        	array(
        		'label' => 'Day One',
        		'name'  => 'Saturday',
        		'slots' => $luau_track_day,
        	),
        	array(
        		'label' => 'Day Two',
        		'name'  => 'Sunday',
        		'slots' => $luau_track_day,
        	),
        );

        foreach ( $luau_schedule as $luau_i => $luau_day ) :
        	?>
        	<article class="luau-day luau-reveal" style="--d:<?php echo (int) ( $luau_i * 140 ); ?>ms">
        		<p class="luau-day__label"><?php echo esc_html( $luau_day['label'] ); ?></p>
        		<h3 class="luau-day__name"><?php echo wp_kses_post( $luau_day['name'] ); ?></h3>
        		<ul class="luau-slots">
        			<?php foreach ( $luau_day['slots'] as $luau_slot ) : ?>
        				<li class="luau-slot">
        					<span class="luau-slot__time"><?php echo wp_kses_post( $luau_slot[0] ); ?></span>
        					<span class="luau-slot__what"><?php echo wp_kses_post( $luau_slot[1] ); ?></span>
        				</li>
        			<?php endforeach; ?>
        		</ul>
        	</article>
        <?php endforeach; ?>
      </div>

      <p class="luau-lede luau-reveal" style="--d:280ms;margin-top:2rem;font-size:1rem;">
        Times are the plan, not a promise — weather and car count move things around.
      </p>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════════════════════════════
       VENUE
  ════════════════════════════════════════════════════════════════════ -->
  <section class="luau-section">
    <div class="luau-wrap">
      <p class="luau-eyebrow luau-reveal">Where</p>
      <h2 class="luau-h2 luau-reveal" style="--d:80ms">Find Us</h2>

      <div class="luau-venue-card luau-reveal" style="--d:160ms">
        <div>
          <h3 style="font-size:clamp(1.6rem,4vw,2.4rem);text-transform:uppercase;color:#fff;margin-bottom:.5rem;">
            <?php echo esc_html( $luau_venue ); ?>
          </h3>
          <p style="font-size:1.2rem;color:rgba(255,255,255,.66);">
            <?php echo esc_html( $luau_city ); ?> &nbsp;·&nbsp; <?php echo esc_html( $luau_dates . ', ' . $luau_year ); ?>
          </p>
        </div>
        <a class="luau-btn luau-btn--ghost" href="<?php echo esc_url( $luau_map_url ); ?>" target="_blank" rel="noopener noreferrer">
          Open in Maps
        </a>
      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════════════════════════════
       FINAL CTA
  ════════════════════════════════════════════════════════════════════ -->
  <section class="luau-final">
    <div class="luau-aurora" aria-hidden="true">
      <span class="luau-bloom luau-bloom--a"></span>
      <span class="luau-bloom luau-bloom--c"></span>
    </div>
    <div class="luau-grid-floor" aria-hidden="true"></div>
    <div class="luau-palm luau-palm--right" aria-hidden="true"><?php echo apex_luau_palm_svg(); ?></div>

    <div class="luau-wrap">
      <p class="luau-eyebrow luau-reveal">Don't Miss It</p>
      <h2 class="luau-final__title luau-reveal" style="--d:80ms">
        See You <span class="luau-script" style="text-transform:none;color:#d9ffcc;text-shadow:var(--glow-lime);">there</span>
      </h2>
      <p class="luau-lede luau-reveal" style="--d:160ms;margin:1.5rem auto 0;">
        <?php echo esc_html( $luau_dates . ', ' . $luau_year ); ?> at <?php echo esc_html( $luau_venue ); ?>.
        Flamingos optional. Shirts are not.
      </p>
      <div class="luau-cta-row luau-reveal" style="--d:240ms">
        <a class="luau-btn luau-btn--solid" href="#luau-tickets">Get Tickets</a>
      </div>
    </div>
  </section>

</div><!-- /.luau-page -->

<script>
(function () {
  var page = document.querySelector('.luau-page');
  if (!page) { return; }

  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ── Scroll reveal ──────────────────────────────────────────────────── */
  var reveals = page.querySelectorAll('.luau-reveal');

  if (reduced || !('IntersectionObserver' in window)) {
    // No observer (or the visitor asked for less motion): show everything.
    reveals.forEach(function (el) { el.classList.add('is-in'); });
  } else {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-in');
          io.unobserve(entry.target);
        }
      });
    }, { rootMargin: '0px 0px -12% 0px', threshold: 0.1 });

    reveals.forEach(function (el) { io.observe(el); });
  }

  /* ── Countdown ──────────────────────────────────────────────────────── */
  var cd = page.querySelector('[data-luau-countdown]');
  if (cd) {
    var target = parseInt(cd.getAttribute('data-luau-target'), 10);
    var out = {
      days:  cd.querySelector('[data-luau-days]'),
      hours: cd.querySelector('[data-luau-hours]'),
      mins:  cd.querySelector('[data-luau-mins]'),
      secs:  cd.querySelector('[data-luau-secs]')
    };
    var pad = function (n) { return n < 10 ? '0' + n : String(n); };

    var tick = function () {
      var diff = target - Date.now();

      if (diff <= 0) {
        // Event is live (or done) — swap the boxes to a single status readout.
        cd.querySelectorAll('.luau-cd').forEach(function (box, i) {
          box.classList.add('luau-cd--live');
          box.style.display = i === 0 ? '' : 'none';
        });
        cd.querySelector('.luau-cd').style.minWidth = 'auto';
        out.days.textContent = 'LIVE';
        out.days.style.fontSize = '1.4rem';
        cd.querySelector('.luau-cd__label').textContent = 'Right now';
        return true;
      }

      var s = Math.floor(diff / 1000);
      out.days.textContent  = Math.floor(s / 86400);
      out.hours.textContent = pad(Math.floor(s / 3600) % 24);
      out.mins.textContent  = pad(Math.floor(s / 60) % 60);
      out.secs.textContent  = pad(s % 60);
      return false;
    };

    if (!tick()) {
      var timer = setInterval(function () {
        if (tick()) { clearInterval(timer); }
      }, 1000);
    }
  }

  /* ── Pointer-tracked glow on the hero title ─────────────────────────── */
  if (!reduced && window.matchMedia('(hover: hover)').matches) {
    var hero  = page.querySelector('.luau-hero');
    var flam  = page.querySelector('.luau-flamingo');
    if (hero && flam) {
      hero.addEventListener('pointermove', function (e) {
        var r = hero.getBoundingClientRect();
        var dx = (e.clientX - r.width / 2) / r.width;
        var dy = (e.clientY - r.top - r.height / 2) / r.height;
        // Gentle parallax — the flamingo leans away from the cursor.
        flam.style.translate = (dx * -18).toFixed(1) + 'px ' + (dy * -14).toFixed(1) + 'px';
      });
      hero.addEventListener('pointerleave', function () {
        flam.style.translate = '';
      });
    }
  }

  /* ── Smooth anchor scrolling for the CTAs ───────────────────────────── */
  page.querySelectorAll('a[href^="#luau-"]').forEach(function (link) {
    link.addEventListener('click', function (e) {
      var t = document.getElementById(link.getAttribute('href').slice(1));
      if (!t) { return; }
      e.preventDefault();
      t.scrollIntoView({ behavior: reduced ? 'auto' : 'smooth', block: 'start' });
    });
  });
})();
</script>

<?php get_footer(); ?>
