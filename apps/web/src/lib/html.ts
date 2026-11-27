import { WORDPRESS_PUBLIC_URL, WORDPRESS_URL } from './config';

const NAMED: Record<string, string> = {
  amp: '&',
  lt: '<',
  gt: '>',
  quot: '"',
  apos: "'",
  nbsp: ' ',
  ndash: '–',
  mdash: '—',
  hellip: '…',
  lsquo: '‘',
  rsquo: '’',
  ldquo: '“',
  rdquo: '”',
  middot: '·',
  copy: '©',
  reg: '®',
  trade: '™',
  dollar: '$',
};

/** Decode HTML entities in WordPress strings (titles, costs). Output is plain text. */
export function decodeEntities(input: string | null | undefined): string {
  if (!input) return '';
  return input.replace(/&(#x[0-9a-f]+|#\d+|[a-z]+);/gi, (match, code: string) => {
    if (code[0] === '#') {
      const n = code[1] === 'x' || code[1] === 'X' ? parseInt(code.slice(2), 16) : parseInt(code.slice(1), 10);
      return Number.isFinite(n) ? String.fromCodePoint(n) : match;
    }
    return NAMED[code.toLowerCase()] ?? match;
  });
}

/** Plain text from an HTML fragment. */
export function stripTags(html: string | null | undefined): string {
  return decodeEntities((html ?? '').replace(/<[^>]*>/g, ' ')).replace(/\s+/g, ' ').trim();
}

/**
 * Paths WordPress keeps serving: shopping, checkout, accounts and forms.
 * Mirrors WORDPRESS_PATHS in next.config.ts and the plugin's allowlist.
 */
const WORDPRESS_ONLY = [
  /^\/cart\/?$/,
  /^\/checkout(\/|$)/,
  /^\/my-account(\/|$)/,
  /^\/shop(\/|$)/,
  /^\/product(-category)?\//,
  /^\/tickets-(checkout|order)(\/|$)/,
  /^\/media\/media-application\/?$/,
  /^\/mfos-(driver-registration|drivers-list)\/?$/,
  /^\/wp-(admin|login|content|includes|json)/,
  /^\/feed(\/|$)/,
];

export function isWordPressOnlyPath(path: string): boolean {
  return WORDPRESS_ONLY.some((re) => re.test(path));
}

const WP_ORIGINS = Array.from(new Set([WORDPRESS_URL, WORDPRESS_PUBLIC_URL, 'https://apex-idaho.com', 'https://www.apex-idaho.com']));

/**
 * Turn a WordPress URL into where it lives now: a path on this site for
 * content, or the public WordPress URL for anything WordPress still serves.
 * Returns the input unchanged for other sites.
 */
export function toAppHref(href: string): string {
  const origin = WP_ORIGINS.find((o) => href === o || href.startsWith(`${o}/`));
  if (!origin) return href;

  const rest = href.slice(origin.length) || '/';
  const url = new URL(rest, 'http://x');
  if (isWordPressOnlyPath(url.pathname)) return `${WORDPRESS_PUBLIC_URL}${rest}`;

  let path = url.pathname.replace(/\/+$/, '') || '/';
  const event = path.match(/^\/event\/([^/]+)$/);
  if (event) path = `/events/${event[1]}`;
  return `${path}${url.search}${url.hash}`;
}

/**
 * Rewrite links in WordPress-rendered HTML (page and event content) so they
 * point at this site, and make relative uploads absolute.
 */
export function rewriteWordPressHtml(html: string): string {
  return html
    .replace(/(<a\b[^>]*?\shref=)(["'])(.*?)\2/gi, (_m, pre: string, q: string, href: string) => `${pre}${q}${toAppHref(decodeEntities(href)).replace(/&/g, '&amp;')}${q}`)
    .replace(/(\s(?:src|srcset)=["'])\/wp-content\//gi, `$1${WORDPRESS_URL}/wp-content/`);
}
