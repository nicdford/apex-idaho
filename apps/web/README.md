# APEX Idaho web

The public site: a Next.js app that reads content from WordPress over the REST
API. WordPress stays the backend, where staff edit events, pages, posts,
sponsors and media partners, and it keeps serving everything that takes money
or personal details: cart, checkout, ticket purchase, accounts and forms.

```
visitor ──▶ Next.js (this app)  ──REST──▶  WordPress (apps/wordpress)
               │                               │  Events Calendar, Event Tickets,
               │  /cart, /checkout,            │  WooCommerce, Pods, Carbon Fields,
               │  ticket forms, /my-account    │  apex-headless plugin, apex-idaho theme
               └──────── redirect ───────────▶ │
                       ◀── POST /api/revalidate (on every edit)
```

## What lives where

| Page | Served by | Content comes from |
| --- | --- | --- |
| Home `/` | Next | Copy in `src/content/home.ts`; events from The Events Calendar |
| Events `/events`, `/events/[slug]` | Next | The Events Calendar REST API |
| Sponsors `/sponsors` | Next | `sponsor` posts, via `apex/v1/sponsors` |
| Media `/media` | Next | Copy in `src/content/media.ts`; partner photos from `apex/v1/media-partners` |
| News `/news`, posts | Next | Posts (`wp/v2/posts`) |
| Any other page (`/about`, `/tech-rules`, …) | Next | Pages (`wp/v2/pages`), block-editor HTML rendered as-is |
| Ticket purchase (`/event/[slug]` on WordPress) | WordPress | Event Tickets + WooCommerce |
| Cart, checkout, shop, account, forms | WordPress | WooCommerce and the site's form plugin |

"Get tickets" on an event page links to that event on WordPress, where the
ticket form is. The paths WordPress keeps are listed in `next.config.ts`
(`WORDPRESS_PATHS`) and `src/lib/html.ts`; requests for them here redirect to
WordPress, and links to them inside WordPress content are rewritten to go
straight there.

The WordPress pages that remain (event and ticket pages, cart, checkout) are
styled by the `apex-idaho` block theme, so the two halves look the same.

## Design

The block theme is the single source of the design. `npm run sync:tokens`
generates `src/styles/tokens.css` from the theme's `theme.json` and section
styles, with the same `--wp--preset--*` custom properties and `has-*` classes
WordPress outputs, and copies the theme's `assets/css/theme.css` and
`blocks/blocks.css` into `src/styles/theme/`. HTML from the block editor
therefore renders here exactly as it does on WordPress, and the React
components use the same class names as the theme's blocks.

To change colours, type or spacing, edit the theme, run `npm run sync:tokens`
here and commit both. CI fails if they drift. Styles that only the app needs
(layout, header, navigation) are in `src/styles/app.css`.

Fonts are Barlow and Barlow Condensed (SIL OFL, `src/fonts/`), self-hosted
through `next/font`.

## Running it

Node 20.9 or later (22 in `.nvmrc`).

```sh
cd apps/web
cp .env.example .env.local   # defaults read the live site
npm install
npm run dev                  # http://localhost:3000
```

Against the local Docker WordPress (`apps/wordpress`), set
`WORDPRESS_URL=http://localhost:<port>` in `.env.local` and activate the
**APEX Idaho Headless API** plugin there.

| Script | |
| --- | --- |
| `npm run dev` | Development server |
| `npm run build` | Production build (standalone server in `.next/standalone`) |
| `npm run lint` | ESLint |
| `npm run typecheck` | Route types + `tsc` |
| `npm run sync:tokens` | Regenerate design tokens from the block theme |

### Environment

| Variable | When | |
| --- | --- | --- |
| `WORDPRESS_URL` | build + run | Where the REST API is read. Default `https://apex-idaho.com`. |
| `WORDPRESS_PUBLIC_URL` | build + run | Where visitors go for cart, checkout and tickets. Defaults to `WORDPRESS_URL`. |
| `NEXT_PUBLIC_SITE_URL` | build | This site's URL, for canonical links, the sitemap and social cards. |
| `REVALIDATE_SECRET` | run | Shared with WordPress; authorises the revalidation webhook. |
| `REVALIDATE_SECONDS` | run | Background refresh interval, a backstop to the webhook. Default 300. |

Redirects and allowed image hosts are fixed into the build, so a change to
`WORDPRESS_URL` or `WORDPRESS_PUBLIC_URL` needs a rebuild.

## Caching and updates

Pages are prerendered at build time and cached. Every WordPress request is
tagged (`site`, `events`, `sponsors`, `partners`, `pages`, `posts`). When
someone saves, trashes or deletes content, or changes the site title, logo or
icon, the `apex-headless` plugin calls `POST /api/revalidate` with the
affected tags and the next visit renders the change. Pages also refresh in the
background every `REVALIDATE_SECONDS` in case a webhook is missed.

If WordPress is unreachable, requests fail soft: pages keep their last cached
content, and a fresh build still succeeds with empty sections.

Refresh everything by hand:

