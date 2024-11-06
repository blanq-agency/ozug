<template>
  <div class="flex flex-col">
    <div v-if="showHeaderLine" class="px-4 py-2 space-y-2 bg-white border-b border-black lg:flex lg:items-center lg:justify-between lg:space-y-0 md:px-6">
      <div class="text-xs font-medium tracking-wider uppercase">
        {{ $t('commentaries') }}
      </div>

      <div class="flex flex-col space-y-2 lg:flex-row lg:space-x-2 lg:space-y-0">
        <FlyoutMenuWithDividers
          v-if="legalDomains.length > 0"
          class="lg:min-w-[300px] lg:max-w-[300px] xl:min-w-[450px] xl:max-w-[450px] rounded-md uppercase tracking-wider"
          :label="$t('legal_domain_filter_label')"
          :options="legalDomains"
          :active-option="activeLegalDomain"
          @changed="onFilter"
        />
      </div>
    </div>

    <div v-if="showTitleLine" class="px-4 py-2 bg-white md:px-12 lg:px-24 xl:px-32 lg:py-12 border-b border-black">
      <div class="flex flex-col items-center">
        <div class="font-serif text-3xl md:text-4xl xl:text-5xl text-center">{{ title }}</div>
      </div>
    </div>

    <GridListView
      :items="filteredCommentaries"
      class="sm:gap-px">
      <template v-slot:item="commentary">
        <a
          class="h-[310px] md:h-[420px] xl:h-[500px] relative group transition ease-in-out delay-150 bg-white hover:bg-ok-orange p-4 md:p-8 cursor-pointer"
          :href="'/' + locale + '/kommentierungen/' + commentary.slug">

          <div class="relative flex flex-col items-center w-full h-full">

            <div v-if="commentary.legal_domain" class="mb-8 text-xs tracking-wider text-center uppercase">
              {{ commentary.legal_domain.label }}
            </div>
            <div v-else class="mb-8 text-xs tracking-wider text-center uppercase">
              &nbsp;
            </div>

            <h2 class="my-4 font-serif text-3xl font-medium text-center lg:text-4xl 2xl:text-5xl lg:my-8">
              {{ commentary.title }}
            </h2>

            <div class="text-sm text-center lg:text-base attribution">
              <p v-if="commentary.assigned_authors.length > 0 && commentary.assigned_authors[0] !== ''">
                {{ $t('commentary_by') }} <i>{{ commentary.assigned_authors.join(' ' + $t('and') + ' ') }}</i>
              </p>
              <p v-if="commentary.assigned_editors.length > 0 && commentary.assigned_editors[0] !== ''">
                {{ $t('edited_by') }} <i>{{ commentary.assigned_editors.join(' ' + $t('and') + ' ') }}</i>
              </p>
            </div>

            <div class="absolute bottom-0 flex w-full">
              <button
                type="button"
                class="pb-10 mx-auto ok-button">
                {{ $t('view_commentary') }}
              </button>
            </div>
          </div>
        </a>
      </template>
    </GridListView>
  </div>
</template>

<script setup>
  import { ref } from 'vue'
  import GridListView from './Partials/GridListView'
  import FlyoutMenuWithDividers from '@/components/Menus/FlyoutMenuWithDividers'

  const props = defineProps({
    locale: { type: String, required: true },
    commentaries: { type: Array, required: true },
    legalDomains: { type: Array, required: false, default: [] },
    showHeaderLine: { type: Boolean, required: false, default: true },
    showTitleLine: { type: Boolean, required: false, default: false },
    title: { type: String, required: false }
  })


  const filteredCommentaries = ref(props.commentaries)

  const activeLegalDomain = ref(props.legalDomain ? props.legalDomains[0] : null)

  const onFilter = (legalDomain) => {
    // reset the list of filtered commentaries
    filteredCommentaries.value = props.commentaries

    if (legalDomain.id) {
      filteredCommentaries.value = filteredCommentaries.value.filter(commentary => {
        return commentary.legal_domain?.id === legalDomain.id
      })
    }
  }
</script>

<style lang="postcss" scoped>
  .attribution p, h2 {
    @apply leading-snug;
    hyphens: auto;
    -webkit-hyphens: auto;
    -moz-hyphens: auto;
    word-wrap: break-word;
    overflow-wrap: break-word;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    max-width: 100%;
  }
  @media (min-width: 768px) {
    .attribution p, h2 {
      -webkit-line-clamp: 3;
    }
  }
</style>
