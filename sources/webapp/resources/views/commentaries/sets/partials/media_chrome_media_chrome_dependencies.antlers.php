{{ once }}
  {{ push:styles }}
    <style>
      media-controller {
        --media-tooltip-display: none;
      }
    </style>
  {{ /push:styles }}

  {{ push:scripts }}
    <script src="{{ mix src='/js/media.js' }}" defer></script>
  {{ /push:scripts }}
{{ /once }}
