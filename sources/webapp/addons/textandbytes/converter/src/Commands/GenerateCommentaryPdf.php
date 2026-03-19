<?php

namespace Textandbytes\Converter\Commands;

use App\Jobs\GenerateCommentaryPdf as GenerateCommentaryPdfJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Statamic\Facades\Collection;

class GenerateCommentaryPdf extends Command
{
    protected $signature = 'converter:generate-commentary-pdf {slug?} {--all} {--sync}';

    public function handle()
    {
        $slug = $this->argument('slug');

        if (!$slug && !$this->option('all')) {
            $this->error('Provide a slug or use --all');
            return Command::FAILURE;
        }

        $disk = Storage::disk('pdf');

        if ($this->option('all')) {
            $this->info('Deleting all commentary PDFs...');
            $disk->deleteDirectory('commentary');
        }

        foreach (['de', 'en'] as $locale) {
            $this->info("Generating commentary PDFs for locale: {$locale}");

            $tree = Collection::findByHandle('commentaries')
                ->structure()
                ->in($locale);

            $entries = $tree
                ->flattenedPages()
                ->filter(fn ($page) => $page->entry()->blueprint()->handle() === 'commentary')
                ->filter(fn ($page) => $page->entry()->published())
                ->map(fn ($page) => $page->entry())
                ->values();

            if ($slug) {
                $entries = $entries->filter(fn ($entry) => $entry->slug() === $slug);
            }

            if ($entries->isEmpty()) {
                $this->warn("No published commentaries found for locale: {$locale}");
                continue;
            }

            $this->info("Found {$entries->count()} entries");

            foreach ($entries as $entry) {
                $entrySlug = $entry->slug();
                $this->info("Processing: {$entry->get('title')} ({$entrySlug})");

                $disk->delete([
                    "commentary/{$locale}/{$entrySlug}-md.pdf",
                    "commentary/{$locale}/{$entrySlug}-lg.pdf",
                ]);

                if ($this->option('sync')) {
                    GenerateCommentaryPdfJob::dispatchSync($entry->id(), $locale);
                } else {
                    GenerateCommentaryPdfJob::dispatch($entry->id(), $locale);
                }
            }

            $this->info("Finished for locale: {$locale}");
        }

        return Command::SUCCESS;
    }
}
