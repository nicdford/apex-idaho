import type { Metadata } from 'next';
import Image from 'next/image';
import { notFound } from 'next/navigation';
import { Button, Buttons } from '@/components/Button';
import { Countdown } from '@/components/Countdown';
import { EventCards } from '@/components/EventCards';
import { Eyebrow } from '@/components/Eyebrow';
import { WordPressContent } from '@/components/WordPressContent';
import { eventDescriptionHtml, formatDateRange, formatStart, getEvent, getPastEvents, getUpcomingEvents, hasNotStarted, isUpcoming, mapUrl, type ApexEvent } from '@/lib/events';
import heroImage from '../../../../public/images/hero-drift.jpg';

type Props = { params: Promise<{ slug: string }> };

export async function generateStaticParams() {
  const [upcoming, past] = await Promise.all([getUpcomingEvents(50), getPastEvents(12)]);
  return [...upcoming, ...past].map((e) => ({ slug: e.slug }));
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const event = await getEvent((await params).slug);
  if (!event) return {};
  const when = formatDateRange(event.start, event.end, true);
  return {
    title: event.title,
    description: [when, event.venue?.name, event.summary].filter(Boolean).join(' · '),
    openGraph: event.image ? { images: [{ url: event.image.url, width: event.image.width, height: event.image.height }] } : undefined,
  };
}

const gcal = (e: ApexEvent) => {
  if (!e.startsAt || !e.endsAt) return null;
  const stamp = (ms: number) => new Date(ms).toISOString().replace(/[-:]/g, '').replace(/\.\d{3}/, '');
  const params = new URLSearchParams({
    action: 'TEMPLATE',
    text: e.title,
    dates: `${stamp(e.startsAt)}/${stamp(e.endsAt)}`,
    location: [e.venue?.name, e.venue?.address, e.venue?.city, e.venue?.state].filter(Boolean).join(', '),
  });
  return `https://calendar.google.com/calendar/render?${params}`;
};

export default async function EventPage({ params }: Props) {
  const { slug } = await params;
  const event = await getEvent(slug);
  if (!event) notFound();

  const upcoming = isUpcoming(event);
  const others = (await getUpcomingEvents(4)).filter((e) => e.id !== event.id).slice(0, 3);
  const map = mapUrl(event);
  const calendar = gcal(event);

  return (
    <>
      <section className="apex-texture-night apex-cut-bottom apex-into-asphalt has-carbon-bg" style={{ paddingBlock: 'var(--wp--preset--spacing--70) calc(var(--wp--preset--spacing--70) + 3rem)' }}>
        <div className="apex-wrap apex-split-hero">
          <div className="apex-stack apex-stack--lg">
            <Eyebrow>{upcoming ? 'APEX Idaho presents' : 'Past event'}</Eyebrow>
            <h1 className="wp-block-heading" style={{ fontSize: 'var(--wp--preset--font-size--huge)' }}>
              {event.title}
            </h1>
            <dl className="apex-facts">
              <div>
                <dt>When</dt>
                <dd>{formatDateRange(event.start, event.end, true)}</dd>
              </div>
              {event.venue ? (
                <div>
                  <dt>Where</dt>
                  <dd>
                    {event.venue.name}
                    {event.venue.city ? `, ${event.venue.city}` : ''}
                  </dd>
                </div>
              ) : null}
              {event.cost ? (
                <div>
                  <dt>Entry</dt>
                  <dd>{event.cost}</dd>
                </div>
              ) : null}
            </dl>
            <p className="apex-meta">{formatStart(event)}</p>
            {upcoming ? (
              <Buttons>
                {event.ticketed || event.cost ? <Button href={event.ticketsUrl}>Get tickets</Button> : null}
                {calendar ? (
                  <Button href={calendar} variant="outline">
                    Add to calendar
                  </Button>
                ) : null}
                {map ? (
                  <Button href={map} variant="outline">
                    Map &amp; directions
                  </Button>
                ) : null}
              </Buttons>
            ) : null}
          </div>
          <div className="apex-split-hero__media apex-stack">
            {event.image ? (
              <Image src={event.image.url} alt="" width={event.image.width} height={event.image.height} sizes="(max-width: 900px) 100vw, 48vw" priority />
            ) : (
              <Image src={heroImage} alt="" sizes="(max-width: 900px) 100vw, 48vw" priority placeholder="blur" />
            )}
            {event.startsAt && hasNotStarted(event) ? <Countdown target={event.startsAt} label="Lights out in" /> : null}
          </div>
        </div>
      </section>

      <section className="apex-section has-asphalt-bg" style={{ paddingTop: 'var(--wp--preset--spacing--60)' }}>
        <div className="apex-wrap apex-wrap--narrow apex-stack apex-stack--lg">
          <Eyebrow>Event details</Eyebrow>
          <WordPressContent html={eventDescriptionHtml(event)} />
          {upcoming && (event.ticketed || event.cost) ? (
            <p className="has-smoke-color">
              Tickets, driver entries and any extras are sold on the event&rsquo;s ticket page. You&rsquo;ll check out through the APEX Idaho store.
            </p>
          ) : null}
        </div>
      </section>

      {others.length ? (
        <section className="apex-section apex-texture-speed has-carbon-bg">
          <div className="apex-wrap apex-stack apex-stack--lg">
            <div className="apex-stack apex-stack--sm">
              <Eyebrow>Also on the calendar</Eyebrow>
              <h2 className="wp-block-heading">
                More <strong>events</strong>
              </h2>
            </div>
            <EventCards events={others} />
          </div>
        </section>
      ) : null}
    </>
  );
}
