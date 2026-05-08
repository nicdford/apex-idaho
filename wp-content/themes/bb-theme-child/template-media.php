<?php
/**
 * Template Name: Media
 */

get_header();

/**
 * Helper: fetch featured image URL from a media_partner CPT post by slug.
 */
function apex_partner_photo( $slug, $size = 'medium_large' ) {
    $post = get_page_by_path( $slug, OBJECT, 'media_partner' );
    if ( $post ) {
        $url = get_the_post_thumbnail_url( $post->ID, $size );
        return $url ?: '';
    }
    return '';
}
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
    font-size: 1.3rem;
    color: rgba(255,255,255,0.5);
    max-width: 560px;
    margin: 0 auto 2.5rem;
    line-height: 1.5;
  }
  .media-hero__bar {
    width: 50px;
    height: 4px;
    background: var(--grad);
    margin: 0 auto 2.5rem;
  }
  .media-hero__nav {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
  }
  .media-hero__nav a {
    font-family: 'Barlow Condensed', sans-serif;
    font-weight: 700;
    font-size: 1.05rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    padding: 0.7rem 1.75rem;
    transition: opacity 0.2s ease, transform 0.15s ease;
    clip-path: polygon(0 0, calc(100% - 8px) 0, 100% 8px, 100% 100%, 8px 100%, 0 calc(100% - 8px));
    text-decoration: none;
  }
  .media-hero__nav a:hover { opacity: 0.85; transform: translateY(-2px); }
  .media-hero__nav a.btn-primary {
    background: var(--grad);
    color: white;
  }
  .media-hero__nav a.btn-outline {
    background: transparent;
    color: white;
    border: 2px solid rgba(255,255,255,0.35);
  }
  .media-hero__nav a.btn-outline:hover {
    border-color: white;
  }

  /* ── Section wrapper ── */
  .media-section {
    max-width: 1100px;
    margin: 0 auto;
    padding: 4rem 1.5rem;
  }
  .media-section__header {
    margin-bottom: 2.5rem;
  }
  .accent-bar {
    width: 50px;
    height: 4px;
    background: var(--grad);
    margin-bottom: 1rem;
  }
  .section-label {
    font-family: 'Barlow Condensed', sans-serif;
    font-weight: 600;
    font-size: 0.85rem;
    letter-spacing: 0.25em;
    text-transform: uppercase;
    background: var(--grad);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
  }
  .section-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(2.5rem, 6vw, 4rem);
    color: #111;
    line-height: 1;
    margin: 0 0 0.5rem;
    letter-spacing: 0.03em;
  }
  .section-subtitle {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 1.2rem;
    color: rgba(0,0,0,0.45);
    margin: 0;
    line-height: 1.5;
  }

  /* ── Contact Card (Jordan / Have Questions) ── */
  .media-contact {
    background: #f5f5f5;
    padding: 3rem 1.5rem;
  }
  .media-contact__inner {
    max-width: 1100px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 2rem;
    flex-wrap: wrap;
  }
  .media-contact__text {
    flex: 1 1 300px;
  }
  .media-contact__question {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 2.25rem;
    color: #111;
    margin: 0 0 0.25rem;
    letter-spacing: 0.03em;
  }
  .media-contact__name {
    font-family: 'Barlow Condensed', sans-serif;
    font-weight: 700;
    font-size: 1.3rem;
    color: #111;
    margin: 0 0 0.1rem;
  }
  .media-contact__biz {
    font-family: 'Barlow Condensed', sans-serif;
    font-weight: 400;
    font-size: 1.1rem;
    color: rgba(0,0,0,0.45);
    margin: 0;
  }
  .media-contact__actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    align-items: center;
    flex: 0 0 auto;
  }
  .media-contact__actions a {
    font-family: 'Barlow Condensed', sans-serif;
    font-weight: 700;
    font-size: 1rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    padding: 0.7rem 1.5rem;
    text-decoration: none;
    clip-path: polygon(0 0, calc(100% - 8px) 0, 100% 8px, 100% 100%, 8px 100%, 0 calc(100% - 8px));
    transition: opacity 0.2s ease, transform 0.15s ease;
  }
  .media-contact__actions a:hover { opacity: 0.85; transform: translateY(-2px); }
  .media-contact__actions a.btn-primary { background: var(--grad); color: white; }
  .media-contact__actions a.btn-outline {
    background: transparent;
    color: var(--pink);
    border: 2px solid var(--pink);
  }

  /* ── Partners Grid ── */
  .media-partners-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
  }

  /* ── Partner Card ── */
  .partner-card {
    background: #ffffff;
    border: 1px solid rgba(0,0,0,0.08);
    padding: 0;
    transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    position: relative;
    overflow: hidden;
    text-decoration: none;
    display: block;
    color: inherit;
  }
  .partner-card:hover {
    border-color: rgba(232,25,125,0.3);
    box-shadow: 0 8px 30px rgba(232,25,125,0.1);
    transform: translateY(-3px);
  }

  /* ── Partner Photo ── */
  .partner-card__photo {
    width: 100%;
    aspect-ratio: 4 / 3;
    overflow: hidden;
    position: relative;
    background: #1a1a1a;
  }
  .partner-card__photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center top;
    display: block;
    transition: transform 0.4s ease;
  }
  .partner-card:hover .partner-card__photo img {
    transform: scale(1.04);
  }
  .partner-card__avatar {
    width: 100%;
    height: 100%;
    background: var(--grad);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 4rem;
    color: rgba(255,255,255,0.9);
    letter-spacing: 0.1em;
  }

  /* ── Partner Card Body ── */
  .partner-card__body {
    padding: 1.5rem 1.75rem 2rem;
  }

  .partner-card__accent {
    width: 40px;
    height: 4px;
    background: var(--grad);
    margin-bottom: 1.25rem;
  }
  .partner-card__specialty {
    font-family: 'Barlow Condensed', sans-serif;
    font-weight: 600;
    font-size: 0.8rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    background: var(--grad);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.25rem;
  }
  .partner-card__name {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 2rem;
    color: #111;
    line-height: 1;
    margin: 0 0 0.2rem;
    letter-spacing: 0.04em;
  }
  .partner-card__biz {
    font-family: 'Barlow Condensed', sans-serif;
    font-weight: 400;
    font-size: 1.1rem;
    color: rgba(0,0,0,0.4);
    margin: 0 0 1.25rem;
  }
  .partner-card__links {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }
  .partner-card__link {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-family: 'Barlow Condensed', sans-serif;
    font-weight: 500;
    font-size: 1.05rem;
    color: rgba(0,0,0,0.5);
    text-decoration: none;
    transition: color 0.2s ease;
  }
  .partner-card__link:hover { color: var(--pink); }
  .partner-card__link-icon {
    width: 16px;
    height: 16px;
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
    -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M22 9V7h-2V5c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-2h2v-2h-2v-2h2v-2h-2V9h2zm-4 10H4V5h14v14z'/%3E%3C/svg%3E");
    mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M22 9V7h-2V5c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-2h2v-2h-2v-2h2v-2h-2V9h2zm-4 10H4V5h14v14z'/%3E%3C/svg%3E");
  }
  .icon-linktree {
    -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M7.95 5.27L12 1.22l4.05 4.05-1.41 1.41L13 5.04V13h-2V5.04L9.36 6.68 7.95 5.27zM13 17h-2v2.96l-2.64-2.64-1.41 1.41L12 23.78l5.05-5.05-1.41-1.41L13 19.96V17zM4 11h4v2H4v-2zm12 0h4v2h-4v-2z'/%3E%3C/svg%3E");
    mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M7.95 5.27L12 1.22l4.05 4.05-1.41 1.41L13 5.04V13h-2V5.04L9.36 6.68 7.95 5.27zM13 17h-2v2.96l-2.64-2.64-1.41 1.41L12 23.78l5.05-5.05-1.41-1.41L13 19.96V17zM4 11h4v2H4v-2zm12 0h4v2h-4v-2z'/%3E%3C/svg%3E");
  }
  .icon-youtube {
    -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M21.58 7.19c-.23-.86-.91-1.54-1.77-1.77C18.25 5 12 5 12 5s-6.25 0-7.81.42c-.86.23-1.54.91-1.77 1.77C2 8.75 2 12 2 12s0 3.25.42 4.81c.23.86.91 1.54 1.77 1.77C5.75 19 12 19 12 19s6.25 0 7.81-.42c.86-.23 1.54-.91 1.77-1.77C22 15.25 22 12 22 12s0-3.25-.42-4.81zM10 15V9l5.2 3-5.2 3z'/%3E%3C/svg%3E");
    mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M21.58 7.19c-.23-.86-.91-1.54-1.77-1.77C18.25 5 12 5 12 5s-6.25 0-7.81.42c-.86.23-1.54.91-1.77 1.77C2 8.75 2 12 2 12s0 3.25.42 4.81c.23.86.91 1.54 1.77 1.77C5.75 19 12 19 12 19s6.25 0 7.81-.42c.86-.23 1.54-.91 1.77-1.77C22 15.25 22 12 22 12s0-3.25-.42-4.81zM10 15V9l5.2 3-5.2 3z'/%3E%3C/svg%3E");
  }

  /* ── Rules section ── */
  .media-rules {
    background: #f5f5f5;
    padding: 5rem 1.5rem;
  }
  .media-rules__inner {
    max-width: 1100px;
    margin: 0 auto;
  }
  .rules-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(460px, 1fr));
    gap: 2rem;
    margin-top: 2.5rem;
  }
  .rules-box {
    background: white;
    border: 1px solid rgba(0,0,0,0.08);
    padding: 2rem 1.75rem;
  }
  .rules-box__title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.5rem;
    color: #111;
    letter-spacing: 0.05em;
    margin: 0 0 1.25rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid;
    border-image: var(--grad) 1;
  }
  .rules-box ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.7rem;
  }
  .rules-box ul li {
    display: flex;
    gap: 0.75rem;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 1.15rem;
    color: rgba(0,0,0,0.65);
    line-height: 1.45;
  }
  .rules-box ul li::before {
    content: '';
    width: 8px;
    height: 8px;
    flex-shrink: 0;
    margin-top: 0.4em;
    background: var(--grad);
    transform: rotate(45deg);
    display: inline-block;
  }

  /* ── Apply CTA ── */
  .media-apply {
    background: linear-gradient(135deg, #e8197d 0%, #f97316 100%);
    padding: 4.5rem 1.5rem;
    text-align: center;
    position: relative;
    overflow: hidden;
  }
  .media-apply__watermark {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
    user-select: none;
  }
  .media-apply__watermark span {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(5rem, 16vw, 12rem);
    color: rgba(255,255,255,0.1);
    white-space: nowrap;
    line-height: 1;
  }
  .media-apply__inner { position: relative; }
  .media-apply__title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(2.5rem, 6vw, 4.5rem);
    color: white;
    margin: 0 0 0.75rem;
    letter-spacing: 0.03em;
    line-height: 1;
  }
  .media-apply__text {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 1.3rem;
    color: rgba(255,255,255,0.85);
    margin: 0 auto 2rem;
    max-width: 520px;
    line-height: 1.5;
  }
  .media-apply__btn {
    display: inline-block;
    font-family: 'Barlow Condensed', sans-serif;
    font-weight: 700;
    font-size: 1.15rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: var(--pink);
    background: white;
    padding: 0.85rem 2.5rem;
    text-decoration: none;
    transition: opacity 0.2s ease, transform 0.15s ease;
    clip-path: polygon(0 0, calc(100% - 10px) 0, 100% 10px, 100% 100%, 10px 100%, 0 calc(100% - 10px));
  }
  .media-apply__btn:hover { opacity: 0.9; transform: translateY(-2px); color: var(--pink); }

  /* ── Divider label ── */
  .partners-divider {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin: 2.5rem 0 1.5rem;
  }
  .partners-divider__line { flex: 1; height: 1px; background: rgba(0,0,0,0.1); }
  .partners-divider__label {
    font-family: 'Barlow Condensed', sans-serif;
    font-weight: 600;
    font-size: 0.8rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    background: var(--grad);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    white-space: nowrap;
  }

  @media (max-width: 640px) {
    .media-partners-grid { grid-template-columns: 1fr; }
    .rules-grid { grid-template-columns: 1fr; }
    .media-contact__inner { flex-direction: column; }
  }
