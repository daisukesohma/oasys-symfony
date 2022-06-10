<template>
    <div class="modal d-block" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mémo pour {{ event.name }}</h5>
                    <button type="button" class="close" @click="$emit('close')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <textarea class="form-control" v-model="memo" rows="5" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-consultant-light" @click="$emit('close')">Annuler</button>
                    <button type="button" class="btn btn-gradient-primary" @click="$emit('confirm', memo)">
                        {{ !event.memo ? "Enregistrer" : "Modifier" }}
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
                memo: ''
            };
        },
        mounted() {
            this.memo = this.event.memo;
        },
    };
</script>
