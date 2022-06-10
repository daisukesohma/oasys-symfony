<template>
    <vue-autosuggest ref="autosuggest"
                     :value="value"
                     :suggestions="suggestions"
                     :input-props="props"
                     :get-suggestion-value="getSuggestionValue"
                     @selected="selectSuggestion"
                     @item-changed="$event => $emit('item-changed', $event)"
                     @input="$event => $emit('input', $event)">
        <template slot="after-input">
            <div class="loader" v-show="loading"></div>
        </template>
        <template slot-scope="{suggestion}">
            <div v-if="renderSuggestion" v-html="renderSuggestion(suggestion)" />
            <div v-else-if="getSuggestionValue">{{ getSuggestionValue(suggestion) }}</div>
            <div v-else>{{ suggestion.item }}</div>
        </template>
    </vue-autosuggest>
</template>
<script>
    import {VueAutosuggest} from 'vue-autosuggest';

    export default {
        name: "Autocomplete",
        props: {
            placeholder: {
              type: String,
              required: false,
            },
            disabled: {
                type: Boolean,
                required: false,
                default: false
            },
            suggestions: {
                type: Array,
                required: false,
            },
            getSuggestionValue: {
                type: Function,
                required: false,
            },
            renderSuggestion: {
                type: Function,
                required: false,
            },
            loading: {
                type: Boolean,
                required: false,
                default: false,
            },
            classList: {
                type: Object,
                required: false,
            },
            value: {
                type: String,
                required: false,
            },
        },
        data: () => ({
            props: {},
        }),
        components: {
            VueAutosuggest,
        },
        watch: {
            classList () {
                this.props = {
                    class: {
                        'form-control': true,
                        ...(this.classList ? this.classList : {}),
                    },
                    disabled: this.disabled,
                    placeholder: 'Chercher',
                };
            },
        },
        mounted () {
            this.$refs.autosuggest.$watch('loading', value => {
                if (value) {
                    this.$emit('changeLoading', false);
                }
            });

            this.props = {
                class: {
                    'form-control': true,
                    ...(this.classList ? this.classList : {}),
                },
                placeholder: this.placeholder,
                disabled: this.disabled,
            };
        },
        methods: {
            selectSuggestion (suggestion) {
                this.$emit('item-selected', suggestion.item);
            },
        },
    };
</script>
