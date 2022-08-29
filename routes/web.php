<?php

use Illuminate\Support\Arr;

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

Route::get("/api/document",function(){
   return view("index");
});

Route::get("/api/download-doc",function(){
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

$router->group(["prefix" => "api/v1/members"], function () use ($router) {
    $router->get("/", "MemberController@index");
    $router->get("/deleted", "MemberController@deleted");
});

$router->group(["prefix" => "api/v1/member"], function () use ($router) {
    $router->post("/", "MemberController@store");
    $router->get("/{id}", "MemberController@show");
    $router->put("/{id}", "MemberController@update");
    $router->delete("/{id}", "MemberController@destroy");
});
Route::get('/api/test', function () {

    $col1 = collect([
        ["id" => 1, "fk_id" => 2, "name" => "name1"],
        ["id" => 2, "fk_id" => 1, "name" => "name2"],
        ["id" => 3, "fk_id" => 3, "name" => "name2"],
    ]);
    $col2 = collect([
        ["id" => 1, "status" => "OK"],
        ["id" => 2, "status" => "Not OK"],
    ]);

    $col3 = $col1->map(function ($item, $key) use ($col2) {
        $selected = $col2->firstWhere("id", $item["fk_id"]);
        $selected = $selected == null ? null : Arr::only($selected, ["status"]);
        return Arr::add($item, "status", $selected);
    });
    return response()->json($col3);

});
