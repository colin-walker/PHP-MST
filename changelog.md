# Changelog

**2nd March 2023**

Changed pleaseNotify url parameter from 'url1' to 'url'

Updated files:

- pleaseNotify.php
- /admin/pleaseNotify.php

**23rd Feb 2023**

A few minor tweaks.

Updates files:

- clean.php
- ping.php
- style.css
- /admin/pleaseNotify.php

**21st Feb 2023**

Some additional styling, fixes & enhancements for replies

Updated Files:

- edit.php
- mst.php
- style.css

**21st Feb 2023**

Replies!

An initial stab at reply functionality is now live using a new ['mst' namespace](https://github.com/colin-walker/mst-namespace) to provide a `<mst:reply>` item level element which contains the URL of the item being replied to.

Click the reply icon next to any post to set a hidden input field which then gets added to the RSS feed for that post.

mst.php and page.php then check for the presence of this additional element to display replies.

There is also a little more tidying up.

Added files:

- /images/doreply.png
- /images/doreplydark.png
- /images/hascomment.png
- /images/hascommentdark.png

Updated Files:

- mst.php
- page.php
- post.php
- rss.php
- /about/about.php
- /admin/admin.php

**20th Feb 2023**

Ensured notify path correctly calculated regardless of where the install is done (sub domain, root, sub directory etc.)

Existing items from a feed are now immediately added upon subscription rather than having to wait for a notification to be received.

When a feed is deleted the items from that feed are also removed from the timeline.

Tidied up _some_ inline CSS in /admin and removed content border from /about

Updated files:

- mst.css
- mst.php
- pleaseNotify.php
- style.css
- /admin/admin.php
- /admin/pleaseNotify.php

**19th Feb**

Some minor tweaks and fixes

Updated files:

- clean.php
- edit.php
- mst.css
- mst.php
- pleaseNotify.php
- style.css

**17th Feb 2023**

Added `source:markdown` support to the RSS feed

**17th Feb 2023**

Big rewrite!

We have agreed that MST apps should display up to 100 items to keep it manageable so PHP-MST now temporarily stores up to ~100 remote posts (items.csv).

On receipt of a notification from the rssCloud server new items will be added to the csv file in the background and the timeline built from this file rather than from reading feeds on the fly.

The timeline will only show 100 items.

Your own items can now be edited by double-clicking them in the timeline – this will trigger a rebuild of your RSS feed and ping the rssCloud server.
