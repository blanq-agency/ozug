<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Statamic\Entries\Entry;
use Statamic\Facades\Term;
use Statamic\Facades\User;

class DataCite
{
    protected const PUBLISHER = 'Open Access Kommentar';

    public function registerCommentary(Entry $entry, Entry $legalDomain): string
    {
        $date = $entry->lastModified();

        return $this->create(array_filter([
            'doi' => $this->doi("{$legalDomain->slug()}-{$entry->slug()}", $date),
            'url' => $this->commentaryUrl($entry),
            'titles' => [['title' => $entry->get('title')]],
            'creators' => $this->people($entry->value('assigned_authors')),
            'contributors' => $this->people($entry->value('assigned_editors'), 'Editor'),
            'publisher' => self::PUBLISHER,
            'publicationYear' => (int) $date->format('Y'),
            'types' => [
                'resourceTypeGeneral' => 'Text',
                'resourceType' => 'Commentary',
            ],
            'subjects' => $this->subjects($legalDomain),
            'language' => 'de',
            'rightsList' => $this->rightsList($entry),
            'version' => $this->version($date),
            'relatedIdentifiers' => $this->relatedIdentifiers($legalDomain),
        ]));
    }

    public function registerCollection(Entry $legalDomain, SupportCollection $commentaries): string
    {
        $date = $this->collectionDate($commentaries);

        return $this->create(array_filter([
            'doi' => $this->doi($legalDomain->slug(), $date),
            'url' => url($legalDomain->url()),
            'titles' => [['title' => $legalDomain->get('title')]],
            'creators' => $this->people($this->gather($commentaries, 'assigned_authors')),
            'contributors' => $this->people($this->gather($commentaries, 'assigned_editors'), 'Editor'),
            'publisher' => self::PUBLISHER,
            'publicationYear' => (int) $date->format('Y'),
            'types' => [
                'resourceTypeGeneral' => 'Collection',
            ],
            'subjects' => $this->subjects($legalDomain),
            'language' => 'de',
            'rightsList' => $this->rightsList($legalDomain),
            'version' => $this->version($date),
        ]));
    }

    public function storeDoi(Entry $entry, string $doi): void
    {
        $entry->set('doi', $doi)->save();

        collect([$entry])
            ->merge($entry->descendants())
            ->each(fn ($localization) => Cache::forget(
                "commentary_view:{$localization->locale()}:{$localization->slug()}:{$localization->get('updated_at')}::"
            ));
    }

    public function collectionDate(SupportCollection $commentaries): Carbon
    {
        $date = $commentaries->map->lastModified()->filter()->max();

        if (! $date) {
            throw new RuntimeException(__('The collection has no published commentaries to take a version date from.'));
        }

        return $date;
    }

    public function doi(string $suffix, Carbon $date): string
    {
        return config('services.datacite.prefix')."/oak:{$suffix}:{$this->version($date)}";
    }

    public function version(Carbon $date): string
    {
        return $date->format('d.m.Y');
    }

    protected function commentaryUrl(Entry $entry): string
    {
        $revision = $entry->revisions()
            ->filter(fn ($revision) => $revision->action() === 'publish')
            ->sortByDesc(fn ($revision) => $revision->date()->timestamp)
            ->first();

        return $revision
            ? url("{$entry->url()}/versions/{$revision->date()->timestamp}")
            : url($entry->url());
    }

    protected function gather(SupportCollection $commentaries, string $field): SupportCollection
    {
        return $commentaries
            ->flatMap(fn ($commentary) => collect($commentary->value($field)))
            ->filter()
            ->unique()
            ->values();
    }

    protected function people($ids, ?string $contributorType = null): array
    {
        return collect($ids)
            ->map(fn ($id) => User::find($id))
            ->filter()
            ->map(function ($user) use ($contributorType) {
                $name = trim($user->get('name'));
                $family = Str::afterLast($name, ' ');
                $given = Str::contains($name, ' ') ? Str::beforeLast($name, ' ') : null;

                return array_filter([
                    'name' => $given ? "{$family}, {$given}" : $family,
                    'nameType' => 'Personal',
                    'givenName' => $given,
                    'familyName' => $family,
                    'nameIdentifiers' => $user->get('orcid')
                        ? [[
                            'nameIdentifier' => 'https://orcid.org/'.$user->get('orcid'),
                            'nameIdentifierScheme' => 'ORCID',
                            'schemeUri' => 'https://orcid.org',
                        ]]
                        : null,
                    'affiliation' => $user->get('affiliation')
                        ? [['name' => $user->get('affiliation')]]
                        : null,
                    'contributorType' => $contributorType,
                ]);
            })
            ->values()
            ->all();
    }

    protected function subjects(Entry $legalDomain): array
    {
        return [
            ['subject' => $legalDomain->get('title')],
            ['subject' => 'Law'],
        ];
    }

    protected function rightsList(Entry $entry): ?array
    {
        $slug = collect($entry->value('licenses'))->filter()->first();
        $term = $slug ? Term::find("licenses::{$slug}") : null;

        if (! $term) {
            return null;
        }

        return [array_filter([
            'rights' => $term->get('title_long'),
            'rightsUri' => $term->get('extern_url'),
        ])];
    }

    protected function relatedIdentifiers(Entry $legalDomain): ?array
    {
        if (! $doi = $legalDomain->get('doi')) {
            return null;
        }

        return [[
            'relatedIdentifier' => $doi,
            'relatedIdentifierType' => 'DOI',
            'relationType' => 'IsPartOf',
        ]];
    }

    protected function create(array $attributes): string
    {
        $missing = collect(['url', 'prefix', 'username', 'password'])
            ->reject(fn ($key) => config("services.datacite.{$key}"));

        if ($missing->isNotEmpty()) {
            throw new RuntimeException(__('DataCite is not configured. Missing: ').$missing->map(fn ($key) => 'DATACITE_'.Str::upper($key))->implode(', '));
        }

        $response = Http::withBasicAuth(
            config('services.datacite.username'),
            config('services.datacite.password'),
        )
            ->withHeaders(['Content-Type' => 'application/vnd.api+json'])
            ->post(rtrim(config('services.datacite.url'), '/').'/dois', [
                'data' => [
                    'type' => 'dois',
                    'attributes' => $attributes,
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException($this->errorMessage($response));
        }

        return $response->json('data.attributes.doi');
    }

    protected function errorMessage($response): string
    {
        $errors = collect($response->json('errors'))
            ->map(fn ($error) => trim(($error['title'] ?? '').' '.($error['detail'] ?? '')))
            ->filter()
            ->implode('; ');

        return __('DataCite rejected the registration').' ('.$response->status().')'.($errors ? ": {$errors}" : '.');
    }
}
