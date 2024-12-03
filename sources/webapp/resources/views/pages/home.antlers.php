<?php

use Statamic\Facades\Entry;

if ($show_latest_commentaries) {
    // get the latest 3 commentaries that have valid content
    $commentaries = Entry::query()
      ->where('collection', 'commentaries')
      ->where('locale', app()->getLocale())
      ->where('status', 'published')
      ->whereNotNull('content')
      ->limit(3)
      ->orderBy('updated_at', 'desc')
      ->get()
      ->map(function ($commentary, $key) {
        return [
          'id' => $commentary['id'],
          'slug' => $commentary['slug'],
          'title' => $commentary['title'],
          'legal_domain' => Entry::query()
            ->where('collection', 'legal_domains')
            ->where('id', $commentary->value('legal_domain'))
            ->get()
            ->map(function ($legal_domain, $key) {
              return [
                'id' => $legal_domain['id'],
                'label' => __($legal_domain['title']),
              ];
            })
            ->first(),
          'assigned_authors' => $commentary['assigned_authors']->map(function ($author, $key) {
            return $author['name'];
          })->toArray(),
          'assigned_editors' => $commentary['assigned_editors']->map(function ($editor, $key) {
            return $editor['name'];
          })->toArray(),
        ];
      })
      ->toArray();
}
?>

<div class="max-w-3xl mx-auto mb-auto mt-8 p-6">
  <div class="mt-8 text-4xl leading-snug">{{ content }}</div>
  <a
    href="/{{ locale }}/ueber-openaccesskommentar"
    class="inline-block mt-4 uppercase rounded-full border border-black text-xs px-4 py-2 font-medium tracking-widest">
    {{ trans:home_more_link }}
  </a>
</div>


{{ if legislative_acts }}
  {{?
    use Statamic\Facades\Entry;

    $legal_domains = Entry::query()
      ->where('collection', 'commentaries')
      ->where('blueprint', 'legal_domain')
      ->where('locale', app()->getLocale())
      ->where('status', 'published')
      ->whereIn('legal_domain', $page->legislative_acts->map(function ($legislative_act) {
        return $legislative_act['id'];
      })->toArray())
      ->get();
  ?}}

  <div class="mt-16 flex justify-between text-sm uppercase">
    <span>{{ "Legislative Acts" | trans }}</span>
  </div>
  <p class="mt-2">
    <div class="overflow-hidden divide-y divide-gray-800 sm:divide-y-0 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 sm:gap-px">
      {{ legislative_acts }}
        <a
          href="{{ locale}}/kommentierungen/{{$ $legal_domains->first(function ($value) use ($legislative_acts, $index) {
            return in_array($legislative_acts->raw()[$index], $value->toArray()['legal_domain']);
          })['slug'] $}}"
          class="
            h-[262px] md:h-[372px] xl:h-[452px] relative group
            cursor-pointer
            bg-white hover:bg-ok-orange
            p-4 md:p-8
            transition ease-in-out delay-150
          "
        >
          <div class="relative flex flex-col items-center w-full h-full">
            <h2
              class="
                max-w-full
                font-serif font-medium text-3xl text-center lg:text-4xl 2xl:text-5xl
                line-clamp-2 md:line-clamp-3
                hyphens-auto break-words
                my-4 lg:my-8
              "
              style="line-height: 1.375"
            >{{ title }}</h2>

            <div class="text-sm text-center lg:text-base attribution">
              {{ if assigned_editors }}
                <p>
                  {{ 'edited_by' | trans }}
                  <i>
                    {{ assigned_editors as="editors"}}
                      {{ editors | pluck("name") | join(" " + { "and" | trans } + " ") }}
                    {{ /assigned_editors }}
                  </i>
                </p>
              {{ /if assigned_editors }}
            </div>

            <div class="absolute bottom-0 flex w-full">
              <button
                type="button"
                class="pb-10 mx-auto ok-button">
                {{ trans:view_commentary }}
              </button>
            </div>
          </div>
        </a>
      {{ /legislative_acts }}
    </div>
  </p>
{{ /if legislative_acts }}


{{ if show_latest_commentaries }}
  <div class="mt-16 flex justify-between text-sm uppercase">
    <span>{{ trans:newest_comments }}</span>
    <div class="flex">
      <a href="{{ locale}}/kommentierungen"><span class="mr-2">{{ trans:all_comments }}</span>
        <svg class="inline-block mb-1" xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11">
          <g id="Gruppe_51" data-name="Gruppe 51" transform="translate(10.5 -16.116) rotate(90)">
            <g id="Icon_feather-arrow-up" data-name="Icon feather-arrow-up" transform="translate(16.822)">
              <path id="Pfad_15" data-name="Pfad 15" d="M18,17.5V7.5" transform="translate(-13 -7.5)" fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="1"/>
              <path id="Pfad_16" data-name="Pfad 16" d="M7.5,13.178l5-5.678,5,5.678" transform="translate(-7.5 -7.5)" fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="1"/>
            </g>
          </g>
        </svg>
      </a>
    </div>
  </div>
  <p class="mt-2">
    <commentaries
      locale="{{ locale }}"
      :commentaries='<?= json_encode($commentaries, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>'
      :show-header-line="false"
    ></commentaries>
  </p>
{{ /if }}
