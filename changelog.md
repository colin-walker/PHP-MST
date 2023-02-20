# Changelog

**20th Feb 2023**

Ensured notify path correctly calculated regardless of where the install is done (sub domain, root, sub directory etc.)

Existing items from a feed are now immediately added upon subscription rather than having to wait for a notification to be received.

When a feed is deleted the items from that feed are also removed from the timeline.

Updated files:

- pleaseNotify.php
- /admin/admin.php
- /admin/pleaseNotify.php

**17th Feb 2023**

Added `source:markdown` support to the RSS feed

**17th Feb 2023**

Big rewrite!

We have agreed that MST apps should display up to 100 items to keep it manageable so PHP-MST now temporarily stores up to ~100 remote posts (items.csv).

On receipt of a notification from the rssCloud server new items will be added to the csv file in the background and the timeline built from this file rather than from reading feeds on the fly.

The timeline will only show 100 items.

Your own items can now be edited by double-clicking them in the timeline – this will trigger a rebuild of your RSS feed and ping the rssCloud server.
