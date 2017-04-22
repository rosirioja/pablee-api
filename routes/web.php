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

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->group(['prefix' => 'api/v1'], function() use ($app) {

  $app->group(['prefix' => 'requests'], function() use ($app) {
    $app->get('/', 'RequestController@index');
    $app->get('recent', 'RequestController@getRecent');
    $app->get('{id}', 'RequestController@view');
    $app->get('{id}/cancel', 'RequestController@cancel');

    $app->post('/', 'RequestController@store');
    $app->put('{id}', 'RequestController@update');
  });

  $app->group(['prefix' => 'offers'], function() use ($app) {
    $app->get('{id}/cancel', 'OfferController@cancel');
    $app->post('/', 'OfferController@store');
    $app->put('{id}', 'OfferController@update');
  });

  $app->get('trips/recent', 'TripController@getRecent');
});
