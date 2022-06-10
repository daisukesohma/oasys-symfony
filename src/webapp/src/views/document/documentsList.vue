<template>
  <dash-wrap article="Liste de" title="Documents" mode="folders">
    <template v-slot:mainaction>
      <a
        class="nav-link main-link"
        :class="{ active: mainNavActive }"
        @click="mainNavActive = !mainNavActive"
      >
        <img v-if="mainNavActive" src="@/assets/img/croix-white.svg" alt="" />
        <img v-else src="@/assets/img/burger-white.svg" alt="" />
      </a>
      <div
        class="link-menu d-flex d-md-none"
        :class="{ active: mainNavActive }"
      >
        <button class="btn nav-link">
          <i class="fas fa fa-external-link-alt text-white"></i>
        </button>
        <router-link class="btn nav-link" :to="{ name: 'DocumentForm' }">
          <img src="@/assets/img/plus-white.svg" alt="" />
        </router-link>
      </div>
    </template>
    <div class="container text-left my-5">
      <!-- <div class="row mt-3">
                <div class="col-md-6">
                    <h1>Liste des documents</h1>
                </div>
      </div>-->
      <div class="row">
        <div class="col-md-12">
          <filters
            ref="filters"
            name="documents"
            :filters="filterList"
            :values="filters"
            @filter="updateFilters($event)"
            @reset="resetFilters"
          />
        </div>
      </div>
      <div class="row mt-4 mb-4" v-if="isCoach || isAdmin">
        <div class="d-none d-md-flex col-md-12 align-items-end justify-content-end">
          <button class="btn btn-gradient-primary px-4" @click="() => $router.push({ name: 'DocumentForm' })">
            <i class="fa fa-plus" aria-hidden="true"></i>
            Créer un document
          </button>
          <button class="btn btn-gradient-primary px-4 ml-2" @click="exportDocuments" v-if="isAdmin">
            <i class="fas fa fa-external-link-alt"></i>
            Exporter
          </button>
        </div>
      </div>
      <div class="row mt-2 document-type-card--list">
        <div class="col-sm-12 col-md-6 col-lg-3" v-for="category in allDocumentCategories.items" :key="category.id">
          <document-type-card
            :title="category.label"
            :number="category.documents.count"
            :icon="'icon-' + category.id"
            :active="category.id === filterCategoryId"
            @click="$event => selectDocumentCategory(category)"
          />
        </div>
      </div>

      <div class="row mt-2">
        <div class="col-md-12">
          <list-documents
            :current="modalDocumentIndex"
            name="documents"
            ref="list"
            :items="listItems"
            :total="allDocuments.count"
            no-items-message="Aucun document ne correspond à ce filtre"
            :loading="loading"
            :per-page="limit"
            @edit="
              (document) =>
                $router.push({
                  name: 'DocumentForm',
                  params: { id: document.id },
                })
            "
            @see="seeDocument"
            @download="downloadDocument"
            @sort="updateSort($event)"
            @paginate="updatePage($event)"
          />
        </div>
      </div>
      <confirm-dialog
        v-if="deleteModal.show"
        title="Etes-vous sûr de vouloir supprimer ce document ?"
        @close="closeDisableModal()"
        @confirm="deleteDocument()"
      />
      <document-view-panel
        :active="modalDocument != null"
        :document="modalDocument"
        @close="modalDocument = modalDocumentIndex = null"
        @delete="(document) => showDeleteDocumentModal(document)"
      />
    </div>
  </dash-wrap>
</template>
<script>
import ListDocuments from "@/components/docs/ListDocuments";
import DocumentTypeCard from "@/components/docs/DocumentTypeCard";
import DocumentViewPanel from "@/components/docs/DocumentViewPanel";
import moment from "moment";
import { FILTER_LIST } from "@/enum/visibilityEnum";
import Filters from "@/components/filters/Filters";
import ConfirmDialog from "@/components/utils/ConfirmDialog";
import { ALL_DOCUMENTS } from "@/graphql/document/all-documents-query";
import { DELETE_DOCUMENT } from "@/graphql/document/delete-document-mutation";
import izitoast from "izitoast";
import { LOGGED_USER } from "@/graphql/security/logged-user-query";
import { FILE_PATH } from "@/enum/FilePathConstant";
import {ALL_DOCUMENTS_FOR_COACH} from "@/graphql/document/all-documents-for-coach-query";
import {ALL_DOCUMENTS_FOR_ADMIN} from "@/graphql/document/all-documents-for-admin-query";
import {ALL_DOCUMENT_CATEGORIES} from "@/graphql/document/all-document-categories-query";
import {ALL_DOCUMENTS_FOR_CANDIDATE} from "@/graphql/document/all-documents-for-candidate-query";
import {CUSTOM, LIVRABLE} from "@/enum/documentCategoryEnum";

