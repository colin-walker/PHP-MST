# PHP-MST

An initial PHP implementation of '**My Status Tool**'.

## WHAT IS PHP-MST?

PHP-MST is a simple PHP implementation of [My Status Tool](https://github.com/andysylvester/MyStatusToolDemo) by Andy Sylvester.

In Andy's words:

> My Status Tool is an application that provides the basic posting and reading functionality within Twitter, but using RSS and rssCloud as the enabling technologies.

PHP-MST includes authentication and the posting box only shows when logged in. It has the following functionality:

- make a short post (although there is technically no limit)
- add posts to a local file and build an RSS feed (currently limited to 10 items)
- each post has a page
- posts are sorted by timestamp along with those from any other subscribed feeds
- an admin page for feed subscription & removal

PHP-MST uses Emanuil Rusev's [Parsedown](https://github.com/erusev/parsedown) library for Markdown and [my forked version](https://github.com/colin-walker/rss-php) of David Grudl's RSS-PHP.

It doesn't auto-refresh but prompts when your subscribed feeds have been updated so you can manually reload the page.

## Installation:

- copy all files to your chosen location and go to that location in a browser
- the setup page will be shown letting you specify:
    - username and password
    - your name (or the name you want for the instance)
    - the base URL of the installation (where you browsed to above)
    - your email address
    - an avatar (image to be used for the RSS feed)
    - choose date format (between UK - dd/mm/yyyy and US mm/dd/yyyy)
    
Submitting this takes you to the admin page and deletes the setup file (we don't want any accidents, do we.)

## Admin page

The admin page allows you to subscribe to RSS feeds and remove them. It is intended feeds will be from other 'My Status Tool' instances which support rssCloud notifications.

You can also specify the 'refresh' time – how frequently PHP-MST checks for updates in minutes, and the number of items published to your local RSS feed (min 10, max 50).

The refresh time is stored in admin/refresh.txt and item count in admin/items.txt

## Usage

Add feeds as above to see updates from other people. The timeline is built on the fly from your own RSS feed and those you are subscribed to.

The posting form will be visible when logged in allowing you to post new statuses. These are then added to posts.csv (it is created on first post) and an RSS feed is created/updated in feed.xml. New posts will immediately be shown in the timeline alng with those from other feeds you are subscribed to.

When a subscribed feed notifies of an update a new file notify/count.txt is created. Using HTMX, count.php is reloaded at the refresh interval to check for the existence of count.txt – if it exists (or holds a value greater than zero) it will show a div prompting to load new items. Reloading the page will delete count.txt.

## To do:

- look at pagination or limit the total number of items (discussion has suggested 100)
- potential for temporary storage of items (up to the above limit) so that they can be retrieved in the background and the timeline built from this storage rather than on the fly
- think about some kind of 'reply' mechanism