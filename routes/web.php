<?php

$app->get('/', function () use ($app) {
    return $app->version();
});

//product routes
$app->get('products', 'ProductsController@index');
$app->get('products/{id}', 'ProductsController@show');
$app->put('products/{id}', 'ProductsController@update');
$app->post('products', 'ProductsController@store');
$app->delete('products/{id}', 'ProductsController@destroy');
