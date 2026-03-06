{{ content }}
    {{ if type == "text" }}

        {{ text }}

    {{ elseif type == "image" }}

        <figure>
            {{ image }}
                <img src="{{ config:app:url }}{{ url }}" alt="{{ alt }}" />
            {{ /image }}
            {{ if title || description }}
                <figcaption>
                    {{ if title }}<strong>{{ title | sanitize }}</strong>{{ /if }}
                    {{ if title && description }}<br>{{ /if }}
                    {{ if description }}<em>{{ description | sanitize }}</em>{{ /if }}
                </figcaption>
            {{ /if }}
        </figure>

    {{ elseif type == "image_embed" }}

        <figure>
            {{ image }}
                <img src="{{ url }}" alt="{{ alt }}" />
            {{ /image }}
            {{ if title || description }}
                <figcaption>
                    {{ if title }}<strong>{{ title | sanitize }}</strong>{{ /if }}
                    {{ if title && description }}<br>{{ /if }}
                    {{ if description }}<em>{{ description | sanitize }}</em>{{ /if }}
                </figcaption>
            {{ /if }}
        </figure>

    {{ elseif type == "media_grid" }}

        <div class="media-grid">
            {{ media_grid }}
                {{ if index % 2 == 0 }}<div class="media-grid-row">{{ /if }}
                <div class="media-grid-cell">
                    {{ if type == "image" }}
                        <figure>
                            {{ image }}
                                <img src="{{ config:app:url }}{{ url }}" alt="{{ alt }}" />
                            {{ /image }}
                            {{ if title || description }}
                                <figcaption>
                                    {{ if title }}<strong>{{ title | sanitize }}</strong>{{ /if }}
                                    {{ if title && description }}<br>{{ /if }}
                                    {{ if description }}<em>{{ description | sanitize }}</em>{{ /if }}
                                </figcaption>
                            {{ /if }}
                        </figure>
                    {{ elseif type == "image_embed" }}
                        <figure>
                            {{ image }}
                                <img src="{{ url }}" alt="{{ alt }}" />
                            {{ /image }}
                            {{ if title || description }}
                                <figcaption>
                                    {{ if title }}<strong>{{ title | sanitize }}</strong>{{ /if }}
                                    {{ if title && description }}<br>{{ /if }}
                                    {{ if description }}<em>{{ description | sanitize }}</em>{{ /if }}
                                </figcaption>
                            {{ /if }}
                        </figure>
                    {{ else }}
                        <figure>
                            <div class="qr-media">
                                <img src="{{ qr_code }}" class="qr-code-img" />
                                <a href="{{ article_url }}">{{ article_url }}</a>
                            </div>
                            {{ if title || description }}
                                <figcaption>
                                    {{ if title }}<strong>{{ title | sanitize }}</strong>{{ /if }}
                                    {{ if title && description }}<br>{{ /if }}
                                    {{ if description }}<em>{{ description | sanitize }}</em>{{ /if }}
                                </figcaption>
                            {{ /if }}
                        </figure>
                    {{ /if }}
                </div>
                {{ if index % 2 == 1 || last }}</div>{{ /if }}
            {{ /media_grid }}
        </div>

    {{ else }}

        <figure>
            <div class="qr-media">
                <img src="{{ qr_code }}" class="qr-code-img" />
                <a href="{{ article_url }}">{{ article_url }}</a>
            </div>
            {{ if title || description }}
                <figcaption>
                    {{ if title }}<strong>{{ title | sanitize }}</strong>{{ /if }}
                    {{ if title && description }}<br>{{ /if }}
                    {{ if description }}<em>{{ description | sanitize }}</em>{{ /if }}
                </figcaption>
            {{ /if }}
        </figure>

    {{ /if }}
{{ /content }}
