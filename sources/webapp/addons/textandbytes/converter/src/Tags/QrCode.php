<?php

namespace Textandbytes\Converter\Tags;

use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\QRCode as QRCodeRenderer;
use chillerlan\QRCode\QROptions;
use Statamic\Tags\Tags;

class QrCode extends Tags
{
    protected static $handle = 'qr_code';

    public function index()
    {
        $options = new QROptions([
            'outputInterface' => QRMarkupSVG::class,
            'svgUseCssProperties' => false,
            'quietzoneSize' => 2,
        ]);

        return (new QRCodeRenderer($options))->render($this->params->get('url'));
    }
}
