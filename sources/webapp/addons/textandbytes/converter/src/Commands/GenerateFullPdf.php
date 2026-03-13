<?php

namespace Textandbytes\Converter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Statamic\Facades\Collection;
use Textandbytes\Converter\Converter;

class GenerateFullPdf extends Command
{
    protected $signature = 'converter:generate-full-pdf {locale=de} {--all} {--test}';

    public function handle()
    {
        $locales = $this->option('all')
            ? ['de', 'en']
            : [$this->argument('locale')];

        $converter = new Converter;

        foreach ($locales as $locale) {
            $this->info("Generating full PDF for locale: {$locale}");

            $tree = Collection::findByHandle('commentaries')
                ->structure()
                ->in($locale);

            $entries = $tree
                ->flattenedPages()
                ->filter(fn ($page) => $page->entry()->blueprint()->handle() === 'commentary')
                ->filter(fn ($page) => $page->entry()->published())
                ->map(fn ($page) => $page->entry())
                ->values();

            if ($this->option('test')) {
                $entries = $entries->take(2);
            }

            if ($entries->isEmpty()) {
                $this->warn("No published commentaries found for locale: {$locale}");
                continue;
            }

            $this->info("Found {$entries->count()} entries");

            $volumes = [];
            $currentVolume = [];
            $currentPageCount = 0;

            foreach ($entries as $entry) {
                $pages = $converter->estimateEntryPages($entry);

                if ($currentPageCount + $pages > 2000 && !empty($currentVolume)) {
                    $volumes[] = $currentVolume;
                    $currentVolume = [];
                    $currentPageCount = 0;
                }

                $currentVolume[] = $entry;
                $currentPageCount += $pages;
            }

            if (!empty($currentVolume)) {
                $volumes[] = $currentVolume;
            }

            $totalVolumes = count($volumes);
            $generationDate = now()->format('d.m.Y');
            $outputDir = storage_path("app/full-pdf/{$locale}");

            File::ensureDirectoryExists($outputDir);
            File::cleanDirectory($outputDir);

            $manifest = [
                'generation_date' => $generationDate,
                'total_volumes' => $totalVolumes,
                'files' => [],
            ];

            foreach ($volumes as $index => $volumeEntries) {
                $volumeNumber = $index + 1;
                $this->info("Rendering volume {$volumeNumber}/{$totalVolumes} (" . count($volumeEntries) . " entries)...");

                $pdfFile = $converter->entriesToHtmlPdf(
                    $volumeEntries,
                    $tree,
                    $locale,
                    $volumeNumber,
                    $totalVolumes,
                    $generationDate
                );

                $filename = $totalVolumes > 1
                    ? "oak-vol-{$volumeNumber}.pdf"
                    : 'oak.pdf';

                File::move($pdfFile, "{$outputDir}/{$filename}");

                $manifest['files'][] = $filename;

                $this->info("Saved {$filename}");
            }

            File::put("{$outputDir}/manifest.json", json_encode($manifest, JSON_PRETTY_PRINT));

            $this->info("Full PDF generation finished for locale: {$locale}");
        }

        return Command::SUCCESS;
    }
}
