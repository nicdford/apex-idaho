import type { NextConfig } from 'next';
import { KBD_STANDINGS_URL } from './src/lib/config';

/**
 * WORDPRESS_URL: where the app reads the WordPress REST API.
 * WORDPRESS_PUBLIC_URL: where visitors are sent for pages WordPress keeps
 *   serving (cart, checkout, ticket purchase, forms). Defaults to WORDPRESS_URL.
 */
const wordpress = new URL(process.env.WORDPRESS_URL || 'https://apex-idaho.com');
const wordpressPublic = (process.env.WORDPRESS_PUBLIC_URL || wordpress.origin).replace(/\/$/, '');
const isLocalWordPress = ['localhost', '127.0.0.1'].includes(wordpress.hostname);

const remote = (pathname: string) => ({
  protocol: wordpress.protocol.replace(':', '') as 'http' | 'https',
  hostname: wordpress.hostname,
  port: wordpress.port,
  pathname,
});

/** Paths WordPress serves itself; requests for them here are sent across. */
const WORDPRESS_PATHS = [
  '/cart',
  '/checkout/:path*',
  '/my-account/:path*',
  '/shop/:path*',
  '/product/:path*',
  '/product-category/:path*',
  '/tickets-checkout/:path*',
  '/tickets-order/:path*',
  '/media/media-application',
  '/mfos-driver-registration',
  '/mfos-drivers-list',
  '/wp-admin/:path*',
  '/wp-login.php',
  '/feed/:path*',
];

const nextConfig: NextConfig = {
  output: 'standalone',
  poweredByHeader: false,
  images: {
    qualities: [75],
    remotePatterns: [remote('/wp-content/uploads/**'), remote('/wp-content/themes/**')],
    // Only for local development against WordPress on localhost.
    dangerouslyAllowLocalIP: isLocalWordPress,
  },
  async redirects() {
    return [
      // WordPress event permalinks (/event/slug/) live at /events/slug here.
      { source: '/event/:slug', destination: '/events/:slug', permanent: true },
      // Old WordPress page URLs.
      { source: '/kbd-comp', destination: KBD_STANDINGS_URL, permanent: false },
      { source: '/refund_returns', destination: '/refund-policy', permanent: true },
      { source: '/mfos-schedule', destination: '/events', permanent: false },
      { source: '/media-partner/:slug*', destination: '/media#partners', permanent: true },
      ...WORDPRESS_PATHS.map((source) => ({
        source,
        destination: `${wordpressPublic}${source}`,
        permanent: false,
      })),
    ];
  },
};

export default nextConfig;
