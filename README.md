Beeminder API
=============

A PHP interface to the Beeminder API. Handles everything in v1, although it
still needs much better error handling. 


Usage
-----

First you'll need to include the autoloader using the following snippet:

```php
require_once '/path/to/lib/Beeminder/Autoloader.php';
Beeminder_Autoloader::register();
```

Just replace `/path/to/` with the actual path the API is stored in.

Once the autoloader is included and setup, it's time to create an instance of
`Beeminder_Client`. After that, simply login using authorisation tokens (either
OAuth or the private API token) and then call whatever method(s) you
want. Here's an example:

```php

// Include the autoloader
require_once dirname(__FILE__) . '/vendor/beeminder-api/lib/Beeminder/Autoloader.php';
Beeminder_Autoloader::register();

// Create new client
$api = new Beeminder_Client();

// Setup auth (private token)
$api->login('username', 'secret_token', Beeminder_Client::AUTH_PRIVATE_TOKEN);

// Fetch a list of goals for the user
$goals = $api->getGoalApi()->getGoals();

// Output some happy info
foreach ($goals as $goal) {
    echo "{$goal->title}\n";
    echo "{$goal->goal_type}\n";
}

```


Projects that use this library
------------------------------

* [beeminder-ping](http://github.com/sodaware/beeminder-ping/) -- A WordPress
  plugin to ping Beeminder when a post is made.


Credits
-------

* [Beeminder](https://www.beeminder.com/) is pretty neat. You should use it :)
* API library design based on [php-github-api](https://github.com/ornicar/php-github-api/).
