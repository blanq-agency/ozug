<figure class="mx-auto min-w-0 {{ width_limit ? 'max-w-[476px]' : 'max-w-full' }}" id="{{ id }}">
  {{ if link }}<a href="{{ link }}" target="_blank" rel="noopener">{{ /if }}
    {{ image }}
      <img
        src="{{ url }}"
        alt="{{ alt }}"
        width="{{ width }}"
        height="{{ height }}"
      />
    {{ /image }}
  {{ if link }}</a>{{ /if }}
  {{ partial src="commentaries/sets/partials/caption" }}
</figure>
