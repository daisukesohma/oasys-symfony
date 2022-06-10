<template>
    <div class="faq-theme mb-5" v-if="allQuestions.items.length">
      <h2 class="title">{{this.theme.label}}</h2>
      <div class="questions row no-gutters">
        <template v-for="(question, index) in allQuestionsList" >
          <router-link :to="'/question/' + question.id" :key="index" class="col-12 col-lg-6 question mb-2">{{question.question}}</router-link>
        </template>
      </div>

      <div class="mt-4 text-center">
        <paginate
                v-if="allQuestions.count > this.limit"
                :value="page"
                :page-count="totalPages"
                :click-handler="$event => paginate($event)"
                container-class="pagination justify-content-center"
                prev-text="chevron_left"
                next-text="chevron_right"
                page-class="page-item"
                disabled-class="page-link-disabled"
                page-link-class="page-link"
                prev-link-class="page-link previous md-icon"
                next-link-class="page-link next md-icon"
        />
      </div>
    </div>
</template>
<script>
    import Paginate from "vuejs-paginate";
    import {ALL_QUESTIONS_FAQ_PAGE} from "@/graphql/faq/all-questions-query-faq-page";

    export default {
        name: "QuestionsGroupedByTheme",
        components: {
            Paginate
        },
        props: {
          theme: {
              type: Object,
              required: true
          },
        },
        data: () => ({
            page: 1,
            offset: 0,
            limit: 5,
            allQuestions: {
                items: [],
                count: 0,
            },
        }),
        apollo: {
            allQuestions: {
                query: ALL_QUESTIONS_FAQ_PAGE,
                variables() {
                    return this.allQuestionQueryVariables;
                },
                update(data) {
                    return data.allQuestions;
                }
            },
        },
        computed: {
            allQuestionQueryVariables() {
                return {
                    limit: this.limit,
                    theme: this.theme.value,
                    offset: this.offset,
                }
            },
            allQuestionsList() {
                return this.allQuestions.items.map(item => ({
                    ...item,
                }));
            },
            totalPages() {
                return Math.ceil(this.allQuestions.count / 5);
            }
        },
        methods: {
            paginate(page) {
                this.page = page;
                this.offset = (page - 1) * this.limit;
            },
        }
    };
</script>