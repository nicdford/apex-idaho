# APEX Idaho block theme

Full Site Editing theme that replaces the Beaver Builder setup
(`bb-theme` + `bb-theme-child`). Pages, templates, header and footer are all
Gutenberg blocks, editable under **Appearance → Editor**.

Design direction: modern, aggressive, automotive, in red and black. Carbon-black
base, one accent (APEX Red), no colour gradients, condensed italic display
type that leans forward, slanted "parallelogram" buttons, racing stripes,
checkered flags and timing-sheet tables.

Colours live in `theme.json` and come from the logo: **APEX Red** `#BE1E2D`
(the logo's red: buttons, badges, stripes, red sections, and red text on light
backgrounds), **Signal Red** `#EF4554` (a lighter tint for small red text on
black, where APEX Red would fall below 4.5:1) and **Deep Red** `#8F1622`
(hover on light backgrounds).

The logo (`assets/images/apex-idaho-logo.png`, from the original artwork) is
set as the Site Logo when the theme is activated: it reuses the logo already
in the media library (`uploads/2023/07/APEX_Idaho_logo-2.png`) or imports the
bundled copy. Change it in Site Editor → Header → logo. Before activation (in
Live Preview) the header shows the bundled copy.

## Role in the headless setup

The public site is moving to a Next.js front end (`apps/web`) that reads
WordPress over the REST API. This theme stays active on WordPress and does two
jobs there:

- It renders the pages WordPress keeps serving after the switch: event pages
  with the ticket form, cart, checkout, shop, account and forms. These match
  the Next.js site because both use this design.
- It is the single source of the design. `apps/web` generates its CSS tokens
  from `theme.json` and `styles/sections/` and copies `assets/css/theme.css`
  and `blocks/blocks.css`. After changing any of them, run
  `npm run sync:tokens` in `apps/web` and commit both; CI fails otherwise.

Everything below still applies, and the theme works on its own if the
headless front end is rolled back. See `apps/web/README.md` for the cutover.

## Cutting over from Beaver Builder

Both themes deploy side by side (the workflow mirrors the whole `themes/`
folder), so merging this changes nothing on the live site until someone
switches themes.

1. **Preview.** Two levels, both invisible to visitors:
   - *Quick look:* get the theme onto the server (merge, or upload a zip of
     this folder with `vendor/` included under Appearance → Themes → Add New →
     Upload), then Appearance → Themes → APEX Idaho → *Live Preview*. For a
     block theme this opens the Site Editor in preview mode: browse templates
     and pages with real content, and nothing is saved until you click
     Activate. Don't *activate* a hand-uploaded copy on the live site before
     this is merged: the deploy mirrors `themes/` with `--delete`, so the next
     deploy from a branch without this theme removes it from the server.
   - *Full test:* Live Preview can't exercise the front end (ticket checkout
     modal, cart, checkout). For that, clone the site to a Cloudways staging
     app, activate the theme there and click through with test payments.
2. **Logo.** Activation sets the APEX Idaho logo as the Site Logo (see
   above). Nothing to do unless you want a different file.
3. **Check every page in the preview**, especially the ones this theme was
   not able to test locally (see below).
4. **Activate.** Rollback is re-activating *APEX Idaho Child Theme*; nothing
   in the database is changed by the switch.
5. **Migrate the remaining Beaver Builder pages** one at a time: open the page,
   insert one of the *APEX: Full Pages* patterns (or build from sections),
   then turn Beaver Builder off for that page.
6. When no page uses Beaver Builder any more, deactivate Beaver Builder and
   Beaver Themer, then delete `bb-theme` and `bb-theme-child` from the repo.

### What carries over automatically

| Thing | How |
| --- | --- |
| Pages using *Festival of Speed*, *Sponsors*, *Media* templates | Block templates with the same slugs (`template-festival-of-speed`, etc.) take over, so those pages switch to the new design with no edits. |
| Luau Party landing (event 2749) | `template-luau-party.php` copied as-is, routed the same way, now rendered between the block header and footer. |
| Sponsors (post type, logos, Carbon Fields meta) | Same post type, same field names. Sponsors also get an *Order* field now (they were already sorted by it). |
| Media partner photos + focal points | Same meta keys; the picker box is unchanged. |
| WooCommerce: auto-complete orders, media package photo upload | Ported with identical hooks, product ID (2825) and order meta keys. |
| Event Tickets email templates (`tribe/tickets/emails`) | Copied verbatim. |
| Social links (BB customizer) | Read from the old theme's settings; Instagram falls back to @apex_idaho. |
| Pages still built in Beaver Builder | Still render (the plugin works with any theme). They get a white content canvas so their light-designed rows stay readable, and the theme's title band is hidden on them. |

