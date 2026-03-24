{{ once }}
  {{ push:styles }}
    <style></style>
  {{ /push:styles }}

  {{ push:scripts }}
    <script type="module" src="{{ mix src='/js/h5p-resizer.js' }}"></script>
  {{ /push:scripts }}
{{ /once }}
