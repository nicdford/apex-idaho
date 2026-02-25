<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<?php do_action( 'fl_head_open' ); ?>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<?php echo apply_filters( 'fl_theme_viewport', "<meta name='viewport' content='width=device-width, initial-scale=1.0' />\n" ); ?>
<?php echo apply_filters( 'fl_theme_xua_compatible', "<meta http-equiv='X-UA-Compatible' content='IE=edge' />\n" ); ?>
<link rel="profile" href="https://gmpg.org/xfn/11" />
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow+Condensed:wght@400;600;700&display=swap" rel="stylesheet">
<?php wp_head(); ?>
<?php FLTheme::head(); ?>
</head>
<body <?php body_class(); ?><?php FLTheme::print_schema( ' itemscope="itemscope" itemtype="https://schema.org/WebPage"' ); ?>>
<?php FLTheme::header_code(); ?>
<?php do_action( 'fl_body_open' ); ?>

<style>
  .apex-header {
    background: #111111;
    position: sticky;
    top: 0;
    z-index: 9999;
    width: 100%;
  }
  .apex-header__grad-bar {
    height: 3px;
    background: linear-gradient(135deg, #e8197d 0%, #f97316 100%);
  }

  /* Top bar */
  .apex-header__topbar {
    background: #0a0a0a;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    padding: 0.4rem 1.5rem;
  }
  .apex-header__topbar-inner {
    max-width: 1176px;
    margin: 0 auto;
    display: flex;
    justify-content: flex-end;
  }
  .apex-header__topbar a {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.4);
    text-decoration: none;
    transition: color 0.2s ease;
  }
  .apex-header__topbar a:hover { color: #e8197d; }

  /* Main header row */
  .apex-header__main {
    max-width: 1176px;
    margin: 0 auto;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    height: 70px;
  }

  /* Logo */
  .apex-header__logo img {
    height: 38px;
    width: auto;
    display: block;
  }

  /* Desktop nav */
  .apex-header__nav {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    list-style: none;
    margin: 0;
    padding: 0;
  }
  .apex-header__nav a {
    font-family: 'Barlow Condensed', sans-serif;
    font-weight: 600;
    font-size: 1rem;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.65);
    text-decoration: none;
    padding: 0.4rem 0.75rem;
    display: block;
    transition: color 0.2s ease;
    white-space: nowrap;
  }
  .apex-header__nav a:hover,
  .apex-header__nav .current-menu-item > a { color: #e8197d; }

  /* Right actions: social + cart */
  .apex-header__actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-shrink: 0;
  }
  .apex-header__social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255,255,255,0.45);
    text-decoration: none;
    transition: color 0.2s ease;
    padding: 0.25rem;
  }
  .apex-header__social-link:hover { color: #e8197d; }

  .apex-header__cart {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-family: 'Barlow Condensed', sans-serif;
    font-weight: 700;
    font-size: 0.9rem;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.65);
    text-decoration: none;
    padding: 0.4rem 0.75rem;
    border: 1px solid rgba(255,255,255,0.15);
    transition: color 0.2s ease, border-color 0.2s ease;
  }
  .apex-header__cart:hover {
    color: #e8197d;
    border-color: #e8197d;
  }
  .apex-header__cart-count {
    background: linear-gradient(135deg, #e8197d, #f97316);
    color: white;
    font-size: 0.65rem;
    font-weight: 700;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
  }

  /* Hamburger */
  .apex-header__hamburger {
    display: none;
    flex-direction: column;
    gap: 5px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.5rem;
  }
  .apex-header__hamburger span {
    display: block;
    width: 24px;
    height: 2px;
    background: rgba(255,255,255,0.7);
    transition: all 0.25s ease;
  }
  .apex-header__hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
  .apex-header__hamburger.open span:nth-child(2) { opacity: 0; }
  .apex-header__hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

  /* Mobile nav drawer */
  .apex-header__mobile-nav {
    display: none;
    background: #0d0d0d;
    border-top: 1px solid rgba(255,255,255,0.06);
    overflow: hidden;
    max-height: 0;
    transition: max-height 0.35s ease;
  }
  .apex-header__mobile-nav.open { max-height: 600px; }
  .apex-header__mobile-nav ul {
    list-style: none;
    margin: 0;
    padding: 0.75rem 0 1rem;
  }
  .apex-header__mobile-nav li a {
    display: block;
    font-family: 'Barlow Condensed', sans-serif;
    font-weight: 600;
    font-size: 1.1rem;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.65);
    text-decoration: none;
    padding: 0.65rem 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.04);
    transition: color 0.2s ease, background 0.2s ease;
  }
  .apex-header__mobile-nav li a:hover {
    color: #e8197d;
    background: rgba(255,255,255,0.03);
  }
  .apex-header__mobile-nav .apex-header__mobile-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem 0.5rem;
  }

  /* Responsive */
  @media (max-width: 900px) {
    .apex-header__nav { display: none; }
    .apex-header__actions .apex-header__social-link { display: none; }
    .apex-header__hamburger { display: flex; }
    .apex-header__mobile-nav { display: block; }
  }
