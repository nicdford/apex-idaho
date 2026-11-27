import type { CSSProperties, ReactNode } from 'react';

type Props = {
  items: ReactNode[];
  className?: string;
  /** Seconds per loop. */
  duration?: number;
  /** Repeat the items until a half holds at least this many, so wide screens never show a gap. */
  minItems?: number;
  slashes?: boolean;
  label?: string;
  /** Space between items (CSS length). */
  gap?: string;
};

/**
 * Seamless scrolling strip with no JavaScript: the items render twice and the
 * track slides by exactly half. The copy is hidden from assistive tech, and
 * reduced-motion users get a static row.
 */
export function Marquee({ items, className = '', duration = 45, minItems = 8, slashes = false, label, gap }: Props) {
  if (!items.length) return null;
  const repeat = Math.max(1, Math.ceil(minItems / items.length));
  const half = Array.from({ length: repeat }, (_, r) => items.map((item, i) => ({ item, key: `${r}-${i}`, copy: r > 0 })))
    .flat();

  const style = { '--apex-marquee-duration': `${duration}s`, ...(gap ? { '--apex-marquee-gap': gap } : {}) } as CSSProperties;

  return (
    <div className={`apex-marquee${slashes ? ' apex-marquee--slashes' : ''} ${className}`.trim()} style={style} aria-label={label} role={label ? 'region' : undefined}>
      <div className="apex-marquee__track">
        {[0, 1].map((pass) => (
          <div key={pass} className="apex-marquee__half" aria-hidden={pass === 1 ? true : undefined} inert={pass === 1 ? true : undefined}>
            {half.map(({ item, key, copy }) => (
              <div key={key} className="apex-marquee__item" aria-hidden={copy && pass === 0 ? true : undefined} inert={copy && pass === 0 ? true : undefined}>
                {item}
              </div>
            ))}
          </div>
        ))}
      </div>
    </div>
  );
}
