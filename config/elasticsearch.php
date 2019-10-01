<?php

return [
    'client' => [
        'hosts' => [
            sprintf(
                'http://%s:%d',
                env('ES_HOST', 'elasticsearch'),
                env('ES_PORT', 9200)
            )
        ]
    ],
    'index' => 'images',
    'mapping' => [
        'properties' => [
            'colors' => 'nested',
        ]
    ],
];
