{{ if title || description }}
  <figcaption class="mt-2 lg:mt-4">
    {{ if title }}
      <span class="text-sm lg:text-base font-medium">{{ title | sanitize }}</span>
    {{ /if }}
    {{ if title && description }}
      <br>
    {{ /if }}
    {{ if description }}
      <span class="text-sm lg:text-base italic">{{ description | sanitize }}</span>
    {{ /if }}
  </figcaption>
{{ /if }}
