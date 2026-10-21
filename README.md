# Monica Starter Site

Wordpress + Docker + Commonly used tools to build beautiful sites.
## WP-CLI against the live site

The live site is on Cloudways. `scripts/wp-live` runs WP-CLI there over SSH:

```
scripts/wp-live option get siteurl
scripts/wp-live post list --post_type=tribe_events --fields=ID,post_title
```

It needs an `apex-live` host in `~/.ssh/config` with a key that Cloudways has
accepted (add the public key in the console under Server Management → Master
Credentials → SSH Public Keys — keys added by hand to `authorized_keys` are
ignored). No credentials live in this repo.

`wp-cli.yml` defines the same target as a `wp @live` alias, which is nicer to
type but needs PHP installed locally for WP-CLI's `--ssh` launcher.

### Querying events

The Events Calendar filters `tribe_events` WP_Query down to *upcoming* events,
so `wp post list --post_type=tribe_events` returns nothing once every event is
in the past — the posts are still there. Pass the suppression flag to see them:

```
scripts/wp-live post list --post_type=tribe_events --post_status=any \
    --tribe_suppress_query_filters=1 --fields=ID,post_title,post_date
```

`--skip-plugins` works too, and also surfaces drafts that have no occurrence
row in `wp_tec_occurrences`.

For the local Docker site, use the `wpcli` service instead:

```
docker compose run --rm wpcli wp <args>
```
