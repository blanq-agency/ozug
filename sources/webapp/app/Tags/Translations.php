<?php

namespace App\Tags;

use Illuminate\Support\Facades\File;
use Statamic\Facades\Site;
use Statamic\Tags\Tags;

class Translations extends Tags
{
    /**
     * The {{ translations }} tag — outputs the current site's JSON language
     * file as a safely encoded object for inlining into a <script> tag.
     */
    public function index()
    {
        $path = lang_path(Site::current()->shortLocale().'.json');

        $messages = File::exists($path)
            ? json_decode(File::get($path), true)
            : [];

        return json_encode($messages, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
    }
}
