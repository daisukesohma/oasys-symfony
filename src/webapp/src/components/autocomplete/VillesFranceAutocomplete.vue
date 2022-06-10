<template>
    <div>
        <autocomplete :suggestions="suggestions"
                      :value="currentQuery"
                      :get-suggestion-value="suggestion => suggestion.item.name"
                      :render-suggestion="renderSuggestion"
                      :loading="loading"
                      placeholder="12345"
                      :disabled="disabled"
                      :class-list="classList"
                      @input="query => queryResults(query)"
                      @item-selected="$event => $emit('item-selected', $event)"
                      @select="$event => $emit('input', $event)" />
    </div>
</template>
<script>
    import Autocomplete from './Autocomplete';
    import {ALL_COMPANIES} from '@/graphql/company/all-companies-query';
    import {ALL_VILLES_FRANCE} from "@/graphql/ville/all-villes-france-query";

    export default {
        name: "VillesFranceAutocomplete",
        props: {
            initialQuery: {
                type: String,
            },
            disabled: {
                type: Boolean,
                required: false,
                default: false
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
        watch: {
            initialQuery () {
                this.currentQuery = this.initialQuery;
            },
        },
        methods: {
            queryResults (query) {
                console.log('Query:' + query);
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
                    query: ALL_VILLES_FRANCE,
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
                        {data: result.data.allVillesFrance.items},
                    ];
                })(query));
            },
            renderSuggestion (suggestion) {
                return suggestion.item.codePostal + ' - ' + suggestion.item.commonName
                    + '<small class="d-block text-muted">' + suggestion.item.departmentName + ' - ' + suggestion.item.regionName + '</small>';
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
