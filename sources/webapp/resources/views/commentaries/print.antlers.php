<header class="header">
    <span class="running-title">{{ title }}</span>
    <span class="running-authors">{{ assigned_authors | pluck('name') | join(' / ') }}</span>
    <img src="{{ config:app:url }}/img/oak-logo-text.svg" class="header-logo">
    {{ if original_language && original_language !== site }}
        <div class="header-translation">
            {{ trans key="ATTENTION: This version of the commentary is an automatic machine translation of the original. The original version is in :original_language. The translation was done with www.deepl.com. Only the original version is authoritative. The translated form of the commentary cannot be cited." original_language="{ trans :key="original_language" }" }}
        </div>
    {{ /if }}
    {{ unless hide_labels }}
        <p class="header-label">
            {{ trans:commentary_on }}
        </p>
    {{ /unless }}
    <h1 class="header-title">
        {{ title }}
    </h1>
    <p class="header-authors">
        {{ unless hide_labels }}{{ trans:commentary_by }} {{ /unless }}{{ assigned_authors | pluck('name') | join(' / ') }}<br>
        {{ trans:edited_by }} {{ assigned_editors | pluck('name') | join(' / ') }}
    </p>
</header>

<section class="status-of-processing">
    <p>{{ trans:status_of_processing }} {{ date format="d.m.Y" }}</p>
    {{ licenses }}
        <p>{{ trans:license }}: <a href="{{ extern_url }}">{{ title_long }}</a></p>
    {{ /licenses }}
    {{ if doi }}
        <p>DOI: {{ doi }}</p>
    {{ /if }}
</section>

{{ if suggested_citation_long || suggested_citation_short }}
    <section class="citation">
        {{ if suggested_citation_long }}
            <p class="citation-label">
                {{ trans:suggested_citation }}
            </p>
            <p class="citation-text">
                {{ suggested_citation_long }}
            </p>
        {{ /if }}
        {{ if suggested_citation_short }}
            <p class="citation-text">
                {{ trans:short_citation }}: {{ suggested_citation_short }}
            </p>
        {{ /if }}
    </section>
{{ /if }}

{{ if legal_text }}
    <section class="legal-text">
        {{ legal_text }}
    </section>
{{ /if }}

<section class="entry-toc">
    <p class="header-label">
        {{ trans:table_of_contents }}
    </p>
    {{ toc }}
</section>

<main class="content">
    {{ content }}
</main>
