<?php

namespace App\Actions;

use App\Services\CommentaryTree;
use App\Services\DataCite;
use RuntimeException;
use Statamic\Actions\Action;
use Statamic\Contracts\Entries\Entry;

class RegisterCommentaryDoi extends Action
{
    public static function title()
    {
        return __('Register DOI');
    }

    public function visibleTo($item)
    {
        return $this->context['view'] === 'form'
            && $item instanceof Entry
            && $item->collectionHandle() === 'commentaries'
            && $item->blueprint()->handle() === 'commentary';
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
        return __('Register DOI');
    }

    public function confirmationText()
    {
        return __('A new draft DOI will be registered with DataCite for the current version of this commentary and saved to this entry. Are you sure?');
    }

    public function run($entries, $values)
    {
        $entry = $entries->first();

        if (! $entry->published()) {
            throw new RuntimeException(__('Only published commentaries can be registered, because the DOI has to point at a page a reader can open.'));
        }

        if ($entry->locale() !== 'de') {
            throw new RuntimeException(__('DOIs are registered against the German original, not its translations.'));
        }

        if ($entry->hasWorkingCopy()) {
            throw new RuntimeException(__('This commentary has unpublished changes. Publish or discard them first, otherwise publishing them later would overwrite the DOI.'));
        }

        $legalDomain = CommentaryTree::findLegalDomainAncestor($entry, $entry->locale());

        if (! $legalDomain) {
            throw new RuntimeException(__('This commentary does not sit under a legislative act, so its DOI cannot be built.'));
        }

        $datacite = new DataCite;
        $doi = $datacite->registerCommentary($entry, $legalDomain);
        $datacite->storeDoi($entry, $doi);

        return __('DOI :doi registered as a draft.', ['doi' => $doi]);
    }
}
