# Bug Report

A list of all identified bugs in the php-mvc project that need to be resolved.

---

## ✅ Bug #1 — `Dispatcher::getControllerName` mutates instance state [FIXED]

**File:** `src/Core/Dispatcher.php`

**Description:**
`$this->namespace` is appended to on every call to `getControllerName()` when a route has a `namespace` param. In a long-running process or during testing, repeated calls would produce a corrupted namespace like `App\Controllers\Role\Role\Role...`.

**Affected code:**
```php
if (array_key_exists("namespace", $params)) {
  $this->namespace .= "\\" . ucwords($params["namespace"]);
}
```

**Fix:**
Use a local variable instead of mutating `$this->namespace`:
```php
$namespace = $this->namespace;
if (array_key_exists("namespace", $params)) {
  $namespace .= "\\" . ucwords($params["namespace"]);
}
return $namespace . "\\" . $controller_name;
```

---

## ✅ Bug #2 — `ExceptionHandler` re-throws exception after rendering in production [FIXED]

**File:** `src/Core/ExceptionHandler.php`

**Description:**
`throw $exception` is placed outside the `if/else` block, so it runs in **both** dev and production modes. In production, the error view is rendered and then the exception is re-thrown, which triggers PHP's default fatal error handler and can result in double output or broken responses.

**Affected code:**
```php
if ($show_errors) {
    // dev
} else {
    // prod: renders error view
    echo $viewer->render(...);
}
throw $exception; // ← runs in both modes
```

**Fix:**
Call `exit` after rendering in production mode so the re-throw only happens in development:
```php
if ($show_errors) {
    throw $exception;
} else {
    echo $viewer->render(...);
    exit;
}
```

---

## 🐛 Bug #3 — `SHOW_ERROR` env variable string `"false"` is truthy

**File:** `src/Core/ExceptionHandler.php`

**Description:**
When `.env` contains `SHOW_ERROR=false`, `$_ENV["SHOW_ERROR"]` holds the **string** `"false"`. In PHP, any non-empty string is truthy, so the app always runs in development mode regardless of the env value.

**Affected code:**
```php
$show_errors = $_ENV["SHOW_ERROR"] ?? true;
```

**Fix:**
Perform an explicit string comparison:
```php
$show_errors = ($_ENV["SHOW_ERROR"] ?? "true") !== "false";
```

---

## ✅ Bug #4 — `DotEnv::load` crashes on empty lines and comment lines [FIXED]

**File:** `src/Core/DotEnv.php`

**Description:**
Any blank line or comment line (e.g. `# This is a comment`) in the `.env` file does not contain an `=` sign, causing `explode()` to return a 1-element array. The destructuring `[$key, $value] = $line` then throws an error.

**Affected code:**
```php
foreach ($file_lines as $line) {
    $line = explode("=", $line, 2);
    [$key, $value] = $line; // ← crashes on lines with no "="
    $_ENV[$key] = $value;
}
```

**Fix:**
Skip empty lines and comment lines before processing:
```php
foreach ($file_lines as $line) {
    $line = trim($line);
    if ($line === "" || str_starts_with($line, "#")) {
        continue;
    }
    [$key, $value] = explode("=", $line, 2);
    $_ENV[trim($key)] = trim($value);
}
```

---

## 🐛 Bug #5 — `Blogs::show` does not handle a missing blog

**File:** `src/App/Controllers/Blogs.php`

**Description:**
`Blog::find()` returns `false` when no record is found. The `show()` method does not check for this and immediately accesses `$blog->id`, causing a fatal error when an invalid ID is requested.

**Affected code:**
```php
public function show(int $id = 1): void
{
    $blog = $this->blog->find($id);
    // No null/false check!
    echo $this->viewer->render("shared/header", ["title" => "Blog #{$blog->id}"]);
```

**Fix:**
Check the return value and throw a `PageNotFoundException`:
```php
public function show(int $id = 1): void
{
    $blog = $this->blog->find($id);

    if ($blog === false) {
        throw new \Core\Exceptions\PageNotFoundException("Blog with ID {$id} was not found!");
    }

    echo $this->viewer->render("shared/header", ["title" => "Blog #{$blog->id}"]);
    echo $this->viewer->render("blogs/show", ["blog" => $blog]);
    echo $this->viewer->render("shared/footer");
}
```

---

## ✅ Bug #6 — `autoload.php` uses `require` instead of `require_once` [FIXED]

