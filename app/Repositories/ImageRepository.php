<?php

namespace App\Repositories;

use App\Image;
use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Illuminate\Support\Collection;

class ImageRepository
{
    protected $elasticsearch;

    protected $index;

    public function __construct(Client $client)
    {
        $this->elasticsearch = $client;
        $this->index = config('elasticsearch.index');
    }

    public function createIndex()
    {
        $this->elasticsearch->indices()->create([
            'index' => $this->index,
        ]);
    }

    public function deleteIndex(): bool
    {
        try {
            $this->elasticsearch->indices()->delete([
                'index' => $this->index
            ]);
        } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
            return false;
        }

        return true;
    }

    public function createMapping(): void
    {
        $this->elasticsearch->indices()->putMapping([
            'index' => $this->index,
            'body' => [
                'properties' => [
                    'hue' => [
                        'type' => 'float',
                    ],
                    'hsl' => [
                        'type' => 'nested',
                    ],
                    'hsv' => [
                        'type' => 'nested',
                    ],
                    'rgb' => [
                        'type' => 'nested',
                    ]
                ]
            ],
        ]);
    }

    public function index(array $data): void
    {
        $this->elasticsearch->index([
            'index' => $this->index,
            'id' => $data['attributes']['id'],
            'body' => $data,
        ]);
    }

    public function refreshIndex(): void
    {
        $this->elasticsearch->indices()->refresh([
            'index' => $this->index
        ]);
    }

    public function get(int $id): ?Image
    {
        try {
            $response = $this->elasticsearch->get([
                'index' => $this->index,
                'id' => $id,
            ]);

            return $this->createImage($response);
        } catch (Missing404Exception $e) {
            return null;
        }
    }

    public function search(array $body): Collection
    {
        $response = $this->elasticsearch->search([
            'index' => $this->index,
            'body' => $body,
        ]);

        return collect($response['hits']['hits'])->map(function ($hit) {
            $image = $this->createImage($hit);
            $image['score'] = $hit['_score'];
            return $image;
        });
    }

    public function explain(int $id, array $body): array
    {
        return $this->elasticsearch->explain([
            'index' => $this->index,
            'id' => $id,
            'body' => $body,
        ]);
    }

    protected function createImage(array $result): Image
    {
        $image = new Image();
        $image->setRawAttributes($result['_source']['attributes']);
        return $image;
    }
}
