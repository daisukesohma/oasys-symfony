<template>
    <div>
        <autocomplete :initial-query="initialQuery"
                      :value="currentQuery"
                      :suggestions="suggestions"
                      :get-suggestion-value="suggestion => suggestion.item.name"
                      :render-suggestion="renderSuggestion"
                      :loading="loading"
                      :disabled="disabled"
                      :class-list="classList"
                      @changeLoading="changeLoading"
                      @input="query => queryResults(query)"
                      @item-selected="$event => $emit('item-selected', $event)"
                      @selected="$event => $emit('input', $event)" />
    </div>
</template>
<script>
    import Autocomplete from './Autocomplete';
    import {ALL_EVENTS} from '@/graphql/event/all-events-query';

    export default {
        name: "EventAutocomplete",
        props: {
            initialQuery: {
                type: String,
                required: false,
            },
            disabled: {
                type: Boolean,
                required: false,
                default: false
            },
            value: {
                type: String,
                required: true,
            },
            name: {
                type: String,
                required: false,
                default: '',
            },
            classList: {
                type: Object,
                required: false,
            },
        },
        data: () => ({
            suggestions: [],
            loading: false,
            currentQuery: '',
        }),
        components: {
            Autocomplete,
        },
        methods: {
            queryResults (query) {
                this.currentQuery = query;
                if (query.length < 3) {
                    return;
                }

                this.$emit('input', '');
                this.loading = true;
                this.$apollo.query({
                    query: ALL_EVENTS,
                    variables: {
                        search: query,
                        limit: 10,
                    },
                }).then(result => {
                    this.loading = false;
                    this.suggestions = [
                        {data: result.data.allEvents.items.filter(e=>e.program===null)},
                    ];
                });
            },
            renderSuggestion (suggestion) {
                return suggestion.item.name + '<small class="text-muted ml-1">' + suggestion.item.description + '</small>';
            },
            changeLoading (val) {
                this.loading = val;
            },
        },
        mounted () {
            this.currentQuery = this.initialQuery;
        }
    };
</script>