</style>

<div class="fl-page">
  <?php do_action( 'fl_page_open' ); ?>

  <header class="apex-header" role="banner" itemscope itemtype="https://schema.org/WPHeader">

    <!-- Gradient bar -->
    <div class="apex-header__grad-bar"></div>

    <!-- Top bar -->
    <div class="apex-header__topbar">
      <div class="apex-header__topbar-inner">
        <a href="<?php echo esc_url( home_url( '/kbd-comp/' ) ); ?>">KBD Comp Series Standings</a>
      </div>
    </div>

    <!-- Main header -->
    <div class="apex-header__main">

      <!-- Logo -->
      <a class="apex-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="APEX Idaho Home">
        <img src="https://apex-idaho.com/wp-content/uploads/2023/07/APEX_Idaho_logo-2-300x77.png" alt="APEX Idaho">
      </a>

      <!-- Desktop nav -->
      <nav aria-label="Primary navigation">
        <?php
        wp_nav_menu([
          'theme_location' => 'primary',
          'menu_class'     => 'apex-header__nav',
          'container'      => false,
          'depth'          => 1,
          'fallback_cb'    => function() {
            $links = [
              'Events'              => '/events/',
              'Media'               => '/media/',
              'Shop'                => '/shop/',
              'About'               => '/about/',
              'Official Tech &amp; Rules' => '/tech/',
            ];
            echo '<ul class="apex-header__nav">';
            foreach ( $links as $label => $path ) {
              echo '<li><a href="' . esc_url( home_url( $path ) ) . '">' . $label . '</a></li>';
            }
            echo '</ul>';
          },
        ]);
        ?>
      </nav>

      <!-- Right: social + cart + hamburger -->
      <div class="apex-header__actions">

        <?php $fb = get_theme_mod( 'fl-social-facebook' ); if ( $fb ) : ?>
          <a class="apex-header__social-link" href="<?php echo esc_url( $fb ); ?>" target="_blank" rel="noopener" aria-label="Facebook">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
          </a>
        <?php endif; ?>

        <?php $ig = get_theme_mod( 'fl-social-instagram' ); if ( $ig ) : ?>
          <a class="apex-header__social-link" href="<?php echo esc_url( $ig ); ?>" target="_blank" rel="noopener" aria-label="Instagram">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
          </a>
        <?php endif; ?>

        <?php if ( function_exists( 'wc_get_cart_url' ) ) : ?>
          <a class="apex-header__cart" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="Cart">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            Cart
            <?php $count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0; if ( $count > 0 ) : ?>
              <span class="apex-header__cart-count"><?php echo $count; ?></span>
            <?php endif; ?>
          </a>
        <?php endif; ?>

        <!-- Hamburger -->
        <button class="apex-header__hamburger" aria-label="Toggle menu" aria-expanded="false" onclick="apexToggleMenu(this)">
          <span></span><span></span><span></span>
        </button>

      </div>
    </div>

    <!-- Mobile nav drawer -->
    <div class="apex-header__mobile-nav" id="apex-mobile-nav">
      <ul>
        <?php
        $links = [
          'Events'                => '/events/',
          'Media'                 => '/media/',
          'Shop'                  => '/shop/',
          'About'                 => '/about/',
          'Official Tech & Rules' => '/tech-rules/',
          'Cart'                  => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '/cart/',
        ];
        foreach ( $links as $label => $url ) :
          $href = is_string( $url ) && strpos( $url, 'http' ) === false ? home_url( $url ) : $url;
        ?>
          <li><a href="<?php echo esc_url( $href ); ?>"><?php echo esc_html( $label ); ?></a></li>
        <?php endforeach; ?>
      </ul>

      <?php if ( $fb || $ig ) : ?>
        <div class="apex-header__mobile-actions">
          <?php if ( $fb ) : ?>
            <a class="apex-header__social-link" href="<?php echo esc_url( $fb ); ?>" target="_blank" rel="noopener" aria-label="Facebook">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
            </a>
          <?php endif; ?>
          <?php if ( $ig ) : ?>
            <a class="apex-header__social-link" href="<?php echo esc_url( $ig ); ?>" target="_blank" rel="noopener" aria-label="Instagram">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
            </a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>

  </header>

  <?php do_action( 'fl_before_content' ); ?>

  <div id="fl-main-content" class="fl-page-content" itemprop="mainContentOfPage" role="main">
    <?php do_action( 'fl_content_open' ); ?>

<script>
function apexToggleMenu(btn) {
  var nav = document.getElementById('apex-mobile-nav');
  var isOpen = nav.classList.toggle('open');
  btn.classList.toggle('open', isOpen);
  btn.setAttribute('aria-expanded', isOpen);
}
</script>
