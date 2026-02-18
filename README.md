# Clara

A tiny educational PHP MVC framework with DI, routing, controllers, views, and a PDO wrapper.

## Setup

1. Install dependencies:

```bash
composer install
```

2. Point your web server document root to `public/`.
3. Configure application values in `config/`:
   - `config/app.php`
   - `config/database.php`
   - `config/routes.php`

## Project Structure

```text
/clara
  /config
    app.php               # App config (name + controller namespace)
    database.php          # Database DSN/credentials/PDO options
    routes.php            # Route list + not-found handler
  /public
    index.php             # Front controller / bootstrap
    favicon.ico
  /src
    /app
      /controllers
      /models
      /views
    /core
      DB.php              # PDO wrapper with run() helper
      Request.php         # HTTP request accessors
      Response.php        # Status/headers/views/redirects
      Router.php          # Route registry + dispatch(method, uri)
```

## Configuration

`config/database.php` returns a simple array so users can control DSN and PDO options directly:

```php
return [
    'dsn' => 'sqlite:' . BASE_PATH . '/ephermal/db.sqlite',
    'username' => null,
    'password' => null,
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];
```

`DB` is instantiated with those values:

```php
$db = new DB($dsn, $username, $password, $options);
```

## Routing

Define routes in `config/routes.php`:

```php
return [
    'not_found' => '_404@index',
    'routes' => [
        ['method' => 'GET', 'path' => '/', 'handler' => 'Home@index'],
    ],
];
```

`Router::dispatch($method, $uri)` returns the matched handler string (or `null`), and the front controller handles 404 fallback behavior.

## Request Flow

1. `public/index.php` loads `config/*.php`.
2. It builds the DI container and registers `DB` from config values.
3. It registers configured routes with `Router`.
4. It dispatches with request method + URI.
5. It resolves `Controller@action`, invokes it, or falls back to configured not-found handler.

