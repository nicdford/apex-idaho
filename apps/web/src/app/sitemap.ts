import type { MetadataRoute } from 'next';
import { SITE_URL } from '@/lib/config';
import { getPastEvents, getUpcomingEvents } from '@/lib/events';
import { getPostPaths } from '@/lib/wp';

/** Static pages of this app. */
const PAGES = ['/', '/events', '/sponsors', '/media', '/news', '/about', '/tech-rules', '/refund-policy'];

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const [posts, upcoming, past] = await Promise.all([getPostPaths(), getUpcomingEvents(50), getPastEvents(50)]);
  return [
    ...PAGES.map((path) => ({ url: `${SITE_URL}${path === '/' ? '' : path}` })),
    ...[...upcoming, ...past].map((e) => ({ url: `${SITE_URL}${e.path}` })),
    ...posts.map(({ path, modified }) => ({ url: `${SITE_URL}${path}`, lastModified: modified })),
  ];
}
