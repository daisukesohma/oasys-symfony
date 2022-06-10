<template>
  <dash-wrap mode="candidate" article="" :title="titlePage">
    <h1 class="title-faq mb-5">Recherchez dans l'aide en ligne, vos réponses sur le dispositif</h1>

    <filters :filters="filterList" name="questions" @filter="updateFilters($event)" />

    <div v-if="filters.search" class="questions-search row mt-5">
      <template v-for="(question, index) in allQuestions.items" >
        <router-link :to="'/question/' + question.id" :key="index" class="col-12 question mb-2">{{question.question}}</router-link>
      </template>
    </div>
    <questions-grouped-by-theme v-else v-for="theme in questionThemes" :key="theme.value" :theme="theme" class="mt-5"/>
  </dash-wrap>
</template>
<script>
    import {LOGGED_USER} from "@/graphql/security/logged-user-query";
    import {QUESTION_THEMES} from "@/enum/QuestionThemeEnum";
    import QuestionsGroupedByTheme from "@/components/lists/QuestionsGroupedByTheme";
    import Filters from '@/components/filters/Filters';
    import {ALL_QUESTIONS_FAQ_PAGE} from "@/graphql/faq/all-questions-query-faq-page";

    export default {
        name: "QuestionsForCandidate",
        components: {
            QuestionsGroupedByTheme,
            Filters
        },
        data: () => ({
            me: {},
            titlePage: 'Questions les plus fréquentes',
            filterList: [
                {key: 'search', type: 'text', label: 'Question ou Réponse',
                    classList: {
                      "faq-search-field": true
                    }},
            ],
            filters: {},
        }),
        apollo: {
            me: {
                query: LOGGED_USER,
            },
            allQuestions: {
                query: ALL_QUESTIONS_FAQ_PAGE,
                    variables() {
                    return this.allQuestionQueryVariables;
                },
                update(data) {
                    return data.allQuestions;
                },
                skip() {
                    return !this.filters.search
                }
            },
        },
        computed: {
            questionThemes() {
                return QUESTION_THEMES;
            },
            allQuestionQueryVariables() {
                return {
                    ...this.filters,
                    limit: 10,
                    offset: this.offset,
                    sortColumn: this.sortColumn,
                    sortDirection: this.sortDirection,
                }
            },
        },
        methods: {
            updateFilters (filters) {
                this.filters = {...filters};
                this.offset = 0;
            },
            titlePageChange() {
                const vw = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0);
                if(vw < 768) {
                    this.titlePage = 'FAQ'
                } else {
                    this.titlePage = 'Questions les plus fréquentes';
                }
            },
        },
        created() {
            window.addEventListener("resize", this.titlePageChange);
        }
    };
</script>