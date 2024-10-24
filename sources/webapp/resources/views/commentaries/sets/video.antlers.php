{{ partial src="commentaries/sets/partials/media_chrome_dependencies" }}

<figure class="mx-auto {{ width_limit ? 'max-w-[476px]' : null }}">
  <media-controller class="w-full">
    {{ video }}
      <video
        slot="media"
        src="{{ glide :src="url" }}"
        width="{{ width }}"
        height="{{ height }}"
      >
        <source src="{{ url }}" type="{{ mime_type }}" />
        {{ captions }}
          <track label="{{ srclang:value | trans }}" kind="captions" srclang="{{ srclang:value }}" src="{{ file:url }}" />
        {{ /captions }}
      </video>
    {{ /video }}
    {{ partial src="commentaries/sets/partials/video_media_control_bar" }}
  </media-controller>
  {{ partial src="commentaries/sets/partials/caption" }}
</figure>
