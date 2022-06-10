<template>
    <div>
        <autocomplete :initial-query="initialQuery"
                      :value="currentQuery"
                      :suggestions="suggestions"
                      :get-suggestion-value="suggestion => suggestion.item.name"
                      :render-suggestion="renderSuggestion"
                      :loading="loading"
                      placeholder="Chercher"
                      :disabled="disabled"
                      :class-list="classList"
                      @changeLoading="changeLoading"
                      @input="query => queryResults(query)"
                      @selected="$event => $emit('input', $event)" />
    </div>
</template>
<script>
    import Autocomplete from './Autocomplete';
    import {ALL_COMPANIES} from '@/graphql/company/all-companies-query';

    export default {
        name: "CompanyAutocomplete",
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
            apolloQuery: null,
            resultQuery: '',
            previousQuery: null,
            currentQuery: '',
        }),
        components: {
            Autocomplete,
        },
        methods: {
            queryResults (query) {
                this.currentQuery = query;
                this.$emit('query', query);
                if (query.length < 3) {
                    this.$emit('input', '');
                    return;
                }

                if (this.resultQuery.length && query.indexOf(this.resultQuery) === -1) {
                    this.suggestions = [{
                        data: [],
                    }];
                }

                if (this.previousQuery !== null) {
                    this.previousQuery.abort();
                }

                this.previousQuery = new AbortController();
                let { signal } = this.previousQuery;

                this.$emit('input', '');
                this.loading = true;
                this.apolloQuery = this.$apollo.query({
                    query: ALL_COMPANIES,
                    variables: {
                        search: query,
                        limit: 10,
                    },
                    context: {
                        fetchOptions: {
                            signal,
                        }
                    },
                });
                this.apolloQuery.then((query => result => {
                    this.resultQuery = query;
                    this.loading = false;
                    this.suggestions = [
                        {data: result.data.allCompanies.items},
                    ];
                })(query));
            },
            renderSuggestion (suggestion) {
                return suggestion.item.name + '<small class="text-muted ml-1">' + suggestion.item.code + '</small>';
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
