import Image from 'next/image';
import { Button, Buttons } from '@/components/Button';
import { Countdown } from '@/components/Countdown';
import { EventCards } from '@/components/EventCards';
import { Eyebrow } from '@/components/Eyebrow';
import { Marquee } from '@/components/Marquee';
import { SmartLink } from '@/components/SmartLink';
import { closing, disciplines, hero, mediaPackage, paths, series } from '@/content/home';
import { formatDateRange, getUpcomingEvents, nextToStart } from '@/lib/events';
import heroImage from '../../public/images/hero-drift.jpg';

export default async function HomePage() {
  const events = await getUpcomingEvents(3);
  const next = nextToStart(events);

  return (
    <>
      {/* Hero */}
      <section className="apex-hero apex-cut-bottom">
        <Image src={heroImage} alt="" className="apex-hero__media" fill priority sizes="100vw" style={{ objectPosition: hero.image.focal }} placeholder="blur" />
        <div className="apex-hero__shade" aria-hidden="true" />
        <div className="apex-wrap apex-hero__inner">
          <div className="apex-stack apex-stack--lg">
            <Eyebrow>{hero.eyebrow}</Eyebrow>
            <h1 className="wp-block-heading">
              {hero.titleTop}
              <br />
              <strong>{hero.titleAccent}</strong>
            </h1>
            <p className="is-style-apex-lede">{hero.lede}</p>
            <Buttons>
              <Button href={hero.primary.href}>{hero.primary.label}</Button>
              <Button href={hero.secondary.href} variant="outline">
                {hero.secondary.label}
              </Button>
            </Buttons>
          </div>
          <div>
            {next?.startsAt ? (
              <Countdown target={next.startsAt} title={next.title} href={next.path} meta={[formatDateRange(next.start, next.end), next.venue?.name].filter(Boolean).join(' · ')} />
            ) : null}
          </div>
        </div>
      </section>

      {/* Scrolling disciplines */}
      <div className="apex-band-ticker has-red-background-color has-white-color" style={{ paddingBlock: '1.1rem', fontSize: 'clamp(2.25rem, 5vw, 3.75rem)' }} aria-hidden="true">
        <Marquee items={disciplines.map((d) => d)} gap="2.25rem" slashes duration={40} minItems={7} />
      </div>

      {/* Upcoming events */}
      <section className="apex-section apex-texture-speed has-carbon-bg">
        <div className="apex-wrap apex-stack apex-stack--lg">
          <div className="apex-row">
            <div className="apex-stack apex-stack--sm">
              <Eyebrow>On the calendar</Eyebrow>
              <h2 className="wp-block-heading">
                Upcoming <strong>events</strong>
              </h2>
            </div>
            <Buttons>
              <Button href="/events" variant="outline">
                Full calendar
              </Button>
            </Buttons>
          </div>
          <EventCards events={events} />
        </div>
      </section>

      {/* Pick your line */}
      <section className="apex-section has-asphalt-bg">
        <div className="apex-wrap apex-stack apex-stack--lg">
          <div className="apex-stack apex-stack--sm">
            <Eyebrow>Get involved</Eyebrow>
            <h2 className="wp-block-heading">
              Pick your <strong>line.</strong>
            </h2>
          </div>
          <div className="apex-grid">
            {paths.map((p) => (
              <div key={p.index} className="apex-panel apex-card apex-path">
                <p className="apex-index" aria-hidden="true">
                  {p.index}
                </p>
                <h3 className="wp-block-heading has-huge-font-size">{p.title}</h3>
                <p className="has-smoke-color">{p.body}</p>
                <p className="apex-card__link apex-link-arrow">
                  <SmartLink href={p.link.href}>{p.link.label}</SmartLink>
                </p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Comp series + media package */}
      <section className="apex-section has-carbon-bg" style={{ paddingBlock: 'var(--wp--preset--spacing--70)' }}>
        <div className="apex-wrap apex-split">
          <div className="apex-promo is-style-section-red">
            <Eyebrow>{series.eyebrow}</Eyebrow>
            <h2 className="wp-block-heading has-huge-font-size">{series.title}</h2>
            <p>{series.body}</p>
            <Buttons>
              <Button href={series.link.href}>{series.link.label}</Button>
            </Buttons>
          </div>
          <div className="apex-promo is-style-section-panel apex-texture-carbon">
            <Eyebrow>{mediaPackage.eyebrow}</Eyebrow>
            <h2 className="wp-block-heading has-huge-font-size">
              Pro shots of <strong>your car</strong> on track
            </h2>
            <p className="has-smoke-color">{mediaPackage.body}</p>
            <Buttons>
              <Button href={mediaPackage.link.href}>{mediaPackage.link.label}</Button>
            </Buttons>
          </div>
        </div>
      </section>

      {/* Closing */}
      <section className="apex-section apex-texture-night apex-stripe-top has-carbon-bg">
        <div className="apex-wrap apex-stack apex-center">
          <Eyebrow center>{closing.eyebrow}</Eyebrow>
          <h2 className="wp-block-heading has-text-align-center has-mega-font-size">
            {closing.titleTop}
            <br />
            <strong>{closing.titleAccent}</strong>
          </h2>
          <Buttons>
            <Button href={closing.primary.href}>{closing.primary.label}</Button>
            <Button href={closing.secondary.href} variant="outline">
              {closing.secondary.label}
            </Button>
          </Buttons>
        </div>
      </section>
    </>
  );
}
