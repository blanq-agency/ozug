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

      media-mute-button + media-volume-range {
        display: none;
      }
      @media (min-width: 640px) {
        media-mute-button:hover + media-volume-range,
        media-volume-range:hover {
          display: flex;
        }
      }
    </style>
  {{ /push:styles }}

  {{ push:scripts }}
    <script type="module" src="{{ mix src='/js/media.js' }}"></script>
  {{ /push:scripts }}
{{ /once }}
