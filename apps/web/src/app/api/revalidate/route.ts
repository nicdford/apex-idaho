import { timingSafeEqual } from 'node:crypto';
import { revalidateTag } from 'next/cache';
import type { Tag } from '@/lib/wp';

const TAGS: Tag[] = ['site', 'events', 'sponsors', 'partners', 'pages', 'posts'];

function authorized(header: string | null): boolean {
  const secret = process.env.REVALIDATE_SECRET;
  if (!secret || !header) return false;
  const a = Buffer.from(header);
  const b = Buffer.from(secret);
  return a.length === b.length && timingSafeEqual(a, b);
}

/**
 * Called by the WordPress plugin (apex-headless) when content changes:
 *   POST /api/revalidate  { "tags": ["events"] }  header x-revalidate-secret
 * Without a body every tag is refreshed. Cached data expires immediately, so
 * the next visit renders the edit.
 */
export async function POST(request: Request) {
  if (!authorized(request.headers.get('x-revalidate-secret'))) {
    return Response.json({ ok: false, error: 'Unauthorized' }, { status: 401 });
  }

  let requested: unknown = null;
  try {
    requested = ((await request.json()) as { tags?: unknown }).tags;
  } catch {
    // Empty or non-JSON body: refresh everything.
  }

  const tags = Array.isArray(requested) ? TAGS.filter((t) => requested.includes(t)) : TAGS;
  for (const tag of tags) revalidateTag(tag, { expire: 0 });

  return Response.json({ ok: true, revalidated: tags, now: Date.now() });
}
