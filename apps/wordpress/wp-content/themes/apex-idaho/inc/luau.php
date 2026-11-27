<?php
/**
 * Luau Party: landing template routing and the homepage callout.
 *
 * Ported from bb-theme-child. Function names and filters are unchanged
 * (apex_luau_post_id, apex_luau_callout_auto, the [luau_callout] shortcode), so
 * anything that hooked into them keeps working.
 *
 * The landing page itself is template-luau-party.php in the theme root. It is a
 * hand-built PHP page with its own neon art direction, rendered between the
 * block theme's header and footer by header.php / footer.php.
 *
 * @package apex-idaho
 */

defined( 'ABSPATH' ) || exit;

define( 'APEX_LUAU_POST_ID', 2749 );

add_filter( 'template_include', 'apex_luau_template_include', 9999 );
function apex_luau_template_include( $template )
{
    if ( ! is_singular() ) {
        return $template;
    }

    $target = (int) apply_filters( 'apex_luau_post_id', APEX_LUAU_POST_ID );
    if ( (int) get_queried_object_id() !== $target ) {
        return $template;
    }

    $custom = locate_template( 'template-luau-party.php' );

    // Fall through to the normal event template if the file is ever missing,
    // rather than fataling on a live ticket-selling page.
    return $custom ? $custom : $template;
}

/**
 * Luau callout band.
 *
 * Registered two ways: a [luau_callout] shortcode (drop it into a Shortcode
 * block anywhere, including the front-page template in the Site Editor), and an
 * automatic injection at the top of the front page's post content. In this
 * theme front-page.html does not render post content, so the injection only
 * fires if the front page is switched to a template that does.
 *
 * It renders nothing at all when the event is missing, unpublished, or already
 * over — a stale event promo on the homepage is worse than none.
 */
/**
 * Renders "September 5-6", "September 5", or "September 30 - October 1".
 *
 * Lives here rather than in template-luau-party.php because the homepage
 * callout needs it too and that template is not loaded on the front page.
 * The template keeps its own function_exists-guarded copy, which simply does
 * not redefine when this one is already present.
 */
