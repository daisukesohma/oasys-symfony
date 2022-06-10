<template>
  <dash-wrap mode="candidate" article="" :title="'Questions les plus Fréquentes'">
    <div class="container">
      <page-title :title="questionById.theme"
                  @back="$router.push({name: 'CandidateFaq'})" />
      <div class="row" v-if="this.$apollo.loading">
        <div class="col-md-12">Loading...</div>
      </div>
      <div class="question-faq" v-else>
        <p class="last-update mb-4">Dernière mise à jour : {{questionById.updatedAt ? questionById.updatedAt : questionById.createdAt}}</p>
        <h1 class="title">{{questionById.question}}</h1>
        <p class="response">{{questionById.response}}</p>
      </div>
    </div>
  </dash-wrap>
</template>
<script>
    import {LOGGED_USER} from "@/graphql/security/logged-user-query";
    import {QUESTION_THEMES} from "@/enum/QuestionThemeEnum";
    import {QUESTION_BY_ID} from "@/graphql/faq/question-by-id-query";
    import localMoment from '@/utils/localMoment';
    import PageTitle from "@/components/utils/PageTitle";

    export default {
        name: "FaqQuestion",
        components: {
            PageTitle
        },
        data() {
            return {
                me: {},
                questionThemes: QUESTION_THEMES
            }
        },
        apollo: {
            me: {
                query: LOGGED_USER,
            },
            questionById: {
                query: QUESTION_BY_ID,
                variables() {
                    return {
                        id: this.$route.params.id
                    }
                },
                update(data) {
                    return {
                        ...data.questionById,
                        theme: this.questionThemes.find(e => e.value === data.questionById.theme).label,
                        updatedAt: data.questionById.updatedAt !== null ? localMoment( data.questionById.updatedAt).format('DD/MM/YYYY') : data.questionById.updatedAt,
                        createdAt: localMoment(data.questionById.createdAt ? data.questionById.createdAt : data.questionById.createdAt).format('DD/MM/YYYY')
                    };
                }
            },
        },
    };
</script>