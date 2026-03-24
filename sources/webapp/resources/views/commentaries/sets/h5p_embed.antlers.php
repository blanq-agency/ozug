{{ partial src="commentaries/sets/partials/h5p_dependencies" }}
{{ partial src="commentaries/sets/partials/iframe-cookie_dependencies" }}

<figure class="mx-auto w-full {{ width_limit ? 'max-w-[476px]' : null }}">
    <div class="relative w-full aspect-video" data-oak-marketing-cookies-consent>
        <iframe
          data-src="{{ source }}"
          frameborder="0"
          class="w-full h-full"
          title="{{ titel }}"
        ></iframe>
      <div class="absolute inset-0 grid place-items-center bg-ok-beige" data-oak-marketing-cookies-consent-banner>
        <div class="no-prose text-center p-4">
          <p class="">Marketing-Cookies akzeptieren um dieses Video anzuzeigen.</p>
          <button class="ok-button" data-oak-marketing-cookies-consent-button>Akzeptieren</button>
        </div>
      </div>
    </div>
</figure>
