<?php

namespace Textandbytes\ApiTokens;

use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $fieldtypes = [
        Fieldtypes\ApiTokens::class,
    ];

    protected $scripts = [
        __DIR__.'/../dist/js/addon.js',
    ];
}
