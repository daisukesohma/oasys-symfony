<template>
  <dash-wrap active="admin" :hide-tabs="true">
    <page-title
      pre-title="question"
      :title="$route.params.id ? `Modifier une question` : `Créer une question`"
      @back="$router.push({name: 'FaqList'})"/>
    <div class="col-12 col-md-10 offset-md-1">
      <div class="content">
        <div class="row" v-if="this.$apollo.loading || this.loading">
          <div class="col-md-12">Loading...</div>
        </div>
        <form v-if="!this.$apollo.loading && !this.loading">
          <fieldset>
            <legend>Détails</legend>
            <div class="panel panel-default">
              <div class="panel-body">
                <div class="form-row">
                  <div class="form-group select col-11">
                    <label for="type">Thème*</label>
                    <select
                      class="form-control"
                      id="type"
                      name="type"
                      v-model="question.theme"
                      :class="{'is-invalid': errors.has('theme')}"
                      v-validate="'required'">
                      <option v-for="type in questionThemes" :key="type.id" :value="type.value">
                        {{ type.label }}
                      </option>
                    </select>
                  </div>
                  <div class="form-group col-11">
                    <label>Question*</label>
                    <input
                      v-model="question.question"
                      name="question"
                      v-validate="'required'"
                      class="form-control"
                      type="text"
                      :class="{ 'is-invalid': errors.has('question') }"
                      placeholder="Question..."/>
                    <div
                      v-if="errors.firstByRule('question', 'required')"
                      class="invalid-feedback">
                      <p>{{ " Le champ question est obligatoire " }}</p>
                    </div>
                  </div>
                  <div class="form-group col-11">
                    <label>Réponse*</label>
                    <textarea
                      v-model="question.response"
                      v-validate="'required'"
                      id="response"
                      name="response"
                      class="form-control"
                      :class="{ 'is-invalid': errors.has('response') }"
                      type="text"
                      placeholder="Réponse..."/>
                    <div
                      v-if="errors.firstByRule('response', 'required')"
                      class="invalid-feedback">
                      <p>{{ " Le champ réponse est obligatoire " }}</p>
                    </div>
                  </div>
                  <div class="form-group col-11">
                    <label>Prestation</label>
                    <div class="row">
                      <div class="col-md-12">
                        <program-autocomplete v-model="question.programId"
                                              v-validate="'required'"
                                              :empty-query-after-selection="true"
                                              :class-list="{'is-invalid': errors.has('program')}"
                                              :initial-query="question.program ? question.program.name : ''"
                                              name="program" />
                      </div>
                    </div>
                    <div v-if="errors.has('program')" class="invalid-feedback">
                      <p>{{ " Le champ prestation est obligatoire " }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </fieldset>

          <div class="form-btn-wrap">
            <button
              type="button"
              class="btn btn-outline-consultant-light"
              @click.prevent="$router.push({name: 'FaqList'})">
              Annuler</button>
            <button
              type="button"
              class="btn btn-gradient-primary"
              @click="handleSubmit">
              Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </dash-wrap>
</template>

<script>
    import izitoast from "izitoast";
    import localMoment from '@/utils/localMoment';
    import ProgramAutocomplete from "@/components/autocomplete/ProgramAutocomplete";
    import deleteQueriesFromApolloCache from "@/utils/deleteQueriesFromApolloCache";
    import {CREATE_QUESTION} from '@/graphql/faq/create-question-mutation';
    import {QUESTION_BY_ID} from "@/graphql/faq/question-by-id-query";
    import {IZITOAST_CONSTANTS} from "@/enum/IzitoastConstants";
    import {UPDATE_QUESTION} from "@/graphql/faq/update-question-mutation";
    import {QUESTION_THEMES} from "@/enum/QuestionThemeEnum";

    export default {
        name: "QuestionForm",
        components: {
            ProgramAutocomplete,
        },
        data() {
            return {
                question: {
                    question: "",
                    response: "",
                    theme: "",
                    createBy: null,
                    createdAt: null,
                },
                loading: 0,
                questionThemes: QUESTION_THEMES
            };
        },
        apollo: {
            question: {
                query: QUESTION_BY_ID,
                variables() {
                    return  {
                        id: this.$route.params.id
                    }
                },
                update: data => ({
                  ...data.questionById,
                  programId: data.questionById.program ? data.questionById.program.id : '',
                }),
                skip() {
                    return !this.$route.params.id
                }
            }
        },
        methods: {
            handleSubmit() {
                this.loading = true;
                this.$validator.validate().then(valid => {
                    if (!valid) {
                        izitoast.error({
                            timeout: IZITOAST_CONSTANTS.TIME_OUT,
                            position: "topRight",
                            title: "Erreur",
                            message: "Veuillez vérifier le formulaire pour les erreurs"
                        });
                        this.loading = false;
                        return;
                    }

                    this.$apollo
                        .mutate({
                            mutation: this.$route.params.id ? UPDATE_QUESTION : CREATE_QUESTION,
                            variables: {
                                id: this.$route.params.id,
                                ...this.question
                            },
                            update(store, result) {
                                if (result.data.createQuestion) {
                                    deleteQueriesFromApolloCache(store, "allQuestions");
                                }
                            }
                        })
                        .then(() => {
                            this.loading = false;
                            this.$router.push({ name: "FaqList" });
                            izitoast.success({
                                position: "topRight",
                                title: "Succès",
                                message: this.$route.params.id
                                    ? "La question a été modifiée avec succès"
                                    : "La question a été créée avec succès"
                            });
                        })
                        .catch(() => {
                            this.loading = false;
                            izitoast.error({
                                timeout: IZITOAST_CONSTANTS.TIME_OUT,
                                position: "topRight",
                                title: "Erreur",
                                message: "Une erreur est survenue"
                            });
                        });

                });
            },
        }
    };
</script>
