<template>
  <header class="w-full pl-5 pr-4 mx-auto md:pl-12 md:pr-4 lg:px-8 xl:w-10/12 print:hidden">
    <div
      class="flex items-center justify-between px-4 py-4"
      :class="{ 'border-b border-black': currentPage !== 'home' }">
      <a
        class="z-40 hidden lg:flex items-end"
        :href="'/' + locale"
        :style="{ height: currentPage !== 'home' ? '78px' : '108px' }"
      >
        <img
          :src="'/img/oak-logo-text.svg'"
          alt="Onlinekommentar – der frei zugängliche Rechtskommentar"
          title="Onlinekommentar – der frei zugängliche Rechtskommentar"
          style="height: 80%"
        />
      </a>
      <a
        class="z-40 flex w-10 md:w-16 lg:hidden lg:w-auto"
        :href="'/' + locale">
        <img
          src="/img/oak-logo.svg"
          alt="Onlinekommentar – der frei zugängliche Rechtskommentar"
          title="Onlinekommentar – der frei zugängliche Rechtskommentar"
        />
      </a>
      <div>
        <AppNav
          :locale="locale"
          :locales="locales"
          :query="query">
        </AppNav>
      </div>

      <AppSidebar>
        <template v-slot:content>
          <slot name="sidebar-content" />
        </template>
      </AppSidebar>
    </div>
  </header>
</template>

<script setup>
  import AppNav from './AppNav.vue'
  import AppSidebar from './AppSidebar.vue'

  defineProps({
    currentPage: { type: String, required: true },
    locale: { type: String, required: true },
    locales: { type: Array, required: true },
    query: { type: String, required: false }
  })
</script>
