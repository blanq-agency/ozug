<?php

namespace Textandbytes\Converter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Statamic\Facades\Collection;
use Statamic\View\View;
use Textandbytes\Converter\Converter;

class CalibratePdfEstimator extends Command
{
    protected $signature = 'converter:calibrate-pdf-estimator';

    public function handle()
    {
        $converter = new Converter;
        $totalWords = 0;
        $totalMediaItems = 0;
        $textPages = 0;
        $mediaPages = 0;

        foreach (['de', 'en'] as $locale) {
            $this->info("Processing locale: {$locale}");

            app()->setLocale($locale);

            $tree = Collection::findByHandle('commentaries')
                ->structure()
                ->in($locale);

            $entries = $tree
                ->flattenedPages()
                ->filter(fn ($page) => $page->entry()->blueprint()->handle() === 'commentary')
                ->filter(fn ($page) => $page->entry()->published())
                ->map(fn ($page) => $page->entry())
                ->values();

            if ($entries->isEmpty()) {
                $this->warn("No published commentaries found for locale: {$locale}");
                continue;
            }

            $this->info("Found {$entries->count()} entries");

            $entryData = [];

            foreach ($entries as $entry) {
                $counts = $converter->getEntryContentCounts($entry);
                $totalWords += $counts['words'];
                $totalMediaItems += $counts['media'];

                $entryData[] = [
                    'rendered_content' => $converter->renderEntryContent($entry),
                ];
            }

            $html = (new View)
                ->template('commentaries.print-calibration')
                ->layout('print')
                ->with([
                    'entries' => $entryData,
                    'stylesheet' => 'print-legal-domain.css',
                    'locale' => $locale,
                    'text' => 'md',
                ])
                ->render();

            $disk = Storage::disk('pdf');

            $this->info('Rendering text-only PDF...');
            $textOnlyHtml = $this->injectCss($html, '[data-media] { display: none !important; }');
            $disk->put("calibration/{$locale}-text-only.html", $textOnlyHtml);
            $textOnlyPdf = $converter->renderWeasyPdf($textOnlyHtml);
            $textPages += $this->countPdfPages($textOnlyPdf);
            $disk->put("calibration/{$locale}-text-only.pdf", file_get_contents($textOnlyPdf));
            @unlink($textOnlyPdf);
            $this->info("  Text pages: {$textPages}");

            $this->info('Rendering media-only PDF...');
            $mediaOnlyHtml = $this->injectCss($html, '[data-words] { display: none !important; }');
            $disk->put("calibration/{$locale}-media-only.html", $mediaOnlyHtml);
            $mediaOnlyPdf = $converter->renderWeasyPdf($mediaOnlyHtml);
            $mediaPages += $this->countPdfPages($mediaOnlyPdf);
            $disk->put("calibration/{$locale}-media-only.pdf", file_get_contents($mediaOnlyPdf));
            @unlink($mediaOnlyPdf);
            $this->info("  Media pages: {$mediaPages}");
        }

        $this->newLine();
        $this->info('=== Results ===');
        $this->newLine();

        $wordsPerPage = ($textPages > 0 && $totalWords > 0)
            ? round($totalWords / $textPages)
            : null;

        $mediaPerPage = ($mediaPages > 0 && $totalMediaItems > 0)
            ? round($totalMediaItems / $mediaPages, 1)
            : null;

        if ($wordsPerPage) {
            $this->info("Words per page: {$wordsPerPage}");
        } else {
            $this->warn('Could not calculate words per page (no text content found)');
        }

        if ($mediaPerPage) {
            $this->info("Media per page: {$mediaPerPage}");
        } else {
            $this->warn('Could not calculate media per page (no media found)');
        }

        $this->newLine();
        $this->info('Update Converter.php:');
        $this->newLine();
        $this->line("    const WORDS_PER_PAGE = {$wordsPerPage};");
        $this->line("    const MEDIA_PER_PAGE = {$mediaPerPage};");

        return Command::SUCCESS;
    }

    protected function injectCss(string $html, string $css): string
    {
        return str_replace('</body>', "<style>{$css}</style></body>", $html);
    }

    protected function countPdfPages(string $pdfFile): int
    {
        $output = shell_exec("pdfinfo " . escapeshellarg($pdfFile) . " 2>/dev/null");

        if ($output && preg_match('/Pages:\s*(\d+)/', $output, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }
}
