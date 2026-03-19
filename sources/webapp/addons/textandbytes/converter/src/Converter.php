<?php

namespace Textandbytes\Converter;

use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Gotenberg\Gotenberg;
use Gotenberg\Stream;
use Illuminate\Support\Traits\Localizable;
use JackSleight\StatamicDistill\Facades\Distill;
use Pontedilana\PhpWeasyPrint\Pdf;
use Statamic\Entries\Entry;
use Statamic\Support\Str;
use Statamic\View\View;
use Textandbytes\Converter\Marks\ParagraphNumber;
use Textandbytes\Converter\Nodes\Cleaner;
use Textandbytes\Converter\Nodes\Footnote;
use Tiptap\Editor;
use Tiptap\Marks;
use Tiptap\Nodes;
use TOC\MarkupFixer;
use TOC\TocGenerator;

class Converter
{
    use Localizable;

    // If the page layout changes run `php artisan converter:calibrate-pdf-estimator` to recalculate these numbers
    const WORDS_PER_PAGE = 479;
    const MEDIA_PER_PAGE = 2.8;

    public function htmlToProsemirror($html)
    {
        /* ProseMirror input must be UTF-8. Samples coming from tests will be
           but if we're processing a Word HTML file we need to convert it first */
        if (Str::contains($html, 'charset=windows-1252')) {
            $html = mb_convert_encoding($html, 'utf-8', 'windows-1252');
            $html = str_replace('charset=windows-1252', 'charset=utf-8', $html);
        }

        $html = Cleaner::preConvert($html);
        $html = ParagraphNumber::preConvert($html);

        $data = (new Editor([
            'extensions' => [
                new Marks\Bold(),
                new Marks\Italic(),
                new Marks\Link(),
                new Marks\Superscript(),
                new Marks\Underline(),
                new Nodes\BulletList(),
                new Nodes\HardBreak(),
                new Nodes\Heading(),
                new Nodes\ListItem(),
                new Nodes\OrderedList(),
                new Nodes\Paragraph(),
                new Nodes\Document(),
                new Nodes\Text(),
                new Cleaner(),
                new Footnote(),
                new ParagraphNumber(),
            ],
        ]))->setContent($html)->getDocument()['content'];

        $data = Cleaner::postConvert($data);

        return json_encode($data);
    }

    public function prosemirrorToWord($data)
    {
        $data = json_decode($data);

        return (new WordRenderer)->render($data);
    }

    public function entryToWord($entry)
    {
        if ($entry->collection()->handle() !== 'commentaries') {
            throw new \Exception('Entry is not a commentary');
        }

        $data = array_merge(
            $this->makeHeading($entry->title, 0),
            $entry->get('content') ?? [],
            $this->makeHeading('Assigned Authors', 1),
            $this->makeParagraph($entry->assigned_authors->pluck('name')->join(', ')),
            $this->makeHeading('Assigned Editors', 1),
            $this->makeParagraph($entry->assigned_editors->pluck('name')->join(', ')),
            $this->makeHeading('Suggested Citation', 1),
            $this->makeParagraph($entry->suggested_citation_long),
            $this->makeHeading('Legal Text', 1),
            $entry->get('legal_text') ?? [],
        );

        $data = json_decode(json_encode($data));

        return (new WordRenderer)->render($data);
    }

    public function entryToPdf($entry)
    {
        $wordFile = $this->entryToWord($entry);

        $dir = storage_path('app');
        $request = Gotenberg::libreOffice(config('services.gotenberg.url'))
            ->convert(Stream::path($wordFile));
        $pdfFile = $dir.'/'.Gotenberg::save($request, $dir);

        unlink($wordFile);

        return $pdfFile;
    }

    public function entryToHtml($entry, $params = [])
    {
        return $this->withLocale($entry->locale(), function () use ($entry, $params) {
            $html = $this->renderEntryContent($entry);

            $tocGenerator = new TocGenerator;
            $toc = $tocGenerator->getHtmlMenu($html);

            $entryUrl = $entry->absoluteUrl();

            return (new View)
                ->template('commentaries.print')
                ->layout('print')
                ->cascadeContent($entry)
                ->with([
                    'content' => $html,
                    'toc' => $toc,
                    'qr_code' => $this->generateQrCodeDataUri($entryUrl),
                    'entry_url' => $entryUrl,
                    ...$params,
                ])
                ->render();
        });
    }

    public function entryToHtmlPdf($entry, $params = [])
    {
        $html = $this->entryToHtml($entry, $params);

        return $this->renderWeasyPdf($html, 30);
    }

    public function getEntryContentCounts(Entry $entry): array
    {
        $content = $entry->augmentedValue('content');
        
        $words = str_word_count(Distill::text($content));
        $media = Distill::query($content)
            ->type([
                'set:image',
                'set:image_embed',
                'set:video',
                'set:video_embed',
                'set:audio',
                'set:audio_embed',
                'set:h5p_embed',
            ])
            ->count();

        return [
            'words' => $words,
            'media' => $media,
        ];
    }

    public function estimateEntryPages(Entry $entry): float
    {
        $counts = $this->getEntryContentCounts($entry);

        $pages = $counts['words'] / static::WORDS_PER_PAGE
            + $counts['media'] / static::MEDIA_PER_PAGE
            + 1    // entry title page
            + 1    // entry TOC
            + 0.5; // blank page padding for odd-page starts

        return max($pages, 1);
    }

