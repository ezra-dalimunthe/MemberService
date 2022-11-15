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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

Route::get("/api/document", function () {
    return view("index");
});

Route::get("/api/download-doc", function () {
    return view("downloaddoc");
});

Route::get('/api/docs', function () {
    $paths = [
        base_path() . '/app/Models',
        base_path() . '/app/Http/Controllers',
    ];
    $openapi = \OpenApi\Generator::scan($paths);
    return response()->json($openapi)->withHeaders([
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
    ]);;
});

$router->group(["prefix" => "api/v1/members", "middleware" => "auth"], function () use ($router) {
    $router->get("/", "MemberController@index");
    $router->get("/deleted", "MemberController@deleted");
});

$router->group(["prefix" => "api/v1/member", "middleware" => "auth"], function () use ($router) {
    $router->post("/", "MemberController@store");
    $router->get("/{id}", "MemberController@show");
    $router->put("/{id}", "MemberController@update");
    $router->delete("/{id}", "MemberController@destroy");
});

$router->group(["prefix" => "api/v1/entity"], function () use ($router) {
    $router->get("member/{id}", "EntityServiceController@showMember");
    $router->get("members", "EntityServiceController@showMembers");
});

$router->group(["prefix" => "api/v1/statistic"], function () use ($router) {
    $router->get("/member-by-status", "StatisticController@memberByStatus");
    $router->get("/member-by-gender", "StatisticController@memberByGender");
    $router->get("/member-by-age", "StatisticController@memberByAge");
});


Route::get('/dbcheck', function () {
    // Test database connection
    try {
        DB::connection()->getPdo();
        echo "database " . DB::connection()->getDatabaseName(), " is ready\n" ;
    } catch (\Exception $e) {
        die("Could not connect to the database. Please check your configuration. error:" . $e );
   }
});
