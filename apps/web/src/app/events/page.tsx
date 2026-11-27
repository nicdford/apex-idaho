import type { Metadata } from 'next';
import { EventCards, EventRows } from '@/components/EventCards';
import { Eyebrow } from '@/components/Eyebrow';
import { PageHero } from '@/components/PageHero';
import { getPastEvents, getUpcomingEvents } from '@/lib/events';

export const metadata: Metadata = {
  title: 'Events',
  description: 'Drift, grip and comp series events from APEX Idaho: dates, venues and tickets.',
};

export default async function EventsPage() {
  const [upcoming, past] = await Promise.all([getUpcomingEvents(12), getPastEvents(12)]);

  return (
    <>
      <PageHero eyebrow="On the calendar" title={<>Upcoming <strong>events</strong></>} lede="Driver entries and spectator tickets for every APEX Idaho event, from comp rounds to full festival weekends." />

      <section className="apex-section has-carbon-bg" style={{ paddingTop: 'var(--wp--preset--spacing--60)' }}>
        <div className="apex-wrap apex-stack apex-stack--lg">
          <EventCards events={upcoming} headingLevel={2} />
        </div>
      </section>

      {past.length ? (
        <section className="apex-section apex-texture-grid has-asphalt-bg">
          <div className="apex-wrap apex-stack apex-stack--lg">
            <div className="apex-stack apex-stack--sm">
              <Eyebrow>Timing sheet</Eyebrow>
              <h2 className="wp-block-heading">
                Recent <strong>events</strong>
              </h2>
            </div>
            <EventRows events={past} />
          </div>
        </section>
      ) : null}
    </>
  );
}
