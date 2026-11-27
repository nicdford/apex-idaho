/**
 * WordPress REST client. Server-side only.
 *
 * Every request is cached and tagged; the WordPress plugin (apex-headless)
 * calls /api/revalidate with the tags that changed when content is saved.
 * Requests never throw: a failed or missing endpoint returns null and the
 * page renders its empty state, so a WordPress hiccup can't break a build.
 */
import { REVALIDATE_SECONDS, WORDPRESS_URL } from './config';
import { decodeEntities } from './html';

export type Tag = 'events' | 'sponsors' | 'posts';

export type WPImage = { url: string; width: number; height: number; alt?: string };

async function wpJson<T>(path: string, tags: Tag[], revalidate = REVALIDATE_SECONDS): Promise<T | null> {
  const url = `${WORDPRESS_URL}/wp-json/${path}`;
  try {
    const res = await fetch(url, {
      headers: { Accept: 'application/json' },
      next: { revalidate, tags },
    });
    if (!res.ok) {
      if (res.status !== 404) console.warn(`[wordpress] ${res.status} ${url}`);
      return null;
    }
    return (await res.json()) as T;
  } catch (error) {
    console.warn(`[wordpress] request failed: ${url}`, error instanceof Error ? error.message : error);
    return null;
  }
}

/* ------------------------------------------------------------ sponsors -- */

export type Sponsor = {
  id: number;
  name: string;
  url: string;
  logo: WPImage;
  /** CSS filter tuned for a white background (grayscale, brightness(0), invert). */
  filter: string;
  invert: boolean;
};

export async function getSponsors(): Promise<Sponsor[]> {
  return (await wpJson<Sponsor[]>('apex/v1/sponsors', ['sponsors'])) ?? [];
}

/* ---------------------------------------------------------------- posts -- */
/*
 * Only posts (news) come from WordPress. Pages are static content in this
 * app (src/app, src/content); WordPress keeps events, tickets and the shop.
 */

type Rendered = { rendered: string };

type WPPost = {
  id: number;
  slug: string;
  link: string;
  date: string;
  modified: string;
  title: Rendered;
  content: Rendered;
  excerpt: Rendered;
  _embedded?: { 'wp:featuredmedia'?: { source_url: string; alt_text?: string; media_details?: { width: number; height: number } }[] };
};

export type Post = {
  id: number;
  slug: string;
  /** Path on this site: the post's WordPress permalink path. */
  path: string;
  title: string;
  html: string;
  excerpt: string;
  date: string;
  modified: string;
  image: WPImage | null;
};

const FIELDS = 'id,slug,link,date,modified,title,content,excerpt,_links,_embedded';

const pathOf = (link: string) => new URL(link).pathname.replace(/\/+$/, '') || '/';

function toPost(e: WPPost): Post {
  const media = e._embedded?.['wp:featuredmedia']?.[0];
  return {
    id: e.id,
    slug: e.slug,
    path: pathOf(e.link),
    title: decodeEntities(e.title.rendered),
    html: e.content.rendered,
    excerpt: e.excerpt.rendered,
    date: e.date,
    modified: e.modified,
    image: media?.source_url
      ? { url: media.source_url, width: media.media_details?.width ?? 1600, height: media.media_details?.height ?? 900, alt: media.alt_text }
      : null,
  };
}

/**
 * A post by its permalink path. The permalink structure is a WordPress
 * setting (the live site uses /%postname%/%category%/), so every segment is
 * tried as the slug and the post whose own link matches the path wins.
 */
export async function getPost(path: string): Promise<Post | null> {
  const segments = path.split('/').filter(Boolean);
  if (!segments.length) return null;
  const slugs = [...new Set(segments)].map(encodeURIComponent).join(',');
  const list = await wpJson<WPPost[]>(`wp/v2/posts?slug=${slugs}&_embed=wp:featuredmedia&_fields=${FIELDS}`, ['posts']);
  const wanted = `/${segments.join('/')}`;
  const match = list?.find((p) => pathOf(p.link) === wanted) ?? (segments.length === 1 ? list?.[0] : undefined);
  return match ? toPost(match) : null;
}

export async function getPosts(perPage = 12): Promise<Post[]> {
  const list = await wpJson<WPPost[]>(`wp/v2/posts?per_page=${perPage}&_embed=wp:featuredmedia&_fields=${FIELDS}`, ['posts']);
  return (list ?? []).map(toPost);
}

/** Every published post path, for the sitemap and prerendering. */
export async function getPostPaths(): Promise<{ path: string; modified: string }[]> {
  const list = await wpJson<Pick<WPPost, 'link' | 'modified'>[]>('wp/v2/posts?per_page=100&_fields=link,modified', ['posts']);
  return (list ?? []).map((e) => ({ path: pathOf(e.link), modified: e.modified }));
}

export { wpJson };
