<template>
  <div :class="{'col-lg-4 col-md-6': !compact, 'col-lg-6 col-md-12': compact}">
    <div class="document-card mt-2" :class="elClass">
      <div class="document-card--content" @click="$event => onDocumentClick()">
        <strong>{{ document.fileDescriptor.name }}</strong>
      </div>
      <img class="document-card--image" :src="icon" alt="" />
      <div class="document-card--action" v-if="toSign || (me.type !== 'candidate' && isPending)">
        <el-tooltip effect="light" :content="toSign ? 'Signer' : 'Lancer signature Candidat'" placement="top">
          <button @click="onSignClick" class="btn btn-gradient-primary-round">
            <img src="@/assets/img/edit-white.svg" alt v-if="toSign" />
            <img src="@/assets/img/icon-msg.svg" alt v-else />
          </button>
        </el-tooltip>
      </div>
    </div>
  </div>
</template>
<script>
import {FILE_TYPES} from "@/enum/fileTypesEnum";
import {FILE_PATH} from "@/enum/FilePathConstant";
import {START_SIGN_DOCUMENT} from "@/graphql/document/start-sign-document";
import {MEMBER_PENDING, PROCEDURE_ACTIVE, PROCEDURE_PENDING} from "@/enum/procedureYouSignStatusEnum";
import izitoast from "izitoast";

export default {
  name: "DocumentFileCard",
  props: {
    document: {
      type: Object,
      required: true,
    },
    me: {
      type: Object,
      required: true
    },
    compact: {
      type: Boolean,
    }
  },
  computed: {
    type () {
      let type = this.document.fileDescriptor.name.split(".").pop();
      return FILE_TYPES[type] ? type : "pdf";
    },
    icon () {
      return require('@/assets/img/' + FILE_TYPES[this.type])
    },
    elClass () {
      let classes = {
        pdf: "pdf",

        png: "image",
        jpg: "image",
        jpeg: "image",

        mp4: "video",
        mpeg: "video",
        avi: "video",

        mp3: "audio",
        xlsx: "excel",
        txt: "txt",
        powerpoint: "powerpoint",
        doc: "word",
        docx: "word",
      };

      return classes[this.type];
    },
    toSign() {
      if (this.document.documentsSignersForUser) {
        return this.document.documentsSignersForUser.statusSignature === MEMBER_PENDING;
      }

      return false;
    },
    isPending() {
      return this.document.statusSignature === PROCEDURE_PENDING
    },
  },
  methods: {
    onDocumentClick() {
      let baseUrl = process.env.VUE_APP_GRAPHQL_HTTP.substr(
          0,
          process.env.VUE_APP_GRAPHQL_HTTP.lastIndexOf("/")
      );
      window.open(baseUrl + FILE_PATH + this.document.fileDescriptor.id, "_blank");
    },
    startSignDocument(id) {
      this.$apollo.mutate({
        mutation: START_SIGN_DOCUMENT,
        variables: { id: id },
      }).then(() => {
        this.document.statusSignature = PROCEDURE_ACTIVE
        izitoast.success({
          position: 'topRight',
          title: 'Succès',
          message: "La procédure de signature à bien démarrée",
        });
      });
    },
    onSignClick () {
      if (this.toSign) {
        this.$router.push({
          name: 'DocumentSign',
          params: {
            member: this.document.documentsSignersForUser.memberId
          },
          query: {
            back: this.$route.path
          }
        });
      } else {
        this.startSignDocument(this.document.id)
      }
    }
  }
}
</script>