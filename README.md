💠 **Clara**
*A modern MVC framework built with PHP 8*

---

## Philosophy

Clara was born from curiosity.

It started as a personal exploration. Not to compete with established frameworks, but to understand them. To look beneath the abstractions of modern PHP development and study how an MVC framework truly works under the hood.

Every route, controller call, and model operation is written in plain, readable code you can follow step by step. The aim was to keep the framework simple and understandable.

Clara values:

* **Transparency over magic**
  Behavior should be traceable, readable, and debuggable.

* **Simplicity over cleverness**
  Straightforward architecture teaches more than hidden automation.

* **Control over convenience**
  Developers should understand the full request lifecycle.

* **Learning through building**
  The best way to understand frameworks is to create one.

Clara is both a framework and a study artifact. A place to experiment, break things, and refine understanding of modern PHP design.

---

## Why Clara Exists

Modern frameworks accelerate development, but they also abstract fundamentals. Clara was built to:

* Deconstruct MVC architecture piece by piece
* Study routing, dependency flow, and application bootstrapping
* Explore PHP 8 features in a controlled environment
* Serve as a lightweight foundation for custom experimentation
* Provide a reference implementation without enterprise complexity

It is intentionally minimal.

---

## Requirements

* PHP 8.3+
* Composer 2+

---

## Installation

Install a local copy with the instructions below.

### 1. Install LAMP Stack

It is assumed you already know how to install a LAMP stack.
Laragon is recommended because it simplifies environment setup. It is portable, isolated, fast, and tailored for PHP development with MySQL.

Download: https://laragon.org/download/

---

### 2. Install Composer

Installation guide: https://getcomposer.org/download/

---

### 3. Setup Server

1. Create a dedicated directory for hosting Clara files
2. Clone or copy Clara into the directory
3. Run:

```bash
composer install
```

4. **Point your web server's document root to the `public/` directory** — not the project root.

   In Laragon: Menu → Apache → `sites-enabled/auto.clara.test.conf` → set `DocumentRoot` to `C:/laragon/www/clara/public` and update the `<Directory>` path to match.

   This prevents direct HTTP access to source code, config files, and the database.

### 4. Run Locally (PHP Built-in Server)

For quick local testing (without Apache/Nginx), run:

```bash
php -S 127.0.0.1:8000 -t public
```

Then open `http://127.0.0.1:8000` in your browser.

---

## Project Structure

```
/clara
  composer.json            ← Dependency declarations and PSR‑4 autoload map
  /public                  ← Web server document root
    .htaccess              ← Rewrites all URLs to index.php
    index.php              ← Entry point: boots the framework
    favicon.ico
  /ephermal                ← Runtime data (e.g. SQLite database, not committed)
  /config
    app.php                ← Application + database configuration
    routes.php             ← All route definitions
  /src
    /core
      Bootstrap.php        ← Kicks off routing
      Router.php           ← Matches URLs to controller actions
      Request.php          ← Reads incoming HTTP data
      Response.php         ← Sends HTTP responses and renders views
      Controller.php       ← Base class all controllers extend
      DB.php               ← PDO database wrapper
      Route.php            ← Static facade for route registration
    /app
      /controllers         ← Your controllers (Home, Todos, _404)
      /models              ← Your models (Todo)
      /views               ← PHP view templates
  /vendor                  ← Composer‑managed packages (not committed)
```

Only the `public/` directory is exposed to the web. Everything above it — `src/`, `ephermal/`, `composer.json` — is inaccessible via HTTP.

---

## Dependencies

Clara uses two Composer packages. Understanding what they do is essential to understanding how Clara works.

### PHP‑DI (`php-di/php-di`)

**What it is:** A dependency injection (DI) container for PHP.

**Why Clara needs it:** Clara's core classes depend on each other. For example, `Router` needs `Request`, `Response`, and the `Container` itself. Instead of manually creating and passing these objects everywhere, Clara asks PHP‑DI's `Container` to build them automatically.

When you write:

```php
$container = new Container();
$router = $container->get(Router::class);
```

