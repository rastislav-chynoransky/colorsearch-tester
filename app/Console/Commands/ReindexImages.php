<?php

namespace App\Console\Commands;

use App\Image;
use App\Repositories\ImageRepository;
use Illuminate\Console\Command;
use Primal\Color\Parser;

class ReindexImages extends Command
{
    protected $name = 'images:reindex';

    public function handle(ImageRepository $repository)
    {
        $repository->deleteIndex();
        $repository->createIndex();
        $repository->createMapping();

        $progressBar = $this->output->createProgressBar(Image::count());

        Image::chunk(100, function ($images) use ($progressBar, $repository) {
            /** @var Image $image */
            foreach ($images as $image) {
                $progressBar->advance();

                $hsls = $hsvs = $rgbs = [];
                foreach ($image->getColors() as $hex => $amount) {
                    $color = Parser::Parse($hex);

                    $rgb = $color->toRGB();
                    $rgbs[] = [
                        'r' => $rgb->red,
                        'g' => $rgb->green,
                        'b' => $rgb->blue,
                        'amount' => $amount,
                    ];

                    $hsl = $color->toHSL();
                    $hsls[] = [
                        'h' => $hsl->hue,
                        's' => $hsl->saturation,
                        'l' => $hsl->luminance,
                        'amount' => $amount,
                    ];

                    $hsv = $color->toHSV();
                    $hsvs[] = [
                        'h' => $hsv->hue,
                        's' => $hsv->saturation,
                        'v' => $hsv->value,
                        'amount' => $amount,
                    ];
                }

                $repository->index([
                    'attributes' => $image->getAttributes(),
                    'hue' => $hsls[0]['h'] ?? null,
                    'hsl' => $hsls,
                    'hsv' => $hsvs,
                    'rgb' => $rgbs,
                ]);
            }
        });

        $progressBar->finish();
    }
}
