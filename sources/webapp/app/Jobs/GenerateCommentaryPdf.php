<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Statamic\Facades\Entry;
use Textandbytes\Converter\Converter;

class GenerateCommentaryPdf implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;

    public function __construct(
        protected string $entryId,
        protected string $locale,
    ) {}

    public function uniqueId(): string
    {
        return "commentary:{$this->locale}:{$this->entryId}";
    }

    public function handle(): void
    {
        $entry = Entry::find($this->entryId);

        if (!$entry || !$entry->published()) {
            return;
        }

        app()->setLocale($this->locale);

        $converter = new Converter;
        $disk = Storage::disk('pdf');
        $slug = $entry->slug();

        foreach (['md', 'lg'] as $text) {
            $file = $converter->entryToHtmlPdf($entry, ['text' => $text]);
            $path = "commentary/{$this->locale}/{$slug}-{$text}.pdf";
            $disk->put($path, file_get_contents($file));
            @unlink($file);
        }
    }
}
