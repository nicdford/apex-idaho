import type { ReactNode } from 'react';
import { Eyebrow } from './Eyebrow';

/** Dark title band with an angled bottom edge (WordPress: hidden-page-hero / page band pattern). */
export function PageHero({ eyebrow, title, lede, children, into }: { eyebrow?: string; title: ReactNode; lede?: string; children?: ReactNode; into?: 'asphalt' | 'paper' }) {
  return (
    <section className={`apex-page-hero apex-texture-night apex-cut-bottom has-carbon-bg${into ? ` apex-into-${into}` : ''}`} style={{ paddingBlock: 'var(--wp--preset--spacing--70) calc(var(--wp--preset--spacing--70) + 2.5rem)' }}>
      <div className="apex-wrap apex-stack">
        {eyebrow ? <Eyebrow>{eyebrow}</Eyebrow> : null}
        <h1 className="wp-block-heading">{title}</h1>
        {lede ? <p className="is-style-apex-lede">{lede}</p> : null}
        {children}
      </div>
    </section>
  );
}
