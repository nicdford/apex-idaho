/**
 * Homepage copy. Mirrors the block theme's patterns (hero-home, band-ticker,
 * paths-three, split-series-media, cta-line). Events and the countdown come
 * from WordPress; everything here is editable text.
 */
import { KBD_STANDINGS_URL, WORDPRESS_PUBLIC_URL } from '@/lib/config';

export const hero = {
  eyebrow: 'Drift · Grip · Comp Series',
  titleTop: 'Idaho drives',
  titleAccent: 'sideways.',
  lede: 'Drift, grip and full-throttle competition across the Gem State, from Meridian Speedway to Magic Valley. Every skill level on track, spectators welcome, and a paddock that knows how to party.',
  primary: { label: 'See the schedule', href: '/events' },
  secondary: { label: 'Shop & tickets', href: `${WORDPRESS_PUBLIC_URL}/shop/` },
  image: { src: '/images/hero-drift.jpg', alt: 'Two drift cars in tandem at Magic Valley Speedway', focal: '55% 50%' },
};

export const disciplines = ['Drift', 'Grip', 'Tandems', 'Comp Series', 'Night Sessions', 'Camping', 'Driver Parties'];

export const paths = [
  {
    index: '01',
    title: 'Drive',
    body: 'Grab a driver entry, pass tech and get on track. Paved pits, camping and driver parties come with our multi-day events.',
    link: { label: 'Driver entry', href: '/events' },
  },
  {
    index: '02',
    title: 'Watch',
    body: 'Spectator tickets put you trackside for tandems, comp runs and the paddock. Bring the crew — it is a weekend, not an afternoon.',
    link: { label: 'Spectator tickets', href: '/events' },
  },
  {
    index: '03',
    title: 'Shoot',
    body: 'Photographers and videographers: apply for media credentials and shoot from the inside. Pink vest, trackside access.',
    link: { label: 'Media program', href: '/media' },
  },
];

export const series = {
  eyebrow: 'Points on the line',
  title: 'KBD Comp Series',
  body: 'Hot laps, comp layouts and fields capped at sixteen drivers. Every round counts — follow the standings as the season plays out.',
  link: { label: 'View standings', href: KBD_STANDINGS_URL },
};

export const mediaPackage = {
  eyebrow: 'For drivers',
  title: 'Pro shots of your car on track',
  body: 'Book the APEX Idaho Media Package and our media team captures your runs. High-quality photos, delivered after the event.',
  link: { label: 'Book media package', href: `${WORDPRESS_PUBLIC_URL}/product/media-package/` },
};

export const closing = {
  eyebrow: 'Your move',
  titleTop: 'See you at',
  titleAccent: 'the line.',
  primary: { label: 'Get tickets', href: '/events' },
  secondary: { label: 'Tech & rules', href: '/tech-rules' },
};
