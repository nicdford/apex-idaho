/**
 * Events from The Events Calendar's REST API (/wp-json/tribe/events/v1).
 *
 * Dates arrive as site-local wall-clock strings ("2027-05-28 08:00:00") plus
 * a UTC copy. Display uses the wall-clock parts as-is (formatted in UTC so the
 * server's own timezone can't shift them); the countdown uses the UTC instant.
 */
import { WORDPRESS_PUBLIC_URL } from './config';
import { decodeEntities, rewriteWordPressHtml, stripTags } from './html';
import { wpJson, type WPImage } from './wp';

type TecVenue = { venue?: string; city?: string; state?: string; address?: string; zip?: string };

type TecEvent = {
  id: number;
  slug: string;
  title: string;
  url: string;
  description: string;
  excerpt: string;
  start_date: string;
  end_date: string;
  utc_start_date?: string;
  utc_end_date?: string;
  all_day: boolean;
  cost: string;
  ticketed?: string[] | false;
  featured?: boolean;
  image: (WPImage & { sizes?: Record<string, WPImage> }) | false;
  venue: TecVenue | [] | null;
};

type TecList = { events: TecEvent[]; total: number };

export type ApexEvent = {
  id: number;
  slug: string;
  title: string;
  /** Path on this site. */
  path: string;
  /** The event page on WordPress, where the ticket form lives. */
  ticketsUrl: string;
  description: string;
  summary: string;
  start: string;
  end: string;
  /** Milliseconds since the epoch, or null when the API gave no UTC time. */
  startsAt: number | null;
  endsAt: number | null;
  allDay: boolean;
  cost: string;
  ticketed: boolean;
  image: WPImage | null;
  venue: { name: string; city: string; state: string; address: string } | null;
};

const utc = (s?: string) => (s ? Date.parse(`${s.replace(' ', 'T')}Z`) : NaN);

function normalize(e: TecEvent): ApexEvent {
  const venue = e.venue && !Array.isArray(e.venue) && e.venue.venue ? e.venue : null;
  const startsAt = utc(e.utc_start_date);
  const endsAt = utc(e.utc_end_date);
  const image = e.image ? (e.image.sizes?.large ?? e.image) : null;
  return {
    id: e.id,
    slug: e.slug,
    title: decodeEntities(e.title),
    path: `/events/${e.slug}`,
    ticketsUrl: e.url.replace(/^https?:\/\/[^/]+/, WORDPRESS_PUBLIC_URL),
    description: e.description ?? '',
    summary: stripTags(e.excerpt || e.description).slice(0, 220),
    start: e.start_date,
    end: e.end_date,
    startsAt: Number.isFinite(startsAt) ? startsAt : null,
    endsAt: Number.isFinite(endsAt) ? endsAt : null,
    allDay: !!e.all_day,
    cost: decodeEntities(e.cost),
    ticketed: Array.isArray(e.ticketed) ? e.ticketed.length > 0 : !!e.ticketed,
    image: image ? { url: image.url, width: image.width, height: image.height } : null,
    venue: venue
      ? { name: decodeEntities(venue.venue), city: decodeEntities(venue.city), state: decodeEntities(venue.state), address: decodeEntities(venue.address) }
      : null,
  };
}

/** Next events, soonest first (includes events already under way). */
export async function getUpcomingEvents(count = 3): Promise<ApexEvent[]> {
  const data = await wpJson<TecList>(`tribe/events/v1/events?per_page=${Math.min(50, count)}`, ['events']);
  return (data?.events ?? []).map(normalize);
}

/** Recent past events, newest first. */
export async function getPastEvents(count = 12): Promise<ApexEvent[]> {
  const data = await wpJson<TecList>('tribe/events/v1/events?per_page=50&start_date=2015-01-01&end_date=now', ['events']);
  const now = Date.now();
  return (data?.events ?? [])
    .map(normalize)
    .filter((e) => (e.endsAt ?? 0) < now)
    .sort((a, b) => b.start.localeCompare(a.start))
    .slice(0, count);
}

