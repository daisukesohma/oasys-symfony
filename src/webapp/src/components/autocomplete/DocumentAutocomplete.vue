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
                      :placeholder="placeholder"
                      ref="autocomplete"
                      @changeLoading="changeLoading"
                      @input="query => queryResults(query)"
                      @item-selected="$event => $emit('item-selected', $event)"
                      @selected="$event => $emit('input', $event)" />
    </div>
</template>
<script>
    import Autocomplete from './Autocomplete';
    import {ALL_DOCUMENTS} from '@/graphql/document/all-documents-query';
    import {DOCUMENT_TYPE_FILE} from "@/enum/documentTypeEnum";
    import {DOCUMENTS_LIVRABLE} from "@/graphql/document/documents-livrable-query";

    export default {
        name: "DocumentAutocomplete",
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
            avoidProgram: {
                type: String,
                required: false,
            },
            avoidEvent: {
                type: String,
                required: false,
            },
            category: {
                type: String,
                required: false
            },
            queryLivrable: {
                type: Boolean,
                required: false,
            },
            programId: {
                type: String,
                required: false
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
            currentQuery: '',
            previousQuery: null,
        }),
        components: {
            Autocomplete,
        },
        watch: {
            initialQuery () {
                if (!this.currentQuery) {
                    this.currentQuery = this.initialQuery;
                }
            }
        },
        methods: {
            resetQuery () {
                this.currentQuery = '';
            },
            queryResults (query) {
                this.currentQuery = query;
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
                    query: this.queryLivrable ? DOCUMENTS_LIVRABLE : ALL_DOCUMENTS,
                    variables: {
                        search: query,
                        limit: 10,
                        avoidHidden: true,
                        avoidProgram: this.avoidProgram ? this.avoidProgram : null,
                        avoidEvent: this.avoidEvent ? this.avoidEvent : null,
                        type: DOCUMENT_TYPE_FILE,
                        category: this.category,
                        programId: this.programId,
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
                        {data: this.queryLivrable ? result.data.documentsLivrable.items : result.data.allDocuments.items},
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
            this.currentQuery = this.initialQuery;
        }
    };
</script>
