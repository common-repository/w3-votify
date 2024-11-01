=== Voting Plugin -- W3 Votify ===
Contributors: bookbinder
Tags: social bookmarking, ajax, voting, points, rating, trending, upvote, downvote
Requires at least: 4.7
Stable tag: trunk
Tested up to: 4.9.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Rank posts and comments based on what's trending by allowing users to upvote/downvote content. (Uses WP's built in karma functionality...so it's compatible with other themes and plugins that use comment points/karma).

== Description ==

Create your own social bookmarking site.

Add Reddit-like voting functionality to WordPress posts and comments.

Order comments/pages based on what's trending or use standard criteria like title, post date, etc. You can also order pages based on their page rating (the percentage of upvotes out of total votes cast).

W3 Votify works perfectly with with cached web pages (so pages don't need to be regenerated/refreshed whenever a new vote is cast).

You can modify your theme to use Votify with your comments or you can use W3 Ajax Comments (with Votify support built in).



= Related Plugins =

**W3 Directory Builder**
Add W3 Directory Builder to create a power link/social bookmarking web site.

https://wordpress.org/plugins/w3-directory-builder-basic/

**W3 Ajax Comments**
Add W3 Ajax Comments to enable Disqus-like comment functionality (edit and create threaded/nested comments without reloading the page).

https://wordpress.org/plugins/w3-ajax-comments/

== Installation ==

= BASIC =

Visit wp-admin-> Settings -> W3 Votify and configure the "Auto Insert Vote Buttons" options on the screen (before post, after post, before comment, after comment).


= ADVANCED =

**Function**
w3vx_vote_buttons_wrapper($id, $type, $icon, $orientation);

**Parameters**

__$id__
(int) Post/Comment ID


__$type__
(string) post|comment

__$icon__
(string, default: triangle) chevron|triangle|thumb|arrow
_placeholder/in development_

__$orientation__
(string) horizontal|vertical

== Screenshots ==

1. W3 Votify settings page.

2. Example: Vote Buttons (via auto insert).

3. Example: Vote Buttons (via theme modification). 

