<template>
    <div class="modal d-block" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ title }}</h5>
                    <button type="button" class="close" @click="$emit('close')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-md-12">
                            <event-autocomplete
                                v-model="events"
                                @item-selected="addEvent"
                                :class-list="{'is-invalid': selectedEvents.length === 0 && submitted}"
                                :empty-query-after-selection="true"
                            />
                        </div>
                        <div class="col-md-12">
                            <el-tag
                                v-for="event in selectedEvents"
                                :key="event.id"
                                closable
                                @close="removeEvent(event)"
                                type="success">
                                {{event.name}}
                            </el-tag>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-consultant-light" @click="$emit('close')">Annuler</button>
                    <button type="button" class="btn btn-gradient-primary" @click="confirmAssociate">Confirmer</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import EventAutocomplete from "@/components/autocomplete/EventAutocomplete";

    export default {
        name: "AssociateEventToProgramDialog",
        components: {
            EventAutocomplete,
        },
        props: {
            title: {
                type: String,
                required: true,
            },
        },
        data() {
            return {
                submitted: false,
                selectedEvents: []
            };
        },
        methods: {
            addEvent (event) {
                this.selectedEvents = this.selectedEvents.filter(u => u.id != event.id);
                this.selectedEvents.push(event);
            },
            removeEvent (event) {
                this.selectedEvents = this.selectedEvents.filter(u => u.id != event.id);
            },
            confirmAssociate() {
                this.submitted = true;
                if (this.selectedEvents.length==0) {
                    return;
                }
                this.$emit('confirm', this.selectedEvents);
            }
        }
    };
</script>