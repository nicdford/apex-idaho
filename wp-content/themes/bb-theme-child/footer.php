<?php do_action( 'fl_content_close' ); ?>

	</div><!-- .fl-page-content -->

	<?php do_action( 'fl_after_content' ); ?>

<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow+Condensed:wght@400;600;700&display=swap" rel="stylesheet">

<style>
.apex-footer {
  background: #111111;
  color: white;
  font-family: 'Barlow Condensed', sans-serif;
}
.apex-footer a { text-decoration: none; transition: color 0.2s ease; }
.apex-footer__inner {
  max-width: 1100px;
  margin: 0 auto;
  padding: 0 1.5rem;
}
.apex-footer__top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 2rem;
  padding: 3rem 0 2.5rem;
  border-bottom: 1px solid rgba(255,255,255,0.08);
  flex-wrap: wrap;
}
.apex-footer__logo img { height: 48px; width: auto; display: block; }
.apex-footer__logo-text {
  font-family: 'Bebas Neue', sans-serif;
  font-size: 1.8rem;
  letter-spacing: 0.1em;
  color: white;
}
.apex-footer__social {
  display: flex;
  align-items: center;
  gap: 1rem;
}
.apex-footer__social a {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border: 1px solid rgba(255,255,255,0.15);
  color: rgba(255,255,255,0.6);
}
.apex-footer__social a:hover {
  border-color: #e8197d;
  color: #e8197d;
}
.apex-footer__nav {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.25rem 2rem;
  flex-wrap: wrap;
  padding: 2rem 0;
  border-bottom: 1px solid rgba(255,255,255,0.08);
}
.apex-footer__nav a {
  font-family: 'Barlow Condensed', sans-serif;
  font-weight: 600;
  font-size: 1.05rem;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: rgba(255,255,255,0.55);
}
.apex-footer__nav a:hover { color: #e8197d; }
.apex-footer__bottom {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem 0;
}
.apex-footer__copy {
  font-size: 0.95rem;
  color: rgba(255,255,255,0.3);
  letter-spacing: 0.05em;
}
.apex-footer__grad-bar {
  height: 4px;
  background: linear-gradient(135deg, #e8197d 0%, #f97316 100%);
}
</style>

<footer class="apex-footer" role="contentinfo">
  <div class="apex-footer__grad-bar"></div>

  <div class="apex-footer__inner">

    <!-- Top: Logo + Social -->
    <div class="apex-footer__top">

      <div class="apex-footer__logo">
        <?php if ( has_custom_logo() ) : ?>
          <?php the_custom_logo(); ?>
        <?php else : ?>
          <span class="apex-footer__logo-text"><?php bloginfo( 'name' ); ?></span>
        <?php endif; ?>
      </div>

      <div class="apex-footer__social">
        <?php $fb = get_theme_mod( 'fl_social_facebook' ); ?>
        <?php $ig = get_theme_mod( 'fl_social_instagram' ); ?>

        <?php if ( $fb ) : ?>
          <a href="<?php echo esc_url( $fb ); ?>" target="_blank" rel="noopener" aria-label="Facebook">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
          </a>
        <?php endif; ?>

        <?php if ( $ig ) : ?>
          <a href="<?php echo esc_url( $ig ); ?>" target="_blank" rel="noopener" aria-label="Instagram">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
          </a>
        <?php endif; ?>
      </div>

    </div>

    <!-- Nav links -->
    <nav class="apex-footer__nav" aria-label="Footer navigation">
      <?php
      $nav_links = [
        'Events'          => '/events/',
        'Media'           => '/media/',
        'Shop'            => '/shop/',
        'About'           => '/about/',
        'Tech &amp; Rules' => '/tech-rules/',
      ];
      foreach ( $nav_links as $label => $path ) : ?>
        <a href="<?php echo esc_url( home_url( $path ) ); ?>"><?php echo $label; ?></a>
      <?php endforeach; ?>
    </nav>

    <!-- Copyright -->
    <div class="apex-footer__bottom">
      <p class="apex-footer__copy">
        &copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?>. All rights reserved.
      </p>
    </div>

  </div>
</footer>

<?php do_action( 'fl_page_close' ); ?>
</div><!-- .fl-page -->

<?php
wp_footer();
do_action( 'fl_body_close' );
FLTheme::footer_code();
?>
</body>
</html>
