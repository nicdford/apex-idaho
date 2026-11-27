import Image from 'next/image';
import { FOOTER_NAV, SITE_NAME, SOCIAL } from '@/lib/config';
import { getSponsors } from '@/lib/wp';
import logo from '../../public/images/apex-idaho-logo.png';
import { Eyebrow } from './Eyebrow';
import { SmartLink } from './SmartLink';
import { SocialLinks } from './SocialLinks';
import { SponsorMarquee } from './SponsorBoard';

export async function SiteFooter() {
  const sponsors = await getSponsors();

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
            <Image src={logo} alt={SITE_NAME} sizes="220px" />
            <p className="has-smoke-color">
              Drift, grip and full-throttle competition across Idaho. Drivers of every skill level, spectators welcome, and a paddock that sticks around after the track goes cold.
            </p>
            <SocialLinks links={SOCIAL} />
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
            © {new Date().getFullYear()} {SITE_NAME}. All rights reserved.
          </p>
          <p>Built in Idaho. Fueled by tire smoke.</p>
        </div>
      </div>
      <div className="apex-checker" aria-hidden="true" />
    </footer>
  );
}
