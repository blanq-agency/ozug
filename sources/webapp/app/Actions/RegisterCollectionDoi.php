<?php

namespace App\Actions;

use App\Services\CommentaryTree;
use App\Services\DataCite;
use RuntimeException;
use Statamic\Actions\Action;
use Statamic\Contracts\Entries\Entry;

class RegisterCollectionDoi extends Action
{
    public static function title()
    {
        return __('Register Collection DOI');
    }

    public function visibleTo($item)
    {
        return $this->context['view'] === 'form'
            && $item instanceof Entry
            && $item->collectionHandle() === 'commentaries'
            && $item->blueprint()->handle() === 'legal_domain';
    }

    public function visibleToBulk($items)
    {
        return false;
    }

    public function authorize($user, $entry)
    {
        return $user->can('publish', $entry);
    }

    public function buttonText()
    {
        return __('Register Collection DOI');
    }

    public function confirmationText()
    {
        return __('A new draft DOI will be registered with DataCite for the current version of this collection and saved to this entry. Are you sure?');
    }

    public function run($entries, $values)
    {
        $entry = $entries->first();

        if (! $entry->published()) {
            throw new RuntimeException(__('Only published legislative acts can be registered, because the DOI has to point at a page a reader can open.'));
        }

        if ($entry->locale() !== 'de') {
            throw new RuntimeException(__('DOIs are registered against the German original, not its translations.'));
        }

        if ($entry->hasWorkingCopy()) {
            throw new RuntimeException(__('This legislative act has unpublished changes. Publish or discard them first, otherwise publishing them later would overwrite the DOI.'));
        }

        $commentaries = CommentaryTree::getCommentariesForLegalDomain($entry, $entry->locale());

        $datacite = new DataCite;
        $doi = $datacite->registerCollection($entry, $commentaries);
        $datacite->storeDoi($entry, $doi);

        return __('DOI :doi registered as a draft.', ['doi' => $doi]);
    }
}
