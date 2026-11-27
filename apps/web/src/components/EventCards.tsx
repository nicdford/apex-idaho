import Image from 'next/image';
import Link from 'next/link';
import { dateBadge, formatDateRange, formatStart, venueLine, type ApexEvent } from '@/lib/events';
import fallbackImage from '../../public/images/hero-drift.jpg';

/** Event cards (WordPress: apex/upcoming-events, "cards" layout). */
export function EventCards({
  events,
  cta = 'Details & Tickets',
  emptyMessage,
  headingLevel = 3,
}: {
  events: ApexEvent[];
  cta?: string;
  emptyMessage?: string;
  /** Card title level: 3 under a section heading, 2 directly under the page title. */
  headingLevel?: 2 | 3;
}) {
  if (!events.length) return <EventsEmpty message={emptyMessage} />;
  const Heading = headingLevel === 2 ? 'h2' : 'h3';

  return (
    <div className="apex-events apex-events--cards">
      <ol className="apex-events__cards" role="list">
        {events.map((event, i) => {
          const badge = dateBadge(event.start, event.end);
          const where = venueLine(event);
          return (
            <li key={event.id} className="apex-event-card">
              <div className="apex-event-card__media" aria-hidden="true">
                {event.image ? (
                  <Image src={event.image.url} alt="" width={event.image.width} height={event.image.height} sizes="(max-width: 700px) 100vw, 50vw" />
                ) : (
                  // No featured image: the drift photo, as on the event page.
                  <Image src={fallbackImage} alt="" sizes="(max-width: 700px) 100vw, 50vw" placeholder="blur" />
                )}
                <span className="apex-event-card__num">{String(i + 1).padStart(2, '0')}</span>
              </div>
              <div className="apex-event-card__body">
                <p className="apex-event-card__date">
                  <span className="apex-event-card__day">{badge.day}</span>
                  <span className="apex-event-card__month">{badge.month}</span>
                </p>
                <Heading className="apex-event-card__title">
                  <Link href={event.path}>{event.title}</Link>
                </Heading>
                {where ? <p className="apex-event-card__where">{where}</p> : null}
                <p className="apex-event-card__when">{formatStart(event)}</p>
                <p className="apex-event-card__foot">
                  {event.cost ? <span className="apex-event-card__cost">{event.cost}</span> : null}
                  <span className="apex-event-card__cta" aria-hidden="true">
                    {cta} <span className="apex-arrow">→</span>
                  </span>
                </p>
              </div>
              <span className="screen-reader-text">{formatDateRange(event.start, event.end, true)}</span>
            </li>
          );
        })}
      </ol>
    </div>
  );
}

/** Timing-sheet list (WordPress: apex/upcoming-events, "list" layout). */
export function EventRows({ events, emptyMessage }: { events: ApexEvent[]; emptyMessage?: string }) {
  if (!events.length) return <EventsEmpty message={emptyMessage} />;

  return (
    <div className="apex-events apex-events--list">
      <ol className="apex-events__rows" role="list">
        {events.map((event, i) => (
          <li key={event.id} className="apex-event-row">
            <span className="apex-event-row__pos" aria-hidden="true">
              P{i + 1}
            </span>
            <span className="apex-event-row__date">{formatDateRange(event.start, event.end, true)}</span>
            <span className="apex-event-row__main">
              <Link className="apex-event-row__title" href={event.path}>
                {event.title}
              </Link>
              {venueLine(event) ? <span className="apex-event-row__venue">{venueLine(event)}</span> : null}
            </span>
            {event.cost ? <span className="apex-event-row__cost">{event.cost}</span> : <span />}
            <span className="apex-event-row__go" aria-hidden="true">
              →
            </span>
          </li>
        ))}
      </ol>
    </div>
  );
}

export function EventsEmpty({ message }: { message?: string }) {
  return (
    <div className="apex-events is-empty">
      <div className="apex-events__empty">
        <p className="apex-events__empty-flag">Pit lane closed</p>
        <p className="apex-events__empty-text">
          {message ?? "The next season's schedule drops soon. Follow @apex_idaho for the announcement."}
        </p>
      </div>
    </div>
  );
}
