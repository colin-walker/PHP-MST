# Changelog

**17th Feb 2023**

Big rewrite!

We have agreed that MST apps should display up to 100 items to keep it manageable so PHP-MST now temporarily stores up to ~100 remote posts (items.csv).

On receipt of a notification from the rssCloud server new items will be added to the csv file in the background and the timeline built from this file rather than from reading feeds on the fly.

The timeline will only show 100 items.

Your own items can now be edited by double-clicking them in the timeline – this will trigger a rebuild of your RSS feed and ping the rssCloud server.