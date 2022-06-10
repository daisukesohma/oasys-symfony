<template>
    <div class="modal d-block document-dialog" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow rounded" :class="{'h-100': addingExistingDocument === false}">
                <div class="modal-body mr-2 mb-2" :class="{'h-100': addingExistingDocument === false}">
                    <perfect-scrollbar :class="{'h-100': addingExistingDocument === false}">
                        <page-title pre-title="document"
                                    :title="'Ajouter un document'"
                                    @back="close" />
                        <form class="container panel-body">
                            <fieldset>
                                <div class="row">
                                    <div class="col-md-12 form-group radio">
                                        <label>A partir d'un document existant</label>
                                        <div class="radio-items">
                                            <div class="radio-item">
                                                <input class="form-check-input" type="radio" :value="true" v-model="addingExistingDocument" id="add_existing_document_yes" />
                                                <label class="form-check-label" for="add_existing_document_yes">Oui</label>
                                            </div>
                                            <div class="radio-item">
                                                <input class="form-check-input" type="radio" :value="false" v-model="addingExistingDocument" id="add_existing_document_no" />
                                                <label class="form-check-label" for="add_existing_document_no">Non</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" v-if="addingExistingDocument">
                                    <div class="col-md-12 form-group">
                                        <label>Document</label>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <document-autocomplete v-model="documentId"
                                                                       name="document"
                                                                       :avoid-program="this.programId"
                                                                       :avoid-event="this.eventId" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <div class="form-btn-wrap" v-if="addingExistingDocument">
                                <button type="button"
                                        class="btn btn-gradient-primary ml-2 px-5"
                                        :disabled="!documentId"
                                        @click="submit">
                                    {{loadingMutation ? "Loading..." : "Ajoutez le document"}}
                                </button>
                            </div>
                        </form>
                        <document @close="close" @submit="$emit('submit')" :is-form-dialog="true" v-if="addingExistingDocument === false" />
                    </perfect-scrollbar>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import Document from './document';
    import { PerfectScrollbar } from "vue2-perfect-scrollbar";
    import DocumentAutocomplete from "@/components/autocomplete/DocumentAutocomplete";
    import {ADD_DOCUMENT_TO_EVENT} from "@/graphql/event/add-document-to-event-mutation";
    import {ADD_DOCUMENT_TO_PROGRAM} from "@/graphql/program/add-document-to-program-mutation";

    export default {
        name: "documentFormDialog",
        components : {
            Document,
            PerfectScrollbar,
            DocumentAutocomplete,
        },
        data: () => ({
            addingExistingDocument: null,
            documentId: '',
            loadingMutation: false,
        }),
        computed: {
            eventId () {
                if (this.$route.params.eid) {
                    return this.$route.params.eid;
                } else {
                    return this.$route.name !== 'ProgramForm' ? this.$route.params.id : null;
                }
            },
            programId () {
                return this.$route.name === 'ProgramForm' ? this.$route.params.id : null;
            },
        },
        methods: {
            close() {
                this.$emit('close')
            },
            submit () {
                this.loadingMutation = true;
                this.$apollo.mutate({
                    mutation: this.eventId ? ADD_DOCUMENT_TO_EVENT : ADD_DOCUMENT_TO_PROGRAM,
                    variables: {
                        eventId: this.eventId,
                        programId: this.programId,
                        documentId: this.documentId,
                    }
                }).then(() => {
                    this.loadingMutation = false;
                    this.$emit('submit');
                });
            }
        }
    }
</script>