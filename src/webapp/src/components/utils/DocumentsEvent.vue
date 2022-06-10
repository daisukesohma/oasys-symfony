<template>
  <div>
    <coache-title-design :wide="false">Docs</coache-title-design>
    <circle-spin v-if="loading"></circle-spin>
    <div class="row mt-2" v-if="!loading">
      <document-file-card v-for="item in normalDocuments" :document="item" :me="me" :key="item.id" :compact="compact" />
      <add-file v-show="!(event.type === 'workshop' && me.type === 'candidate')" @added="$event => createDocument($event, false, event)" />
    </div>

    <template v-if="toSignDocuments.length && !loading">
      <coache-title-design style="margin-right:-20px" :wide="false">Docs à signer</coache-title-design>
      <div class="row mt-2">
        <document-file-card v-for="(item, index) in toSignDocuments" :document="item" :me="me" :key="index" :compact="compact" />
      </div>
    </template>
  </div>
</template>
<script>
    import DocumentFileCard from "@/components/docs/DocumentFileCard";
    import CoacheTitleDesign from "@/components/coache/TitleDesign.vue";
    import ChosenFile from "@/components/coache/ChosenFile.vue";
    import AddFile from "@/components/coache/AddFile.vue";

    export default {
        name: "DocumentsEvent",
        components: {
            CoacheTitleDesign,
            AddFile,
            DocumentFileCard,
        },
        props: {
            event: {
                type: Object,
                required: true,
            },
            me: {
                type: Object,
                required: true,
            },
            compact: {
                type: Boolean,
            },
            loading: {
                type: Boolean,
            }
        },
        computed: {
            normalDocuments() {
                return this.event.documents.filter((item) => {
                    if(item.documentsSignersForUser) {
                        return item.documentsSignersForUser.statusSignature === 'done'
                    }

                    return true;
                });
            },
            toSignDocuments() {
                return this.event.documents.filter((item) => {
                    if(item.documentsSignersForUser) {
                        return item.documentsSignersForUser.statusSignature === 'pending'
                    }

                    return false;
                });
            },
        },
        methods: {
            createDocument(file, program, event) {
                this.$emit('createDocument', file, program, event)
            }
        }
    };
</script>