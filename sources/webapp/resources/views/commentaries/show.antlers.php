<article class="commentary w-full">
  <commentary
    app-name="{{ config:app:name }}"
    locale="{{ locale }}"
    base-path-prefix="{{ base_path_prefix }}"
    :commentary="{
      id: '{{ id }}',
      slug: '{{ slug }}',
      title: '{{ title }}',
      doi: '{{ doi }}',
      date: '{{ date iso_format="DD.MM.YYYY" }}',
      assigned_editors: [
        {{ foreach:assigned_editors as="assigned_editor" }}
          {
            {{ foreach:assigned_editor }}
              '{{ key }}': '{{ value | add_slashes | sanitize:true }}',
            {{ /foreach:assigned_editor }}
          },
        {{ /foreach:assigned_editors }}
      ],
      assigned_authors: [
        {{ foreach:assigned_authors as="assigned_author" }}
          {
            {{ foreach:assigned_author }}
              '{{ key }}': '{{ value | add_slashes | sanitize:true }}',
            {{ /foreach:assigned_author }}
          },
        {{ /foreach:assigned_authors }}
      ],
      legal_text: '{{ legal_text | add_slashes | sanitize:true }}',
      suggested_citation_long: '{{ suggested_citation_long | collapse_whitespace | add_slashes | sanitize:true }}',
      suggested_citation_short: '{{ suggested_citation_short | add_slashes | sanitize:true }}',
      original_language: '{{ original_language }}',
      locale: '{{ locale }}',
      pdf_commentary_path: '<?php echo Storage::url('commentaries/pdf/') ?>',
      pdf_commentary_filename: '{{ pdf_commentary:basename }}',
      additional_documents: {{ additional_documents | to_json | sanitize:true }},
    }"
    :versions="[
      {{ revisions:commentary :id='id' :locale='locale' }}
        {
          id: '{{ unix_timestamp }}',
          timestamp: '{{ unix_timestamp }}',
          label: '{{ human_readable_timestamp }}',
          label_date_only: '{{ human_readable_timestamp_date_only }}'
        },
      {{ /revisions:commentary }}
    ]"
    version-timestamp="{{ versionTimestamp }}">
    <template v-slot:table-of-contents>
      {{ toc }}
    </template>

    <template v-slot:content>
      {{ content }}
        {{ if type == "text" }}
          {{ text }}

        {{ elseif type == "media_grid" }}
          <div class="
            max-lg:max-w-[476px] grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-4
            mt-8 mb-10 max-lg:mx-auto"
          >
            {{ media_grid }}
              {{ partial src="commentaries/sets/{type}" }}
            {{ /media_grid }}
          </div>

        {{ else }}
          <div class="mt-8 mb-10">
            {{ partial src="commentaries/sets/{type}" }}
          </div>

        {{ /if }}
      {{ /content }}
    </template>

    {{ scope:page }}
      {{ licenses }}
        <template v-slot:license>
          <h2 class="mt-12 mb-4 font-sans text-xl tracking-wider uppercase">
            {{ 'creative_commons_license' | trans }}
          </h2>
          <p>
            {{ config:app:name }}, {{ 'commentary_on' | trans }} {{ page:title }}
            <span>
              {{ 'creative_commons_text' | trans }}
              <a href="{{ extern_url }}" class="underline">{{ title_long }} {{ 'license' | trans | ucfirst }}</a>.
            </span>
          </p>
          <p class="mt-4">
            <a href="{{ extern_url }}">
              <img src="{{ image }}" alt="Creative Commons" style="width: 116px">
            </a>
          </p>
        </template>
      {{ /licenses }}
    {{ /scope:page }}
  </commentary>

  {{ if versionComparisonResult }}
    <version-comparison-modal-dialog
      locale="{{ locale }}"
      :commentary="{
        slug: '{{ slug }}',
      }"
      version-timestamp="{{ versionTimestamp }}"
      :open="true">
      <template v-slot:title>
        {{$ __('compare_versions') $}}
      </template>
      <template v-slot:body>
        <div class="prose max-w-full version-comparison">
          {{ versionComparisonResult }}
        </div>
      </template>
    </version-comparison-modal-dialog>
  {{ /if }}
</article>
