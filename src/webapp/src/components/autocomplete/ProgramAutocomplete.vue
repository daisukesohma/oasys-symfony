<template>
    <div>
        <autocomplete :initial-query="initialQuery"
                      :suggestions="suggestions"
                      :get-suggestion-value="suggestion => suggestion.item.name"
                      :render-suggestion="renderSuggestion"
                      :loading="loading"
                      :disabled="disabled"
                      :class-list="classList"
                      :placeholder="placeholder"
                      :value="currentValue"
                      ref="autocomplete"
                      @changeLoading="changeLoading"
                      @input="query => queryResults(query)"
                      @item-selected="$event => $emit('item-selected', $event)"
                      @selected="$event => $emit('input', $event)" />
    </div>
</template>
<script>
    import Autocomplete from './Autocomplete';
    import {PROGRAMS_FOR_AUTOCOMPLETE} from '@/graphql/program/programs-for-autocomplete-query';

    export default {
        name: "ProgramAutocomplete",
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
            placeholder: {
                type: String,
            },
        },
        data: () => ({
            suggestions: [],
            loading: false,
            apolloQuery: null,
            resultQuery: '',
            previousQuery: null,
            currentValue: '',
        }),
        components: {
            Autocomplete,
        },
        watch: {
            initialQuery () {
                if (!this.value) {
                    this.currentValue = this.initialQuery;
                }
            }
        },
        methods: {
            resetQuery () {
                this.currentValue = '';
            },
            queryResults (query) {
                this.currentValue = query;
                if (query.length < 3) {
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
                this.$apollo.query({
                    query: PROGRAMS_FOR_AUTOCOMPLETE,
                    variables: {
                        search: query,
                        limit: 10,
                    },
                    context: {
                        fetchOptions: {
                            signal,
                        }
                    },
                }).then((query => result => {
                    this.resultQuery = query;
                    this.loading = false;
                    this.suggestions = [
                        {data: result.data.programsForAutocomplete.items},
                    ];
                })(query));
            },
            renderSuggestion (suggestion) {
                return suggestion.item.name + '<small class="text-muted ml-1">' + suggestion.item.description + '</small>';
            },
            changeLoading (val) {
                this.loading = val;
            },
        },
        mounted () {
            if (this.$route.query.programId) {
                this.$apollo.query({
                    query: PROGRAMS_FOR_AUTOCOMPLETE,
                    variables: {
                        search: this.$route.query.programId,
                        limit: 1,
                    },
                }).then(result => {
                    let item = result.data.programsForAutocomplete.items[0];
                    this.currentValue = item.name;
                });
                this.$emit('input', this.$route.query.programId);
            }
            this.currentValue = this.initialQuery;
        },
    };
</script>
