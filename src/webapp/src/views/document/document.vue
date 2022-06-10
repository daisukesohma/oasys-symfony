<template>
    <div class="container text-left">
      <div class="content">
        <page-title
          v-if="!isFormDialog"
          pre-title="document"
          :title="isModify ? `Modifier un document` : `Créer un document`"
          @back="cancel"
        />
        <div class="row" v-if="$apollo.loading">
          <div class="col-md-12">Loading...</div>
        </div>
        <form v-if="!$apollo.loading">
          <fieldset>
            <legend>Détails</legend>
            <div class="panel panel-default">
              <div class="panel-body">

                <div v-if="document.type === 'file' && (eventId || programId)" class="row">
                  <div class="col-md-4 form-group radio">
                    <label>Le document est-il a signer ?</label>
                    <div class="radio-items">
                      <div class="radio-item">
                        <input class="form-check-input" type="radio" :value="true" @change="toSignChange" v-model="document.toSign" id="to_sign_yes" />
                        <label class="form-check-label" for="to_sign_yes">Oui</label>
                      </div>
                      <div class="radio-item">
                        <input class="form-check-input" type="radio" :value="false" @change="toSignChange" v-model="document.toSign" id="to_sign_no" />
                        <label class="form-check-label" for="to_sign_no">Non</label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-8" v-if="document.toSign && eventProgramId">
                    <div class="form-group select" :class="{'is-invalid': errors.has('livrableId')}">
                      <label>Livrable</label>
                      <document-autocomplete v-model="document.livrableId"
                                             :disabled="!!document.id"
                                             @item-selected="createDocumentFromLivrable"
                                             :initial-query="document.livrable ? document.livrable.name : ''"
                                             name="livrableId"
                                             :query-livrable="true"
                                             :program-id="eventProgramId" />
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group radio col-md-4" v-if="!isCoach">
                    <label>Page d'accueil</label>
                    <div class="radio-items">
                      <div class="radio-item">
                        <input class="form-check-input" type="radio" :value="true" v-model="document.toBeDisplayedInHomePage" :disabled="!canEdit" id="document_homepage_yes"  />
                        <label class="form-check-label" for="document_homepage_yes">Oui</label>
                      </div>
                      <div class="radio-item">
                        <input class="form-check-input" type="radio" :value="false" v-model="document.toBeDisplayedInHomePage" :disabled="!canEdit" id="document_homepage_no" />
                        <label class="form-check-label" for="document_homepage_no">Non</label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4" v-if="!isCoach">
                    <div class="form-group select" :class="{'is-invalid': errors.has('type')}">
                      <label>Type*</label>
                        <select class="form-control"
                                name="type"
                                v-model="document.type"
                                :class="{'is-invalid': errors.has('type')}"
                                :disabled="!canEdit"
                                @change="removeFile"
                                v-validate="'required'">
                          <option value="file">Fichier</option>
                          <option value="article">Article</option>
                        </select>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group select" :class="{'is-invalid': errors.has('categoryId')}">
                      <label>Catégorie*</label>
                      <select class="form-control"
                              name="categoryId"
                              v-model="document.categoryId"
                              :class="{'is-invalid': errors.has('categoryId')}"
                              :disabled="!canEdit"
                              v-validate="'required'">
                        <option value="">Choisir une catégorie</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.label }}</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4" v-if="!eventId">
                    <div
                      class="form-group select"
                      :class="{ 'is-invalid':  errors.has('visibility') }"
                    >
                      <label>Visibilité*</label>

                      <select
                        class="form-control"
                        name="visibility"
                        v-model="document.visibility"
                        :class="{ 'is-invalid':  errors.has('visibility') }"
                        :disabled="!canEdit || ($route.params.id && document.visibility === 'protected')"
                        v-validate="'required'"
                      >
                        <option value disabled>Sélectionner une visibilité</option>
                        <option
                          v-for="visibility in this.visibilityList"
                          :key="visibility.value"
                          :value="visibility.value"
                        >{{ visibility.label }}</option>
                        <option value="protected" v-if="$route.params.id && document.visibility === 'protected'">
                          Protégé
                        </option>
                      </select>
                      <div v-if=" errors.has('visibility')" class="invalid-feedback">
                        <p>{{ " Le champ visibility est obligatoire " }}</p>
                      </div>
                    </div>
                  </div>
                  <div :class="{'col-md-8': !eventId, 'col-md-12': eventId}">
                    <div class="form-group">
                      <label>Nom*</label>
                      <input
                        v-model="document.name"
                        v-validate="'required'"
                        id="name"
                        name="name"
                        class="form-control"
                        type="text"
                        placeholder="Nom..."
                        :class="{ 'is-invalid':  errors.has('name') }"
                        :disabled="!canEdit"
                      />
                      <div v-if="errors.has('name')" class="invalid-feedback">
                        <p>{{ " Le champ Nom est obligatoire " }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Description*</label>

                      <textarea
                              v-validate="'required'"
                              id="description"
                              :class="{ 'is-invalid':  errors.has('description') }"
                              name="description"
                              class="form-control"
                              v-model="document.description"
                              rows="3"
                              :disabled="!canEdit" />
                      <div v-if=" errors.has('description')" class="invalid-feedback">
                        <p>{{ " Le champ description est obligatoire " }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row" v-if="!isCandidate">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Auteur*</label>
                      <user-autocomplete
                              v-if="canEdit"
                              :initial-query="document.author && document.author.firstName ? document.author.firstName + ' ' + document.author.lastName : ''"
                              v-model="document.authorId"
                              v-validate="'required'"
                              name="author"
                              :types="userAutocompleteTypes"
                              :class-list="{'is-invalid':  errors.has('author')}"
                      />
                      <div class="row" v-if="!canEdit">
                        <div class="col-md-12">
                          <single-user :user="document.author" :has-action="false" />
                        </div>
                      </div>
                      <div v-if=" errors.has('author')" class="invalid-feedback d-block">
                        <p>{{ " Le champ auteur est obligatoire " }}</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Date d'élaboration</label>

                      <div class="row">
                        <div class="col-md-12">
                          <el-date-picker
                                  v-model="document.elaborationDate"
                                  format="dd/MM/yyyy"
                                  name="elaborationDate"
                                  :disabled="!canEdit"
                                  :calendar-button="true"
                                  :bootstrap-styling="true"/>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row" v-if="!isCandidate">
                  <div class="col-md-12">
                    <div class="form-group tag">
                      <label>Tags</label>
                      <div class="form-control d-flex" :class="{'has-focus': tagInputVisible}">
                        <el-tag
                                :key="index"
                                v-for="(tag, index) in document.tags"
                                :closable="canEdit"
                                :disable-transitions="false"
                                @close="handleRemoveTag(tag)"
                        >{{tag}}</el-tag>

                        <input
                                class="form-control-tag form-control form-control-sm"
                                v-model="tagInput"
                                v-if="tagInputVisible"
                                ref="saveTagInput"
                                @keyup.enter="handleAddTag"
                                @blur="handleAddTag"
                        />
                        <button
                                type="button"
                                v-if="canEdit"
                                class="btn btn-outline-primary btn-sm button-new-tag btn-tag"
                                @click="showInputTag"
                        >
                          +
                          Nouveau Tag
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row" v-if="document.type === 'article'">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Lien vers l'article*</label>
                      <input v-validate="'required'"
                             id="contentLink"
                             :class="{ 'is-invalid':  errors.has('contentLink') }"
                             name="contentLink"
                             class="form-control"
                             v-model="document.articleLink"
                             :disabled="!canEdit" />
                      <div v-if=" errors.has('contentLink')" class="invalid-feedback">
                        <p>{{ " Le champ lien vers l'article est obligatoire " }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </fieldset>
          <div class="form-card">
            <div class="form-group">
              <upload-file-drop
                :onlyPdf="document.toSign"
                :label="document.type === 'file' ? 'Cliquez ici pour ajouter votre document*' : 'Image de l\'article'"
                :is-picture="document.type !== 'file'"
                :disabled="!canEdit"
                :value="document.fileDescriptor"
                @select-file="saveFile"
                :error="document.fileDescriptorError"
                @remove-file="removeFile"
              />
              <div class="info-drop-file" v-if="document.type === 'file'">
                Pour faciliter la lecture des documents, privilégier le format .pdf.
                Les fichiers de type .doc / .xlsx / .ppt et les liens internet sont acceptés.
                La taille des fichiers doit être inférieure à 10 Mo
              </div>
            </div>
          </div>

          <div class="form-btn-wrap">
            <button
              v-if="!loadingMutation"
              type="button"
              class="btn btn-outline-consultant-light"
              @click="cancel"
            >Annuler</button>
            <button
              type="button"
              class="btn btn-gradient-primary ml-2 px-5"
              @click="() => !canEdit ? enableEdit() : handleSubmit()"
            >{{loadingMutation ? "Loading..." : "Enregistrer"}}</button>
          </div>
        </form>
      </div>
    </div>

</template>
<script>
import SingleUser from "@/components/utils/SingleUser";
import "vue-phone-number-input/dist/vue-phone-number-input.css";
import fr from "vuejs-datepicker/dist/locale/translations/fr.js";
import UserAutocomplete from "@/components/autocomplete/UserAutocomplete";
import DocumentAutocomplete from "@/components/autocomplete/DocumentAutocomplete";
import izitoast from "izitoast";
import deleteQueriesFromApolloCache from "@/utils/deleteQueriesFromApolloCache";
import UploadFileDrop from "@/components/file/UploadFileDrop";
import moment from "moment";
import localMoment from "@/utils/localMoment";
import { FORM_LIST } from "@/enum/visibilityEnum";
import {LOGGED_USER} from "@/graphql/security/logged-user-query";
import { CREATE_DOCUMENT } from "@/graphql/document/create-document-mutation.ts";
import { CREATE_DOCUMENT_FROM_EVENT } from "@/graphql/document/create-document-from-event-mutation.ts";
import { CREATE_DOCUMENT_FROM_PROGRAM } from "@/graphql/document/create-document-from-program-mutation.ts";
import { UPDATE_DOCUMENT } from "@/graphql/document/update-document-mutation.ts";
import { DOCUMENT_BY_ID } from "@/graphql/document/document-by-id-query";
import { UPLOAD_FILE } from "@/graphql/file/upload-file-mutation";
import {IZITOAST_CONSTANTS} from "@/enum/IzitoastConstants";
import {CUSTOM, DOCUMENT_CATEGORY_LIST, LIVRABLE} from "@/enum/documentCategoryEnum";
import {ALL_DOCUMENT_CATEGORIES} from "@/graphql/document/all-document-categories-query";
import {ADMINISTRATOR, COACH} from "@/enum/userTypeEnum";

export default {
  name: "documentForm",
  components: {
    SingleUser,
    UserAutocomplete,
    UploadFileDrop,
    DocumentAutocomplete
  },
  props: {
    isFormDialog: {
      type: Boolean,
      default: false,
    }
  },
  data() {
    return {
      fr: fr,
      me: {
      },
      document: {
        name: "",
        toSign: false,
        visibility: "",
        fileDescriptor: {},
        fileDescriptorId: "",
        uploadedFile: null,
        fileDescriptorError: null,
        description: "",
        tags: [],
        author: {},
        authorId: "",
        elaborationDate: "",
        document: "",
        type: 'file',
        articleLink: '',
        toBeDisplayedInHomePage: false,
        livrableId: '',
        categoryId: '',
      },
      tagInputVisible: false,
      tagInput: "",
      visibilityList: FORM_LIST,
      uploading: false,
      loadingMutation: false,
      isProgramForm: this.$route.name.startsWith('Program'),
      routeParams: this.$route.params,
      livrable: LIVRABLE,
      categories: [],
      userAutocompleteTypes: [COACH, ADMINISTRATOR],
    };
  },
  apollo: {
    me: {
      query: LOGGED_USER,
    },
    document: {
        query: DOCUMENT_BY_ID,
        variables() {
            return {
                id: this.routeParams.id
            }
        },
        update(data) {
            return {
                ...data.documentById,
                fileDescriptor: data.documentById.fileDescriptor
                    ? data.documentById.fileDescriptor
                    : {},
                tags: data.documentById.tags.split(",")[0]  ? data.documentById.tags.split(",") : [],
                elaborationDate: data.documentById.elaborationDate
                    ? localMoment(data.documentById.elaborationDate).toDate()
                    : "",
                toSign: data.documentById.toBeSigned,
                name:
                    data.documentById.name.substr(0, 3) === "+33"
                        ? data.documentById.name.substr(3)
                        : data.documentById.name,
                description: data.documentById.description
                    ? data.documentById.description
                    : {},
                visibility: data.documentById.visibility,
                authorId: data.documentById.author
                    ? data.documentById.author.id
                    : "",
                fileDescriptorId: data.documentById.fileDescriptor
                    ? data.documentById.fileDescriptor.id
                    : "",
                fileDescriptorName: data.documentById.fileDescriptor
                    ? data.documentById.fileDescriptor.name
                    : {},
                categoryId: data.documentById.category
                    ? data.documentById.category.id
                    : "",
                livrableId: data.documentById.livrable
                    ? data.documentById.livrable.id
                    : "",
            };
        },
        skip() {
            return !this.isModify;
        },
    },
    categories: {
      query: ALL_DOCUMENT_CATEGORIES,
      update: data => data.allDocumentCategories.items,
    },
  },
  computed: {
    canEdit() {
      return this.$route.params.mode !== "view";
    },
    isModify () {
        if (this.isFormDialog) {
            return false;
        }
        if (this.$route.params.id) {
            return true;
        }
        return false;
    },
    eventId () {
        if (this.$route.params.eid) {
            return this.$route.params.eid;

        }
        if (!this.$route.params.event && this.$route.name !== 'DocumentForm') {
            return !this.isProgramForm ? this.$route.params.id : null;
        }
        return null;
    },
    eventProgramId () {
      return this.$route.params.eid ? this.$route.params.id : null;
    },
    programId () {
        return this.$route.name ==='ProgramForm' ? this.$route.params.id : null;
    },
    isCandidate () {
      return this.me.type === 'candidate'
    },
    isCoach () {
      return this.me.type === 'coach';
    },
  },
  methods: {
    handleSubmit(e) {
      const isNew = !this.isModify;
      this.$validator.resume();
      this.$validator.validate().then(async valid => {
        if (!valid || !this.document.fileDescriptorName) {
          if (!this.document.fileDescriptorName) {
            this.document.fileDescriptorError =
              "Le champ fichier est obligatoire";
          }
          izitoast.error({
            timeout: IZITOAST_CONSTANTS.TIME_OUT,
            position: "topRight",
            title: "Erreur",
            message: "Veuillez vérifier le formulaire pour les erreurs"
          });
          return;
        }

        if (this.uploading) {
          izitoast.error({
            timeout: IZITOAST_CONSTANTS.TIME_OUT,
            position: "topRight",
            title: "Erreur",
            message: "Veuillez attendre la fin du téléchargement du fichier"
          });
          return;
        }

        let fileDescriptorId = this.document.fileDescriptorId;
        if (this.document.uploadedFile) {
            let response = await this.$apollo.mutate({
                mutation: UPLOAD_FILE,
                variables: {
                    file: this.document.uploadedFile
                }
            });
            fileDescriptorId = response.data.uploadFile.id;
        }


        if(this.document.type !== 'file') {
            this.document.toSign = false;
        }

        let createMutation = CREATE_DOCUMENT;
        if (this.eventId) {
          createMutation = CREATE_DOCUMENT_FROM_EVENT;
        } else if (this.programId) {
          createMutation = CREATE_DOCUMENT_FROM_PROGRAM;
        }

        this.document.eventId = this.eventId;
        this.document.programId = this.programId;
        this.loadingMutation = true;
        this.$apollo
          .mutate({
            mutation: this.isModify ? UPDATE_DOCUMENT : createMutation,
            variables: {
              ...this.document,
              fileDescriptorId: fileDescriptorId,
              name: this.document.name,
              description: this.document.description,
              authorId: this.document.authorId,
              tags: this.document.tags.join(),
              visibility: this.document.visibility,
              elaborationDate: this.document.elaborationDate
                ? moment(
                    this.document.elaborationDate,
                    "D/M/YYYY"
                  ).toISOString()
                : ""
            },
            update(proxy) {
              if (isNew) {
                deleteQueriesFromApolloCache(proxy, "allDocuments");
                deleteQueriesFromApolloCache(proxy, "allDocumentsForCandidate");
                deleteQueriesFromApolloCache(proxy, "allDocumentsForCoach");
                deleteQueriesFromApolloCache(proxy, "allDocumentsForAdmin");
                deleteQueriesFromApolloCache(proxy, "allDocumentCategories");

              }
            }
          })
          .then(() => {
            this.loadingMutation = false;
            if (this.$route.query.back) {
              this.$router.push(this.$route.query.back);
            } else if (this.isFormDialog) {
                this.$emit('submit');
            } else {
                this.$router.push({name: 'DocumentsList'});
            }
            izitoast.success({
              position: "topRight",
              title: "Succès",
              message: this.isModify
                ? "Le document a été mis à jour avec succès"
                : "Le document a été créé avec succès"
            });
          });
      });
    },
    selectAuthor(authorId) {
      this.document.authorId = authorId;
    },
    uploadStart() {
      this.uploading = true;
    },
    handleAddTag() {
      let inputValue = this.tagInput;
      if (inputValue) {
        this.document.tags.push(inputValue);
      }
      this.tagInputVisible = false;
      this.tagInput = "";
    },
    handleRemoveTag(tag) {
      this.document.tags.splice(this.document.tags.indexOf(tag), 1);
    },
    showInputTag() {
      this.tagInputVisible = true;
      this.$nextTick(_ => {
        this.$refs.saveTagInput.focus();
      });
    },
    saveFile(file) {
      this.uploading = false;
      this.document.uploadedFile = file;
      this.document.fileDescriptorName = file.name;
    },
    toSignChange() {
        if(this.document.toSign) {
            this.removeFile();
        }
    },
    removeFile() {
      this.uploading = false;
      this.document.fileDescriptor = {};
      this.document.fileDescriptorId = "";
      this.document.uploadedFile = null;
    },
    dateFormatter(date) {
      return moment(date).format("DD/MM/YYYY");
    },
    cancel () {
        if (this.$route.query.back) {
          this.$router.push(this.$route.query.back);
        }

        if (this.programId || this.eventId) {
            this.$emit('close');
        } else {
            this.$router.push({name: 'DocumentsList'});
        }
    },
    enableEdit () {
      this.$router.push({name: 'DocumentForm', params: {id: this.$route.params.id, mode: ''}});
    },
    createDocumentFromLivrable (document) {
      this.document.name = document.name;
      this.document.description = document.description;
      this.document.author = document.author;
      this.document.authorId = document.author.id;
      this.document.elaborationDate = document.elaborationDate;
    }
  },
  created() {
    this.$validator.pause();
    if (this.isCoach) {
      this.document.authorId = this.me.id;
      this.document.author = this.me;
    }
    if (['EventDocumentForm', 'ProgramEventDocumentForm'].includes(this.$route.name)) {
      this.document.categoryId = CUSTOM;
    }
  }
};
</script>