PHP‑DI reads `Router`'s constructor, sees it requires `Request`, `Response`, and `Container`, creates those first, then injects them into the `Router`. This is called **autowiring** — the container resolves the entire dependency tree for you.

The same pattern is used by `Application`: it asks the container for `Router` and other dependencies, then coordinates the request lifecycle from one clear place. This means every class gets exactly the collaborators it needs without a single manual `new` call for core services.

Autowiring extends to the application layer too. The `Todos` controller declares `Todo` as a constructor dependency. PHP‑DI sees `Todo` requires `DB`, resolves `DB` first, then injects it into `Todo`, then injects `Todo` into `Todos`. The entire dependency chain is resolved automatically.

**Where to see it:**

* `bootstrap/app.php` + `Application` — container creation and route registration
* `Router::dispatch()` — `$this->container->get($controller)` to instantiate controllers
* `Todos` controller — `Todo` model injected via constructor, which itself receives `DB`

---

### Kint (`kint-php/kint`)

**What it is:** A debugging tool for PHP. It provides `d()` and `dd()` helper functions.

**Why Clara needs it:** During development, calling `d($variable)` displays a rich, interactive dump of any variable directly in the browser. `dd()` does the same but halts execution immediately after. It replaces messy `var_dump()` / `print_r()` calls with something far more readable.

**How to use it:** Call `d()` or `dd()` anywhere in your code:

```php
public function index(): void
{
    d($this->request);   // Dump and continue
    dd($someData);        // Dump and die
}
```

---

### PSR‑4 Autoloading

In `composer.json`, the autoload section maps the `Clara\` namespace to the `src/` directory:

```json
"autoload": {
    "psr-4": {
        "Clara\\": "src/"
    }
}
```

This means a class like `Clara\core\Router` is expected to live at `src/core/Router.php`. Composer generates the autoloader in `vendor/autoload.php`, so any class following this convention is loaded automatically — no manual `require` statements needed for your own classes.

---

## How It Works — The Full Request Lifecycle

This is the core of Clara. Every HTTP request follows this exact path from browser to screen. Read the files alongside this guide to see each step in the actual code.

### Step 1 · URL Rewriting (`public/.htaccess`)

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [QSA,L]
```

The `.htaccess` file lives inside `public/`, which is the web server's document root. Apache's `mod_rewrite` intercepts every incoming request. If the URL does not point to an existing file or directory on disk within `public/`, it silently forwards the request to `public/index.php`. This is called the **front controller pattern** — one file handles all requests regardless of URL.

Because only `public/` is exposed, requests like `/config/app.php` never reach the filesystem — Apache looks inside `public/` for that path, finds nothing, and routes to `index.php` instead.

---

### Step 2 · Entry Point (`public/index.php`)

```php
define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';

$app = require BASE_PATH . '/bootstrap/app.php';
$app->run();
```

This file lives in `public/` and executes top to bottom:

1. **BASE_PATH** — `dirname(__DIR__)` resolves to the project root (one level above `public/`). Every other file uses this constant, so paths are always relative to the project root.
2. **Autoloader** — Loads Composer's autoloader so all `Clara\*` classes and vendor packages resolve automatically.
3. **Application bootstrap** — Loads `bootstrap/app.php`, which boots the container, registers the router in the `Route` facade, and loads route definitions.
4. **Run** — `$app->run()` dispatches the current request through the router.

---

### Step 3 · Configuration (`config/app.php`)

```php
return [
    'app' => [
        'name' => 'Clara',
    ],
    'database' => [
        'dsn' => 'sqlite:' . BASE_PATH . '/ephermal/db.sqlite',
        'username' => null,
        'password' => null,
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ],
    ],
];
```

Configuration is defined as a returned PHP array, similar to Laravel-style config files. The `dsn` string is what PDO expects, so switching databases only means changing values in this file. For MySQL, it would look like:

```php
return [
    'database' => [
        'dsn' => 'mysql:host=localhost;dbname=clara;charset=utf8mb4',
        'username' => 'root',
        'password' => '',
        'options' => [],
    ],
];
```

