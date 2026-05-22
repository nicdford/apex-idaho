import { env } from "./env.js";

async function call(path: string, params: Record<string, string | number | boolean | undefined> = {}) {
  const url = new URL(`${env.wpBaseUrl}/wp-json/pitwall/v1${path}`);
  for (const [k, v] of Object.entries(params)) {
    if (v === undefined || v === "" || v === null) continue;
    url.searchParams.set(k, String(v));
  }
  const res = await fetch(url, {
    headers: {
      "X-Api-Key": env.wpApiKey,
      Accept: "application/json",
    },
  });
  if (!res.ok) {
    const body = await res.text();
    throw new Error(`WP ${res.status} ${path}: ${body.slice(0, 300)}`);
  }
  return res.json();
}

export type EventSummary = { id: number; title: string; start: string | null; end: string | null };

let eventsCache: { at: number; data: EventSummary[] } | null = null;
const EVENTS_CACHE_TTL_MS = 30_000;

export async function listEventsCached(): Promise<EventSummary[]> {
  if (eventsCache && Date.now() - eventsCache.at < EVENTS_CACHE_TTL_MS) {
    return eventsCache.data;
  }
  const data = (await call("/events")) as EventSummary[];
  eventsCache = { at: Date.now(), data };
  return data;
}

export const wp = {
  listEvents: () => call("/events"),
  listEventTickets: (eventId: number) => call(`/events/${eventId}/tickets`),
  listEventAttendees: (
    eventId: number,
    opts: { ticketId?: number; status?: string; checkedIn?: boolean; includeMeta?: boolean } = {}
  ) =>
    call(`/events/${eventId}/attendees`, {
      ticket_id: opts.ticketId,
      status: opts.status,
      checked_in: opts.checkedIn == null ? undefined : opts.checkedIn ? 1 : 0,
      include_meta: opts.includeMeta ? 1 : undefined,
    }),
  searchAttendees: (q: string, opts: { eventId?: number; includeMeta?: boolean } = {}) =>
    call(`/attendees/search`, {
      q,
      event_id: opts.eventId,
      include_meta: opts.includeMeta ? 1 : undefined,
    }),
};
