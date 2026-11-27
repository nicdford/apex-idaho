import type { Metadata } from 'next';
import Image from 'next/image';
import { Button, Buttons } from '@/components/Button';
import { Eyebrow } from '@/components/Eyebrow';
import { PageHero } from '@/components/PageHero';
import { applicationLink, contact, mediaPackageLink, partnerGroups, rules, type Partner } from '@/content/media';
import { getMediaPartnerPhotos, type MediaPartnerPhoto } from '@/lib/wp';

export const metadata: Metadata = {
  title: 'Media',
  description: 'Official APEX Idaho media partners, the media package for drivers, and how to apply for media credentials.',
};

function PartnerCard({ partner, photo }: { partner: Partner; photo?: MediaPartnerPhoto }) {
  return (
    <article className="apex-partner apex-card is-style-section-panel" style={{ padding: 0, display: 'flex', flexDirection: 'column' }}>
      {photo?.photo ? (
        <figure className="apex-partner-photo">
          <Image src={photo.photo.url} alt={partner.name} width={photo.photo.width} height={photo.photo.height} sizes="(max-width: 700px) 100vw, 25vw" style={{ objectPosition: photo.focal }} />
        </figure>
      ) : (
        <figure className="apex-partner-photo is-placeholder">
          <span className="apex-partner-photo__initials" aria-hidden="true">
            {partner.initials}
          </span>
        </figure>
      )}
      <div className="apex-stack apex-stack--sm" style={{ padding: 'var(--wp--preset--spacing--30) var(--wp--preset--spacing--40) var(--wp--preset--spacing--40)' }}>
        <p className="is-style-apex-eyebrow has-x-small-font-size">{partner.specialty}</p>
        <h3 className="wp-block-heading has-xx-large-font-size">{partner.name}</h3>
        <p className="has-smoke-color">{partner.business}</p>
        <ul className="apex-partner__links" role="list">
          {partner.links.map((link) => (
            <li key={link.label}>{link.href ? <a href={link.href}>{link.label}</a> : link.label}</li>
          ))}
        </ul>
      </div>
    </article>
  );
}

export default async function MediaPage() {
  const photos = await getMediaPartnerPhotos();

  return (
    <>
      <PageHero eyebrow="APEX Idaho" title={<>Me<strong>dia</strong></>} lede="Come capture the magic that is APEX Idaho events.">
        <Buttons>
          <Button href="#partners">Media partners</Button>
          <Button href="#become" variant="outline">
            Become a media partner
          </Button>
        </Buttons>
      </PageHero>

      <section className="is-style-section-red" style={{ paddingBlock: 'var(--wp--preset--spacing--50)' }}>
        <div className="apex-wrap apex-row apex-row--center">
          <div className="apex-stack apex-stack--sm" style={{ flex: '1 1 min(100%, 680px)' }}>
            <Eyebrow>For drivers</Eyebrow>
            <h2 className="wp-block-heading has-xx-large-font-size">Want pro shots of your car on track?</h2>
            <p>Book the APEX Idaho Media Package and get your run captured by our team — high-quality photos delivered after the event.</p>
          </div>
          <Buttons>
            <Button href={mediaPackageLink}>Book media package</Button>
          </Buttons>
        </div>
      </section>

      <section className="has-asphalt-bg" style={{ paddingBlock: 'var(--wp--preset--spacing--50)' }}>
        <div className="apex-wrap apex-row apex-row--center">
          <div className="apex-stack apex-stack--sm">
            <h2 className="wp-block-heading has-xx-large-font-size">
              Have <strong>questions?</strong>
            </h2>
            <p className="has-large-font-size" style={{ fontWeight: 700 }}>
              {contact.name}
            </p>
            <p className="has-smoke-color">
              {contact.business} · <a href={contact.instagram.href}>{contact.instagram.label}</a>
            </p>
          </div>
          <Buttons>
            <Button href={contact.email}>Email</Button>
            <Button href={contact.website} variant="outline">
              Website
            </Button>
          </Buttons>
        </div>
      </section>

      <section id="partners" className="apex-section has-carbon-bg">
        <div className="apex-wrap apex-stack apex-stack--lg">
          {partnerGroups.map((group, i) => (
            <div key={group.label} className="apex-stack apex-stack--lg">
              {i === 0 ? (
                <div className="apex-stack apex-stack--sm">
                  <Eyebrow>{group.label}</Eyebrow>
                  <h2 className="wp-block-heading">
                    Media <strong>partners</strong>
                  </h2>
                </div>
              ) : (
                <div className="apex-row apex-row--center" style={{ flexWrap: 'nowrap' }}>
                  <Eyebrow>{group.label}</Eyebrow>
                  <hr className="wp-block-separator is-style-apex-chevrons" style={{ flex: 1 }} />
                </div>
              )}
              <div className="apex-grid">
                {group.partners.map((p) => (
                  <PartnerCard key={p.slug} partner={p} photo={photos[p.slug]} />
                ))}
              </div>
            </div>
          ))}
        </div>
      </section>

      <section id="become" className="apex-section is-style-section-paper">
        <div className="apex-wrap apex-stack apex-stack--lg">
          <div className="apex-stack apex-stack--sm">
            <Eyebrow>Join the team</Eyebrow>
            <h2 className="wp-block-heading">Become a media partner</h2>
            <p className="has-large-font-size">Media rules &amp; requirements</p>
          </div>
          <div className="apex-split">
            {rules.map((block) => (
              <div key={block.title} className="apex-stack has-white-background-color" style={{ padding: 'var(--wp--preset--spacing--40)', borderTop: '5px solid var(--wp--preset--color--red)' }}>
                <h3 className="wp-block-heading has-x-large-font-size">{block.title}</h3>
                <ul className="wp-block-list is-style-apex-diamonds">
                  {block.items.map((item) => (
                    <li key={item}>{item}</li>
                  ))}
                </ul>
              </div>
            ))}
          </div>
          <p className="has-text-align-center has-small-font-size" style={{ fontStyle: 'italic' }}>
            APEX Idaho reserves the right to revoke any media privileges at any time, for any reason.
          </p>
        </div>
      </section>

      <section className="apex-section apex-texture-night apex-stripe-top has-carbon-bg">
        <div className="apex-wrap apex-stack apex-center">
          <h2 className="wp-block-heading has-text-align-center has-display-font-size">
            Ready to <strong>apply?</strong>
          </h2>
          <p className="is-style-apex-lede has-text-align-center">Fill out the media application, we&rsquo;ll get back to you, and you&rsquo;ll be ready to capture photos and video at APEX Idaho events.</p>
          <Buttons>
            <Button href={applicationLink}>Apply now</Button>
          </Buttons>
        </div>
      </section>
    </>
  );
}
