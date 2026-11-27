import type { ApexEvent } from '@/lib/events';
import { ticketStatus, type Ticket } from '@/lib/tickets';

/**
 * What's on sale for an event, as set up in WordPress (Event Tickets).
 * Read-only: buying happens on the event's WordPress page.
 */
export function TicketList({ event, tickets, now }: { event: ApexEvent; tickets: Ticket[]; now: number }) {
  const day = (ms: number) => new Intl.DateTimeFormat('en-US', { timeZone: event.timezone ?? 'America/Boise', month: 'short', day: 'numeric' }).format(ms);

  return (
    <ul className="apex-tickets" role="list">
      {tickets.map((ticket) => {
        const status = ticketStatus(ticket, now);
        const label =
          status === 'sold-out'
            ? 'Sold out'
            : status === 'ended'
              ? 'Sales closed'
              : status === 'not-yet'
                ? `On sale ${day(ticket.salesStart as number)}`
                : ticket.left !== null
                  ? ticket.capacity
                    ? `${ticket.left} of ${ticket.capacity} left`
                    : `${ticket.left} left`
                  : 'On sale';
        return (
          <li key={ticket.id} className={`apex-ticket${status === 'on-sale' ? '' : ' is-unavailable'}`}>
            <div className="apex-ticket__main">
              <h3 className="apex-ticket__name">{ticket.name}</h3>
              {ticket.description ? <p className="apex-ticket__desc">{ticket.description}</p> : null}
            </div>
            <div className="apex-ticket__side">
              <p className="apex-ticket__price">{ticket.price}</p>
              <p className="apex-ticket__status">{label}</p>
            </div>
          </li>
        );
      })}
    </ul>
  );
}