**File:** `src/autoload.php`

**Description:**
Using `require` instead of `require_once` in the autoloader means that if a class file is somehow loaded more than once, PHP will throw a fatal "Cannot redeclare class" error.

**Affected code:**
```php
require dirname(__DIR__) . "/src/{$module}.php";
```

**Fix:**
```php
require_once dirname(__DIR__) . "/src/{$module}.php";
```

---

## 🐛 Bug #7 — `Home` controller manually instantiates `Viewer` instead of injecting it

**File:** `src/App/Controllers/Home.php`

**Description:**
`Home` instantiates `Viewer` directly inside its constructor, bypassing the DI container. This is inconsistent with `Blogs`, which correctly receives `Viewer` via constructor injection, and makes `Home` harder to test.

**Affected code:**
```php
public function __construct()
{
    $this->viewer = new Viewer();
}
```

**Fix:**
Inject `Viewer` as a constructor dependency:
```php
public function __construct(private Viewer $viewer) {}
```

---

## 🐛 Bug #8 — Magic numbers used instead of named constants

**Files:** `public/index.php`, `src/Core/Router.php`

**Description:**
Magic numbers `5` and `2` are used instead of their named PHP constants, making the code harder to read and understand. There is also a typo in the comment (`ARRRAY_FILTER_USE_KEY`).

**Affected code:**
```php
// public/index.php
$url_path = parse_url($_SERVER["REQUEST_URI"], 5);

// src/Core/Router.php
$params = array_filter($matches, "is_string", 2); //2 ARRRAY_FILTER_USE_KEY
```

**Fix:**
```php
// public/index.php
$url_path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// src/Core/Router.php
$params = array_filter($matches, "is_string", ARRAY_FILTER_USE_KEY);
```

---

## ✅ Bug #9 — `Container` registry always creates a new instance (no singleton caching) [FIXED]

**File:** `src/Core/Container.php`

**Description:**
Every time a registered class (e.g. `Database`) is resolved, the registry closure is invoked and a **new instance** is returned. Two models that both depend on `Database` would each receive a separate `Database` object with its own PDO connection, wasting resources.

**Affected code:**
```php
if (array_key_exists($class, $this->registry)) {
    return $this->registry[$class](); // ← new instance on every call
}
```

**Fix:**
Cache the resolved instance after the first call:
```php
private array $resolved = [];

public function get(string $class): object
{
    if (array_key_exists($class, $this->resolved)) {
        return $this->resolved[$class];
    }

    if (array_key_exists($class, $this->registry)) {
        $this->resolved[$class] = $this->registry[$class]();
        return $this->resolved[$class];
    }
    // ... rest of the method
}
```

---

## ✅ Bug #10 — Typo in class name: `UrlMailformedException` [FIXED]

**File:** `src/Core/Exceptions/UrlMailformedException.php`

**Description:**
The class is named `UrlMailformedException` but the correct spelling is `UrlMalformedException` ("Malformed", not "Mailformed"). The view file `views/malformed-url.php` uses the correct spelling, creating an inconsistency throughout the codebase.

**Fix:**
Rename the file and class from `UrlMailformedException` to `UrlMalformedException` and update all references in:
- `src/Core/Exceptions/UrlMailformedException.php` (rename file)
- `src/Core/ExceptionHandler.php`
- `public/index.php`

---

## 🐛 Bug #11 — `Dispatcher::getMethodArgs` does not guard against missing route params

**File:** `src/Core/Dispatcher.php`

**Description:**
When building method arguments, the dispatcher accesses `$params[$arg_name]` directly without checking if the key exists. If a method parameter name does not match any captured route segment, an undefined array key error is thrown. Parameter default values defined in the method signature are also ignored.

**Affected code:**
```php
$args[$arg_name] = $this->typeCastAndReturnValue(
    $arg_type,
    $params[$arg_name], // ← no existence check
);
```

**Fix:**
Fall back to the parameter's default value if the key is absent:
```php
if (!array_key_exists($arg_name, $params)) {
    if ($parameter->isDefaultValueAvailable()) {
        $args[$arg_name] = $parameter->getDefaultValue();
        continue;
    }
    throw new \RuntimeException("Missing route parameter: '{$arg_name}'");
}
$args[$arg_name] = $this->typeCastAndReturnValue($arg_type, $params[$arg_name]);
```
