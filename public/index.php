<?php
declare(strict_types=1);

// autoload
require __DIR__ . '/../vendor/autoload.php';

// tiny .env loader (reads .env into getenv and $_ENV)
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        [$key, $val] = array_map('trim', explode('=', $line, 2) + [1=>null]);
        if ($key && $val !== null) {
            putenv("$key=$val");
            $_ENV[$key] = $val;
        }
    }
}

use App\Core\Router;
use App\Core\Session;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\PostController;

Session::start();

$router = new Router();
$auth = new AuthController();
$dash = new DashboardController();
$post = new PostController();

// --- Register routes (ALL routes must be registered BEFORE dispatch) ---
$router->get('/', fn() => $auth->showLogin());
$router->get('/login', fn() => $auth->showLogin());
$router->get('/register', fn() => $auth->showRegister());
$router->get('/dashboard', fn() => $dash->index());

// Create a post
$router->post('/post', fn() => $post->create());

// Edit post (show form & submit)
$router->get('/post/edit', fn() => $post->editForm());
$router->post('/post/edit', fn() => $post->edit());

// Delete post (prefer POST)
$router->post('/post/delete', fn() => $post->delete());
// Optional GET delete
$router->get('/post/delete', fn() => $post->delete());

// Search (GET /search?type=users|posts&q=term)  ← FIXED
$router->get('/search', fn() => $dash->search());

// Auth actions
$router->post('/register', fn() => $auth->register());
$router->post('/login', fn() => $auth->login());
$router->get('/logout', fn() => $auth->logout());

// --- Dispatch (no routes should be added AFTER this line) ---
$router->dispatch($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'] ?? 'GET');

// Debug helper — uncomment if you need to log the exact path/method the router sees.
// error_log('ROUTER DEBUG: ' . ($_SERVER['REQUEST_METHOD'] ?? 'GET') . ' ' . ($_SERVER['REQUEST_URI'] ?? '/'));
