<template>

    <div>

        <div v-if="generatedToken" class="mb-3 p-3 bg-green-100 border border-green-300 rounded">
            <div class="font-bold text-sm mb-2">Copy this token now — it will not be shown again, and is only stored once you save the user.</div>
            <code class="break-all text-sm">{{ generatedToken }}</code>
        </div>

        <div class="card p-0 mb-3">

            <div v-if="!tokens.length" class="flex items-center justify-center text-center py-16 text-gray-700">
                {{ __('No tokens') }}
            </div>

            <table v-else class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="token in tokens" :key="token.id">
                        <td>{{ token.name }}</td>
                        <td>{{ formatDate(token.created_at) }}</td>
                        <td class="flex justify-end">
                            <dropdown-list>
                                <dropdown-item :text="__('Remove')" class="warning" @click="revoke(token.id)" />
                            </dropdown-list>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>

        <div class="flex items-center">
            <input
                v-model="newName"
                type="text"
                class="input-text flex-1"
                placeholder="Name"
                @keydown.enter.prevent="generate" />
            <button type="button" class="btn ml-2" :disabled="!newName.trim()" @click="generate">Add Token</button>
        </div>

    </div>

</template>

<script>
import { sha256 } from 'js-sha256'

export default {

    mixins: [Fieldtype],

    data() {
        return {
            newName: '',
            generatedToken: null,
        };
    },

    computed: {

        tokens() {
            return this.value || [];
        },

    },

    methods: {

        generate() {
            const name = this.newName.trim();
            if (! name) return;

            const plaintext = this.randomHex(32);

            this.update([
                ...this.tokens,
                {
                    id: this.randomHex(16),
                    name,
                    token: sha256(plaintext),
                    created_at: new Date().toISOString(),
                },
            ]);

            this.generatedToken = plaintext;
            this.newName = '';
        },

        revoke(id) {
            this.update(this.tokens.filter(token => token.id !== id));
        },

        randomHex(bytes) {
            const array = new Uint8Array(bytes);
            crypto.getRandomValues(array);
            return Array.from(array).map(b => b.toString(16).padStart(2, '0')).join('');
        },

        formatDate(value) {
            if (! value) return '';
            return new Date(value).toLocaleDateString();
        },

    },

};
</script>
