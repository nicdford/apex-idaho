import type { Metadata } from 'next';
import type { CSSProperties } from 'react';
import { Button, Buttons } from '@/components/Button';
import { Eyebrow } from '@/components/Eyebrow';
import { PageHero } from '@/components/PageHero';
import { PersonCard } from '@/components/PersonCard';
import { focus, intro, mission, team } from '@/content/about';
import { closing } from '@/content/home';

export const metadata: Metadata = {
  title: 'About',
  description: 'APEX Idaho has been drifting since 2007: grassroots drift comps, novice driver development and grip / time attack. Meet the team.',
};

export default function AboutPage() {
  return (
    <>
      <PageHero eyebrow={intro.eyebrow} title={<>Ab<strong>out</strong></>} lede={intro.lede} into="asphalt" />

      <section className="apex-section has-asphalt-bg">
        <div className="apex-wrap apex-stack apex-stack--lg">
          <div className="apex-split" style={{ alignItems: 'start' }}>
            <div className="apex-stack apex-stack--sm">
              <Eyebrow>{mission.eyebrow}</Eyebrow>
              <h2 className="wp-block-heading">{mission.title}</h2>
            </div>
            <div className="apex-stack">
              {mission.body.map((p) => (
                <p key={p.slice(0, 24)} className="has-large-font-size">
                  {p}
                </p>
              ))}
            </div>
          </div>
          <ul className="apex-grid" role="list" style={{ listStyle: 'none', margin: 0, padding: 0 }}>
            {focus.map((item, i) => (
              <li key={item} className="apex-panel apex-card apex-path">
                <p className="apex-index" aria-hidden="true">
                  {String(i + 1).padStart(2, '0')}
                </p>
                <h3 className="wp-block-heading has-xx-large-font-size">{item}</h3>
              </li>
            ))}
          </ul>
        </div>
      </section>

      <section className="apex-section has-carbon-bg">
        <div className="apex-wrap apex-stack apex-stack--lg">
          <div className="apex-stack apex-stack--sm">
            <Eyebrow>The crew</Eyebrow>
            <h2 className="wp-block-heading">
              Meet the <strong>team</strong>
            </h2>
          </div>
          <div className="apex-grid" style={{ '--apex-min': '15rem' } as CSSProperties}>
            {team.map((person) => (
              <PersonCard key={person.name} person={person} />
            ))}
          </div>
        </div>
      </section>

      <section className="apex-section apex-texture-night apex-stripe-top has-carbon-bg">
        <div className="apex-wrap apex-stack apex-center">
          <Eyebrow center>{closing.eyebrow}</Eyebrow>
          <h2 className="wp-block-heading has-text-align-center has-display-font-size">
            {closing.titleTop} <strong>{closing.titleAccent}</strong>
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
