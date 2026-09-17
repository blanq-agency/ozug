<?php

namespace Textandbytes\ApiTokens\Fieldtypes;

use Statamic\Fields\Fieldtype;

class ApiTokens extends Fieldtype
{
    protected $icon = 'lock';

    public function preProcess($data)
    {
        return collect($data ?? [])
            ->map(fn ($token) => [
                'id' => $token['id'] ?? null,
                'name' => $token['name'] ?? null,
                'token' => $token['token'] ?? null,
                'created_at' => $token['created_at'] ?? null,
            ])
            ->values()
            ->all();
    }

    public function process($data)
    {
        return collect($data ?? [])
            ->map(fn ($token) => [
                'id' => $token['id'] ?? null,
                'name' => $token['name'] ?? null,
                'token' => $token['token'] ?? null,
                'created_at' => $token['created_at'] ?? null,
            ])
            ->values()
            ->all();
    }
}
