import localFont from 'next/font/local';

/**
 * Barlow and Barlow Condensed, self-hosted (SIL OFL, src/fonts/OFL.txt).
 * The CSS variables feed --wp--preset--font-family--body / --display in
 * styles/tokens.css, matching the WordPress theme.
 */
export const body = localFont({
  variable: '--font-body',
  display: 'swap',
  src: [
    { path: '../fonts/barlow-latin-400-normal.woff2', weight: '400', style: 'normal' },
    { path: '../fonts/barlow-latin-400-italic.woff2', weight: '400', style: 'italic' },
    { path: '../fonts/barlow-latin-500-normal.woff2', weight: '500', style: 'normal' },
    { path: '../fonts/barlow-latin-600-normal.woff2', weight: '600', style: 'normal' },
    { path: '../fonts/barlow-latin-700-normal.woff2', weight: '700', style: 'normal' },
  ],
});

export const display = localFont({
  variable: '--font-display',
  display: 'swap',
  src: [
    { path: '../fonts/barlow-condensed-latin-500-normal.woff2', weight: '500', style: 'normal' },
    { path: '../fonts/barlow-condensed-latin-600-normal.woff2', weight: '600', style: 'normal' },
    { path: '../fonts/barlow-condensed-latin-700-normal.woff2', weight: '700', style: 'normal' },
    { path: '../fonts/barlow-condensed-latin-700-italic.woff2', weight: '700', style: 'italic' },
    { path: '../fonts/barlow-condensed-latin-800-italic.woff2', weight: '800', style: 'italic' },
    { path: '../fonts/barlow-condensed-latin-900-italic.woff2', weight: '900', style: 'italic' },
  ],
});
