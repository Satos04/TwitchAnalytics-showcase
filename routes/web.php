<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/health', fn () => response()->json(['status' => 'ok']));
$router->post('/register', 'RegisterController@handle');
$router->post('/token', 'TokenController@handleTokenRequest');

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('/analytics/search', 'SearchController@searchCategories');
    $router->get('/analytics/games', 'GamesController@getTopGames');
});
