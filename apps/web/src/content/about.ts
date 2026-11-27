/**
 * About page. Static content: edit here and deploy. Team photos live in
 * public/images/team; people without one show their initials.
 */
import type { Person } from '@/components/PersonCard';
import anthony from '../../public/images/team/anthony-barnes.jpg';
import jordan from '../../public/images/team/jordan-vandiest.jpg';
import melissa from '../../public/images/team/melissa-miles.jpg';
import nic from '../../public/images/team/nic-ford.jpg';

export const intro = {
  eyebrow: 'Drifting since 2007',
  lede: 'Developing drivers, cars, tracks, scenes and more.',
};

export const mission = {
  eyebrow: 'Our skills & expertise',
  title: 'Built by drivers, for drivers',
  body: [
    'APEX Idaho has been drifting since 2007. Developing drivers, cars, tracks, scenes and more. We’re dedicated to designing and hosting premier events that showcase the world of drifting and that deeply engage with and contribute to the local and greater motorsport communities.',
    'Our mission is to elevate the experience of each attendee, participant, and sponsor, by creating unforgettable experiences that resonate with the passion and excitement inherent in motorsports culture.',
  ],
};

/** What we put on track. */
export const focus = ['Grassroots drift comps', 'Novice driver development', 'Grip / time attack'];

export const team: Person[] = [
  {
    name: 'Anthony Barnes',
    kicker: 'Head Beard',
    initials: 'AB',
    photo: anthony,
    links: [
      { label: 'Facebook', href: 'https://www.facebook.com/sevensixracing' },
      { label: 'Instagram', href: 'https://www.instagram.com/sevensixusa/' },
    ],
  },
  {
    name: 'Melissa Miles',
    kicker: 'Event Director',
    initials: 'MM',
    photo: melissa,
    links: [{ label: 'Facebook', href: 'https://www.facebook.com/melissa.pullin.9/' }],
  },
  {
    name: 'Danny Wyatt',
    kicker: 'Track Team / Flagger',
    initials: 'DW',
    links: [{ label: 'Facebook', href: 'https://www.facebook.com/daniel.wyatt.9469' }],
  },
  {
    name: 'Jay Caiola',
    kicker: 'Track Team / Tech',
    initials: 'JC',
    links: [{ label: 'Facebook', href: 'https://www.facebook.com/jay.caiola' }],
  },
  {
    name: 'Nic Ford',
    kicker: 'Website / Rebar',
    initials: 'NF',
    photo: nic,
    links: [
      { label: 'Facebook', href: 'https://www.facebook.com/nicdford' },
      { label: 'Instagram', href: 'https://www.instagram.com/nicdford' },
    ],
  },
  {
    name: 'Jordan VanDiest',
    kicker: 'Media / Social Media',
    initials: 'JV',
    photo: jordan,
    links: [{ label: 'Facebook', href: 'https://www.facebook.com/jordan.vandiest.5' }],
  },
];
