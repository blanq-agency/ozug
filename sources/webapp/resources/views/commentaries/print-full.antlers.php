<div class="volume-page">
    <img src="{{ config:app:url }}/img/oak-logo-text.svg" class="header-logo">
    <h1 class="header-title">Open Access Kommentar</h1>
    <p class="volume-page-info">{{ generation_date }}</p>
    {{ if total_volumes > 1 }}
        <p class="volume-page-info">{{ trans:volume }} {{ volume_number }} / {{ total_volumes }}</p>
    {{ /if }}
</div>

<nav class="entries-toc">
    <p class="header-label">{{ trans:table_of_contents }}</p>
    {{ toc_html }}
</nav>

{{ entries }}

    <span class="running-title">{{ title }}</span>
    <span class="running-authors">{{ assigned_authors | pluck('name') | join(' / ') }}</span>
    <span class="running-date">{{ trans:status_of_processing }} {{ date format="d.m.Y" }}</span>

    <header class="header" id="entry-{{ id }}">
        {{ if original_language && original_language !== site }}
            <div class="header-translation">
                {{ trans key="ATTENTION: This version of the commentary is an automatic machine translation of the original. The original version is in :original_language. The translation was done with www.deepl.com. Only the original version is authoritative. The translated form of the commentary cannot be cited." original_language="{ trans :key="original_language" }" }}
            </div>
        {{ /if }}
        <p class="header-label">{{ trans:commentary_on }}</p>
        <h2 class="header-title">{{ title }}</h2>
        <p class="header-authors">
            {{ trans:commentary_by }} {{ assigned_authors | pluck('name') | join(' / ') }}<br>
            {{ trans:edited_by }} {{ assigned_editors | pluck('name') | join(' / ') }}
        </p>
    </header>

    <section class="status-of-processing">
        <p>{{ trans:status_of_processing }} {{ date format="d.m.Y" }}</p>
    </section>

    <section class="citation">
        <p class="citation-label">
            {{ trans:suggested_citation }}
        </p>
        <p class="citation-text">
            {{ suggested_citation_long }}
        </p>
        <p class="citation-text">
            {{ trans:short_citation }}: {{ suggested_citation_short }}
        </p>
    </section>

    {{ if legal_text }}
        <section class="legal-text">
            {{ legal_text }}
        </section>
    {{ /if }}

    {{ if toc }}
        <section class="entry-toc">
            <p class="header-label">
                {{ trans:table_of_contents }}
            </p>
            {{ toc }}
        </section>
    {{ /if }}

    <div class="section content">
        {{ rendered_content }}
    </div>

{{ /entries }}