SQLite setup can be handled at bootstrap time by checking whether the DSN starts with `sqlite:` and creating the directory if needed. The path uses `BASE_PATH` instead of a hardcoded `__DIR__` chain.

---

### Step 4 · Registering Routes (`config/routes.php`)

```php
use Clara\core\Route;

use Clara\app\controllers\Home;
use Clara\app\controllers\Todos;

Route::get('/', [Home::class, 'index']);

Route::get('/todos', [Todos::class, 'index']);
Route::post('/todos', [Todos::class, 'store']);
Route::post('/todos/toggle', [Todos::class, 'toggle']);
Route::post('/todos/delete', [Todos::class, 'delete']);
```

Routes are registered using the static `Route` facade. The format is:

```
Route::{method}(path, [ControllerClass::class, 'actionMethod']);
```

* **method** — `get` or `post` (matches the HTTP method).
* **path** — The URL path to match (e.g. `/todos`).
* **handler** — A class-method pair, e.g. `[Home::class, 'index']`, which points to a concrete controller action.

`Route` is a thin static facade over the `Router` instance. Each call like `Route::get(...)` delegates to `$router->get(...)` internally. The `Route` class is initialized with the `Router` instance inside `Application` during bootstrap.

Routes are stored in a simple array inside the `Router`. They are not executed here — just registered for later matching.

---

### Step 5 · Application Bootstrap (`src/core/Application.php`)

`Application` centralizes bootstrapping in a Laravel-like way while staying lean:

* Build the dependency container
* Register `DB` bindings
* Create `Router` and hand it to the `Route` facade
* Load `config/routes.php`
* Run `dispatch()` through `$app->run()`

This keeps `public/index.php` minimal and easy to reason about.

---

### Step 6 · Routing (`src/core/Router.php`)

This is the heart of Clara. When `dispatch()` is called, the following happens:

#### 6a. Read the Request

```php
$method = $this->request->method() === 'HEAD' ? 'GET' : $this->request->method();
$path = $this->normalizePath($this->request->path());
```

The `Request` object reads `$_SERVER['REQUEST_METHOD']` and `$_SERVER['REQUEST_URI']`, giving the router the HTTP method (`GET`, `POST`) and the clean path (`/todos`). `HEAD` requests are treated as `GET`.

#### 6b. Find a Matching Route

```php
$match = $this->findRoute($method, $path);
```

`findRoute()` loops through the registered `$this->routes` array and looks for an entry where both the method and path match. If found, it returns the route array. If not, it returns `null` and a 404 status is set.

#### 6c. Resolve the Handler

```php
[$controller, $action] = $this->resolveHandler($match['handler'] ?? self::NOT_FOUND_HANDLER);
```

`resolveHandler()` normalizes handlers into `[ControllerClass, 'method']` format.

* It accepts explicit class-method arrays (Laravel-style route handlers)
* It still supports legacy `'Controller@method'` strings for compatibility
* If no route matched, the fallback `_404@index` is used instead.

#### 6d. Instantiate the Controller

```php
$invoked = $this->container->get($controller);
```

PHP‑DI creates (or retrieves) the controller instance. Since every controller extends the base `Controller` class, the container autowires `Request`, `Response`, and optionally `DB` into the controller's constructor.

#### 6e. Call the Action

```php
$invoked->{$action}();
```

The target method is called on the controller instance. This is where your application logic runs. If the method doesn't exist on the resolved controller, the router falls back to `_404@index`.

---

### Step 7 · The Request Object (`src/core/Request.php`)

`Request` is a clean wrapper around PHP's superglobals:

| Method | Reads from | Purpose |
|---|---|---|
| `get($key)` | `$_GET` | Query string parameters |
| `post($key)` | `$_POST` | Form body fields |
| `files($key)` | `$_FILES` | Uploaded files |
| `session($key)` | `$_SESSION` | Session data |
| `cookie($key)` | `$_COOKIE` | Cookie values |
| `server($key)` | `$_SERVER` | Server/environment info |
| `body()` | `php://input` | Raw request body |
| `method()` | `$_SERVER['REQUEST_METHOD']` | HTTP method (GET, POST) |
| `uri()` | `$_SERVER['REQUEST_URI']` | Full URI including query string |
| `path()` | Parsed from URI | Clean path without query string |

