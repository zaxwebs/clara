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

---

## Project Structure

```
/clara
  .htaccess              ← Rewrites all URLs to index.php
  index.php              ← Entry point: boots the framework
  composer.json          ← Dependency declarations and PSR‑4 autoload map
  /ephermal              ← Runtime data (e.g. SQLite database)
  /src
    /setup
      config.php         ← App name and database credentials
      routes.php         ← All route definitions
    /core
      Bootstrap.php      ← Kicks off routing
      Router.php         ← Matches URLs to controller actions
      Request.php        ← Reads incoming HTTP data
      Response.php       ← Sends HTTP responses and renders views
      Controller.php     ← Base class all controllers extend
      DB.php             ← PDO database wrapper
    /app
      /controllers       ← Your controllers (Home, Todos, _404)
      /models            ← Your models (Todo)
      /views             ← PHP view templates
  /vendor                ← Composer‑managed packages
```

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

The same thing happens when `Bootstrap` is created: the container sees it needs `Router` and `Response`, and injects the same instances it already built. This means every class gets exactly the collaborators it needs without a single manual `new` call for core services.

**Where to see it:**

* `index.php` — `$container->get(Router::class)` and `$container->get(Bootstrap::class)`
* `Router::dispatch()` — `$this->container->get($controller)` to instantiate controllers

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

### Step 1 · URL Rewriting (`.htaccess`)

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [QSA,L]
```

Apache's `mod_rewrite` intercepts every incoming request. If the URL does not point to an existing file or directory on disk, it silently forwards the request to `index.php`. This is called the **front controller pattern** — one file handles all requests regardless of URL.

For example, a request to `/todos` does not look for a file called `todos`. Apache sees no such file exists, so it loads `index.php` instead. The original URL (`/todos`) is preserved in the `REQUEST_URI` server variable for PHP to read later.

---

### Step 2 · Entry Point (`index.php`)

```php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/setup/config.php';

$container = new Container();
$router = $container->get(Router::class);

require_once __DIR__ . '/src/setup/routes.php';

$container->get(Bootstrap::class);
```

This file executes top to bottom:

1. **Autoloader** — Loads Composer's autoloader so all `Clara\*` classes and vendor packages resolve automatically.
2. **Config** — Loads `config.php`, defining constants like `APP_NAME`, `DB_HOST`, `DB_NAME`, etc.
3. **Container** — Creates the PHP‑DI container (the dependency injection engine).
4. **Router** — Asks the container for a `Router` instance. PHP‑DI autowires its dependencies. The freshly created `$router` is now available as a local variable.
5. **Routes** — Loads `routes.php`, which calls `$router->get(...)` and `$router->post(...)` to register route definitions.
6. **Bootstrap** — Asks the container for a `Bootstrap` instance, which triggers `$this->router->dispatch()` inside its constructor. The application is now running.

---

### Step 3 · Configuration (`src/setup/config.php`)

```php
const APP_NAME = 'Clara';

const DB_HOST = 'localhost';
const DB_NAME = 'clara';
const DB_CHAR = 'utf8mb4';
const DB_USER = 'root';
const DB_PASS = '';
```

Simple PHP constants. They are globally available once this file is loaded. `DB.php` references these constants to build its PDO connection string.

---

### Step 4 · Registering Routes (`src/setup/routes.php`)

```php
$router->get('/', 'Home@index');

$router->get('/todos', 'Todos@index');
$router->post('/todos', 'Todos@store');
$router->post('/todos/toggle', 'Todos@toggle');
$router->post('/todos/delete', 'Todos@delete');
```

Each line registers a route on the `$router` instance. The format is:

```
$router->{method}(path, 'ControllerName@actionMethod');
```

* **method** — `get` or `post` (matches the HTTP method).
* **path** — The URL path to match (e.g. `/todos`).
* **handler** — A string in `Controller@action` format. `Home@index` means: call the `index()` method on the `Home` controller.

Routes are stored in a simple array inside the `Router`. They are not executed here — just registered for later matching.

---

### Step 5 · Bootstrapping (`src/core/Bootstrap.php`)

```php
final class Bootstrap
{
    public function __construct(
        private readonly Router $router,
        private readonly Response $response,
    ) {
        $this->router->dispatch();
    }
}
```

`Bootstrap` is tiny by design. Its only job is to trigger `$this->router->dispatch()`. Because PHP‑DI creates `Bootstrap` via `$container->get(Bootstrap::class)`, the `Router` and `Response` dependencies are automatically injected — the same instances that were created earlier. The constructor fires `dispatch()` immediately, kicking off route resolution.

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

`resolveHandler()` splits the handler string (e.g. `'Todos@index'`) by the `@` symbol:

* `'Todos'` → fully qualified class `\Clara\app\controllers\Todos`
* `'index'` → the method to call

If no route matched, the fallback `_404@index` is used instead.

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
3. Uses `require` to load the view file at `src/app/views/home.index.php`. Because `extract` ran first, `$message` is available inside that template.

The dot in the view name (`home.index`) maps directly to a filename: `home.index.php`.

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

Models handle data access. The `Todo` model connects to an SQLite database and provides CRUD methods:

```php
class Todo
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = new PDO('sqlite:' . __DIR__ . '/../../../ephermal/db.sqlite');
        $this->migrate(); // Auto-creates the todos table if missing
    }

    public function all(): array { /* SELECT * FROM todos */ }
    public function create(string $title): void { /* INSERT */ }
    public function toggleComplete(int $id): void { /* UPDATE */ }
    public function delete(int $id): void { /* DELETE */ }
}
```

Models are plain classes. There is no base `Model` class — they connect to the database directly via PDO. The `Todo` model uses SQLite and auto-migrates on construction, creating the `todos` table if it does not exist.

The `ephermal/` directory sits outside `src/` to separate runtime data from source code. It is listed in `.gitignore` so the database file is never committed.

> **Note:** Clara also provides a `DB` core class (`src/core/DB.php`) that wraps PDO with a `run()` helper for MySQL connections. Models can use either approach depending on needs.

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
    public function __construct() { /* Reads DB_HOST, DB_NAME, etc. from config.php */ }
    public function run(string $sql, array $args = []): PDOStatement|false { ... }
}
```

`DB` extends PHP's native `PDO` class, preconfigured with sensible defaults:

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
       → findRoute() matches: {method: GET, path: /todos, handler: 'Todos@index'}
       → resolveHandler('Todos@index') → [\Clara\app\controllers\Todos, 'index']
       → $container->get(Todos::class) → autowires Request + Response into constructor
       → $invoked->index()
  ↓
Todos::index() → Creates Todo model → $todo->all() fetches rows from SQLite
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

* Configuration files: `src/setup/`
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
