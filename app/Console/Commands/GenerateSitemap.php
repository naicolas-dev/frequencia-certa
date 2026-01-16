<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'seo:generate-sitemap';
    protected $description = 'Gera o sitemap.xml';

    public function handle(): int
    {
        Sitemap::create()
            ->add(
                Url::create(url('/'))
                    ->setLastModificationDate(now())
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(1.0)
            )
            ->writeToFile(public_path('sitemap.xml'));

        $this->info('sitemap.xml gerado com sucesso');

        return self::SUCCESS;
    }
}
