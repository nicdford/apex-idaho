<?php
/**
 * Template Name: Media
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

  .media-page * { box-sizing: border-box; }
  .media-page { font-family: 'Barlow Condensed', sans-serif; }

  /* ── Hero ── */
  .media-hero {
    background: #111111;
    padding: 5rem 1.5rem 4rem;
    text-align: center;
  }
  .media-hero__label {
    font-family: 'Barlow Condensed', sans-serif;
    font-weight: 600;
    font-size: 0.85rem;
    letter-spacing: 0.25em;
    text-transform: uppercase;
    background: var(--grad);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.75rem;
  }
  .media-hero__title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(3rem, 8vw, 5.5rem);
    color: white;
    line-height: 1;
    margin: 0 0 1rem;
    letter-spacing: 0.03em;
  }
  .media-hero__subtitle {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 1.25rem;
    color: rgba(255,255,255,0.5);
    max-width: 560px;
    margin: 0 auto 1.5rem;
    line-height: 1.5;
  }
  .media-hero__bar {
    width: 50px;
    height: 4px;
    background: var(--grad);
    margin: 0 auto;
  }

  /* ── Team Grid ── */
  .media-team {
    max-width: 1100px;
    margin: 0 auto;
    padding: 4rem 1.5rem 5rem;
  }
  .media-team__grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
  }

  /* ── Member Card ── */
  .media-card {
    background: #ffffff;
    border: 1px solid rgba(0,0,0,0.08);
    padding: 2rem 1.75rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    position: relative;
    overflow: hidden;
  }
  .media-card:hover {
    border-color: rgba(232,25,125,0.3);
    box-shadow: 0 8px 30px rgba(232,25,125,0.1);
    transform: translateY(-3px);
  }
  .media-card__accent {
    width: 40px;
    height: 4px;
    background: var(--grad);
    margin-bottom: 1.25rem;
  }
  .media-card__specialty {
    font-family: 'Barlow Condensed', sans-serif;
    font-weight: 600;
    font-size: 0.8rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    background: var(--grad);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.4rem;
  }
  .media-card__name {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 2.25rem;
    color: #111;
    line-height: 1;
    margin: 0 0 1.25rem;
    letter-spacing: 0.04em;
  }
  .media-card__links {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
    margin-bottom: 1.5rem;
  }
  .media-card__link {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-family: 'Barlow Condensed', sans-serif;
    font-weight: 500;
    font-size: 1.1rem;
    color: rgba(0,0,0,0.55);
    text-decoration: none;
    transition: color 0.2s ease;
  }
  .media-card__link:hover {
    color: var(--pink);
  }
  .media-card__link-icon {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
    background: var(--grad);
    -webkit-mask-size: contain;
    mask-size: contain;
    -webkit-mask-repeat: no-repeat;
    mask-repeat: no-repeat;
    -webkit-mask-position: center;
    mask-position: center;
  }
  .icon-email {
    -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z'/%3E%3C/svg%3E");
    mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z'/%3E%3C/svg%3E");
  }
  .icon-instagram {
    -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z'/%3E%3C/svg%3E");
    mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z'/%3E%3C/svg%3E");
  }
  .icon-website {
    -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z'/%3E%3C/svg%3E");
    mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z'/%3E%3C/svg%3E");
  }
  .icon-portfolio {
    -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M20 6h-2.18c.07-.44.18-.88.18-1.36C18 2.98 16.02 1 13.64 1c-1.38 0-2.49.66-3.28 1.72L12 5l1.64-2.28C14.09 2.27 14.82 2 15.64 2c1.44 0 2.36.96 2.36 2.36 0 .59-.32 1.12-.72 1.64H10V4H4C2.9 4 2 4.9 2 6v13c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zM4 19V6h6v2H4v1h6v2H4v1h6v2H4v1h6v2H4v2zm16 0H12V8h8v11z'/%3E%3C/svg%3E");
    mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M20 6h-2.18c.07-.44.18-.88.18-1.36C18 2.98 16.02 1 13.64 1c-1.38 0-2.49.66-3.28 1.72L12 5l1.64-2.28C14.09 2.27 14.82 2 15.64 2c1.44 0 2.36.96 2.36 2.36 0 .59-.32 1.12-.72 1.64H10V4H4C2.9 4 2 4.9 2 6v13c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zM4 19V6h6v2H4v1h6v2H4v1h6v2H4v1h6v2H4v2zm16 0H12V8h8v11z'/%3E%3C/svg%3E");
  }
  .icon-linktree {
    -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M7.95 5.27L12 1.22l4.05 4.05-1.41 1.41L13 5.04V13h-2V5.04L9.36 6.68 7.95 5.27zM13 17h-2v2.96l-2.64-2.64-1.41 1.41L12 23.78l5.05-5.05-1.41-1.41L13 19.96V17zM4 11h4v2H4v-2zm12 0h4v2h-4v-2z'/%3E%3C/svg%3E");
    mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M7.95 5.27L12 1.22l4.05 4.05-1.41 1.41L13 5.04V13h-2V5.04L9.36 6.68 7.95 5.27zM13 17h-2v2.96l-2.64-2.64-1.41 1.41L12 23.78l5.05-5.05-1.41-1.41L13 19.96V17zM4 11h4v2H4v-2zm12 0h4v2h-4v-2z'/%3E%3C/svg%3E");
  }

  /* ── CTA Banner ── */
  .media-cta {
    background: #111111;
    padding: 4rem 1.5rem;
    text-align: center;
  }
  .media-cta__title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(1.75rem, 4vw, 2.75rem);
    color: white;
    margin: 0 0 0.5rem;
    letter-spacing: 0.05em;
  }
  .media-cta__text {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 1.15rem;
    color: rgba(255,255,255,0.45);
    margin: 0 0 1.75rem;
  }
  .media-cta__btn {
    display: inline-block;
    font-family: 'Barlow Condensed', sans-serif;
    font-weight: 700;
    font-size: 1.1rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: white;
    background: var(--grad);
    padding: 0.85rem 2rem;
    text-decoration: none;
    transition: opacity 0.2s ease, transform 0.15s ease;
    clip-path: polygon(0 0, calc(100% - 10px) 0, 100% 10px, 100% 100%, 10px 100%, 0 calc(100% - 10px));
  }
  .media-cta__btn:hover { opacity: 0.85; transform: translateY(-2px); color: white; }

  @media (max-width: 640px) {
    .media-team__grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="media-page">

  <!-- ── Hero ── -->
  <section class="media-hero">
    <div class="media-hero__label">APEX Idaho</div>
    <h1 class="media-hero__title">Official Media Team</h1>
    <p class="media-hero__subtitle">
      Meet the photographers and drone operators capturing the action at APEX Idaho events.
    </p>
    <div class="media-hero__bar"></div>
  </section>

  <!-- ── Team Grid ── -->
  <section class="media-team">
    <div class="media-team__grid">

      <!-- Caleb Aerials -->
      <div class="media-card">
        <div class="media-card__accent"></div>
        <div class="media-card__specialty">Drone Specialist</div>
        <h2 class="media-card__name">Caleb Aerials</h2>
        <div class="media-card__links">
          <a class="media-card__link" href="mailto:caleb.aerials@gmail.com">
            <span class="media-card__link-icon icon-email"></span>
            caleb.aerials@gmail.com
          </a>
          <a class="media-card__link" href="https://www.instagram.com/caleb.aerials" target="_blank" rel="noopener">
            <span class="media-card__link-icon icon-instagram"></span>
            @caleb.aerials
          </a>
          <a class="media-card__link" href="https://linktr.ee/caleb.aerials" target="_blank" rel="noopener">
            <span class="media-card__link-icon icon-linktree"></span>
            linktr.ee/caleb.aerials
          </a>
        </div>
      </div>

      <!-- John Dow II -->
      <div class="media-card">
        <div class="media-card__accent"></div>
        <div class="media-card__specialty">Photographer</div>
        <h2 class="media-card__name">John Dow II</h2>
        <div class="media-card__links">
          <a class="media-card__link" href="mailto:f83photos@icloud.com">
            <span class="media-card__link-icon icon-email"></span>
            f83photos@icloud.com
          </a>
          <a class="media-card__link" href="https://f83photos.com" target="_blank" rel="noopener">
            <span class="media-card__link-icon icon-website"></span>
            f83photos.com
          </a>
          <a class="media-card__link" href="https://www.instagram.com/f83photos" target="_blank" rel="noopener">
            <span class="media-card__link-icon icon-instagram"></span>
            @f83photos
          </a>
        </div>
      </div>

      <!-- John Kisiel -->
      <div class="media-card">
        <div class="media-card__accent"></div>
        <div class="media-card__specialty">Photographer</div>
        <h2 class="media-card__name">John Kisiel</h2>
        <div class="media-card__links">
          <a class="media-card__link" href="mailto:johnraymondkisiel@gmail.com">
            <span class="media-card__link-icon icon-email"></span>
            johnraymondkisiel@gmail.com
          </a>
          <a class="media-card__link" href="https://www.instagram.com/john.kisiel" target="_blank" rel="noopener">
            <span class="media-card__link-icon icon-instagram"></span>
            @john.kisiel
          </a>
          <a class="media-card__link" href="https://johnkisielphotography.pixieset.com" target="_blank" rel="noopener">
            <span class="media-card__link-icon icon-portfolio"></span>
            johnkisielphotography.pixieset.com
          </a>
        </div>
      </div>

    </div>
  </section>

  <!-- ── CTA ── -->
  <section class="media-cta">
    <h2 class="media-cta__title">Media Inquiries</h2>
    <p class="media-cta__text">Interested in covering an APEX Idaho event? Get in touch with us directly.</p>
    <a class="media-cta__btn" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact Us</a>
  </section>

</div><!-- /.media-page -->

<?php get_footer(); ?>
