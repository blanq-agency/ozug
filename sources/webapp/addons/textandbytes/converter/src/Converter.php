<?php

namespace Textandbytes\Converter;

use Gotenberg\Gotenberg;
use Gotenberg\Stream;
use Illuminate\Support\Traits\Localizable;
use Pontedilana\PhpWeasyPrint\Pdf;
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
        $markupFixer = new MarkupFixer;
        $allTextContent = '';

        foreach ($entry->content as $block) {
            if ($block['type'] === 'text') {
                $allTextContent .= $block['text'];
            }
        }

        $allTextContent = $markupFixer->fix($allTextContent);

        $tocGenerator = new TocGenerator;
        $toc = $tocGenerator->getHtmlMenu($allTextContent);

        return $this->withLocale($entry->locale(), fn () => (new View)
            ->template('commentaries.print')
            ->layout('print')
            ->cascadeContent($entry)
            ->with([
                'content' => $allTextContent,
                'toc' => $toc,
                ...$params,
            ])
            ->render());
    }

    public function entryToHtmlPdf($entry, $params = [])
    {
        $html = $this->entryToHtml($entry, $params);

        $pdfFile = storage_path('app').'/weasyprint-'.uniqid().'.pdf';

        $pdf = new Pdf(config('services.weasyprint.bin'));
        $pdf->setTimeout(30);
        $pdf->generateFromHtml($html, $pdfFile);

        return $pdfFile;
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
