<figure class="mx-auto {{ width_limit ? 'max-w-[476px]' : null }}">
  {{ image }}
    <img
      src="{{ glide :src="url" }}"
      alt="{{ alt }}"
      width="{{ width }}"
      height="{{ height }}"
    />
  {{ /image }}
  {{ partial src="commentaries/sets/partials/caption" }}
</figure>
