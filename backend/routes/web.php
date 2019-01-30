<?php

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

$router->get('/api/user', 'UserController@index');

$router->get('/api/project', 'ProjectController@index');
$router->post('/api/project/{id}', 'ProjectController@save');
$router->get('/api/project/{id}', 'ProjectController@get');
$router->delete('/api/project/{id}', 'ProjectController@delete');
$router->get('/api/project/{id}/flow', 'ProjectController@getFlows');
$router->get('/api/project/deploy/{id}', 'ProjectController@deploy');
$router->get('/api/project/{id}/fieldIds', 'ProjectController@getFlowsFieldIds');
$router->get('/api/project/{id}/formIds', 'ProjectController@getFlowsformIds');
$router->get('/api/project/{id}/nodes', 'ProjectController@getAllNodes');

$router->get('/api/flow/{id}', 'FlowController@index');
$router->get('/api/flow/get/{id}/', 'FlowController@get');
$router->post('/api/flow/{id}', 'FlowController@save');
$router->delete('/api/flow/{id}', 'FlowController@delete');

$router->get('/api/system/ping', 'SystemController@ping');
$router->get('/api/system/login', 'SystemController@login');