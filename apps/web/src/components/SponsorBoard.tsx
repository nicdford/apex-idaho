import type { CSSProperties } from 'react';
import type { Sponsor } from '@/lib/wp';
import { Marquee } from './Marquee';

/**
 * Sponsor logos on a white board (WordPress: apex/sponsors). White on purpose:
 * each sponsor's "convert to black" / "invert" setting was tuned on white.
 */
function Logo({ sponsor, decorative = false, showName = false }: { sponsor: Sponsor; decorative?: boolean; showName?: boolean }) {
  const style = sponsor.filter ? ({ '--apex-logo-filter': sponsor.filter } as CSSProperties) : undefined;
  const img = (
    // eslint-disable-next-line @next/next/no-img-element -- sponsor logos are small, already sized by WordPress, and run in a marquee.
    <img src={sponsor.logo.url} alt={decorative || showName ? '' : sponsor.name} width={sponsor.logo.width} height={sponsor.logo.height} loading="lazy" decoding="async" style={style} />
  );
  const inner = (
    <>
      {img}
      {showName ? <span className="apex-sponsor__name">{sponsor.name}</span> : null}
    </>
  );
  return sponsor.url ? (
    <a href={sponsor.url} target="_blank" rel="noopener" tabIndex={decorative ? -1 : undefined}>
      {inner}
    </a>
  ) : (
    <span className="apex-sponsor__inner">{inner}</span>
  );
}

const itemClass = (s: Sponsor) => `apex-sponsor${s.invert ? ' is-inverted' : ''}${s.filter ? ' has-filter' : ''}`;

export function SponsorMarquee({ sponsors }: { sponsors: Sponsor[] }) {
  if (!sponsors.length) return null;
  return (
    <div className="apex-sponsors apex-sponsors--marquee">
      <div className="apex-sponsors__viewport">
        <Marquee
          label="Sponsors"
          duration={Math.max(24, sponsors.length * 4)}
          minItems={6}
          gap="clamp(2.5rem, 5vw, 4.5rem)"
          items={sponsors.map((s) => (
            <span key={s.id} className={itemClass(s)} style={{ display: 'flex' }}>
              <Logo sponsor={s} />
            </span>
          ))}
        />
      </div>
    </div>
  );
}

export function SponsorGrid({ sponsors, showNames = true }: { sponsors: Sponsor[]; showNames?: boolean }) {
  if (!sponsors.length) {
    return (
      <div className="apex-sponsors is-empty">
        <p>Sponsor logos appear here once they&rsquo;re added under Sponsors in WordPress.</p>
      </div>
    );
  }
  return (
    <div className="apex-sponsors apex-sponsors--grid">
      <ul className="apex-sponsors__grid" role="list">
        {sponsors.map((s) => (
          <li key={s.id} className={itemClass(s)}>
            <Logo sponsor={s} showName={showNames} />
          </li>
        ))}
      </ul>
    </div>
  );
}