    public static function estimateVolumeOverheadPages(): float
    {
        return 1   // volume title page
            + 1;   // volume TOC
    }

    public function entriesToHtml(array $entries, $tocPages, string $locale, int $volumeNumber, int $totalVolumes, string $generationDate, ?string $legalDomainTitle = null): string
    {
        return $this->withLocale($locale, function () use ($entries, $tocPages, $volumeNumber, $totalVolumes, $generationDate, $legalDomainTitle) {
            $tocGenerator = new TocGenerator;
            $entryIds = collect($entries)->map(fn ($e) => $e->id())->all();

            $entryData = collect($entries)->map(function ($entry) use ($tocGenerator) {
                $html = $this->renderEntryContent($entry);
                $html = preg_replace('/<(h[1-6][^>]*)\bid="([^"]*)"/', '<$1id="' . $entry->id() . '-$2"', $html);
                $toc = $tocGenerator->getHtmlMenu($html);

                return array_merge($entry->toAugmentedArray(), [
                    'toc' => $toc,
                    'rendered_content' => $html,
                ]);
            })->all();

            $tocTree = $this->buildTocTree($tocPages, $entryIds);
            $tocHtml = $this->renderTocTree($tocTree);

            return (new View)
                ->template('commentaries.print-full')
                ->layout('print')
                ->with([
                    'entries' => $entryData,
                    'toc_html' => $tocHtml,
                    'volume_number' => $volumeNumber,
                    'total_volumes' => $totalVolumes,
                    'generation_date' => $generationDate,
                    'legal_domain_title' => $legalDomainTitle,
                    'text' => 'md',
                ])
                ->render();
        });
    }

    public function entriesToHtmlPdf(array $entries, $tocPages, string $locale, int $volumeNumber, int $totalVolumes, string $generationDate, ?string $legalDomainTitle = null): string
    {
        $html = $this->entriesToHtml($entries, $tocPages, $locale, $volumeNumber, $totalVolumes, $generationDate, $legalDomainTitle);

        return $this->renderWeasyPdf($html, 600);
    }

    public function renderEntryContent($entry): string
    {
        $entryUrl = $entry->absoluteUrl();
        $qrCode = $this->generateQrCodeDataUri($entryUrl);

        $html = (new View)
            ->template('commentaries.print-content')
            ->cascadeContent($entry)
            ->with([
                'qr_code' => $qrCode,
                'entry_url' => $entryUrl,
            ])
            ->render();

        return (new MarkupFixer)->fix($html);
    }

    public function renderWeasyPdf(string $html, int $timeout = 30): string
    {
        $pdfFile = storage_path('app') . '/weasyprint-' . uniqid() . '.pdf';

        $pdf = new Pdf(config('services.weasyprint.bin'));
        $pdf->setTimeout($timeout);
        $pdf->setOption('pdf-variant', 'pdf/x-4');
        $pdf->setOption('full-fonts', true);
        $pdf->generateFromHtml($html, $pdfFile);

        return $pdfFile;
    }

    protected function generateQrCodeDataUri(string $url): string
    {
        $options = new QROptions([
            'outputInterface' => QRMarkupSVG::class,
            'svgUseCssProperties' => false,
            'quietzoneSize' => 2,
        ]);

        return (new QRCode($options))->render($url);
    }

    protected function renderTocTree(array $items): string
    {
        $html = '<ol>';

        foreach ($items as $item) {
            if ($item['type'] === 'group') {
                $html .= '<li class="toc-group">' . e($item['title']);
                $html .= $this->renderTocTree($item['children']);
                $html .= '</li>';
            } else {
                $html .= '<li><a href="#entry-' . $item['id'] . '">' . e($item['title']) . '</a>';
                if (!empty($item['children'])) {
                    $html .= $this->renderTocTree($item['children']);
                }
                $html .= '</li>';
            }
        }

        $html .= '</ol>';

        return $html;
    }

    protected function buildTocTree($pages, array $entryIds): array
    {
        $tree = [];

        foreach ($pages->all() as $page) {
            $entry = $page->entry();

            if (!$entry->published()) {
                continue;
            }

            $blueprint = $entry->blueprint()->handle();
            $children = $this->buildTocTree($page->pages(), $entryIds);

            if ($blueprint === 'commentary' && in_array($entry->id(), $entryIds)) {
                $tree[] = [
                    'type' => 'entry',
                    'id' => $entry->id(),
                    'title' => $entry->get('title'),
                    'children' => $children,
                ];
            } elseif (!empty($children)) {
                $tree[] = [
                    'type' => 'group',
                    'title' => $entry->get('title'),
                    'children' => $children,
                ];
            }
        }

        return $tree;
    }

    protected function makeParagraph($text)
    {
        return [[
            'type' => 'paragraph',
            'content' => [
                ['type' => 'text', 'text' => $text],
            ],
        ]];
    }

    protected function makeHeading($text, $level)
    {
        return [[
            'type' => 'heading',
            'attrs' => ['level' => $level],
            'content' => [
                ['type' => 'text', 'text' => $text],
            ],
        ]];
    }
}