Every method accepts a `$default` parameter returned when the key is missing. The private `search()` method handles the lookup with `array_key_exists`.

---

### Step 8 · The Base Controller (`src/core/Controller.php`)

```php
abstract class Controller
{
    public function __construct(
        protected readonly Request $request,
        protected readonly Response $response,
        protected readonly ?DB $db = null,
    ) {}
}
```

Every controller you write extends this class. PHP‑DI injects `Request` and `Response` automatically. `DB` is optional (nullable) — it's injected only if the database is configured and available.

The base controller provides shorthand methods so your controllers stay clean:

| Method | Delegates to | What it does |
|---|---|---|
| `$this->view(name, data)` | `Response::view()` | Render a view template with data |
| `$this->setStatus(code)` | `Response::setStatus()` | Set the HTTP status code |
| `$this->setHeader(k, v)` | `Response::setHeader()` | Set a response header |
| `$this->get(key)` | `Request::get()` | Read a `$_GET` parameter |
| `$this->post(key)` | `Request::post()` | Read a `$_POST` parameter |
| `$this->session(key)` | `Request::session()` | Read a session value |
| `$this->cookie(key)` | `Request::cookie()` | Read a cookie value |

---

### Step 9 · The Response Object (`src/core/Response.php`)

`Response` handles everything sent back to the browser.

**Setting status and headers:**

```php
$this->response->setStatus(404);
$this->response->setHeader('Content-Type', 'application/json');
```

These are stored internally and sent when `send()` is called. `send()` emits the HTTP status line and all queued headers via PHP's `header()` function.

**Rendering views:**

```php
$this->response->view('home.index', ['message' => 'Hello World']);
```

1. Calls `send()` to flush status and headers.
2. Calls `extract($data, EXTR_SKIP)` to turn the `$data` array into local variables. The key `'message'` becomes a `$message` variable.
3. Uses `require` to load the view file at `BASE_PATH . '/src/app/views/home.index.php'`. Because `extract` ran first, `$message` is available inside that template.

The dot in the view name (`home.index`) maps directly to a filename: `home.index.php`. The path is resolved via `BASE_PATH` instead of relative `__DIR__` chains.

**Redirecting:**

```php
$this->response->redirect('/todos');  // 302 redirect to /todos
$this->response->back();              // Redirect to the previous page
```

Both methods set a `Location` header, send it, and immediately `exit` to prevent further execution.

---

### Step 10 · Writing a Controller

Here is the `Home` controller as an example:

```php
class Home extends Controller
{
    public function index(): void
    {
        $this->view('home.index', [
            'message' => 'Hello World',
        ]);
    }
}
```

1. Extends `Controller`, so `$this->request`, `$this->response`, and `$this->db` are available.
2. Defines an `index()` method, matching the `Home@index` handler registered in `routes.php`.
3. Calls `$this->view()` to render `src/app/views/home.index.php`, passing `$message = 'Hello World'` into the template.

---

### Step 11 · Writing a Model (`src/app/models/Todo.php`)

Models handle data access. They receive the `DB` instance via constructor injection — no manual connection setup:

```php
class Todo
{
    public function __construct(private readonly DB $db)
    {
        $this->migrate();
    }

    private function migrate(): void
    {
        $this->db->exec('CREATE TABLE IF NOT EXISTS todos (...)');
    }

    public function all(): array
    {
        return $this->db->run('SELECT * FROM todos ORDER BY created_at DESC')->fetchAll();
    }

    public function create(string $title): void
    {
        $this->db->run('INSERT INTO todos (title) VALUES (:title)', ['title' => $title]);
    }

    // toggleComplete() and delete() follow the same pattern
}
```

