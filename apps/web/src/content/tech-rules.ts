/**
 * Official tech rules and driver code of conduct. Static content: edit here
 * and deploy, and change `updated` when the rules change. **text** is bold.
 */

export const updated = '2026-03-12';

export type Rule = {
  text: string;
  /** Sub-points; `ordered` numbers them (1, 2, 3) instead of bullets. */
  items?: string[];
  ordered?: boolean;
};

export const techRules: Rule[] = [
  { text: 'Functional seat belt or harness.', items: ['Non-OEM seats must have a harness.', 'Original seats can use stock seatbelts.'] },
  { text: 'Battery strap or tie down.' },
  { text: 'Functioning tail and brake lights. At least one brake light, one tail light, and one headlight.' },
  { text: 'All lug nuts are present and secured.' },
  { text: 'No cracked or broken glass.' },
  { text: 'All loose items must be removed before getting on track.' },
  {
    text: 'Fire extinguisher: at least 2.5 lbs.',
    items: [
      'Mounted within reach of the driver.',
      'Mounted in a way that can be easily removed, quick release system, or equivalent.',
      'Zip tie mounting will not be allowed.',
      'Don’t forget, fire extinguishers are not for your car; they’re for the cars around you. If your car is on fire, GET OUT, don’t try to get your extinguisher.',
    ],
  },
  { text: 'No loose body parts or excessively damaged panels.' },
  { text: 'Must be free of major leaks, with no open reservoirs or containers.' },
  { text: 'Use of water/water wetter. Coolant is acceptable for stock cars with factory crash bars and stock cooling systems.' },
  { text: 'Roll bars are required for all convertible vehicles.' },
  { text: 'Appropriate clothing.', ordered: true, items: ['Long pants.', 'Short sleeve t-shirt (no tank tops).', 'Full-toe coverage shoes.'] },
  {
    text: 'All drivers will have a **full-face** helmet.',
    ordered: true,
    items: ['Helmets must be Snell 2020 or newer.', 'DOT, ECE, etc. not allowed.', 'Exclusive to ride-along events: ride-along passengers are allowed to have open face helmets.'],
  },
  { text: 'Functioning throttle return spring for cars with throttle cables.' },
  { text: 'No Z31’s.' },
  {
    text: 'Engine compartment needs to be in an orderly fashion: no fuel lines routing over exhaust manifolds, hoses running to nowhere, etc. Extremely loose items need to be secured. For instance, no wiring should be able to go into a serpentine belt.',
  },
  {
    text: 'All seats in the vehicle that will hold passengers need to have four bolts securing them to the car.',
    items: ['Recommended seat belt bolts would be at least 8mm or 5/16″ in diameter and Grade 8.'],
  },
  { text: 'Any excessively loud vehicles will get a warning for one event, and must be fixed before the next event.', ordered: true, items: ['Recommend at least one muffler or resonator.'] },
];

export const codeOfConduct: string[] = [
  '**Drivers must attend morning driver’s meetings.** We will not do personal driver meetings. Please make arrangements with a track official before the day of the event if you absolutely can not make it. All other situations will warrant driving ineligibility, with no refund.',
  'Be alert and pay attention to all flaggers and track officials. If you do not, you will be sent off track. Repeated offenses will result in being ejected from the event.',
  'Keep the pits clean. Use the trash receptacles.',
  'All spills must be cleaned IMMEDIATELY. Water/water wetter is required in the cooling system.',
  'Keep arms inside the vehicle at all times.',
];
