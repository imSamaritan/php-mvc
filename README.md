# PHP MVC Framework

A lightweight, custom-built MVC framework written in PHP from scratch. It features a regex-based router, a reflection-powered dependency injection container, a template viewer, a custom `.env` loader, and a structured exception handling system — all without any third-party dependencies.

---

## Table of Contents

- [Requirements](#requirements)
- [Project Structure](#project-structure)
- [Getting Started](#getting-started)
- [Configuration](#configuration)
- [Architecture Overview](#architecture-overview)
  - [Entry Point](#entry-point)
  - [Router](#router)
  - [Dispatcher](#dispatcher)
  - [DI Container](#di-container)
  - [Viewer](#viewer)
  - [Exception Handler](#exception-handler)
  - [Database](#database)
- [Routing](#routing)
  - [Defining Routes](#defining-routes)
  - [Route Parameters](#route-parameters)
  - [Namespaced Routes](#namespaced-routes)
- [Controllers](#controllers)
  - [Creating a Controller](#creating-a-controller)
  - [Namespaced Controllers](#namespaced-controllers)
- [Models](#models)
  - [Creating a Model](#creating-a-model)
- [Views](#views)
  - [Rendering Views](#rendering-views)
  - [Shared Layouts](#shared-layouts)
- [Dependency Injection](#dependency-injection)
  - [Auto-wiring](#auto-wiring)
  - [Registering Services](#registering-services)
- [Error Handling](#error-handling)
- [Available Routes](#available-routes)

---

## Requirements

- PHP **8.1** or higher
- Apache with **mod_rewrite** enabled
- MySQL (for database features)
- A `.env` file at the project root (see [Configuration](#configuration))

---

## Project Structure

```
php-mvc/
├── config/
│   ├── routes.php           # Route definitions
│   └── services.php         # DI container service registrations
├── logs/
│   └── errors.log           # Production error log (auto-generated)
├── public/
│   ├── css/
│   │   └── style.css        # Global stylesheet
│   ├── .htaccess            # Rewrites all requests to index.php
│   └── index.php            # Application entry point
├── src/
│   ├── App/
│   │   ├── Controllers/
│   │   │   ├── Role/
│   │   │   │   └── Moderator.php
│   │   │   ├── AdminUser.php
│   │   │   ├── Blogs.php
│   │   │   └── Home.php
│   │   ├── Models/
│   │   │   └── Blog.php
│   │   └── Database.php
│   ├── Core/
│   │   ├── Exceptions/
│   │   │   ├── PageNotFoundException.php
│   │   │   └── UrlMailformedException.php
│   │   ├── Container.php
│   │   ├── Dispatcher.php
│   │   ├── DotEnv.php
│   │   ├── ExceptionHandler.php
│   │   ├── Router.php
│   │   └── Viewer.php
│   └── autoload.php
├── views/
│   ├── blogs/
│   │   ├── index.php
│   │   └── show.php
│   ├── home/
│   │   └── index.php
│   ├── shared/
│   │   ├── header.php
│   │   └── footer.php
│   ├── 404.php
│   ├── 500.php
│   └── malformed-url.php
├── .htaccess                # Redirects root traffic into public/
├── .env                     # Environment variables (not committed)
├── BUGS.md                  # Known bugs and their fixes
└── README.md
```

---

## Getting Started

**1. Clone the repository:**

```
git clone https://github.com/your-username/php-mvc.git
cd php-mvc
```

**2. Configure your web server:**

Point your Apache virtual host document root to the `public/` directory, or place the project under your web server's root and rely on the root `.htaccess` to redirect traffic into `public/`.

Example Apache virtual host:

```
<VirtualHost *:80>
    ServerName php-mvc.local
    DocumentRoot /path/to/php-mvc/public
    <Directory /path/to/php-mvc/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**3. Set up the `.env` file:**

Copy the example below and save it as `.env` in the project root:

```
DB_HOST=127.0.0.1
DB_USER=root
DB_PASSWORD=secret
DB_DATABASE=php_mvc
SHOW_ERROR=true
```

**4. Set up the database:**

Create the database and a sample `blogs` table:

```sql
CREATE DATABASE php_mvc;

USE php_mvc;

CREATE TABLE blogs (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    body  TEXT NOT NULL
);

INSERT INTO blogs (title, body) VALUES
('First Post', 'This is the first blog post.'),
('Second Post', 'This is the second blog post.');
```

**5. Visit the app in your browser:**

```
http://php-mvc.local/
```

---

## Configuration

All configuration is driven by the `.env` file located at the project root. It is loaded into `$_ENV` on every request by the `DotEnv` class.

| Key          | Description                                              | Example       |
|--------------|----------------------------------------------------------|---------------|
| `DB_HOST`    | Database host                                            | `127.0.0.1`   |
| `DB_USER`    | Database username                                        | `root`        |
| `DB_PASSWORD`| Database password                                        | `secret`      |
| `DB_DATABASE`| Database name                                            | `php_mvc`     |
| `SHOW_ERROR` | Set to `true` for development, `false` for production    | `true`        |

> **Note:** Never commit your `.env` file to version control. Add it to `.gitignore`.

When `SHOW_ERROR=false`, errors are written to `logs/errors.log` and a user-friendly error page is shown instead of a stack trace.

---

## Architecture Overview

### Entry Point

`public/index.php` bootstraps the entire application in the following order:

1. Defines the `ROOT_PATH` constant.
2. Registers the custom autoloader (`src/autoload.php`).
3. Registers the global error and exception handlers.
4. Loads the `.env` file via `Core\DotEnv`.
5. Parses the incoming URL path.
6. Loads the router from `config/routes.php`.
7. Loads the DI container from `config/services.php`.
8. Creates a `Core\Dispatcher` and dispatches the request.

---

### Router

**File:** `src/Core/Router.php`

The router stores a routing table and matches an incoming URL path against it using regular expressions with named capture groups.

Routes are registered via `$router->add(string $path, array $params = [])`.

Each route path segment can be:
- A **static string** — matched literally (e.g. `home`, `blogs`).
- A **plain wildcard** — `{controller}`, `{action}` matched against `[a-z-]+`.
- A **constrained wildcard** — `{id:\d+}` using a custom regex pattern.

---

### Dispatcher

**File:** `src/Core/Dispatcher.php`

Once a route is matched, the Dispatcher:

1. Converts the matched `controller` param into a fully-qualified class name under `App\Controllers`.
2. Converts the matched `action` param into a camelCase method name (e.g. `print-role` → `printRole`).
3. Resolves the controller instance via the DI Container.
4. Uses `ReflectionMethod` to inspect the action method's parameters.
5. Type-casts route params to match the method's signature and calls the method.

---

### DI Container

**File:** `src/Core/Container.php`

The container resolves class dependencies automatically using PHP's `ReflectionClass`. It supports:

- **Auto-wiring** — recursively resolves constructor dependencies that are non-primitive types.
- **Registry** — manually registered closures for classes that require primitive constructor arguments (e.g. `Database`).

See [Dependency Injection](#dependency-injection) for usage examples.

---

### Viewer

**File:** `src/Core/Viewer.php`

The Viewer renders PHP view files using output buffering. Data is passed as an associative array and extracted into local variables inside the view scope.

```php
$viewer->render('blogs/index', ['blogs' => $blogs]);
```

---

### Exception Handler

**File:** `src/Core/ExceptionHandler.php`

Registered globally via `set_error_handler` and `set_exception_handler`. It:

- Converts PHP errors into `ErrorException` instances.
- Maps exception types to HTTP status codes and error views:
  - `PageNotFoundException` → `404`
  - `UrlMailformedException` → `400`
  - Everything else → `500`
- In **development** (`SHOW_ERROR=true`): displays full error output.
- In **production** (`SHOW_ERROR=false`): renders a user-friendly error page and logs the error to `logs/errors.log`.

---

### Database

**File:** `src/App/Database.php`

A thin wrapper around PHP's PDO. The connection is created **lazily** on the first call to `connect()` and reused for the lifetime of the object. Configuration is read from the `.env` file.

PDO is configured with:
- `ERRMODE_EXCEPTION` — throws exceptions on query errors.
- `FETCH_OBJ` — returns rows as `stdClass` objects by default.

---

## Routing

### Defining Routes

Routes are defined in `config/routes.php`:

```php
$router->add("/", ["controller" => "home", "action" => "index"]);
$router->add("/{controller}/{action}");
$router->add("/{controller}/{action}/{id:\d+}");
```

Routes are matched in the **order they are defined**. The first match wins.

---

### Route Parameters

| Syntax               | Description                                      | Example match      |
|----------------------|--------------------------------------------------|--------------------|
| `{controller}`       | Any lowercase hyphenated string                  | `blogs`, `home`    |
| `{action}`           | Any lowercase hyphenated string                  | `index`, `show`    |
| `{id:\d+}`           | Custom regex constraint (digits only)            | `1`, `42`          |
| `{controller:home\|blogs}` | Pipe-separated allowed values            | `home`, `blogs`    |

Route parameter values are automatically **type-cast** to match the receiving controller method's parameter types (e.g. `int $id` will receive an integer).

---

### Namespaced Routes

A route can specify a `namespace` key to resolve the controller from a sub-namespace of `App\Controllers`:

```php
$router->add("/{controller:moderator}/{action:print-role}", [
    "namespace" => "role",
]);
```

This resolves to `App\Controllers\Role\Moderator::printRole()`.

---

## Controllers

Controllers live in `src/App/Controllers/` and map to URL segments automatically.

### Creating a Controller

1. Create a PHP file in `src/App/Controllers/`.
2. Use the `App\Controllers` namespace.
3. Name the class to match the URL segment (PascalCase).
4. Define public methods for each action.

**Example:** `src/App/Controllers/Posts.php`

```php
<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Viewer;

class Posts
{
    public function __construct(private Viewer $viewer) {}

    public function index(): void
    {
        echo $this->viewer->render("shared/header", ["title" => "Posts"]);
        echo $this->viewer->render("posts/index");
        echo $this->viewer->render("shared/footer");
    }

    public function show(int $id): void
    {
        echo $this->viewer->render("shared/header", ["title" => "Post #{$id}"]);
        echo $this->viewer->render("posts/show", ["id" => $id]);
        echo $this->viewer->render("shared/footer");
    }
}
```

This controller is reachable via:
- `GET /posts/index`
- `GET /posts/show/5`

---

### Namespaced Controllers

Place the controller inside a sub-folder of `Controllers/` and update the namespace accordingly:

```php
namespace App\Controllers\Role;

class Moderator
{
    public function printRole(): void
    {
        echo "Role: Moderator";
    }
}
```

Then register a namespaced route in `config/routes.php`:

```php
$router->add("/{controller:moderator}/{action:print-role}", [
    "namespace" => "role",
]);
```

Reachable via: `GET /moderator/print-role`

---

## Models

Models live in `src/App/Models/` and interact with the database via the `Database` class, which is injected through the container.

### Creating a Model

```php
<?php
declare(strict_types=1);

namespace App\Models;

use App\Database;

class Post
{
    public function __construct(private Database $db) {}

    public function findAll(): array|false
    {
        $connection = $this->db->connect();
        $statement = $connection->prepare("SELECT * FROM posts");

        if ($statement->execute()) {
            return $statement->fetchAll();
        }

        return false;
    }

    public function find(int $id): object|false
    {
        $connection = $this->db->connect();
        $statement = $connection->prepare("SELECT * FROM posts WHERE id = :id");

        if ($statement->execute(["id" => $id])) {
            return $statement->fetch();
        }

        return false;
    }
}
```

Inject the model into a controller via the constructor — the container will wire everything automatically:

```php
class Posts
{
    public function __construct(private Post $post, private Viewer $viewer) {}
}
```

---

## Views

Views are plain PHP files stored in the `views/` directory.

### Rendering Views

Use `Core\Viewer::render(string $view, array $data = [])` to render a view. The `$data` array keys become local variables inside the view file.

```php
echo $this->viewer->render("blogs/show", ["blog" => $blog]);
```

Inside `views/blogs/show.php`:

```php
<h1><?php echo $blog->title; ?></h1>
<p><?php echo $blog->body; ?></p>
```

### Shared Layouts

The layout is split into a header and footer partial, both stored in `views/shared/`:

```php
echo $this->viewer->render("shared/header", ["title" => "Page Title"]);
echo $this->viewer->render("your/view", $data);
echo $this->viewer->render("shared/footer");
```

---

## Dependency Injection

### Auto-wiring

The container automatically resolves any class whose constructor only depends on other non-primitive types:

```php
// Viewer has no constructor — resolved automatically
$viewer = $container->get(Core\Viewer::class);

// Blog depends on Database — resolved automatically if Database is in the registry
$blog = $container->get(App\Models\Blog::class);
```

### Registering Services

For classes whose constructors require primitive values (e.g. strings, integers), register them manually in `config/services.php` using a closure:

```php
$container->save(App\Database::class, function () {
    return new App\Database(
        $_ENV["DB_HOST"],
        $_ENV["DB_USER"],
        $_ENV["DB_PASSWORD"],
        $_ENV["DB_DATABASE"],
    );
});
```

---

## Error Handling

The framework maps exceptions to HTTP responses automatically:

| Exception                  | HTTP Status | View                    |
|----------------------------|-------------|-------------------------|
| `PageNotFoundException`    | `404`       | `views/404.php`         |
| `UrlMailformedException`   | `400`       | `views/malformed-url.php` |
| Any other exception        | `500`       | `views/500.php`         |

To trigger a 404 manually from a controller:

```php
use Core\Exceptions\PageNotFoundException;

throw new PageNotFoundException("Blog with ID {$id} was not found!");
```

---

## Available Routes

| Method | URL                          | Controller                        | Action       |
|--------|------------------------------|-----------------------------------|--------------|
| GET    | `/`                          | `App\Controllers\Home`            | `index`      |
| GET    | `/home`                      | `App\Controllers\Home`            | `index`      |
| GET    | `/blogs`                     | `App\Controllers\Blogs`           | `index`      |
| GET    | `/blogs/show/{id}`           | `App\Controllers\Blogs`           | `show`       |
| GET    | `/moderator/print-role`      | `App\Controllers\Role\Moderator`  | `printRole`  |
| GET    | `/{controller}/{action}`     | Dynamic                           | Dynamic      |
| GET    | `/{controller}/{action}/{id}`| Dynamic                           | Dynamic      |

---

## License

This project is open-sourced under the [MIT License](https://opensource.org/licenses/MIT).