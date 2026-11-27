import Image from 'next/image';
import { DEFAULT_SOCIAL, FOOTER_NAV } from '@/lib/config';
import { getSite, getSponsors } from '@/lib/wp';
import localLogo from '../../public/images/apex-idaho-logo.png';
import { Eyebrow } from './Eyebrow';
import { SmartLink } from './SmartLink';
import { SocialLinks } from './SocialLinks';
import { SponsorMarquee } from './SponsorBoard';

export async function SiteFooter() {
  const [site, sponsors] = await Promise.all([getSite(), getSponsors()]);
  const social = { ...DEFAULT_SOCIAL, ...site.social };

  return (
    <footer className="apex-footer">
      {sponsors.length ? (
        <div className="apex-sponsor-strip has-white-background-color has-carbon-color" style={{ paddingTop: 'var(--wp--preset--spacing--40)' }}>
          <Eyebrow center className="has-carbon-color">
            Powered by our partners
          </Eyebrow>
          <SponsorMarquee sponsors={sponsors} />
        </div>
      ) : null}

      <div className="apex-footer__main apex-texture-grid has-carbon-bg" style={{ paddingBlock: 'var(--wp--preset--spacing--70) var(--wp--preset--spacing--80)' }}>
        <div className="apex-wrap apex-footer__grid">
          <div className="apex-footer__brand apex-stack">
            {site.logo ? (
              <Image src={site.logo.url} alt={site.name} width={site.logo.width} height={site.logo.height} sizes="220px" />
            ) : (
              <Image src={localLogo} alt={site.name} sizes="220px" />
            )}
            <p className="has-smoke-color">
              Drift, grip and full-throttle competition across Idaho. Drivers of every skill level, spectators welcome, and a paddock that sticks around after the track goes cold.
            </p>
            <SocialLinks links={social} />
          </div>
          {FOOTER_NAV.map((column) => (
            <div key={column.heading} className="apex-stack apex-stack--sm">
              <h2 className="has-small-font-size">{column.heading}</h2>
              <ul className="apex-footer__links" role="list">
                {column.links.map((link) => (
                  <li key={link.href}>
                    <SmartLink href={link.href}>{link.label}</SmartLink>
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>
      </div>

      <div className="apex-footer__bottom has-carbon-bg" style={{ paddingBlock: 'var(--wp--preset--spacing--30)' }}>
        <div className="apex-wrap">
          <p>
            © {new Date().getFullYear()} {site.name}. All rights reserved.
          </p>
          <p>Built in Idaho. Fueled by tire smoke.</p>
        </div>
      </div>
      <div className="apex-checker" aria-hidden="true" />
    </footer>
  );
}
