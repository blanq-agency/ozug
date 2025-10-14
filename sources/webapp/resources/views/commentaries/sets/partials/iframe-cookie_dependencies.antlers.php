{{ once }}
  {{ push:styles }}
    <style></style>
  {{ /push:styles }}

  {{ push:scripts }}
    <script type="module" src="{{ mix src='/js/iframe-cookie.js' }}"></script>
  {{ /push:scripts }}
{{ /once }}
