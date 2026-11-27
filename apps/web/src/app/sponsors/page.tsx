import type { Metadata } from 'next';
import { Button, Buttons } from '@/components/Button';
import { PageHero } from '@/components/PageHero';
import { SponsorGrid } from '@/components/SponsorBoard';
import { getSponsors } from '@/lib/wp';

export const metadata: Metadata = {
  title: 'Sponsors',
  description: "APEX Idaho wouldn't be possible without the support of these partners.",
};

export default async function SponsorsPage() {
  const sponsors = await getSponsors();

  return (
    <>
      <PageHero eyebrow="Our partners" title={<>Spon<strong>sors</strong></>} lede="APEX Idaho wouldn't be possible without the support of these incredible partners." />

      <section className="apex-section has-carbon-bg" style={{ paddingTop: 'var(--wp--preset--spacing--60)' }}>
        <div className="apex-wrap">
          <SponsorGrid sponsors={sponsors} />
        </div>
      </section>

      <section className="apex-section apex-texture-speed is-style-section-red" style={{ paddingBlock: 'var(--wp--preset--spacing--70)' }}>
        <div className="apex-wrap apex-stack apex-center">
          <h2 className="wp-block-heading has-text-align-center has-display-font-size">
            Put your name
            <br />
            on the wall.
          </h2>
          <p className="has-text-align-center has-large-font-size">Partner with APEX Idaho and reach the motorsport community across the Treasure Valley and beyond.</p>
          <Buttons>
            <Button href="/about">Get in touch</Button>
          </Buttons>
        </div>
      </section>
    </>
  );
}
