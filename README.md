## WORK IN PROGRESS
Do not use except for testing

Laravel AWX
====================


## Installation

Laravel Awx requires [PHP](https://php.net) 7.2-7.4. This particular version supports Laravel 6-7.

Add the following dependencies to laravel composer.json
```
"require": {
    "sdwru/awx-v2": "dev-master",
    "sdwru/oauth2-awx": "dev-master",
    "sdwru/laravel-awx": "dev-master"
},
"repositories": [
    { "type": "git", "url": "https://github.com/sdwru/awx-v2.git" },
    { "type": "git", "url": "https://github.com/sdwru/oauth2-awx.git" },
    { "type": "git", "url": "https://github.com/sdwru/laravel-awx.git" }
],
```
And run `composer update` from cli.

## Configuration

Laravel Awx requires connection configuration.

To get started, you'll need to publish all vendor assets:

```bash
$ php artisan vendor:publish
```
and select `Provider: Sdwru\Awx\AwxServiceProvider`

This will create a `config/awx.php` file in your app that you can modify to set your configuration. Also, make sure you check for changes to the original config file in this package between releases.

The following options should not be changed.

##### Default Connection Name

This option (`'default'`) is from the upstream code for changing http clients that this package is based on. We only use guzzle in this package so the default value for this setting should be left at `'main'`.

##### Awx Connections

This option (`'connections'`) is where each of the connections are setup for your application if we were to support more than one http client. We only use guzzle in this application.  We left this in this package for now.  It's possible we will support other clients in the future such as the Laravel v7 http client (also based on guzzle).

#### Oauth credentials

Gets the AWX password grant credentials from the Laravel .env file.

#### API

Gets the API configuration from the Laravel .env file


## Usage

##### AwxManager

This is the class of most interest. It is bound to the ioc container as `'awx'` and can be accessed using the `Facades\Awx` facade. This class implements the `ManagerInterface` by extending `AbstractManager`. The interface and abstract class are both part of my [Laravel Manager](https://github.com/GrahamCampbell/Laravel-Manager) package, so you may want to go and checkout the docs for how to use the manager class over at [that repo](https://github.com/GrahamCampbell/Laravel-Manager#usage). Note that the connection class returned will always be an instance of `\Awx\Client`.

##### Facades\Awx

This facade will dynamically pass static method calls to the `'awx'` object in the ioc container which by default is the `AwxManager` class.

##### AwxServiceProvider

This class contains no public methods of interest. This class uses automatic package discovery and therefore does NOT need to be added to the providers array in `config/app.php`. This class will setup ioc bindings.

##### Real Examples

Here you can see an example of just how simple this package is to use. Out of the box, the default adapter is `main`. After you enter your authentication details in the config file, it will just work:

```php
use Sdwru\Awx\Facades\Awx;
// you can alias this in config/app.php if you like

Awx::user()->getById(1);
// we're done here - how easy was that, it just works!

Awx::job()->getAll();
// this example is simple, and there are far more methods available
```

The awx manager will behave like it is a `\AwxV2\AwxV2` class. If you want to call specific connections, you can do with the `connection` method:

```php
use Sdwru\Awx\Facades\Awx;

// the alternative connection is the other example provided in the default config
Awx::connection('alternative')->rateLimit()->getRateLimit()->remaining;

// let's check how long we have until the limit will reset
Awx::connection('alternative')->rateLimit()->getRateLimit()->reset;
```

With that in mind, note that:

```php
use Sdwru\Awx\Facades\Awx;

// writing this:
Awx::connection('main')->region()->getAll();

// is identical to writing this:
Awx::region()->getAll();

// and is also identical to writing this:
Awx::connection()->region()->getAll();

// this is because the main connection is configured to be the default
Awx::getDefaultConnection(); // this will return main

// we can change the default connection
Awx::setDefaultConnection('alternative'); // the default is now alternative
```

If you prefer to use dependency injection over facades then you can easily inject the manager like so:

```php
use Sdwru\Awx\AwxManager;
use Illuminate\Support\Facades\App; // you probably have this aliased already

class Foo
{
    protected $awx;

    public function __construct(AwxManager $awx)
    {
        $this->awx = $awx;
    }

    public function bar()
    {
        $this->awx->region()->getAll();
    }
}

App::make('Foo')->bar();
```

For more information on how to use the `\AwxV2\AwxV2` class we are calling behind the scenes here, check out the docs at https://github.com/toin0u/AwxV2#action, and the manager class at https://github.com/GrahamCampbell/Laravel-Manager#usage.

##### Further Information

There are other classes in this package that are not documented here. This is because they are not intended for public use and are used internally by this package.


## Security

If you discover a security vulnerability within this package, please send an email to Graham Campbell at graham@alt-three.com. All security vulnerabilities will be promptly addressed. You may view our full security policy [here](https://github.com/GrahamCampbell/Laravel-Awx/security/policy).


## License

Laravel Awx is licensed under [The MIT License (MIT)](LICENSE).


---

<div align="center">
	<b>
		<a href="https://tidelift.com/subscription/pkg/packagist-graham-campbell-awx?utm_source=packagist-graham-campbell-awx&utm_medium=referral&utm_campaign=readme">Get professional support for Laravel Awx with a Tidelift subscription</a>
	</b>
	<br>
	<sub>
		Tidelift helps make open source sustainable for maintainers while giving companies<br>assurances about security, maintenance, and licensing for their dependencies.
	</sub>
</div>