if ( ! function_exists( 'apex_luau_format_date_range' ) ) {
    function apex_luau_format_date_range( $start, $end )
    {
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

function apex_luau_callout_should_render()
{
    $id   = (int) apply_filters( 'apex_luau_post_id', APEX_LUAU_POST_ID );
    $post = get_post( $id );

    if ( ! $post || 'publish' !== $post->post_status ) {
        return false;
    }

    if ( function_exists( 'tribe_get_end_date' ) && 'tribe_events' === get_post_type( $id ) ) {
        $end = tribe_get_end_date( $id, false, 'Y-m-d H:i:s' );
        if ( $end && strtotime( $end ) < current_time( 'timestamp' ) ) {
            return false;
        }
    }

    return $id;
}

function apex_luau_callout_html()
{
    $id = apex_luau_callout_should_render();
    if ( ! $id ) {
        return '';
    }

    $start = '';
    $end   = '';
    if ( function_exists( 'tribe_get_start_date' ) && 'tribe_events' === get_post_type( $id ) ) {
        $start = tribe_get_start_date( $id, false, 'Y-m-d H:i:s' );
        $end   = tribe_get_end_date( $id, false, 'Y-m-d H:i:s' );
    }

    // Reuse the landing page's date formatter when it is loaded; otherwise fall
    // back to a plain format so the callout never depends on that template.
    if ( $start && function_exists( 'apex_luau_format_date_range' ) ) {
        $dates = apex_luau_format_date_range( $start, $end );
    } elseif ( $start ) {
        $dates = date_i18n( 'F j', strtotime( $start ) );
    } else {
        $dates = '';
    }

    $venue = function_exists( 'tribe_get_venue' ) ? tribe_get_venue( $id ) : '';
    if ( ! $venue ) {
        $venue = 'Magic Valley Speedway';
    }

    $target = 0;
    if ( $start ) {
        try {
            $dt     = new DateTime( $start, wp_timezone() );
            $target = $dt->getTimestamp() * 1000;
        } catch ( Exception $e ) {
            $target = 0;
        }
    }

    ob_start();
    ?>
    <style>
    /* Self-contained: the landing page's stylesheet is not loaded here. */
    .luau-callout {
      --lc-magenta: #ff2bd6;
      --lc-lime:    #7bff4d;
      --lc-violet:  #8b3cff;
      position: relative;
      isolation: isolate;
      overflow: hidden;
      background: #05000c;
      padding: clamp(2.2rem, 5vw, 3.6rem) clamp(1.25rem, 5vw, 3rem);
      font-family: 'Barlow Condensed', system-ui, sans-serif;
    }
    .luau-callout *, .luau-callout *::before, .luau-callout *::after { box-sizing: border-box; }
    .luau-callout__glow {
      position: absolute; inset: 0; z-index: 0; pointer-events: none;
      background:
        radial-gradient(60% 120% at 12% 50%, rgba(255,43,214,.42), transparent 70%),
        radial-gradient(55% 120% at 88% 50%, rgba(139,60,255,.40), transparent 70%);
    }
    .luau-callout__inner {
      position: relative; z-index: 1;
      max-width: 1180px; margin: 0 auto;
      display: flex; flex-wrap: wrap; align-items: center;
      justify-content: space-between; gap: 1.5rem 2rem;
    }
    .luau-callout__eyebrow {
      margin: 0 0 .35rem;
      font-weight: 700; letter-spacing: .34em; text-transform: uppercase;
      font-size: .72rem; color: rgba(255,255,255,.72);
    }
    .luau-callout__title {
      margin: 0; line-height: .9;
      font-family: 'Archivo Black', Impact, sans-serif;
      text-transform: uppercase;
      font-size: clamp(2.4rem, 6vw, 4rem);
      color: #fff;
      text-shadow: 0 0 4px #fff, 0 0 14px var(--lc-magenta), 0 0 38px rgba(255,43,214,.6);
    }
    .luau-callout__script {
      font-family: 'Dancing Script', cursive; font-weight: 700;
      text-transform: none; font-size: .82em; color: #d9ffcc;
      text-shadow: 0 0 4px #eaffe2, 0 0 14px var(--lc-lime), 0 0 34px rgba(123,255,77,.5);
    }
    .luau-callout__meta {
      margin: .7rem 0 0;
      font-weight: 700; letter-spacing: .12em; text-transform: uppercase;
      font-size: clamp(.92rem, 2vw, 1.12rem); color: rgba(255,255,255,.86);
    }
    .luau-callout__meta span { color: var(--lc-magenta); }
    .luau-callout__side { display: flex; align-items: center; flex-wrap: wrap; gap: 1rem 1.4rem; }
    .luau-callout__countdown { display: flex; gap: .55rem; }
    .luau-callout__countdown span {
      display: grid; justify-items: center; min-width: 62px;
      padding: .5rem .4rem .4rem;
      border: 1px solid rgba(255,43,214,.5); border-radius: 12px;
      background: rgba(255,43,214,.08);
      font-size: .62rem; font-weight: 700; letter-spacing: .18em;
      text-transform: uppercase; color: rgba(255,255,255,.6);
    }
    .luau-callout__countdown b {
      font-family: 'Archivo Black', Impact, sans-serif;
      font-size: 1.35rem; line-height: 1; color: #fff;
      font-variant-numeric: tabular-nums;
      text-shadow: 0 0 10px var(--lc-magenta);
    }
    .luau-callout__btn {
      display: inline-block; padding: .95rem 2.1rem;
      border-radius: 999px; border: 0; text-decoration: none;
      background: linear-gradient(115deg, var(--lc-magenta) 0%, #ff7ae3 46%, var(--lc-lime) 100%);
      color: #14000d !important;
      font-weight: 700; font-size: 1.02rem; letter-spacing: .16em; text-transform: uppercase;
      box-shadow: 0 0 24px rgba(255,43,214,.45);
      transition: transform .25s ease, box-shadow .25s ease;
    }
    .luau-callout__btn:hover, .luau-callout__btn:focus-visible {
      transform: translateY(-2px);
      box-shadow: 0 0 38px rgba(255,43,214,.75);
      color: #14000d !important;
    }
    @media (max-width: 720px) {
      /* The side column has to stretch too, or width:100% on the button just
         resolves against a shrink-to-fit parent and nothing spans. */
      .luau-callout__inner { flex-direction: column; align-items: stretch; }
      .luau-callout__side  { width: 100%; }
      .luau-callout__btn   { width: 100%; text-align: center; }
    }
    @media (prefers-reduced-motion: reduce) {
      .luau-callout__btn { transition: none; }
    }
    </style>
    <aside class="luau-callout">
      <div class="luau-callout__glow" aria-hidden="true"></div>
      <div class="luau-callout__inner">
        <div class="luau-callout__lockup">
          <p class="luau-callout__eyebrow">APEX Idaho Presents</p>
          <h2 class="luau-callout__title">
            Luau <span class="luau-callout__script">Party</span>
          </h2>
          <p class="luau-callout__meta">
            <?php echo esc_html( $dates ); ?>
            <span aria-hidden="true">&nbsp;&#9670;&nbsp;</span>
            <?php echo esc_html( $venue ); ?>
          </p>
        </div>

        <div class="luau-callout__side">
          <?php if ( $target ) : ?>
          <div class="luau-callout__countdown" data-luau-callout-target="<?php echo esc_attr( $target ); ?>" role="timer">
            <span><b data-d>--</b>days</span>
            <span><b data-h>--</b>hrs</span>
            <span><b data-m>--</b>min</span>
          </div>
          <?php endif; ?>
          <a class="luau-callout__btn" href="<?php echo esc_url( get_permalink( $id ) ); ?>">Get Tickets</a>
        </div>
      </div>
    </aside>
    <script>
    (function () {
      var el = document.currentScript.previousElementSibling.querySelector('[data-luau-callout-target]');
      if (!el) { return; }
      var target = parseInt(el.getAttribute('data-luau-callout-target'), 10);
      var d = el.querySelector('[data-d]'), h = el.querySelector('[data-h]'), m = el.querySelector('[data-m]');
      var pad = function (n) { return n < 10 ? '0' + n : String(n); };
      var tick = function () {
        var diff = target - Date.now();
        if (diff <= 0) { d.textContent = 'NOW'; h.parentNode.style.display = 'none'; m.parentNode.style.display = 'none'; return true; }
        var s = Math.floor(diff / 1000);
        d.textContent = Math.floor(s / 86400);
        h.textContent = pad(Math.floor(s / 3600) % 24);
        m.textContent = pad(Math.floor(s / 60) % 60);
        return false;
      };
      if (!tick()) { var t = setInterval(function () { if (tick()) { clearInterval(t); } }, 30000); }
    })();
    </script>
    <?php
    return ob_get_clean();
}

add_shortcode( 'luau_callout', 'apex_luau_callout_html' );

/**
 * Assets load only on requests that will actually show the callout.
 */
add_action( 'wp_enqueue_scripts', 'apex_luau_callout_assets' );
function apex_luau_callout_assets()
{
    if ( ! is_front_page() || ! apex_luau_callout_should_render() ) {
        return;
    }

    wp_enqueue_style(
        'apex-luau-callout-fonts',
        'https://fonts.googleapis.com/css2?family=Archivo+Black&family=Barlow+Condensed:wght@400;600;700&family=Dancing+Script:wght@700&display=swap',
        array(),
        null
    );
}

/**
 * Prepend to the front page. Return the content untouched anywhere else, and
 * only act on the main query in the loop so widgets and excerpts are unaffected.
 */
// Priority 20, not 5: Beaver Builder filters the_content at 10 and REPLACES it
// with the rendered layout, so anything prepended earlier is discarded.
add_filter( 'the_content', 'apex_luau_callout_on_front_page', 20 );
function apex_luau_callout_on_front_page( $content )
{
    if ( is_admin() || ! is_front_page() || ! in_the_loop() || ! is_main_query() ) {
        return $content;
    }

    if ( ! apply_filters( 'apex_luau_callout_auto', true ) ) {
        return $content;
    }

    $callout = apex_luau_callout_html();

    return $callout ? $callout . $content : $content;
}
