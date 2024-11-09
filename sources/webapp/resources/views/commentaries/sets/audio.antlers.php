{{ partial src="commentaries/sets/partials/media_chrome_dependencies" }}

<figure class="flex flex-col">
  <media-controller audio>
    {{ audio }}
      <audio slot="media" src="{{ url }}"></audio>
    {{ /audio }}
    <media-control-bar class="w-full">
      <media-play-button></media-play-button>
      <media-mute-button></media-mute-button>
      <media-volume-range></media-volume-range>
      <media-time-range class="z-0"></media-time-range>
      <media-time-display showduration></media-time-display>
    </media-control-bar>
  </media-controller>
  {{ partial src="commentaries/sets/partials/caption" }}
</figure>
