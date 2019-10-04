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

use App\ColorDistribution;
use App\Repositories\ImageRepository;
use Illuminate\Http\Request;

$router->get('/', function (Request $request, ImageRepository $repository) use ($router) {
    $perPage = 1000;
    $page = (int)$request->query('page');
    $page = $page > 0 ? $page : 1;
    $diff = 15;

    $id = $request->query('id');
    $image = $id !== null ? $repository->get((int)$id) : null;

    if ($image) {
        $distribution = $image->getColorDistribution();
    } else {
        $distribution = new ColorDistribution(array_combine(
            (array)$request->query('colors'),
            (array)$request->query('amounts')
        ));
    }

    $musts = [];
    foreach ($distribution as $color => $amount) {
        $hsl = $color->toHSL();
        $amountDiff = max($amount / 2, 0.15);
        $musts[] = [
            'bool' => [
                'must' => [
                    [
                        'range' => [
                            'hsl.h' => [
                                'gte' => $hsl->hue - $diff,
                                'lte' => $hsl->hue + $diff,
                            ]
                        ]
                    ],
                    [
                        'range' => [
                            'hsl.s' => [
                                'gte' => $hsl->saturation - $diff,
                                'lte' => $hsl->saturation + $diff,
                            ]
                        ]
                    ],
                    [
                        'range' => [
                            'hsl.l' => [
                                'gte' => $hsl->luminance - $diff,
                                'lte' => $hsl->luminance + $diff,
                            ]
                        ]
                    ],
                    [
                        'range' => [
                            'hsl.amount' => [
                                'gte' => $amount - $amountDiff,
                                'lte' => $amount + $amountDiff,
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    $query = [];
    $sort = [];
    if ($musts) {
        foreach ($musts as $must) {
            $query['bool']['should'][]['nested'] = [
                'path' => 'hsl',
                'query' => $must,
            ];
        }
        $query['bool']['minimum_should_match'] = '-30%';
    } else {
        $query['match_all'] = new \stdClass();
        $sort[] = ['hue' => 'desc'];
    }

    $results = $repository->search([
        'from' => ($page - 1) * $perPage,
        'size' => $perPage,
        'query' => $query,
        'sort' => $sort,
    ]);

    return view('images', [
        'colors' => $distribution,
        'images' => $results,
    ]);
});
