/**
 * Media page copy and partner details (WordPress: patterns/page-media.php,
 * media-partners.php, media-rules.php). Photos and focal points come from the
 * Media Partner posts in WordPress, matched by slug.
 */
import { WORDPRESS_PUBLIC_URL } from '@/lib/config';

export type PartnerLink = { label: string; href?: string };
export type Partner = { slug: string; initials: string; specialty: string; name: string; business: string; links: PartnerLink[] };

export const partnerGroups: { label: string; partners: Partner[] }[] = [
  {
    label: 'Official',
    partners: [
      { slug: 'danielle-choutdara', initials: 'DC', specialty: 'Photography', name: 'Danielle Choutdara', business: 'DC Photography', links: [{ label: '@dc_photography444', href: 'https://www.instagram.com/dc_photography444' }, { label: 'daniellelea444@gmail.com', href: 'mailto:daniellelea444@gmail.com' }] },
      { slug: 'lyndon-hunter', initials: 'LH', specialty: 'Photography', name: 'Lyndon Hunter', business: 'LyndonNotFound', links: [{ label: '@lyndonnotfound', href: 'https://www.instagram.com/lyndonnotfound' }, { label: 'lyndonhunter404@gmail.com', href: 'mailto:lyndonhunter404@gmail.com' }, { label: 'lyndonnotfound.squarespace.com', href: 'https://lyndonnotfound.squarespace.com' }] },
      { slug: 'mathew-perez', initials: 'MP', specialty: 'Videography', name: 'Mathew Perez', business: 'Lucid Videography', links: [{ label: '@lucid_videography', href: 'https://www.instagram.com/lucid_videography' }, { label: 'perezhouse.mlp@gmail.com', href: 'mailto:perezhouse.mlp@gmail.com' }, { label: 'YouTube channel' }] },
      { slug: 'orion-martin', initials: 'OM', specialty: 'Photography', name: 'Orion Martin', business: 'Instant Crush', links: [{ label: '@instantcrush.photo', href: 'https://www.instagram.com/instantcrush.photo' }, { label: 'orionmartinmarketing@gmail.com', href: 'mailto:orionmartinmarketing@gmail.com' }, { label: 'instantcrushphoto.darkroom.com', href: 'https://instantcrushphoto.darkroom.com' }] },
      { slug: 'jordan-van-diest', initials: 'JV', specialty: 'Photography & Media', name: 'Jordan Van Diest', business: 'Platinum Media', links: [{ label: '@platinummediallc', href: 'https://www.instagram.com/platinummediallc' }, { label: 'platinummedia208@gmail.com', href: 'mailto:platinummedia208@gmail.com' }, { label: 'linktr.ee/platinummediallc', href: 'https://linktr.ee/platinummediallc' }] },
    ],
  },
  {
    label: 'New for 2026',
    partners: [
      { slug: 'caleb-anderson', initials: 'CA', specialty: 'Drone', name: 'Caleb Anderson', business: 'Caleb Aerials', links: [{ label: '@caleb.aerials', href: 'https://www.instagram.com/caleb.aerials' }, { label: 'caleb.aerials@gmail.com', href: 'mailto:caleb.aerials@gmail.com' }, { label: 'linktr.ee/caleb.aerials', href: 'https://linktr.ee/caleb.aerials' }] },
      { slug: 'john-dow-ii', initials: 'JD', specialty: 'Photography', name: 'John Dow II', business: 'F83 Photos', links: [{ label: '@f83photos', href: 'https://www.instagram.com/f83photos' }, { label: 'f83photos@icloud.com', href: 'mailto:f83photos@icloud.com' }, { label: 'f83photos.com', href: 'https://f83photos.com' }] },
      { slug: 'john-kisiel', initials: 'JK', specialty: 'Photography', name: 'John Kisiel', business: 'John Kisiel Photography', links: [{ label: '@john.kisiel', href: 'https://www.instagram.com/john.kisiel' }, { label: 'johnraymondkisiel@gmail.com', href: 'mailto:johnraymondkisiel@gmail.com' }, { label: 'johnkisielphotography.pixieset.com', href: 'https://johnkisielphotography.pixieset.com' }] },
    ],
  },
];

export const contact = {
  name: 'Jordan Van Diest',
  business: 'Platinum Media',
  instagram: { label: '@platinummediallc', href: 'https://www.instagram.com/platinummediallc' },
  email: 'mailto:platinummedia208@gmail.com',
  website: 'https://linktr.ee/platinummediallc',
};

export const mediaPackageLink = `${WORDPRESS_PUBLIC_URL}/product/media-package/`;
export const applicationLink = `${WORDPRESS_PUBLIC_URL}/media/media-application/`;

export const rules: { title: string; items: string[] }[] = [
  {
    title: 'Application requirements',
    items: [
      'You must submit an application, and be accepted to be on track at any APEX Idaho event. Having a vest and a camera will not get you in for free, or allow you on track.',
      'You must own or work for a professional media business, publication, or have a website that displays your work.',
      'You must provide examples of your work, and be ready to provide references if asked.',
      'You cannot shoot on your phone as your primary equipment, and only have an Instagram profile.',
      'Approved media members that provided media to APEX Idaho for promotional material in previous years, will receive preference in approval.',
      'Approval preference will go to those who can commit to attending most, if not all events.',
      'If you are approved, you will receive an ID card that is good for the 2025 season. You will also receive a pink Hi-Vis vest to wear at all APEX Idaho events.',
    ],
  },
  {
    title: 'Approved media must',
    items: [
      'Be interviewed at first track visit with an APEX Idaho official to verify credentials.',
      'Have your pink Hi-Vis vest on, and only enter and exit the track when it is appropriate and safe to do so.',
      'Maintain a level of professionalism, and respect of others and the track.',
      'Attend ALL media meetings ON TIME. NO EXCEPTIONS. If you cannot make it to the meetings, you will not be approved for that event.',
      "Upload media to APEX Idaho for promotional use via Google Drive within one week of the event's end. Uploads must be at minimum an entry fee's worth.",
      'If posting your work online, tag @apex_idaho on Instagram and APEX Idaho on Facebook.',
      'Provide links to your work on the Facebook APEX Idaho group or event page.',
      'Replacement cost for lost ID card or Hi-Vis vest is $40 each.',
    ],
  },
];
