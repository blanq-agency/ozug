require('./bootstrap')

import { createApp } from 'vue'
import { i18nVue } from 'laravel-vue-i18n'

import mitt from 'mitt'
const emitter = mitt()

import FloatingVue from 'floating-vue'
FloatingVue.options.themes.dropdown.placement = 'top'
FloatingVue.options.themes.dropdown.distance = 10

import AppNav from '@/components/Layouts/AppNav.vue'
import Commentaries from '@/components/Pages/Commentaries.vue'
import Commentary from '@/components/Pages/Commentary.vue'
import Footnote from '@/components/Pages/Partials/Footnote.vue'
import Authors from '@/components/Pages/Authors.vue'
import Editors from '@/components/Pages/Editors.vue'
import User from '@/components/Pages/User.vue'
import VersionComparisonModalDialog from '@/components/Pages/Partials/VersionComparisonModalDialog.vue'

const app = createApp({
  components: {
    'app-nav': AppNav,
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
  'app-sidebar',
  'app-search-box',
].includes(tag)

app.config.globalProperties.emitter = emitter
app
  .use(FloatingVue)
  .use(i18nVue, {
    resolve: (lang) => import(`../../lang/${lang}.json`)
  })
  .mount('#app')


// app-sidebar
customElements.define('app-sidebar', class extends HTMLElement {

  constructor() {
    super()
  }

  connectedCallback() {
    this.querySelector('._handle').addEventListener('click', () => {
      this.classList.toggle('open')
      this.querySelector('._content').classList.toggle('hidden')
    })
  }
})


// app-search-box
customElements.define('app-search-box', class extends HTMLElement {

  #isOpen = false

  constructor() {
    super()
  }

  connectedCallback() {
    this.querySelector('button').addEventListener('click', (e) => {
      if (!this.#isOpen) this.#showSearchBox()
      else this.#hideSearchBox()
    })
  }

  #showSearchBox() {
    this.querySelector('button').classList.add('bg-white', 'border-b-2', 'border-black')
    this.querySelector('input').classList.toggle('hidden')
    this.#isOpen = true

    setTimeout(() => {
      this.querySelector('input').focus()
    }, 100)
  }

  #hideSearchBox() {
    this.querySelector('button').classList.remove('bg-white', 'border-b-2', 'border-black')
    this.querySelector('input').classList.toggle('hidden')
    this.#isOpen = false
  }
})
