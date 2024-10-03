require('./bootstrap')

import { createApp } from 'vue'
import { i18nVue } from 'laravel-vue-i18n'

import mitt from 'mitt'
const emitter = mitt()

import FloatingVue from 'floating-vue'
FloatingVue.options.themes.dropdown.placement = 'top'
FloatingVue.options.themes.dropdown.distance = 10

import LanguageSelector from '@/components/Layouts/Partials/LanguageSelector.vue'
import Commentaries from '@/components/Pages/Commentaries.vue'
import Commentary from '@/components/Pages/Commentary.vue'
import Footnote from '@/components/Pages/Partials/Footnote.vue'
import Authors from '@/components/Pages/Authors.vue'
import Editors from '@/components/Pages/Editors.vue'
import User from '@/components/Pages/User.vue'
import VersionComparisonModalDialog from '@/components/Pages/Partials/VersionComparisonModalDialog.vue'

const app = createApp({
  components: {
    'language-selector': LanguageSelector,
    'commentaries': Commentaries,
    'commentary': Commentary,
    'footnote': Footnote,
    'authors': Authors,
    'editors': Editors,
    'user': User,
    'version-comparison-modal-dialog': VersionComparisonModalDialog
  }
})

app.config.compilerOptions.isCustomElement = (tag) => [
  'app-nav',
  'app-sidebar',
].includes(tag)

app.config.globalProperties.emitter = emitter
app
  .use(FloatingVue)
  .use(i18nVue, {
    resolve: (lang) => import(`../../lang/${lang}.json`)
  })
  .mount('#app')


// app-nav
customElements.define('app-nav', class extends HTMLElement {

  connectedCallback() {
    this.querySelector('#app-nav__menu-toggle')
      .addEventListener('click', this.#toggle.bind(this))

    this.querySelectorAll('#app-nav__menu a').forEach(aTag => {
      aTag.addEventListener('click', this.#toggle.bind(this))
    })
  }

  #toggle() {
    const menu = this.querySelector('#app-nav__menu')

    if (menu.style.display) {
      // Show element.
      menu.style.removeProperty('display')
      menu.style.opacity = '0'
      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          menu.addEventListener('transitionend', () => {
            menu.style.removeProperty('transition')
            menu.style.removeProperty('opacity')
          }, { once: true })
          menu.style.transition = 'opacity 0.5s ease'
          menu.style.opacity = '1'
        })
      })
    }
    else {
      // Hide element.
      menu.style.transition = 'opacity 0.5s ease'
      menu.style.opacity = '0'
      menu.addEventListener('transitionend', () => {
        menu.style.removeProperty('transition')
        menu.style.removeProperty('opacity')
        menu.style.display = 'none'
      }, { once: true })
    }

    this.querySelector('#app-nav__menu-toggle >svg').classList.toggle('hidden')
  }
})


// app-sidebar
customElements.define('app-sidebar', class extends HTMLElement {

  connectedCallback() {
    this.querySelector('._handle').addEventListener('click', () => {
      this.classList.toggle('open')
      this.querySelector('._content').classList.toggle('hidden')
    })
  }
})
