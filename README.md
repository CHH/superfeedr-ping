superfeedr-ping
===============

Simple service which pings Superfeedr when a Github web hook was triggered. This service is designed to run on Heroku.

Setup
-----

1. Set this script up as a Github Webhook for a repository. Point the URL at the `ping.php` endpoint.
2. Configure the events. I used the "page build" event for my Github Pages site.

Configuration
-------------

* `HUB_URL`: URL of your Superfeedr hub, typically `http://yourusername.superfeedr.com`
* `HOOK_SECRET`: Secret configured in the Github webhook's settings
* `FEED_URL`: URL to your feed

License
-------

[MIT License](/LICENSE).
