<?php

namespace App\Console\Commands;

use App\Image;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use League\ColorExtractor\Color;
use League\ColorExtractor\ColorExtractor;

class ImportImages extends Command
{
    protected $name = 'images:import';

    public function handle(ColorExtractor $extractor)
    {
        $all = false;
        $pattern = '/^(?!.+\.\d+[wh]?\.jpe?g).+\.jpe?g$/i';

        $imagesPath = config('images.path');
        $directory = new \RecursiveDirectoryIterator($imagesPath);
        $iterator = new \RecursiveIteratorIterator($directory);
        $regex = new \RegexIterator($iterator, $pattern, null, \RegexIterator::USE_KEY);

        $counter = $this->output->createProgressBar();
        foreach ($regex as $name => $file) {
            $counter->advance();
        }

        $progressBar = $this->output->createProgressBar($counter->getProgress());
        $counter->finish();
        foreach ($regex as $name => $file) {
            $progressBar->advance();

            /** @var Image $image */
            $image = Image::firstOrNew([
                'path' => Str::after($file, $imagesPath),
            ]);

            if ($image->exists && !$all) {
                continue;
            }

            try {
                $colors = $extractor->extract($file, config('images.colors.count'));
                $colors = collect($colors)
                    ->mapWithKeys(function ($amount, $int) {
                        return [Color::fromIntToHex($int) => $amount];
                    })
                    ->sort()
                    ->reverse();

                $image->colors = $colors;
                $image->save();
            } catch (\Exception $e) {
                $this->error(sprintf("\n%s: %s", $name, $e->getMessage()));
            }
        }

        $progressBar->finish();
    }
}
