{{ _id = id }}
<div class="accordion">
  {{ panels }}
    <details class="accordion__item" name="accordion-{{ _id }}">
      <summary>{{ title | sanitize }}</summary>
      <div class="accordion__content">
        {{ content }}
      </div>
    </details>
  {{ /panels }}
</div>
