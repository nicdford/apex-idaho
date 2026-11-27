import Image from 'next/image';
import Link from 'next/link';
import { CART_URL, KBD_STANDINGS_URL, PRIMARY_NAV, SITE_NAME } from '@/lib/config';
import { formatDateRange, getUpcomingEvents } from '@/lib/events';
import logo from '../../public/images/apex-idaho-logo.png';
import { Button, Buttons } from './Button';
import { MainNav } from './MainNav';
import { Marquee } from './Marquee';
import { SmartLink } from './SmartLink';

export async function SiteHeader() {
  const events = await getUpcomingEvents(4);

  const ticker = [
    ...(events.length
      ? events.map((event, i) => (
          <span key={event.id} className="apex-ticker-item">
            <Link href={event.path}>
              <strong>{i === 0 ? 'Next up' : formatDateRange(event.start, event.end)}</strong> {event.title}
              <span className="apex-ticker-item__meta">
                {i === 0 ? formatDateRange(event.start, event.end) : ''}
                {event.venue ? ` · ${event.venue.name}` : ''}
              </span>
            </Link>
          </span>
        ))
      : [<span key="soon">Next season&rsquo;s schedule drops soon</span>]),
    <SmartLink key="kbd" href={KBD_STANDINGS_URL}>
      KBD Comp Series standings
    </SmartLink>,
    <span key="tag">Drift · Grip · All skill levels welcome</span>,
  ];

  return (
    <header className="apex-site-header">
      <div className="apex-header">
        <div className="apex-header__ticker">
          <Marquee items={ticker} slashes duration={60} gap="1.5rem" label={events.length ? 'Upcoming events' : undefined} />
        </div>
        <div className="apex-wrap apex-header__bar">
          <Link href="/" className="apex-header__logo" aria-label={`${SITE_NAME} home`}>
            <Image src={logo} alt={SITE_NAME} priority sizes="172px" />
          </Link>
          <div className="apex-header__right">
            <MainNav items={PRIMARY_NAV} />
            <div className="apex-cart-link">
              <a className="apex-cart-link__a" href={CART_URL}>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="square" aria-hidden="true" focusable="false">
                  <path d="M3 4h3l2.4 11.2a1 1 0 0 0 1 .8h8.9a1 1 0 0 0 1-.8L21 8H7" />
                  <circle cx="10" cy="20" r="1.4" />
                  <circle cx="18" cy="20" r="1.4" />
                </svg>
                <span className="apex-cart-link__label">Cart</span>
              </a>
            </div>
            <Buttons className="apex-header__cta">
              <Button href="/events" variant="plain">
                Get Tickets
              </Button>
            </Buttons>
          </div>
        </div>
      </div>
    </header>
  );
}
