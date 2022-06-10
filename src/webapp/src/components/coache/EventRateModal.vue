<template>
    <div class="modal d-block" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Etes-vous satisfait de votre entretien ?</h5>
                    <button type="button" class="close" @click="$emit('close')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row form-group">
                        <div class="col-md-12 text-center">
                            <rate :length="5" v-model="starsNumber" />
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-12">
                            <label>Commentaire</label>
                            <textarea class="form-control" v-model="comment" :class="{'is-invalid': commentError}" />
                            <small class="text-danger" v-if="commentError">
                                Le commentaire est obligatoire
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-consultant-light" @click="$emit('close')">Annuler</button>
                    <button type="button" class="btn btn-gradient-primary" @click="saveRating">
                        Enregistrer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

    export default {
        name: "MemoModal",
        props: {
            event: {
                type: Object,
                required: true,
            },
        },
        data() {
            return {
                comment: '',
                starsNumber: 5,
                commentError: false,
            };
        },
        mounted() {
            this.memo = this.event.memo;
        },
        methods: {
            saveRating() {
                if (!this.comment && this.starsNumber <= 3) {
                    this.commentError = true;
                    return;
                }

                this.$emit('confirm', {starsNumber: this.starsNumber, comment: this.comment});
            },
        },
    };
</script>
