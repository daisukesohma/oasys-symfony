<template>
  <div
    class="todo-panel document-view-panel"
    v-if="document !== null"
    :class="{ active: active }"
  >
    <div class="todo-panel--wrap">
    <div class="d-flex align-items-center p-2">
      <h3 class="document-view-panel--title">Détails doc</h3>
      <button class="btn p-0 ml-auto" @click="close">
        <img src="@/assets/img/croix-2.svg" alt="" />
      </button>
    </div>
      <div class="d-flex flex-column align-items-center">
        <a :href="documentLink" target="_blank">
          <img :src="isArticle ? documentLink : img" alt="" />
        </a>
        <p class="document-view-panel--name">
          {{ isArticle ? document.name : document.fileDescriptor.name }}
        </p>
      </div>
      <div class="document-view-panel--rows">
        <div class="row">
          <div class="col-4">
            <span class="detail--label">Prestation</span>
          </div>
          <div class="col-8">
            <p class="detail--value">
              {{ document.programs.length > 0 ? document.programs[0].name : (document.events.length > 0 ? document.events[0].program.name : '--') }}
            </p>
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            <span class="detail--label">Visibilité</span>
          </div>
          <div class="col-8">
            <span class="list-document--status protected" v-if="document.visibility === 'protected'">protégé</span>
            <span class="list-document--status private" v-else-if="document.visibility === 'private'">privé</span>
            <span class="list-document--status" v-else>public</span>
          </div>
        </div>
        <div class="row" v-if="isArticle">
          <div class="col-4">
            <span class="detail--label">Lien</span>
          </div>
          <div class="col-8">
            <p class="detail--value">
              <a :href="document.articleLink">
                {{ document.articleLink }}
              </a>
            </p>
          </div>
        </div>
        <div class="row mb-0">
          <div class="col-4">
            <span class="detail--label">Type</span>
          </div>
          <div class="col-8">
            <p class="detail--value">{{ isArticle ? "lien" : type }}</p>
          </div>
        </div>
        <div class="row" v-if="isArticle">
          <div class="col-4">
            <span class="detail--label">Date d’ajout</span>
          </div>
          <div class="col-8">
            <p class="detail--value">{{ formattedDate }}</p>
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            <span class="detail--label">Description</span>
          </div>
          <div class="col-8">
            <p class="detail--value">
              {{ document.description }}
            </p>
          </div>
        </div>
        <div class="row" v-if="tags.length > 0">
          <div class="col-4">
            <span class="detail--label">Tags</span>
          </div>
          <div class="col-8">
            <span class="list-document--tag" v-for="tag in tags" :key="tag">
              {{ tag }}</span>
          </div>
        </div>
        <div class="row" v-if="document.author">
          <div class="col-4">
            <span class="detail--label">Auteur</span>
          </div>
          <div class="col-8">
            <div class="d-flex flex-column">
              <img class="detail--avatar"
                   :src="avatar"
                   alt="" />
              <p class="detail--value">{{ document.author.firstName }} {{ document.author.lastName }}</p>
            </div>
          </div>
        </div>
      </div>
      <div class="mt-auto d-flex justify-content-end p-2">
        <button type="button" class="btn"
                v-if="canEdit"
                @click="$router.push({name: 'DocumentForm', params: {id: document.id}})">
          <img src="@/assets/img/edit-primary.svg" alt="" />
        </button>
        <button type="button" class="btn ml-4"
                v-if="canDelete"
                @click="$event => $emit('delete', document)">
          <img src="@/assets/img/trash-primary.svg" alt="" />
        </button>
      </div>
    </div>
    <div class="todo-panel--bd" @click="close"></div>
  </div>
</template>
<script>
import {FILE_TYPES} from "@/enum/fileTypesEnum";
import localMoment from "@/utils/localMoment";
import {FILE_PATH} from "@/enum/FilePathConstant";
import {DOCUMENT_TYPE_ARTICLE} from "@/enum/documentTypeEnum";
import {LOGGED_USER} from "@/graphql/security/logged-user-query";

export default {
  name: "DocumentView",
  props: {
    document: {
      type: Object,
    },
    active: {
      type: Boolean,
      default: false,
    },
  },
  apollo: {
    me: {
      query: LOGGED_USER,
    },
  },
  computed: {
    type () {
      let ext = this.document.fileDescriptor.name.split(".").pop();
      return ext && FILE_TYPES[ext] ? ext : "pdf";
    },
    img () {
      return require('@/assets/img/' + FILE_TYPES[this.type]);
    },
    formattedDate () {
      return localMoment(this.document.createdAt).format("DD/MM/YYYY");
    },
    tags () {
      return this.document.tags.length === 0 ? [] : this.document.tags.split(",");
    },
    baseUrl() {
      return process.env.VUE_APP_GRAPHQL_HTTP.substr(
          0,
          process.env.VUE_APP_GRAPHQL_HTTP.lastIndexOf("/")
      );
    },
    avatar () {
      if (this.document.author.profilePicture) {
        return this.baseUrl + FILE_PATH + this.document.author.profilePicture.id;
      }

      return require('@/assets/img/user-primary.svg');
    },
    documentLink () {
      if (this.document.fileDescriptor.id) {
        return this.baseUrl + FILE_PATH + this.document.fileDescriptor.id;
      }
      return null;
    },
    isArticle () {
      return this.document.type === DOCUMENT_TYPE_ARTICLE;
    },
    canEdit () {
      return this.me && this.me.rights.includes("ROLE_UPDATE_DOCUMENT");
    },
    canDelete () {
      return this.me && (
            this.me.rights.includes("ROLE_DELETE_DOCUMENT")
            || this.me.id === this.document.author.id
        );
    }
  },
  methods: {
    togglePanel() {
      this.emit("toggle");
    },
    close() {
      this.$emit("close");
    },
  },
};
</script>