### Not verifiable locally — check on staging/preview

The local test site could not install The Events Calendar, Event Tickets,
WooCommerce or Beaver Builder (no wordpress.org access from the build
environment). Their integration code is defensive and styled through each
plugin's own CSS variables, but needs eyes on:

- Events calendar views and single event pages (dark re-theme in `assets/css/compat.css`).
- Ticket purchase form **and the checkout/attendee modal**. The modal is deliberately kept on Event Tickets' stock light styling (see commit 38ee553 for why).
- Shop, product page (incl. Media Package upload box), cart and checkout.
- Beaver Builder pages and any Beaver Themer layouts (e.g. the event single layout).
- Real sponsor logos on the white sponsor board.

## Editing guide

### Heading conventions
In any heading, **bold** text renders in APEX Red and *italic* text renders as
an outline. Plain Cmd/Ctrl+B and Cmd/Ctrl+I, no extra settings.

### Section styles (Group → Styles)
- **Paper**: light section, dark text.
- **Red**: solid APEX Red, white text, black buttons.
- **Asphalt**: slightly lifted dark section.
- **Panel**: bordered card surface.
- **Ticker (scrolling)**: turns a row of paragraphs into a looping marquee.

### Block styles
| Block | Styles |
| --- | --- |
| Paragraph | Eyebrow (kicker label), Lede (intro size) |
| Heading | Outline, Red |
| Button | Arrow; core *Outline* is restyled as a ghost button |
| Separator | Racing Stripe, Checkered Flag, Chevrons |
| List | Diamonds |
| Image | Angled Cut, Chamfer |
| Table | Timing Sheet (schedules, standings) |
| Cover | Speed Lines |

### Theme blocks (APEX Idaho category)
- **Upcoming Events**: next events from The Events Calendar as cards, a timing-sheet list, or the header ticker.
- **Sponsor Board**: logos from Sponsors, scrolling or grid.
- **Race Countdown**: to the next calendar event or a fixed date; hides itself when there is nothing upcoming.
- **Media Partner Photo**: featured image + focal point of a Media Partner post.
- **Cart Link**: cart with item count (only when WooCommerce is active).

All five are server-rendered, so they always show live data and never go
"invalid" in the editor.

### Templates
`front-page` (homepage), `page` (title band + content), `page-canvas`
(*Full-Width Canvas*: content only, for pages built entirely from patterns),
`single`, `single-media_partner`, `archive`, `home` (news), `search`, `404`,
plus the three legacy page templates above.

### Patterns
Under *APEX: Heroes / Sections / Events / Calls to Action / Full Pages* in the
inserter. *Full Pages* also appear as starter layouts when creating a page.

## Development

- No build step. Editor UI for the theme blocks is plain JS in `blocks/editor.js`.
- `composer install` pulls Carbon Fields into `vendor/` (the deploy workflow does this).
- **Bump `Version` in `style.css` whenever you add, remove or rename a file in
  `patterns/`** (or change a pattern's header). WordPress caches the list of
  pattern files per theme version, so a new pattern stays invisible until the
  version changes or transients are cleared (`wp transient delete --all`).
- Design tokens (colours, type scale, spacing) live in `theme.json`; CSS that
  theme.json can't express is in `assets/css/theme.css`, block CSS in
  `blocks/blocks.css`, third-party overrides in `assets/css/compat.css`.
- Never put `transform`, `filter`, `backdrop-filter`, `clip-path`, `contain`
  or a z-index stacking context on a container that can hold page content:
  Event Tickets' checkout modal is a fixed-position descendant and breaks.
  Shapes are drawn with pseudo-elements or on leaf elements only.
- Fonts: Barlow and Barlow Condensed, self-hosted (SIL OFL, `assets/fonts/OFL.txt`).
- Hero photo: `assets/images/hero-drift.jpg`, Platinum Media (resized from the
  Festival of Speed hero in the old theme).
