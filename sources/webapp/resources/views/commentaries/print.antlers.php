<span class="running-title">{{ title }}</span>
<span class="running-authors">{{ assigned_authors | pluck('name') | join(' / ') }}</span>
<span class="running-date">{{ trans:status_of_processing }} {{ date format="d.m.Y" }}</span>

<header class="header">
    <img src="{{ config:app:url }}/img/oak-logo-text.svg" class="header-logo">
    {{ if original_language && original_language !== site }}
        <div class="header-translation">
            {{ trans key="ATTENTION: This version of the commentary is an automatic machine translation of the original. The original version is in :original_language. The translation was done with www.deepl.com. Only the original version is authoritative. The translated form of the commentary cannot be cited." original_language="{ trans :key="original_language" }" }}
        </div>
    {{ /if }}
    <p class="header-label">
        {{ trans:commentary_on }}
    </p>
    <h1 class="header-title">
        {{ title }}
    </h1>
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

<section class="legal-text">
    {{ legal_text }}
</section>

<section class="entry-toc">
    <p class="header-label">
        {{ trans:table_of_contents }}
    </p>
    {{ toc }}
</section>

<main class="content">
    {{ content }}
</main>
