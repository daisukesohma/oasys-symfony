<template>
  <div class="coache-tab-item">
    <img src="@/assets/img/doc-2.svg" alt />
    <div class="d-flex flex-column coache-tab-item--content">
      {{ document.name }}
      <div class="d-flex">
        <template v-if="document.documentsSignersForUser">
          <el-tooltip
              v-if="me.type !== 'candidate'
              && document.statusSignature === 'pending'
              && document.documentsSignersForUser.statusSignature === 'done'"
              effect="light" content="Lancer signature Candidat" placement="top">
            <button
              @click="startSignDocument(document.id)"
              class="btn btn-gradient-primary-round mr-1">
              <img src="@/assets/img/icon-msg.svg" alt />
            </button>
          </el-tooltip>
          <el-tooltip v-else-if="document.documentsSignersForUser.statusSignature === 'pending'" effect="light" content="Signer" placement="top">
            <button
              class="btn btn-gradient-primary-round mr-1"
              @click="$router.push({name: 'DocumentSign', params: {member: document.documentsSignersForUser.memberId}, query: { back: $route.path }})">
                <img src="@/assets/img/edit-white.svg" alt />
            </button>
          </el-tooltip>
        </template>
        <el-tooltip effect="light" content="Télécharger" placement="top">
          <button class="btn btn-gradient-primary-round" @click="openFile(document)">
            <img src="@/assets/img/dl.svg" alt />
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
import {PROCEDURE_ACTIVE} from "@/enum/procedureYouSignStatusEnum";

export default {
  name: "ProgamFile",
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
};
</script>