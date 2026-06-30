import ApiTokens from './components/fieldtypes/ApiTokens.vue'

Statamic.booting(() => {
    Statamic.component('api_tokens-fieldtype', ApiTokens)
})
