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

export type Tag = 'site' | 'events' | 'sponsors' | 'partners' | 'pages' | 'posts';

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

/* ---------------------------------------------------------------- site -- */

export type Site = {
  name: string;
  description: string;
  logo: WPImage | null;
  social: Record<string, string>;
};

export async function getSite(): Promise<Site> {
  const data = await wpJson<Site>('apex/v1/site', ['site']);
  return {
    name: decodeEntities(data?.name) || 'APEX Idaho',
    description: decodeEntities(data?.description),
    logo: data?.logo ?? null,
    social: data?.social && !Array.isArray(data.social) ? data.social : {},
  };
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

/* ------------------------------------------------------ media partners -- */

export type MediaPartnerPhoto = {
  slug: string;
  name: string;
  link: string;
  photo: WPImage | null;
  /** CSS object-position, e.g. "50% 25%". */
  focal: string;
};

export async function getMediaPartnerPhotos(): Promise<Record<string, MediaPartnerPhoto>> {
  const list = (await wpJson<MediaPartnerPhoto[]>('apex/v1/media-partners', ['partners'])) ?? [];
  return Object.fromEntries(list.map((p) => [p.slug, p]));
}

/* --------------------------------------------------------- pages/posts -- */

type Rendered = { rendered: string };

export type WPEntry = {
  id: number;
  slug: string;
  link: string;
  date: string;
  modified: string;
  title: Rendered;
  content: Rendered;
  excerpt: Rendered;
  parent?: number;
  _embedded?: { 'wp:featuredmedia'?: { source_url: string; alt_text?: string; media_details?: { width: number; height: number } }[] };
};

export type Entry = {
  id: number;
  type: 'page' | 'post';
  slug: string;
  path: string;
  title: string;
  html: string;
  excerpt: string;
  date: string;
  modified: string;
  image: WPImage | null;
};

const FIELDS = 'id,slug,link,date,modified,title,content,excerpt,parent,_links,_embedded';

function toEntry(e: WPEntry, type: Entry['type']): Entry {
  const media = e._embedded?.['wp:featuredmedia']?.[0];
  return {
    id: e.id,
    type,
    slug: e.slug,
    path: new URL(e.link).pathname.replace(/\/+$/, '') || '/',
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

/** A page by its full path ("tech-rules", "media/media-application"). */
export async function getPage(path: string): Promise<Entry | null> {
  const segments = path.split('/').filter(Boolean);
  const slug = segments[segments.length - 1];
  if (!slug) return null;
  const list = await wpJson<WPEntry[]>(`wp/v2/pages?slug=${encodeURIComponent(slug)}&_embed=wp:featuredmedia&_fields=${FIELDS}`, ['pages']);
  const wanted = `/${segments.join('/')}`;
  const match = list?.find((p) => toEntry(p, 'page').path === wanted) ?? (segments.length === 1 ? list?.[0] : undefined);
  return match ? toEntry(match, 'page') : null;
}

/**
 * A post by its permalink path. The permalink structure is a WordPress
 * setting (the live site uses /%postname%/%category%/), so every segment is
 * tried as the slug and the post whose own link matches the path wins.
 */
export async function getPost(path: string): Promise<Entry | null> {
  const segments = path.split('/').filter(Boolean);
  if (!segments.length) return null;
  const slugs = [...new Set(segments)].map(encodeURIComponent).join(',');
  const list = await wpJson<WPEntry[]>(`wp/v2/posts?slug=${slugs}&_embed=wp:featuredmedia&_fields=${FIELDS}`, ['posts']);
  const wanted = `/${segments.join('/')}`;
  const match = list?.find((p) => toEntry(p, 'post').path === wanted) ?? (segments.length === 1 ? list?.[0] : undefined);
  return match ? toEntry(match, 'post') : null;
}

export async function getPosts(perPage = 12): Promise<Entry[]> {
  const list = await wpJson<WPEntry[]>(`wp/v2/posts?per_page=${perPage}&_embed=wp:featuredmedia&_fields=${FIELDS}`, ['posts']);
  return (list ?? []).map((p) => toEntry(p, 'post'));
}

/** Every published page and post path, for the sitemap and prerendering. */
export async function getAllEntryPaths(): Promise<{ path: string; modified: string }[]> {
  const fields = '_fields=link,modified';
  const [pages, posts] = await Promise.all([
    wpJson<WPEntry[]>(`wp/v2/pages?per_page=100&${fields}`, ['pages']),
    wpJson<WPEntry[]>(`wp/v2/posts?per_page=100&${fields}`, ['posts']),
  ]);
  return [...(pages ?? []), ...(posts ?? [])].map((e) => ({
    path: new URL(e.link).pathname.replace(/\/+$/, '') || '/',
    modified: e.modified,
  }));
}

export { wpJson };
