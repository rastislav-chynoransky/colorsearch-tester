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

use Elasticsearch\Client;
use Illuminate\Http\Request;
use Faker\Generator;

$router->get('/', function (Request $request, Client $client) use ($router) {
    $page = (int)$request->query('page') > 0 ?: 1;
    $perPage = 100;

    $response = $client->search([
        'index' => config('elasticsearch.index'),
        'body' => [
            'from' => ($page - 1) * $perPage,
            'size' => $perPage,
        ],
    ]);

    $color = (string)$request->query('color');
    $images = [];
    foreach ($response['hits']['hits'] as $hit) {
        $images[] = $hit['_source'];
    }

    return view('images', [
        'color' => $color,
        'images' => $images,
    ]);
});

$router->get('/regenerate', function (Client $client, Generator $faker) {
    $client->deleteByQuery([
        'index' => config('elasticsearch.index'),
        'body' => [
            'query' => [
                'match_all' => new \stdClass,
            ]
        ]
    ]);

    for ($i = 100; $i--;) {
        $colors = [
            ['hex' => $faker->hexColor, 'amount' => 100],
        ];

        $client->index([
            'index' => config('elasticsearch.index'),
            'body' => [
                'id' => $i,
                'colors' => $colors
            ],
        ]);
    }
});