</style>

<div class="media-page">

  <!-- ═══════════════════════════════════
       HERO
  ═══════════════════════════════════ -->
  <section class="media-hero">
    <div class="media-hero__label">APEX Idaho</div>
    <h1 class="media-hero__title">Media</h1>
    <p class="media-hero__subtitle">Come capture the magic that is APEX Idaho events.</p>
    <div class="media-hero__bar"></div>
    <nav class="media-hero__nav">
      <a href="#partners" class="btn-primary">Media Partners</a>
      <a href="#become" class="btn-outline">Become a Media Partner</a>
    </nav>
  </section>


  <!-- ═══════════════════════════════════
       HAVE QUESTIONS? — Jordan Van Diest
  ═══════════════════════════════════ -->
  <section class="media-contact">
    <div class="media-contact__inner">
      <div class="media-contact__text">
        <p class="media-contact__question">Have Questions?</p>
        <p class="media-contact__name">Jordan Van Diest</p>
        <p class="media-contact__biz">Platinum Media &nbsp;·&nbsp; <a href="https://www.instagram.com/platinummediallc" target="_blank" rel="noopener" style="color:var(--pink);text-decoration:none;font-weight:600;">@platinummediallc</a></p>
      </div>
      <div class="media-contact__actions">
        <a href="mailto:platinummedia208@gmail.com" class="btn-primary">Email</a>
        <a href="http://linktr.ee/platinummediallc" target="_blank" rel="noopener" class="btn-outline">Website</a>
      </div>
    </div>
  </section>


  <!-- ═══════════════════════════════════
       MEDIA PARTNERS
  ═══════════════════════════════════ -->
  <section id="partners">
    <div class="media-section">
      <div class="media-section__header">
        <div class="accent-bar"></div>
        <p class="section-label">Official</p>
        <h2 class="section-title">Media Partners</h2>
      </div>

      <div class="media-partners-grid">

        <!-- Danielle Choutdara -->
        <a class="partner-card" href="<?php echo esc_url( home_url( '/media-partner/danielle-choutdara/' ) ); ?>">
          <div class="partner-card__photo">
            <?php $photo = apex_partner_photo('danielle-choutdara'); ?>
            <?php if ( $photo ) : ?>
              <img src="<?php echo esc_url($photo); ?>" alt="Danielle Choutdara">
            <?php else : ?>
              <div class="partner-card__avatar">DC</div>
            <?php endif; ?>
          </div>
          <div class="partner-card__body">
            <div class="partner-card__accent"></div>
            <div class="partner-card__specialty">Photography</div>
            <h3 class="partner-card__name">Danielle Choutdara</h3>
            <p class="partner-card__biz">DC Photography</p>
            <div class="partner-card__links">
              <span class="partner-card__link">
                <span class="partner-card__link-icon icon-instagram"></span>
                @dc_photography444
              </span>
              <span class="partner-card__link">
                <span class="partner-card__link-icon icon-email"></span>
                daniellelea444@gmail.com
              </span>
            </div>
          </div>
        </a>

        <!-- Lyndon Hunter -->
        <a class="partner-card" href="<?php echo esc_url( home_url( '/media-partner/lyndon-hunter/' ) ); ?>">
          <div class="partner-card__photo">
            <?php $photo = apex_partner_photo('lyndon-hunter'); ?>
            <?php if ( $photo ) : ?>
              <img src="<?php echo esc_url($photo); ?>" alt="Lyndon Hunter">
            <?php else : ?>
              <div class="partner-card__avatar">LH</div>
            <?php endif; ?>
          </div>
          <div class="partner-card__body">
            <div class="partner-card__accent"></div>
            <div class="partner-card__specialty">Photography</div>
            <h3 class="partner-card__name">Lyndon Hunter</h3>
            <p class="partner-card__biz">LyndonNotFound</p>
            <div class="partner-card__links">
              <span class="partner-card__link">
                <span class="partner-card__link-icon icon-instagram"></span>
                @lyndonnotfound
              </span>
              <span class="partner-card__link">
                <span class="partner-card__link-icon icon-email"></span>
                lyndonhunter404@gmail.com
              </span>
              <span class="partner-card__link">
                <span class="partner-card__link-icon icon-website"></span>
                lyndonnotfound.squarespace.com
              </span>
            </div>
          </div>
        </a>

        <!-- Mathew Perez -->
        <a class="partner-card" href="<?php echo esc_url( home_url( '/media-partner/mathew-perez/' ) ); ?>">
          <div class="partner-card__photo">
            <?php $photo = apex_partner_photo('mathew-perez'); ?>
            <?php if ( $photo ) : ?>
              <img src="<?php echo esc_url($photo); ?>" alt="Mathew Perez">
            <?php else : ?>
              <div class="partner-card__avatar">MP</div>
            <?php endif; ?>
          </div>
          <div class="partner-card__body">
            <div class="partner-card__accent"></div>
            <div class="partner-card__specialty">Videography</div>
            <h3 class="partner-card__name">Mathew Perez</h3>
            <p class="partner-card__biz">Lucid Videography</p>
            <div class="partner-card__links">
              <span class="partner-card__link">
                <span class="partner-card__link-icon icon-instagram"></span>
                @lucid_videography
              </span>
              <span class="partner-card__link">
                <span class="partner-card__link-icon icon-email"></span>
                perezhouse.mlp@gmail.com
              </span>
              <span class="partner-card__link">
                <span class="partner-card__link-icon icon-youtube"></span>
                YouTube Channel
              </span>
            </div>
          </div>
        </a>

        <!-- Orion Martin -->
        <a class="partner-card" href="<?php echo esc_url( home_url( '/media-partner/orion-martin/' ) ); ?>">
          <div class="partner-card__photo">
            <?php $photo = apex_partner_photo('orion-martin'); ?>
            <?php if ( $photo ) : ?>
              <img src="<?php echo esc_url($photo); ?>" alt="Orion Martin">
            <?php else : ?>
              <div class="partner-card__avatar">OM</div>
            <?php endif; ?>
          </div>
          <div class="partner-card__body">
            <div class="partner-card__accent"></div>
            <div class="partner-card__specialty">Photography</div>
            <h3 class="partner-card__name">Orion Martin</h3>
            <p class="partner-card__biz">Instant Crush</p>
            <div class="partner-card__links">
              <span class="partner-card__link">
                <span class="partner-card__link-icon icon-instagram"></span>
                @instantcrush.photo
              </span>
              <span class="partner-card__link">
                <span class="partner-card__link-icon icon-email"></span>
                orionmartinmarketing@gmail.com
              </span>
              <span class="partner-card__link">
                <span class="partner-card__link-icon icon-portfolio"></span>
                instantcrushphoto.darkroom.com
              </span>
            </div>
          </div>
        </a>

        <!-- Jordan Van Diest -->
        <a class="partner-card" href="<?php echo esc_url( home_url( '/media-partner/jordan-van-diest/' ) ); ?>">
          <div class="partner-card__photo">
            <?php $photo = apex_partner_photo('jordan-van-diest'); ?>
            <?php if ( $photo ) : ?>
              <img src="<?php echo esc_url($photo); ?>" alt="Jordan Van Diest">
            <?php else : ?>
              <div class="partner-card__avatar">JV</div>
            <?php endif; ?>
          </div>
          <div class="partner-card__body">
            <div class="partner-card__accent"></div>
            <div class="partner-card__specialty">Photography &amp; Media</div>
            <h3 class="partner-card__name">Jordan Van Diest</h3>
            <p class="partner-card__biz">Platinum Media</p>
            <div class="partner-card__links">
              <span class="partner-card__link">
                <span class="partner-card__link-icon icon-instagram"></span>
                @platinummediallc
              </span>
              <span class="partner-card__link">
                <span class="partner-card__link-icon icon-email"></span>
                platinummedia208@gmail.com
              </span>
              <span class="partner-card__link">
                <span class="partner-card__link-icon icon-linktree"></span>
                linktr.ee/platinummediallc
              </span>
            </div>
          </div>
        </a>

      </div><!-- /.media-partners-grid -->

      <!-- NEW PARTNERS divider -->
      <div class="partners-divider">
        <div class="partners-divider__line"></div>
        <div class="partners-divider__label">New for 2026</div>
        <div class="partners-divider__line"></div>
      </div>

      <div class="media-partners-grid">

        <!-- Caleb Aerials -->
        <div class="partner-card">
          <div class="partner-card__photo">
            <div class="partner-card__avatar">CA</div>
          </div>
          <div class="partner-card__body">
            <div class="partner-card__accent"></div>
            <div class="partner-card__specialty">Drone</div>
            <h3 class="partner-card__name">Caleb Aerials</h3>
            <p class="partner-card__biz">Aerial Specialist</p>
            <div class="partner-card__links">
              <a class="partner-card__link" href="https://www.instagram.com/caleb.aerials" target="_blank" rel="noopener">
                <span class="partner-card__link-icon icon-instagram"></span>
                @caleb.aerials
              </a>
              <a class="partner-card__link" href="mailto:caleb.aerials@gmail.com">
                <span class="partner-card__link-icon icon-email"></span>
                caleb.aerials@gmail.com
              </a>
              <a class="partner-card__link" href="https://linktr.ee/caleb.aerials" target="_blank" rel="noopener">
                <span class="partner-card__link-icon icon-linktree"></span>
                linktr.ee/caleb.aerials
              </a>
            </div>
          </div>
        </div>

        <!-- John Dow II -->
        <div class="partner-card">
          <div class="partner-card__photo">
            <div class="partner-card__avatar">JD</div>
          </div>
          <div class="partner-card__body">
            <div class="partner-card__accent"></div>
            <div class="partner-card__specialty">Photography</div>
            <h3 class="partner-card__name">John Dow II</h3>
            <p class="partner-card__biz">F83 Photos</p>
            <div class="partner-card__links">
              <a class="partner-card__link" href="https://www.instagram.com/f83photos" target="_blank" rel="noopener">
                <span class="partner-card__link-icon icon-instagram"></span>
                @f83photos
              </a>
              <a class="partner-card__link" href="mailto:f83photos@icloud.com">
                <span class="partner-card__link-icon icon-email"></span>
                f83photos@icloud.com
              </a>
              <a class="partner-card__link" href="https://f83photos.com" target="_blank" rel="noopener">
                <span class="partner-card__link-icon icon-website"></span>
                f83photos.com
              </a>
            </div>
          </div>
        </div>

        <!-- John Kisiel -->
        <div class="partner-card">
          <div class="partner-card__photo">
            <div class="partner-card__avatar">JK</div>
          </div>
          <div class="partner-card__body">
            <div class="partner-card__accent"></div>
            <div class="partner-card__specialty">Photography</div>
            <h3 class="partner-card__name">John Kisiel</h3>
            <p class="partner-card__biz">John Kisiel Photography</p>
            <div class="partner-card__links">
              <a class="partner-card__link" href="https://www.instagram.com/john.kisiel" target="_blank" rel="noopener">
                <span class="partner-card__link-icon icon-instagram"></span>
                @john.kisiel
              </a>
              <a class="partner-card__link" href="mailto:johnraymondkisiel@gmail.com">
                <span class="partner-card__link-icon icon-email"></span>
                johnraymondkisiel@gmail.com
              </a>
              <a class="partner-card__link" href="https://johnkisielphotography.pixieset.com" target="_blank" rel="noopener">
                <span class="partner-card__link-icon icon-portfolio"></span>
                johnkisielphotography.pixieset.com
              </a>
            </div>
          </div>
        </div>

      </div><!-- /.media-partners-grid (new) -->

    </div>
  </section>


  <!-- ═══════════════════════════════════
       BECOME A MEDIA PARTNER
  ═══════════════════════════════════ -->
  <section id="become" class="media-rules">
    <div class="media-rules__inner">

      <div class="media-section__header">
        <div class="accent-bar"></div>
        <p class="section-label">Join the Team</p>
        <h2 class="section-title">Become a Media Partner</h2>
        <p class="section-subtitle">Media Rules &amp; Requirements</p>
      </div>

      <div class="rules-grid">

        <div class="rules-box">
          <h3 class="rules-box__title">Application Requirements</h3>
          <ul>
            <li>You must submit an application, and be accepted to be on track at any APEX Idaho event. Having a vest and a camera will not get you in for free, or allow you on track.</li>
            <li>You must own or work for a professional media business, publication, or have a website that displays your work.</li>
            <li>You must provide examples of your work, and be ready to provide references if asked.</li>
            <li>You cannot shoot on your phone as your primary equipment, and only have an Instagram profile.</li>
            <li>Approved media members that provided media to APEX Idaho for promotional material in previous years, will receive preference in approval.</li>
            <li>Approval preference will go to those who can commit to attending most, if not all events.</li>
            <li>If you are approved, you will receive an ID card that is good for the 2025 season. You will also receive a pink Hi-Vis vest to wear at all APEX Idaho events.</li>
          </ul>
        </div>

        <div class="rules-box">
          <h3 class="rules-box__title">Approved Media Must</h3>
          <ul>
            <li>Be interviewed at first track visit with an APEX Idaho official to verify credentials.</li>
            <li>Have your pink Hi-Vis vest on, and only enter and exit the track when it is appropriate and safe to do so.</li>
            <li>Maintain a level of professionalism, and respect of others and the track.</li>
            <li>Attend ALL media meetings ON TIME. NO EXCEPTIONS. If you cannot make it to the meetings, you will not be approved for that event.</li>
            <li>Upload media to APEX Idaho for promotional use via Google Drive within one week of the event's end. Uploads must be at minimum an entry fee's worth.</li>
            <li>If posting your work online, tag @apex_idaho on Instagram and APEX Idaho on Facebook.</li>
            <li>Provide links to your work on the Facebook APEX Idaho group or event page.</li>
            <li>Replacement cost for lost ID card or Hi-Vis vest is $40 each.</li>
          </ul>
        </div>

      </div>

      <p style="font-family:'Barlow Condensed',sans-serif;font-size:1.05rem;color:rgba(0,0,0,0.4);margin-top:1.75rem;font-style:italic;">
        APEX Idaho reserves the right to revoke any media privileges at any time, for any reason.
      </p>

    </div>
  </section>


  <!-- ═══════════════════════════════════
       APPLY CTA
  ═══════════════════════════════════ -->
  <section class="media-apply">
    <div class="media-apply__watermark"><span>APPLY NOW</span></div>
    <div class="media-apply__inner">
      <h2 class="media-apply__title">Ready to Apply?</h2>
      <p class="media-apply__text">Fill out the Media Application form, we'll get back to you, and you'll be ready to capture photos and videos at our APEX Idaho events.</p>
      <a class="media-apply__btn" href="<?php echo esc_url( home_url( '/media/media-application/' ) ); ?>">Apply Now</a>
    </div>
  </section>

</div><!-- /.media-page -->

<?php get_footer(); ?>
