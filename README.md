superfeedr-ping
===============

Simple service which pings Superfeedr when a Github web hook was triggered. This service is designed to run on Heroku.

[![Deploy](https://www.herokucdn.com/deploy/button.png)](https://heroku.com/deploy)

Setup
-----

1. Set this script up as a Github Webhook for a repository. Point the URL at the `ping.php` endpoint. For example, I hosted mine at `https://chh-blog-superfeedr.herokuapp.com/ping.php`.
2. Set the secret and note it. Set the `HOOK_SECRET` config var to the noted value.
2. Configure the events. I used the "page build" event for my Github Pages site.

Configuration
-------------

Set these as environment variables or Heroku config vars:

* `HUB_URL`: URL of your Superfeedr hub, typically `http://yourusername.superfeedr.com`
* `HOOK_SECRET`: Secret configured in the Github webhook's settings
* `FEED_URL`: URL to your feed

License
-------

[MIT License](/LICENSE).
