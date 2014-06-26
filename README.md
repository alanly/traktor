Traktor
=======

[![Build Status - Master](https://api.travis-ci.org/alanly/traktor.svg?branch=master)](https://travis-ci.org/alanly/traktor)

Traktor is a simple PHP client for the Trakt.tv API service, that allows you to cleanly integrate the data available on Trakt into your application. Currently, it supports only non-developer and GET-based methods.

It aims to be fairly basic and simple to use:

```php
$traktor = new Traktor\Client;
$traktor->setApiKey('foobar');
$summary = $traktor->get('movie.summary', ['the-social-network-2010']);
echo $summary->imdb_id;
// "tt1285016"
echo $summary->tagline;
// "You don't get to 500 million friends without making a few enemies"
```

```php
$traktor = new Traktor\Client;
$traktor->setApiKey('foobar');
$summary = $traktor->get('show.episode.summary', ['silicon-valley', 1, 3]);
echo $summary->show->title;
// "Silicon Valley"
echo $summary->episode->season;
// 1
echo $summary->episode->number;
// 3
echo $summary->episode->title;
// "Articles of Incorporation"
```