export async function getEvent(slug: string): Promise<ApexEvent | null> {
  const data = await wpJson<TecEvent>(`tribe/events/v1/events/by-slug/${encodeURIComponent(slug)}`, ['events']);
  return data?.id ? normalize(data) : null;
}

/** Description HTML with links pointed at this site. */
export function eventDescriptionHtml(event: ApexEvent): string {
  return rewriteWordPressHtml(event.description);
}

/* ------------------------------------------------------------- formatting -- */

type Parts = { y: number; m: number; d: number; h: number; mi: number };

function parts(local: string): Parts | null {
  const m = local.match(/^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})/);
  if (!m) return null;
  return { y: +m[1], m: +m[2], d: +m[3], h: +m[4], mi: +m[5] };
}

const asDate = (p: Parts) => new Date(Date.UTC(p.y, p.m - 1, p.d, p.h, p.mi));
const fmt = (p: Parts, opts: Intl.DateTimeFormatOptions) => new Intl.DateTimeFormat('en-US', { timeZone: 'UTC', ...opts }).format(asDate(p));

/** "May 28", "May 28–30" or "Jun 30 – Jul 1" (optionally with the year). */
export function formatDateRange(start: string, end: string, withYear = false): string {
  const s = parts(start);
  const e = parts(end) ?? s;
  if (!s || !e) return '';
  const year = withYear ? `, ${s.y}` : '';
  const sameDay = s.y === e.y && s.m === e.m && s.d === e.d;
  if (sameDay) return `${fmt(s, { month: 'short', day: 'numeric' })}${year}`;
  if (s.y === e.y && s.m === e.m) return `${fmt(s, { month: 'short', day: 'numeric' })}–${e.d}${year}`;
  return `${fmt(s, { month: 'short', day: 'numeric' })} – ${fmt(e, { month: 'short', day: 'numeric' })}${year}`;
}

/** Badge parts: day ("28" or "28–30") and month ("May" or "May–Jun"). */
export function dateBadge(start: string, end: string): { day: string; month: string } {
  const s = parts(start);
  const e = parts(end) ?? s;
  if (!s || !e) return { day: '', month: '' };
  const month = (p: Parts) => fmt(p, { month: 'short' });
  if (s.m === e.m && s.y === e.y) return { day: s.d === e.d ? `${s.d}` : `${s.d}–${e.d}`, month: month(s) };
  return { day: `${s.d}`, month: `${month(s)}–${month(e)}` };
}

/** "Fri, May 28 · 8:00 AM" (no time for all-day events). */
export function formatStart(event: ApexEvent): string {
  const s = parts(event.start);
  if (!s) return '';
  const date = fmt(s, { weekday: 'short', month: 'short', day: 'numeric' });
  return event.allDay ? date : `${date} · ${fmt(s, { hour: 'numeric', minute: '2-digit' })}`;
}

export function venueLine(event: ApexEvent): string {
  if (!event.venue) return '';
  const place = [event.venue.city, event.venue.state].filter(Boolean).join(', ');
  return [event.venue.name, place].filter(Boolean).join(' · ');
}

export function mapUrl(event: ApexEvent): string | null {
  if (!event.venue) return null;
  const q = [event.venue.name, event.venue.address, event.venue.city, event.venue.state].filter(Boolean).join(' ');
  return `https://maps.google.com/?q=${encodeURIComponent(q)}`;
}

/* ---------------------------------------------------------------- timing -- */

/** True until the event has ended (or when the API gave no end time). */
export function isUpcoming(event: ApexEvent, now = Date.now()): boolean {
  return event.endsAt === null || event.endsAt > now;
}

/** True before the event starts (a countdown makes sense). */
export function hasNotStarted(event: ApexEvent, now = Date.now()): boolean {
  return event.startsAt !== null && event.startsAt > now;
}

/** The first event in the list that has not started yet. */
export function nextToStart(events: ApexEvent[], now = Date.now()): ApexEvent | undefined {
  return events.find((e) => hasNotStarted(e, now));
}
