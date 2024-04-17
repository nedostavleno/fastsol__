<?php
/**
 * BelovTest
 * Routing
 */
use App\Services\App;
use App\Services\Router;

/* Start app */
App::start();

Router::get('/', [\App\View\Index::class, 'render']);
Router::get('/posts', [\App\View\Posts::class, 'render']);
Router::get('/posts/{pageid}', [\App\View\Posts::class, 'render']);
Router::get('/restore', [\App\View\Restore::class, 'render']);
Router::get('/register', [\App\View\Register::class, 'render']);

Router::post('/login', [\App\Controllers\User::class, 'login']);
Router::post('/layout', [\App\Controllers\User::class, 'layout']);
Router::post('/restore', [\App\Controllers\User::class, 'restore']);
Router::post('/register', [\App\Controllers\User::class, 'register']);

Router::start();