```sh
curl -X POST https://apex-idaho.com/api/revalidate -H "x-revalidate-secret: $REVALIDATE_SECRET"
```

## WordPress setup

1. Activate **APEX Idaho Headless API**
   (`apps/wordpress/wp-content/plugins/apex-headless`). It adds read-only
   endpoints for site settings, sponsors and media partner photos, and sends
   the revalidation webhook. It works with either theme active.
2. Add to `wp-config.php`:

   ```php
   define( 'APEX_HEADLESS_FRONTEND_URL', 'https://apex-idaho.com' ); // this app
   define( 'APEX_HEADLESS_REVALIDATE_SECRET', '…' );                // = REVALIDATE_SECRET
   define( 'APEX_HEADLESS_REDIRECT', false );                       // see the cutover
   ```

   Generate the secret with `openssl rand -hex 32`.

The Events Calendar's REST API must stay enabled (it is by default).

## Deploying

The repo's WordPress deploy (`.github/workflows/deploy.yml`) ships the plugin
with the other plugins. This app deploys separately.

**Fly.io** (as `apps/pitwall-bot` does): `fly.toml` and `Dockerfile` are
ready. From `apps/web`:

```sh
fly launch --no-deploy --copy-config
fly secrets set REVALIDATE_SECRET=…
fly deploy
```

Run one machine: cached pages live on the machine, and the webhook only reaches
the machine that answers it. To scale out, add a shared cache handler first.

**Vercel** also works with no changes: import the repo, set the root directory
to `apps/web` and add the environment variables above. Vercel shares the cache
across regions, so the webhook reaches every instance.

**Any Docker host:** build with `--build-arg WORDPRESS_URL=…
--build-arg NEXT_PUBLIC_SITE_URL=…` and run with `-e REVALIDATE_SECRET=…`; the
server listens on port 8080.

## Cutover

The Next.js app takes over the public domain and WordPress moves to a
subdomain. Do it in this order:

1. **Preview.** Deploy this app to its default host (e.g.
   `apex-idaho-web.fly.dev`) with `WORDPRESS_URL=https://apex-idaho.com` and
   `NEXT_PUBLIC_SITE_URL` set to that host. Review every page. Nothing on the
   live site changes.
2. **Plugin.** Activate `apex-headless` on the live site and add the
   constants with `APEX_HEADLESS_FRONTEND_URL` pointing at the preview host.
   Edits now refresh the preview.
3. **Theme.** Switch WordPress to the `apex-idaho` block theme (see its
   README) so the pages WordPress keeps match this app.
4. **Move WordPress to `cms.apex-idaho.com`.** On Cloudways: add the domain
   and its SSL certificate, then `wp search-replace https://apex-idaho.com
   https://cms.apex-idaho.com --all-tables` and update `home`/`siteurl`.
   Update anything outside WordPress that calls it by URL: the webhook URLs
   registered with each WooCommerce payment gateway, ticket check-in apps, the
   Pitwall bot's `WP_BASE_URL`, and the Google Search Console property.
5. **Point the domain at this app.** Rebuild with
   `WORDPRESS_URL=https://cms.apex-idaho.com` and
   `NEXT_PUBLIC_SITE_URL=https://apex-idaho.com`, add the certificate
   (`fly certs add apex-idaho.com` and `www.apex-idaho.com`) and switch DNS.
   Set `APEX_HEADLESS_FRONTEND_URL` to `https://apex-idaho.com`.
6. **Redirect stray visits.** Set `APEX_HEADLESS_REDIRECT` to `true`. Public
   page views on `cms.apex-idaho.com` (from old links or search results) then
   redirect to the same path here. Admin, REST, previews, feeds, and the paths
   WordPress keeps (cart, checkout, `/event/…`, `/my-account`, forms) are never
   redirected. The status is 302; once everything checks out, return 301 with
   the `apex_headless_redirect_status` filter.

Rollback at any step: point DNS back at Cloudways, run the search-replace in
reverse and set `APEX_HEADLESS_REDIRECT` to `false`.

## Editing

- **Events, sponsors, media partner photos, pages and posts:** edit in
  WordPress as before. Changes show within seconds.
- **Site title, logo, icon, social links:** WordPress settings and Customizer.
- **Home and Media page copy** (headlines, intro text, the media partner
  list): `src/content/*.ts`, then deploy. Move these into WordPress fields if
  non-developers need to edit them often.
- **Navigation:** `PRIMARY_NAV` and `FOOTER_NAV` in `src/lib/config.ts`.

Not yet supported: previewing WordPress drafts here (WordPress's own preview
still works on the CMS host).

## Layout

```
src/
  app/            routes (App Router), sitemap, robots, revalidation webhook
  components/     header, footer, navigation, event cards, sponsors, buttons
  content/        copy for the designed pages
  lib/            WordPress client (wp.ts), events (events.ts), HTML rewriting, config
  styles/         tokens.css + theme/ (generated), app.css (app-only)
  fonts/          Barlow, Barlow Condensed
scripts/
  sync-tokens.mjs
```