export default {
  name: "documentsList",
  components: {
    ListDocuments,
    DocumentTypeCard,
    DocumentViewPanel,
    Filters,
    ConfirmDialog,
  },
  data: () => ({
    filters: {},
    filterCategoryId: null,
    sortColumn: "createdAt",
    sortDirection: "desc",
    allDocuments: {
      items: [],
      count: 0,
    },
    loading: false,
    offset: 0,
    limit: 12,
    deleteModal: {
      document: {},
      show: false,
    },
    modalDocument: null,
    modalDocumentIndex: null,
    mainNavActive: false,
    filterEventId: null,
    allDocumentCategories: {
      items: [],
    },
    me: {
      type: "",
    }
  }),
  apollo: {
    me: {
      query: LOGGED_USER,
      result () {
        if (this.isCandidate) {
          this.filterCategoryId = this.$route.query.programId ? LIVRABLE : CUSTOM;
        }
      }
    },
    allDocuments: {
      query() {
        return this.isCoach ? ALL_DOCUMENTS_FOR_COACH : (this.isAdmin ? ALL_DOCUMENTS_FOR_ADMIN : ALL_DOCUMENTS_FOR_CANDIDATE);
      },
      variables () {
        return this.allDocumentsQueryVariables;
      },
      watchLoading (isLoading) {
        this.loading = isLoading;
      },
      update: data => data[Object.keys(data)[0]],
      skip() {
        return !this.me;
      },
    },
    allDocumentCategories: {
      query: ALL_DOCUMENT_CATEGORIES,
      variables () {
        return this.allDocumentsQueryVariables;
      },
      skip() {
        return !this.me;
      },
    }
  },
  computed: {
    listItems() {
      return this.allDocuments.items;
    },
    allDocumentsQueryVariables() {
      return {
        ...this.filters,
        ...(this.filterEventId ? {eventId: this.filterEventId} : {}),
        categoryId: this.filterCategoryId,
        offset: this.offset,
        limit: this.limit,
        sortColumn: this.sortColumn,
        sortDirection: this.sortDirection,
      };
    },
    isCoach() {
      return this.me.type === 'coach';
    },
    isCandidate() {
      return this.me.type === "candidate";
    },
    isAdmin() {
      return this.me.type === "admin" || this.me.type === "support";
    },
    filterList () {
      return [
        {key: "search", type: "text", label: "Nom, description"},
        {key: "createdAt", type: "datepicker", label: "Date de création"},
        ...(this.isCoach || this.isAdmin ? [
          ...(this.filterCategoryId === CUSTOM ? [
            {key: "signaturePending", type: "checkbox", label: "En attente de signature"},
            {key: "signedByCoach", type: "checkbox", label: "Signé par le coach"},
            {key: "signedByCandidate", type: "checkbox", label: "Signé par le candidat"},
          ] : []),
          {key: "livrableId", type: "documentAutocomplete", label: "Livrable", attributes: {
              queryLivrable: true
            }},
          {key: "programId", type: "programAutocomplete", label: "Prestation"},
          ...(this.isAdmin ? [
            {key: "visibility", type: "select", label: "Visibilité", attributes: {
                options: FILTER_LIST,
              }
            }
          ] : [])
        ] : [])
      ];
    },
  },
  methods: {
    limitStringLenght(string, length) {
      if (string.length > length) {
        return string.substring(0, length) + "...";
      }

      return string;
    },
    updateSort($event) {
      this.sortColumn = $event.column;
      this.sortDirection = $event.direction;
      this.resetOffset();
    },
    updatePage(page) {
      this.offset = (page - 1) * this.limit;
    },
    updateFilters(filters) {
      this.filters = { ...filters };
      this.resetOffset();
    },
    deleteDocument() {
      this.closeDisableModal();

      this.$apollo
        .mutate({
          mutation: DELETE_DOCUMENT,
          variables: {
            id: this.deleteModal.document.id,
          },
        })
        .then(() => {
          izitoast.success({
            position: "topRight",
            title: "Succès",
            message: "Le document a bien été supprimé",
          });
          this.$apollo.queries.allDocuments.refetch();
          this.$apollo.queries.allDocumentCategories.refetch();
          this.modalDocument = null;
          this.modalDocumentIndex = null;
        });
    },
    resetOffset() {
      this.offset = 0;
    },
    closeDisableModal() {
      this.deleteModal.show = false;
    },
    showDeleteDocumentModal(document) {
      this.deleteModal.document = document;
      this.deleteModal.show = true;
    },
    seeDocument(index) {
      this.modalDocument = this.listItems[index];
      this.modalDocumentIndex = index;
    },
    downloadDocument(document) {
      window.open(this.getDocumentUrl(document) + "?download=1", "_blank");
    },
    getDocumentUrl(document) {
      let baseUrl = process.env.VUE_APP_GRAPHQL_HTTP.substr(
        0,
        process.env.VUE_APP_GRAPHQL_HTTP.lastIndexOf("/")
      );
      return baseUrl + FILE_PATH + document.fileDescriptor.id;
    },
    selectDocumentCategory (category) {
      this.filterCategoryId = category.id === this.filterCategoryId ? null : category.id;
      this.resetOffset();
    },
    resetFilters () {
      this.filterCategoryId = null;
      this.filterEventId = null;
      this.resetOffset();
    },
    exportDocuments () {
      let query = [];
      for (let i in this.allDocumentsQueryVariables) {
        let value = this.allDocumentsQueryVariables[i];
        if (!value) {
          continue;
        }

        query.push(i + "=" + encodeURIComponent(value));
      }

      window.open(
          process.env.VUE_APP_GRAPHQL_HTTP.substr(0, process.env.VUE_APP_GRAPHQL_HTTP.lastIndexOf('/'))
          + '/export/documents?' + query.join("&"),
          "_blank"
      );
    }
  },
  mounted () {
    if (this.$route.query.programId) {
      this.filters.programId = this.$route.query.programId;
    }
    if (this.$route.query.eventId) {
      this.filterEventId = this.$route.query.eventId;
    }
    if (this.$route.query.livrables) {
      this.filterCategoryId = LIVRABLE;
    }
  }
};
</script>