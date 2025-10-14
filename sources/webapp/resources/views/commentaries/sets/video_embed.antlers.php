{{ partial src="commentaries/sets/partials/media_chrome_dependencies" }}
{{ partial src="commentaries/sets/partials/iframe-cookie_dependencies" }}

<figure class="mx-auto w-full {{ width_limit ? 'max-w-[476px]' : null }}">
  {{ if
    video | contains("youtube.com/watch")
    or video | contains('vimeo.com/')
    or video | contains('commons.wikimedia.org/wiki/')
  }}
    <div class="relative w-full aspect-video" data-oak-marketing-cookies-consent>
      {{ if
        video | contains("youtube.com/watch")
        or video | contains('vimeo.com/')
      }}
        <iframe
          data-src="{{ video | embed_url }}"
          frameborder="0"
          class="w-full h-full"
        ></iframe>
      {{ else video | contains('commons.wikimedia.org/wiki/') }}
        <iframe
          data-src="{{ video + ((video | contains('?')) ? '&embedplayer=yes' : '?embedplayer=yes') }}"
          frameborder="0"
          class="w-full h-full"
        ></iframe>
      {{ /if }}
      <div class="absolute inset-0 grid place-items-center bg-ok-beige" data-oak-marketing-cookies-consent-banner>
        <div class="no-prose text-center p-4">
          <p class="">Marketing-Cookies akzeptieren um dieses Video anzuzeigen.</p>
          <button class="ok-button" data-oak-marketing-cookies-consent-button>Akzeptieren</button>
        </div>
      </div>
    </div>
  {{ else }}
    {{ partial src="commentaries/sets/partials/media_chrome_dependencies" }}
    <media-controller class="w-full">
      <video
        slot="media"
        src="{{ glide :src="video" }}"
        width="{{ width }}"
        height="{{ height }}"
      >
        <source src="{{ url }}" type="{{ mime_type }}" />
      </video>
      {{ partial src="commentaries/sets/partials/video_media_control_bar" }}
    </media-controller>
  {{ /if }}
  {{ partial src="commentaries/sets/partials/caption" }}
</figure>
