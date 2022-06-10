<template>
    <dash-wrap active="admin" tab="faq" >
        <div class="d-flex">
            <filters :filters="filterList" name="questions" @filter="updateFilters($event)" />
            <div class="ml-auto d-none d-md-block">
                <button class="btn btn-gradient-primary ml-2" @click="() => $router.push({name: 'QuestionForm'})">
                    <i class="fa fa-plus" aria-hidden="true" />
                    Créer une question
                </button>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-12" v-if="!filters.model">
                <list ref="list"
                      name="questions"
                      :items="this.allQuestionsList"
                      :total="this.allQuestions.count"
                      :header="header"
                      no-items-message="Aucune question ne correspond à ce filtre"
                      :loading="$apollo.loading"
                      @edit="question => editQuestion(question)"
                      @delete="question => showDeleteModal(question)"
                      @sort="updateSort($event)"
                      @paginate="updatePage($event)" />
            </div>
        </div>
        <confirm-dialog v-if="deleteModal.show"
                        title="Êtes-vous sûr de vouloir supprimer la question ?"
                        @close="closeDeleteModal()"
                        @confirm="deleteQuestion()" />
    </dash-wrap>
</template>
<script>
    import List from '@/components/utils/List';
    import izitoast from "izitoast";
    import Filters from '@/components/filters/Filters';
    import ConfirmDialog from "@/components/utils/ConfirmDialog";
    import {QUESTION_THEMES} from "@/enum/QuestionThemeEnum";
    import localMoment from '@/utils/localMoment';
    import {LOGGED_USER} from "@/graphql/security/logged-user-query";
    import {ALL_QUESTIONS} from "@/graphql/faq/all-questions-query";
    import {DELETE_QUESTION} from "@/graphql/faq/delete-question-mutation";

    export default {
        name: "faqList",
        components: {
            List,
            Filters,
            ConfirmDialog,
        },
        data: () => ({
            header: [
                {key: 'question', label: 'Question', sortable: true},
                {key: 'program', label: 'Prestation', sortable: false},
                {key: 'response', label: 'Response', sortable: true},
                {key: 'theme', label: 'Thème', sortable: false},
                {key: 'createdBy', label: 'Créateur', sortable: true},
                {key: 'createdAt', label: 'Date création', sortable: true},
                {key: 'actions', label: 'Actions', actions: ['edit', 'delete']},
            ],
            allQuestions: {
                items: [],
                count: 0,
            },
            filterList: [
                {key: 'search', type: 'text', label: 'Question ou Réponse'},
                {key: 'theme', type: 'select', label: 'Thème', attributes: {
                    options: QUESTION_THEMES
                }},
            ],
            filters: {},
            sortColumn: 'createdAt',
            sortDirection: 'desc',
            loading: false,
            offset: 0,
            sortModelColumn: 'createdAt',
            sortModelDirection: 'desc',
            offsetModel: 0,
            questionThemes: QUESTION_THEMES,
            deleteModal: {
                question: {},
                show: false,
            },
        }),
        apollo: {
            me: {
                query: LOGGED_USER
            },
            allQuestions: {
                query: ALL_QUESTIONS,
                variables() {
                    return this.allQuestionQueryVariables;
                },
            },
        },
        computed: {
            allQuestionQueryVariables() {
                return {
                    ...this.filters,
                    limit: 10,
                    offset: this.offset,
                    sortColumn: this.sortColumn,
                    sortDirection: this.sortDirection,
                }
            },
            allQuestionsList() {
                const getLabel = (theme) => {
                    let value = this.questionThemes.find(e => e.value === theme)
                    return value ? value.label : 'Thème Invalid';
                }

                return this.allQuestions.items.map(item => ({
                    ...item,
                    program: item.program ? item.program.name : '',
                    response: item.response.length > 250 ? item.response.substring(0,250) + '...' : item.response,
                    theme: getLabel(item.theme),
                    createdAt: localMoment(item.createdAt ? item.createdAt : item.createdAt).format('DD/MM/YYYY'),
                    createdBy: item.createdBy.firstName + ' ' + item.createdBy.lastName.toUpperCase(),
                }));
            }
        },
        methods: {
            updateSort ($event) {
                this.sortColumn = $event.column;
                this.sortDirection = $event.direction;
                this.resetOffset();
            },
            updatePage (page) {
                this.offset = (page - 1) * 10;
            },
            updateFilters (filters) {
                this.filters = {...filters};
                this.resetOffset();
            },
            resetOffset () {
                this.offset = 0;
                this.offsetModel = 0;
            },
            closeDeleteModal () {
                this.deleteModal.show = false;
            },
            showDeleteModal (question) {
                this.deleteModal.question = question;
                this.deleteModal.show = true;
            },
            deleteQuestion () {
                this.closeDeleteModal();

                let variables = this.allQuestionQueryVariables,
                        optimisticResponse = {
                            deleteQuestion: {
                                __typename: "question",
                                id: this.deleteModal.question.id
                            }
                        };

                this.$apollo.mutate({
                    mutation: DELETE_QUESTION,
                    variables: {
                        id: this.deleteModal.question.id
                    },
                    update(store, result) {
                        const data = store.readQuery({
                            query: ALL_QUESTIONS,
                            variables: variables
                        });
                        data.allQuestions.items = data.allQuestions.items.filter(
                                question => question.id !== result.data.deleteQuestion.id
                        );
                        store.writeQuery({
                            query: ALL_QUESTIONS,
                            variables: variables,
                            data
                        });
                    },
                    optimisticResponse: optimisticResponse
                }).then(() => {
                    izitoast.success({
                        position: 'topRight',
                        title: 'Succès',
                        message: "L'événement a bien été supprimé",
                    });
                });
            },
            editQuestion (question) {
                this.$router.push({name: 'QuestionForm', params: {id: question.id}});
            },
        }
    };
</script>
