import type { MetadataRoute } from 'next';
import { SITE_URL } from '@/lib/config';
import { getPastEvents, getUpcomingEvents } from '@/lib/events';
import { isWordPressOnlyPath } from '@/lib/html';
import { getAllEntryPaths } from '@/lib/wp';

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const [entries, upcoming, past] = await Promise.all([getAllEntryPaths(), getUpcomingEvents(50), getPastEvents(50)]);
  const own = ['/', '/events', '/sponsors', '/media', '/news'];
  const pages = entries.filter(({ path }) => !own.includes(path) && path !== '/home' && !isWordPressOnlyPath(path));

  return [
    ...own.map((path) => ({ url: `${SITE_URL}${path === '/' ? '' : path}` })),
    ...pages.map(({ path, modified }) => ({ url: `${SITE_URL}${path}`, lastModified: modified })),
    ...[...upcoming, ...past].map((e) => ({ url: `${SITE_URL}${e.path}` })),
  ];
}
