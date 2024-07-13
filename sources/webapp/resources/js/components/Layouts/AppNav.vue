<template>
  <Transition>
    <div v-show="showMenu" class="absolute top-0 left-0 z-10 w-screen h-screen bg-ok-yellow">
      <nav class="flex items-center justify-center h-screen mr-4 text-3xl text-right md:justify-end lg:mr-32">
        <ul class="flex flex-col space-y-5 list-style-none">
          <NavLink
            :href="'/' + locale + '/kommentare'"
            :active="false"
            @click="toggleMenu">
            {{ $t('commentaries') }}
          </NavLink>

          <NavLink
            :href="'/' + locale + '/autoren'"
            :active="false"
            @click="toggleMenu">
            {{ $t('authors') }}
          </NavLink>

          <NavLink
            :href="'/' + locale + '/herausgeber'"
            :active="false"
            @click="toggleMenu">
            {{ $t('editors') }}
          </NavLink>

          <NavLink
            :href="'/' + locale + '/ueber-onlinekommentar'"
            :active="false"
            @click="toggleMenu">
            {{ $t('about') }}
          </NavLink>

          <NavLink
            :href="'/' + locale + '/contact'"
            :active="false"
            @click="toggleMenu">
            {{ $t('contact') }}
          </NavLink>
        </ul>
      </nav>
    </div>
  </Transition>

  <nav>
    <ul class="flex items-center space-x-0 sm:space-x-2 md:space-x-6">
      <li id="nav-search" class="cursor-pointer">
        <slot name="search-box" />
      </li>

      <LanguageSelector
        id="nav-lang-switcher"
        as="li"
        :languages="locales.count > 0 ? locales : defaultLocales"
        :active-language="locale"
      />

      <li id="nav-menu" @click="toggleMenu" class="z-50">
        <button type="button" class="inline-flex items-center justify-center w-20 py-1 mb-2 mr-2 text-xs font-medium tracking-wider text-center uppercase border border-black rounded-full md:w-28 md:py-2 lg:py-3 lg:w-32">
          {{ $t('menu') }}
          <svg v-show="showMenu" class="w-2 h-2 ml-2 lg:w-4 lg:h-4" xmlns="http://www.w3.org/2000/svg" width="14.707" height="14.707" viewBox="0 0 14.707 14.707">
            <g id="X" transform="translate(-1792.387 -75.877)">
              <g id="Gruppe_17" data-name="Gruppe 17" transform="translate(1792.741 90.231) rotate(-45)">
                <line id="Linie_2" data-name="Linie 2" x2="19.799" fill="none" stroke="#000" stroke-width="1"/>
              </g>
              <g id="Gruppe_18" data-name="Gruppe 18" transform="translate(1806.741 90.231) rotate(-135)">
                <line id="Linie_2-2" data-name="Linie 2" x2="19.799" transform="translate(0 0)" fill="none" stroke="#000" stroke-width="1"/>
              </g>
            </g>
          </svg>
        </button>
      </li>
    </ul>
  </nav>
</template>

<script setup>
  import { ref } from 'vue'
  import NavLink from './Partials/NavLink.vue'
  import LanguageSelector from './Partials/LanguageSelector.vue'

  defineProps({
    locale:  { type: String, required: true },
    locales: { type: Array, required: true },
  })

  const showMenu = ref(false)

  const defaultLocales = ['de', 'en', 'fr']

  const toggleMenu = () => {
    showMenu.value = !showMenu.value
  }
</script>

<style lang="postcss" scoped>
  .v-enter-active,
  .v-leave-active {
    transition: opacity 0.5s ease;
  }

  .v-enter-from,
  .v-leave-to {
    opacity: 0;
  }
</style>
