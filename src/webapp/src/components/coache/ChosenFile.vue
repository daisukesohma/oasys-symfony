<template>
  <div class="chosen-file">
    <div>
      <el-tooltip effect="light" :content="document.name" placement="top">
        <img :src="image" @click="openFile()" />
      </el-tooltip>
      <div class="d-flex justify-content-end action">
        <el-tooltip v-if="toSign" effect="light" content="Signer" placement="top">
          <button @click="$router.push({name: 'DocumentSign', params: {member: document.documentsSignersForUser.memberId}, query: { back: $route.path }})" class="btn btn-gradient-primary-round">
            <img src="@/assets/img/edit-white.svg" alt />
          </button>
        </el-tooltip>
        <el-tooltip v-else-if="me.type !== 'candidate' && isPending" effect="light" content="Lancer signature Candidat" placement="top">
          <button @click="startSignDocument(document.id)" class="btn btn-gradient-primary-round">
            <img src="@/assets/img/icon-msg.svg" alt />
          </button>
        </el-tooltip>
      </div>
    </div>

  </div>
</template>
<script>
import { FILE_PATH } from "@/enum/FilePathConstant";
import {START_SIGN_DOCUMENT} from "@/graphql/document/start-sign-document";
import izitoast from "izitoast";
import {PROCEDURE_ACTIVE, PROCEDURE_PENDING} from "@/enum/procedureYouSignStatusEnum";
import {MEMBER_PENDING} from "@/enum/procedureYouSignStatusEnum";

export default {
  name: "ChosenFile",
  props: {
    document: {
        type: Object,
        required: true,
    },
    me: {
        type: Object,
        required: true
    }
  },
  methods: {
    openFile() {
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
  },
  computed: {
    image() {
      return require(`@/assets/img/chosen-${this.fileType}.svg`);
    },
    toSign() {
      if(this.document.documentsSignersForUser) {
          return this.document.documentsSignersForUser.statusSignature === MEMBER_PENDING;
      }

      return false;
    },
    isPending() {
        return this.document.statusSignature === PROCEDURE_PENDING
    },
    fileType() {
      let filename = this.document.fileDescriptor.name,
          extension = filename.substr(filename.lastIndexOf('.') + 1).toLowerCase();
      if (['doc', 'docx', 'pdf', 'txt', 'xls', 'xlsx'].includes(extension)) {
        return 'document';
      } else if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
        return 'image';
      } else {
        return 'video';
      }
    }
  },
};
</script>