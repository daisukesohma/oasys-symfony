<template>
  <dash-wrap active="admin" :hide-tabs="true">
    <page-title
            pre-title="Prestation / Modifier une prestation"
            title="Importer des événements"
            @back="$router.push({name: 'ProgramForm', params: {id: $route.params.programId}})" />
    <div class="container text-left">
      <div class="content">
        <div class="row" v-if="loading">
          <div class="col-md-12">Loading...</div>
        </div>
        <form v-show="!loading">
          <div class="form-card">
            <div class="form-group">
              <div>
                <upload-file-drop
                        label="Cliquez ici pour ajouter votre fichier*"
                        :only-txt="true"
                        @select-file="savefile"
                        @remove-file="removefile" />
              </div>
              <template v-if="submitted && errorMessage">
                <small class="text-danger">Le fichier téléchargé présente les erreurs suivantes :<br/></small>
                <small v-for="(msg, index) in formatErrors(errorMessage)" :key="index" class="text-danger">{{msg}}<br/></small>
              </template>
            </div>
          </div>
          <div class="form-btn-wrap">
            <button type="button" class="btn btn-outline-consultant-light" @click="$router.push({name: 'ProgramForm', params: {id: $route.params.programId}})">
              Annuler
            </button>
            <button type="button" class="btn btn-gradient-primary" @click="handleSubmit" :disabled="!file">
              Importer
            </button>
          </div>
        </form>
      </div>
    </div>
  </dash-wrap>
</template>
<script>
import izitoast from "izitoast";
import deleteQueriesFromApolloCache from "@/utils/deleteQueriesFromApolloCache";
import UploadFileDrop from "@/components/file/UploadFileDrop";

import {IZITOAST_CONSTANTS} from "@/enum/IzitoastConstants";
import {IMPORT_EVENTS} from "@/graphql/event/import-events-mutation";
import {ERRORS_IMPORT_EVENT} from "@/enum/ErrorsImportEventEnum";

export default {
  name: "importEvents",
  components: {
    UploadFileDrop
  },
  data() {
    return {
      submitted: false,
      loading: false,
      errorMessage: '',
      file: null,
    };
  },
  methods: {
    handleSubmit(e) {
      this.submitted = true;
      this.loading = true;
      this.$apollo
        .mutate({
          mutation: IMPORT_EVENTS,
          variables: {
            programId: this.$route.params.programId,
            file: this.file,
          },
          update(proxy) {
            deleteQueriesFromApolloCache(proxy, "allEvents");
            deleteQueriesFromApolloCache(proxy, "programById");
            deleteQueriesFromApolloCache(proxy, "Program");
          }
        })
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "ProgramForm", params: {id: this.$route.params.programId} });
          izitoast.success({
            position: "topRight",
            title: "Succès",
            message: "Les Événements ont été importés avec succès",
          });
        })
        .catch(response => {
          this.loading = false;
          this.errorMessage = response.networkError.result.errors[0].message;
          izitoast.error({
            timeout: IZITOAST_CONSTANTS.TIME_OUT,
            position: "topRight",
            title: "Erreur",
            message: "Une erreur s'est produite lors de l'importation d'Événements, veuillez consulter le formulaire",
          });
        });
    },
    formatErrors(errors) {
        errors = JSON.parse(errors);
        let msg = [];
        for (var item of errors) {
            msg.push(ERRORS_IMPORT_EVENT.find(e => e.value === item['error']).message(item['variable']));
        }
        return msg;
    },
    savefile(file) {
      this.file = file;
      this.submitted = false;
      this.errorMessage = '';
    },
    removefile() {
      this.file = null;
    },
  },
};
</script>
