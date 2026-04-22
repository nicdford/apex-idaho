<?php
/**
 * Template Name: Sponsors
 */

get_header();
?>

<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow+Condensed:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
  :root {
    --pink:   #e8197d;
    --orange: #f97316;
    --grad:   linear-gradient(135deg, #e8197d 0%, #f97316 100%);
  }

  .sponsors-page * { box-sizing: border-box; }
  .sponsors-page { font-family: 'Barlow Condensed', sans-serif; }

  /* Hero */
  .sponsors-hero {
    background: #111111;
    padding: 4rem 1.5rem 3rem;
    text-align: center;
  }
  .sponsors-hero__label {
    font-family: 'Barlow Condensed', sans-serif;
    font-weight: 600;
    font-size: 0.85rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: #e8197d;
    margin-bottom: 0.75rem;
  }
  .sponsors-hero__title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(2.5rem, 6vw, 4rem);
    color: white;
    line-height: 1;
    margin: 0 0 1rem;
  }
  .sponsors-hero__subtitle {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 1.15rem;
    color: rgba(255,255,255,0.5);
    max-width: 560px;
    margin: 0 auto;
    line-height: 1.5;
  }
  .sponsors-hero__bar {
    width: 60px;
    height: 3px;
    background: var(--grad);
    margin: 1.5rem auto 0;
  }

  /* Grid */
  .sponsors-grid {
    max-width: 1176px;
    margin: 0 auto;
    padding: 3rem 1.5rem 4rem;
  }
  .sponsors-grid__items {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1.5rem;
  }

  /* Card */
  .sponsor-card {
    background: #ffffff;
    border: 1px solid rgba(0,0,0,0.08);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem 1.5rem;
    min-height: 180px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
  }
  .sponsor-card:hover {
    border-color: rgba(232,25,125,0.25);
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
  }
  .sponsor-card a {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    width: 100%;
    height: 100%;
  }
  .sponsor-card__logo {
    max-height: 70px;
    max-width: 100%;
    width: auto;
    display: block;
    transition: filter 0.25s ease;
  }
  .sponsor-card__name {
    font-family: 'Barlow Condensed', sans-serif;
    font-weight: 600;
    font-size: 0.85rem;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(0,0,0,0.4);
    margin-top: 1rem;
    text-align: center;
  }

  /* CTA */
  .sponsors-cta {
    background: #111111;
    padding: 3rem 1.5rem;
    text-align: center;
  }
  .sponsors-cta__title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.8rem;
    color: white;
    margin: 0 0 0.5rem;
  }
  .sponsors-cta__text {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 1.05rem;
    color: rgba(255,255,255,0.5);
    margin: 0 0 1.5rem;
  }
  .sponsors-cta__btn {
    display: inline-block;
    font-family: 'Barlow Condensed', sans-serif;
    font-weight: 700;
    font-size: 1rem;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: white;
    background: var(--grad);
    padding: 0.75rem 2rem;
    text-decoration: none;
    transition: opacity 0.2s ease;
  }
  .sponsors-cta__btn:hover { opacity: 0.85; }

  @media (max-width: 600px) {
    .sponsors-grid__items {
      grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
      gap: 1rem;
    }
    .sponsor-card {
      padding: 1.5rem 1rem;
      min-height: 140px;
    }
    .sponsor-card__logo { max-height: 50px; }
  }
</style>

<div class="sponsors-page">

  <!-- Hero -->
  <section class="sponsors-hero">
    <div class="sponsors-hero__label">Our Partners</div>
    <h1 class="sponsors-hero__title">Sponsors</h1>
    <p class="sponsors-hero__subtitle">
      APEX Idaho wouldn't be possible without the support of these incredible partners.
    </p>
    <div class="sponsors-hero__bar"></div>
  </section>

  <!-- Sponsors Grid -->
  <section class="sponsors-grid">
    <?php
    $sponsors = new WP_Query([
        'post_type'      => 'sponsor',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ]);

    if ( $sponsors->have_posts() ) : ?>
      <div class="sponsors-grid__items">
        <?php while ( $sponsors->have_posts() ) : $sponsors->the_post();
          $logo_id  = carbon_get_post_meta( get_the_ID(), 'sponsor_logo' );
          $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
          if ( ! $logo_url ) continue;

          $to_black = carbon_get_post_meta( get_the_ID(), 'sponsor_to_black' );
          $invert   = carbon_get_post_meta( get_the_ID(), 'sponsor_invert' );
          $site_url = carbon_get_post_meta( get_the_ID(), 'sponsor_url' );

          $filters = array_filter([
              $to_black === 'grayscale'  ? 'grayscale(1)'  : '',
              $to_black === 'brightness' ? 'brightness(0)' : '',
              $invert                    ? 'invert(1)'     : '',
          ]);
          $filter_val = $filters ? implode( ' ', $filters ) : '';
          $hover = ! $invert
              ? 'onmouseover="this.style.filter=\'none\'" onmouseout="this.style.filter=this.dataset.filter"'
              : '';
        ?>
          <div class="sponsor-card">
            <?php if ( $site_url ) : ?>
              <a href="<?php echo esc_url( $site_url ); ?>" target="_blank" rel="noopener">
            <?php endif; ?>

              <img class="sponsor-card__logo"
                   src="<?php echo esc_url( $logo_url ); ?>"
                   alt="<?php echo esc_attr( get_the_title() ); ?>"
                   data-filter="<?php echo esc_attr( $filter_val ); ?>"
                   style="<?php echo $filter_val ? 'filter:' . esc_attr( $filter_val ) . ';' : ''; ?>"
                   <?php echo $hover; ?>>
              <div class="sponsor-card__name"><?php the_title(); ?></div>

            <?php if ( $site_url ) : ?>
              </a>
            <?php endif; ?>
          </div>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>
    <?php else : ?>
      <p style="text-align:center;color:rgba(0,0,0,0.4);font-size:1.1rem;padding:3rem 0;">
        Sponsors coming soon.
      </p>
    <?php endif; ?>
  </section>

  <!-- CTA -->
  <section class="sponsors-cta">
    <h2 class="sponsors-cta__title">Interested in Sponsoring?</h2>
    <p class="sponsors-cta__text">Partner with APEX Idaho and reach the motorsport community in the Treasure Valley.</p>
    <a class="sponsors-cta__btn" href="<?php echo esc_url( home_url( '/about/' ) ); ?>">Get in Touch</a>
  </section>

</div>

<?php get_footer(); ?>
