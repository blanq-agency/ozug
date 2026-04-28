<?php

namespace Textandbytes\Converter\Commands;

use App\Jobs\GenerateLegalDomainPdf as GenerateLegalDomainPdfJob;
use App\Services\CommentaryTree;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateLegalDomainPdf extends Command
{
    protected $signature = 'converter:generate-legal-domain-pdf {slug?} {--all} {--sync}';

    public function handle()
    {
        $slug = $this->argument('slug');

        if (!$slug && !$this->option('all')) {
            $this->error('Provide a slug or use --all');
            return Command::FAILURE;
        }

        $disk = Storage::disk('pdf');

        if ($this->option('all')) {
            $this->info('Deleting all legal domain PDFs...');
            $disk->deleteDirectory('legal-domain');
        }

        foreach (['de', 'en'] as $locale) {
            $this->info("Generating legal domain PDFs for locale: {$locale}");

            $legalDomains = CommentaryTree::getLegalDomainEntries($locale);

            if ($slug) {
                $legalDomains = $legalDomains->filter(fn ($entry) => $entry->slug() === $slug);
            }

            if ($legalDomains->isEmpty()) {
                $this->warn("No legal domain entries found for locale: {$locale}");
                continue;
            }

            $this->info("Found {$legalDomains->count()} legal domains");

            foreach ($legalDomains as $entry) {
                $entrySlug = $entry->slug();
                $this->info("Processing: {$entry->get('title')} ({$entrySlug})");

                $disk->deleteDirectory("legal-domain/{$locale}/{$entrySlug}");

                if ($this->option('sync')) {
                    GenerateLegalDomainPdfJob::dispatchSync($entry->id(), $locale);
                } else {
                    GenerateLegalDomainPdfJob::dispatch($entry->id(), $locale);
                }
            }

            $this->info("Finished for locale: {$locale}");
        }

        return Command::SUCCESS;
    }
}