Models are plain classes. There is no base `Model` class. The `DB` dependency is injected by PHP‑DI using values from `config/app.php`. Models use `$this->db->run()` for all queries — the same `run()` helper that handles both simple queries and parameterized statements.

The `ephermal/` directory (used for SQLite storage) sits outside `src/` to separate runtime data from source code. It is listed in `.gitignore` so the database file is never committed. `public/index.php` can auto-create this directory when the DSN uses SQLite.

---

### Step 12 · Writing a View

Views are plain PHP files that output HTML. Data passed via `$this->view(name, data)` is available as local variables:

```php
<!-- src/app/views/home.index.php -->
<h1><?= htmlspecialchars($message) ?></h1>
<p>Welcome to Clara.</p>
```

* Use `<?= ?>` for echoing and `<?php ?>` for logic.
* Always escape output with `htmlspecialchars()` to prevent XSS.
* The view naming convention is `controller.action.php` (e.g. `home.index.php`, `todos.index.php`).

---

### Step 13 · 404 Handling

If no route matches the request, Clara falls back to the `_404` controller:

```php
class _404 extends Controller
{
    public function index(): void
    {
        $this->setStatus(404);
        $this->view('_404.index');
    }
}
```

The `Router` triggers this automatically in two cases:

1. No route matched the requested method + path combination.
2. A route matched, but the specified action method does not exist on the controller.

---

### Step 14 · The DB Wrapper (`src/core/DB.php`)

```php
class DB extends PDO
{
    public function __construct(string $dsn, ?string $username = null, ?string $password = null, array $options = [])
    {
        parent::__construct($dsn, $username, $password, $options);
    }

    public function run(string $sql, array $args = []): PDOStatement|false { ... }
}
```

`DB` now receives PDO connection parameters directly in its constructor (`dsn`, `username`, `password`, `options`) and passes them to `PDO`. It works with any PDO-supported driver — SQLite, MySQL, PostgreSQL — controlled entirely by config values.

`DB` extends PHP's native `PDO` class and accepts whichever PDO options you provide in config. A common default set is:

* **Exception mode** — Errors throw exceptions instead of silent failures.
* **Associative fetch** — Query results return associative arrays by default.
* **Real prepared statements** — Emulated prepares are disabled for security.

The `run()` method simplifies queries:

```php
// Simple query (no parameters)
$this->db->run('SELECT * FROM users');

// Parameterized query (safe from SQL injection)
$this->db->run('SELECT * FROM users WHERE id = :id', ['id' => 1]);
```

If you pass parameters, it uses `prepare()` + `execute()`. If not, it uses `query()` directly.

---

## Putting It All Together

Here is the complete lifecycle for a `GET /todos` request:

```
Browser → GET /todos
  ↓
.htaccess → No file called "todos" exists → forward to index.php
  ↓
index.php → Load autoloader, config, create Container, create Router, load routes
  ↓
Bootstrap → $router->dispatch()
  ↓
Router → Request says method=GET, path=/todos
       → findRoute() matches: {method: GET, path: /todos, handler: [Todos::class, 'index']}
       → resolveHandler(...) keeps the class/method pair
       → $container->get(Todos::class) → autowires Request, Response, DB, and Todo
       → $invoked->index()
  ↓
Todos::index() → $this->todo->all() fetches rows via injected DB
              → $this->view('todos.index', ['todos' => $rows])
  ↓
Response::view() → send() emits HTTP 200 + headers
               → extract() turns ['todos' => $rows] into $todos variable
               → require('src/app/views/todos.index.php')
  ↓
View → Renders HTML using $todos → sent to browser
```

---

## Usage

* Configuration files: `config/`
* Core framework files: `src/core/`
* Controllers: `src/app/controllers/`
* Models: `src/app/models/`
* Views: `src/app/views/`

Clara follows a traditional MVC separation while keeping the internal flow explicit and easy to trace.

---

Clara is not about scale.
It is about understanding.

Not about abstraction layers.
About seeing the layers that already exist.

Build with it. Break it. Learn from it.
