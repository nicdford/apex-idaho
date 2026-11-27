/**
 * Environment and site-wide constants. Server-side values are read at build
 * and request time; nothing here is a secret except REVALIDATE_SECRET, which
 * is only read by the revalidate route.
 */

const trim = (url: string) => url.replace(/\/+$/, '');

/** Where the app reads the WordPress REST API. */
export const WORDPRESS_URL = trim(process.env.WORDPRESS_URL || 'https://apex-idaho.com');

/** Where visitors go for pages WordPress keeps serving (checkout, tickets, forms). */
export const WORDPRESS_PUBLIC_URL = trim(process.env.WORDPRESS_PUBLIC_URL || WORDPRESS_URL);

/** This site's public URL. */
export const SITE_URL = trim(process.env.NEXT_PUBLIC_SITE_URL || 'http://localhost:3000');

/** Background refresh interval for WordPress data, in seconds (webhook is the primary trigger). */
export const REVALIDATE_SECONDS = Number(process.env.REVALIDATE_SECONDS) || 300;

export const SITE_NAME = 'APEX Idaho';
export const SITE_DESCRIPTION =
  'Drift, grip and full-throttle competition across Idaho. Every skill level on track, spectators welcome.';

/** KBD Comp Series standings (series manager). Update each season; /kbd-comp redirects here. */
export const KBD_STANDINGS_URL = 'https://series-manager.fly.dev/s/2026-kbd-comp-series';

export type NavItem = { label: string; href: string };

export const PRIMARY_NAV: NavItem[] = [
  { label: 'Events', href: '/events' },
  { label: 'Sponsors', href: '/sponsors' },
  { label: 'Media', href: '/media' },
  { label: 'Shop', href: `${WORDPRESS_PUBLIC_URL}/shop/` },
  { label: 'About', href: '/about' },
  { label: 'Tech & Rules', href: '/tech-rules' },
];

export const FOOTER_NAV: { heading: string; links: NavItem[] }[] = [
  {
    heading: 'Explore',
    links: [
      { label: 'Events', href: '/events' },
      { label: 'Media', href: '/media' },
      { label: 'Shop', href: `${WORDPRESS_PUBLIC_URL}/shop/` },
      { label: 'About', href: '/about' },
    ],
  },
  {
    heading: 'Drivers',
    links: [
      { label: 'Tech & Rules', href: '/tech-rules' },
      { label: 'KBD Standings', href: KBD_STANDINGS_URL },
      { label: 'Media Package', href: `${WORDPRESS_PUBLIC_URL}/product/media-package/` },
      { label: 'Refund Policy', href: '/refund-policy' },
    ],
  },
  {
    heading: 'Get Involved',
    links: [
      { label: 'Sponsors', href: '/sponsors' },
      { label: 'Media Application', href: `${WORDPRESS_PUBLIC_URL}/media/media-application/` },
    ],
  },
];

export const CART_URL = `${WORDPRESS_PUBLIC_URL}/cart/`;

/** Social profiles shown in the footer (keys match the icons in components/SocialLinks). */
export const SOCIAL: Record<string, string> = {
  instagram: 'https://www.instagram.com/apex_idaho/',
  facebook: 'https://www.facebook.com/profile.php?id=100091864913810',
};
