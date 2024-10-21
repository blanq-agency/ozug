{{ partial src="commentaries/sets/partials/media_chrome_dependencies" }}

<figure class="mx-auto {{ width_limit ? 'max-w-[476px]' : null }}">
  <media-controller>
    {{ video }}
      <video
        slot="media"
        src="{{ glide :src="url" }}"
        width="{{ width }}"
        height="{{ height }}"
      >
        <source src="{{ url }}" type="{{ mime_type }}" />
      </video>
    {{ /video }}
    <media-control-bar>
      <media-play-button></media-play-button>
      <media-time-range class="z-0"></media-time-range>
      <media-time-display showduration></media-time-display>
      <media-captions-menu hidden anchor="auto"></media-captions-menu>
      <media-captions-menu-button ></media-captions-menu-button>
      <media-fullscreen-button></media-fullscreen-button>
    </media-control-bar>
  </media-controller>
  {{ partial src="commentaries/sets/partials/caption" }}
</figure>
