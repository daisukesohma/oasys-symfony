<template>
    <autocomplete :initial-query="initialQuery"
                  ref="autocomplete"
                  :value="value"
                  :suggestions="suggestions"
                  :get-suggestion-value="suggestion => suggestion.item.firstName + ' ' + suggestion.item.lastName"
                  :loading="loading"
                  :placeholder="placeholder"
                  :disabled="disabled"
                  :class-list="classList"
                  :empty-query-after-selection="emptyQueryAfterSelection"
                  @input="query => queryResults(query)"
                  @item-selected="$event => $emit('item-selected', $event)"
                  @selected="$event => $emit('input', $event)" />
</template>
<script>
    import Autocomplete from './Autocomplete';
    import {ALL_USERS} from '@/graphql/user/all-users-query';
    import {USERS_TO_ASSOCIATE_TO_PROGRAM} from '@/graphql/user/users-to-associate-to-program-query';

    export default {
        name: "UserAutocomplete",
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
            name: {
                type: String,
                required: false,
                default: '',
            },
            classList: {
                type: Object,
                required: false,
            },
            types: {
                type: Array,
                default: () =>[],
            },
            emptyQueryAfterSelection: {
                type: Boolean,
                default: false,
            },
            resource: {
                type: String,
                required: false,
                default: '',
            },
            placeholder: {
                type: String,
                default: 'Chercher',
            },
            companyId: {
                type: String,
                required: false
            },
        },
        data: () => ({
            suggestions: [],
            loading: false,
            selectedValue: null,
            apolloQuery: null,
            resultQuery: '',
            previousQuery: null,
            value: '',
        }),
        watch: {
            initialQuery () {
                if (!this.value) {
                    this.value = this.initialQuery;
                }
            }
        },
        components: {
            Autocomplete,
        },
        methods: {
            resetQuery () {
                this.value = '';
            },
            queryResults (query) {
                this.value = query;
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

                this.loading = true;
                this.apolloQuery = this.$apollo.query({
                    query: this.resource === 'getUsersToAssociateToProgram' ? USERS_TO_ASSOCIATE_TO_PROGRAM : ALL_USERS,
                    variables: {
                        search: query,
                        limit: 40,
                        types: this.types,
                        companyId: this.companyId,
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
                    this.suggestions = [{
                        data: this.resource === 'getUsersToAssociateToProgram' ? result.data.usersToAssociateToProgram.items : result.data.allUsers.items
                    }];
                })(query));
            },
        },
        mounted () {
            this.value = this.initialQuery;
        }
    };
</script>
