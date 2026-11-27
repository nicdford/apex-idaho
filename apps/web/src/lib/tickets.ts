/**
 * Tickets from Event Tickets' REST API (/wp-json/tribe/tickets/v1). Tickets
 * are created, priced and sold in WordPress (WooCommerce checkout on the event
 * page); this app only shows what is on offer and how many are left.
 *
 * Cached under the "events" tag: the apex-headless plugin refreshes it when a
 * ticket is saved or a sale changes the stock, and the background refresh
 * (REVALIDATE_SECONDS) catches the rest.
 */
import type { ApexEvent } from './events';
import { decodeEntities, stripTags } from './html';
import { wpJson } from './wp';

type TecTicket = {
  id: number;
  title: string;
  description: string;
  cost: string;
  status: string;
  capacity: number | '' | null;
  capacity_details?: { available: number; available_percentage: number };
  available_from?: string;
  available_until?: string;
};

export type Ticket = {
  id: number;
  name: string;
  description: string;
  /** As Event Tickets formats it, e.g. "$380.00". */
  price: string;
  /** Tickets left, or null when unlimited. */
  left: number | null;
  capacity: number | null;
  /** Sale window as instants (ms), or null when open-ended. */
  salesStart: number | null;
  salesEnd: number | null;
};

export type TicketStatus = 'on-sale' | 'sold-out' | 'not-yet' | 'ended';

/* Wall-clock time in an IANA zone -> epoch ms. */
function zoneOffset(ts: number, timeZone: string): number {
  const parts = new Intl.DateTimeFormat('en-US', { timeZone, hourCycle: 'h23', year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' }).formatToParts(ts);
  const get = (type: string) => Number(parts.find((p) => p.type === type)?.value);
  return Date.UTC(get('year'), get('month') - 1, get('day'), get('hour'), get('minute'), get('second')) - ts;
}

function toInstant(local: string | undefined, event: ApexEvent): number | null {
  const m = local?.match(/^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})/);
  if (!m) return null;
  const wall = Date.UTC(+m[1], +m[2] - 1, +m[3], +m[4], +m[5]);
  if (event.timezone) {
    try {
      const first = wall - zoneOffset(wall, event.timezone);
      return wall - zoneOffset(first, event.timezone);
    } catch {
      // Unknown zone name: fall through to the event's own offset.
    }
  }
  // Fallback: the offset between the event's local and UTC start.
  const start = event.start.match(/^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})/);
  if (event.startsAt === null || !start) return wall;
  return wall + (event.startsAt - Date.UTC(+start[1], +start[2] - 1, +start[3], +start[4], +start[5]));
}

export async function getTickets(event: ApexEvent): Promise<Ticket[]> {
  const data = await wpJson<{ tickets: TecTicket[] }>(`tribe/tickets/v1/tickets?include_post=${event.id}&per_page=50`, ['events']);
  return (data?.tickets ?? [])
    .filter((t) => t.status === 'publish')
    .map((t) => {
      const left = t.capacity_details?.available;
      return {
        id: t.id,
        name: decodeEntities(t.title),
        description: stripTags(t.description ?? ''),
        price: decodeEntities(t.cost),
        left: left === undefined || left < 0 ? null : left,
        capacity: typeof t.capacity === 'number' && t.capacity > 0 ? t.capacity : null,
        salesStart: toInstant(t.available_from, event),
        salesEnd: toInstant(t.available_until, event),
      };
    });
}

export function ticketStatus(ticket: Ticket, now = Date.now()): TicketStatus {
  if (ticket.salesEnd !== null && now > ticket.salesEnd) return 'ended';
  if (ticket.left === 0) return 'sold-out';
  if (ticket.salesStart !== null && now < ticket.salesStart) return 'not-yet';
  return 'on-sale';
}
