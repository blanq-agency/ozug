<?php

namespace App\Listeners;

use App\Jobs\GenerateCommentaryPdf;
use App\Jobs\GenerateLegalDomainPdf;
use App\Services\CommentaryTree;
use Illuminate\Support\Facades\Storage;
use Statamic\Events\EntryDeleted;
use Statamic\Events\EntrySaved;

class GeneratePdfs
{
    public function handle(EntrySaved|EntryDeleted $event): void
    {
        $entry = $event->entry;

        if ($entry->collectionHandle() !== 'commentaries') {
            return;
        }

        $locale = $entry->locale();
        $slug = $entry->slug();
        $blueprint = $entry->blueprint()->handle();

        if ($blueprint === 'commentary') {
            $this->handleCommentary($event, $entry, $locale, $slug);
        } elseif ($blueprint === 'legal_domain') {
            $this->handleLegalDomain($event, $entry, $locale, $slug);
        }
    }

    protected function handleCommentary(EntrySaved|EntryDeleted $event, $entry, string $locale, string $slug): void
    {
        $disk = Storage::disk('pdf');

        $disk->delete([
            "commentary/{$locale}/{$slug}-md.pdf",
            "commentary/{$locale}/{$slug}-lg.pdf",
        ]);

        if ($event instanceof EntrySaved) {
            GenerateCommentaryPdf::dispatch($entry->id(), $locale);
        }

        $ancestor = CommentaryTree::findLegalDomainAncestor($entry, $locale);

        if ($ancestor) {
            $disk->deleteDirectory("legal-domain/{$locale}/{$ancestor->slug()}");
            GenerateLegalDomainPdf::dispatch($ancestor->id(), $locale);
        }
    }

    protected function handleLegalDomain(EntrySaved|EntryDeleted $event, $entry, string $locale, string $slug): void
    {
        Storage::disk('pdf')->deleteDirectory("legal-domain/{$locale}/{$slug}");

        if ($event instanceof EntrySaved) {
            GenerateLegalDomainPdf::dispatch($entry->id(), $locale);
        }
    }
}
