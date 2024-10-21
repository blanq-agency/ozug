{{ once }}
  {{ push:styles }}
    <style>
      media-controller {
        --media-tooltip-display: none;
        --media-primary-color: black;
        --media-secondary-color: #f4f4f2;
        --media-control-hover-background: hsl(60, 8%, 90%);

        --media-range-track-background: white;
        --media-preview-time-text-shadow: none;
      }
    </style>
  {{ /push:styles }}

  {{ push:scripts }}
    <script src="{{ mix src='/js/media.js' }}" defer></script>
  {{ /push:scripts }}
{{ /once }}
