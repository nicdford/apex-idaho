import Image, { type StaticImageData } from 'next/image';

export type PersonLink = { label: string; href?: string };

export type Person = {
  name: string;
  /** Small red label above the name (role or specialty). */
  kicker: string;
  /** Line under the name (business, crew, …). */
  detail?: string;
  initials: string;
  photo?: StaticImageData;
  /** CSS object-position for the photo crop. */
  focal?: string;
  links: PersonLink[];
};

/** Photo card for crew and media partners (theme: .apex-partner). */
export function PersonCard({ person }: { person: Person }) {
  return (
    <article className="apex-partner apex-card is-style-section-panel" style={{ padding: 0, display: 'flex', flexDirection: 'column' }}>
      {person.photo ? (
        <figure className="apex-partner-photo">
          <Image src={person.photo} alt={person.name} sizes="(max-width: 700px) 100vw, 25vw" placeholder="blur" style={{ objectPosition: person.focal }} />
        </figure>
      ) : (
        <figure className="apex-partner-photo is-placeholder">
          <span className="apex-partner-photo__initials" aria-hidden="true">
            {person.initials}
          </span>
        </figure>
      )}
      <div className="apex-stack apex-stack--sm" style={{ padding: 'var(--wp--preset--spacing--30) var(--wp--preset--spacing--40) var(--wp--preset--spacing--40)' }}>
        <p className="is-style-apex-eyebrow has-x-small-font-size">{person.kicker}</p>
        <h3 className="wp-block-heading has-xx-large-font-size">{person.name}</h3>
        {person.detail ? <p className="has-smoke-color">{person.detail}</p> : null}
        {person.links.length ? (
          <ul className="apex-partner__links" role="list">
            {person.links.map((link) => (
              <li key={link.label}>{link.href ? <a href={link.href}>{link.label}</a> : link.label}</li>
            ))}
          </ul>
        ) : null}
      </div>
    </article>
  );
}
