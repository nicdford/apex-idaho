import { env } from "./env.js";

async function call(path: string, params: Record<string, string | number | boolean | undefined> = {}) {
  const url = new URL(`${env.wpBaseUrl}/wp-json/attendee-api/v1${path}`);
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

export const wp = {
  listEvents: () => call("/events"),
  listEventTickets: (eventId: number) => call(`/events/${eventId}/tickets`),
  listEventAttendees: (eventId: number, opts: { ticketId?: number; status?: string; includeMeta?: boolean } = {}) =>
    call(`/events/${eventId}/attendees`, {
      ticket_id: opts.ticketId,
      status: opts.status,
      include_meta: opts.includeMeta ? 1 : undefined,
    }),
  searchAttendees: (q: string, opts: { eventId?: number; includeMeta?: boolean } = {}) =>
    call(`/attendees/search`, {
      q,
      event_id: opts.eventId,
      include_meta: opts.includeMeta ? 1 : undefined,
    }),
};
