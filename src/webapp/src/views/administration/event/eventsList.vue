<template>
    <dash-wrap active="admin" tab="events" >
        <template v-slot:mainaction>
           <router-link class="btn nav-link" :to="{name:'EventForm'}">
               <img src="@/assets/img/plus-white.svg" alt="">
           </router-link>
        </template>
        <div class="d-flex justify-content-end mb-2">
            <button v-if="isAdmin"
                    class="btn btn-outline-secondary ml-3"
                    @click="exportEvents">
                <i class="fa fa-file-excel" aria-hidden="true" />
                Exporter
            </button>
            <button v-if="isAdmin"
                    class="btn btn-outline-secondary ml-3"
                    @click="exportEventsCrossTalent">
                <i class="fa fa-file-excel" aria-hidden="true" />
                Exporter CrossTalent
            </button>
        </div>
        <div class="d-flex">
            <filters :filters="filterList" name="events" @filter="updateFilters($event)" />
            <div class="ml-auto d-none d-md-block" v-if="me.rights.includes('ROLE_CREATE_EVENT')">
                <button class="btn btn-gradient-primary ml-2" @click="() => $router.push({name: 'EventForm'})">
                    <i class="fa fa-plus" aria-hidden="true" />
                    Créer un événement
                </button>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-12" v-if="!filters.model">
                <list ref="list"
                      name="events"
                      :items="listItems"
                      :total="allEvents.count"
                      :header="header"
                      no-items-message="Aucun événement ne correspond à ce filtre"
                      :loading="$apollo.loading"
                      @see="event => viewEvent(event)"
                      @edit="event => editEvent(event)"
                      @delete="event => showDeleteModal(event)"
                      @sort="updateSort($event)"
                      @paginate="updatePage($event)" />
            </div>
            <div class="col-md-12" v-else>
                <list ref="list"
                      name="eventModels"
                      :items="listModelItems"
                      :total="allEventModels.count"
                      :header="headerModel"
                      :loading="$apollo.loading"
                      no-items-message="Aucun modèle d'événement ne correspond à ce filtre"
                      @edit="event => editEventModel(event)"
                      @delete="event => showDeleteModal(event)"
                      @sort="updateModelSort($event)"
                      @paginate="updateModelPage($event)" />
            </div>
        </div>
        <confirm-dialog v-if="deleteModal.show"
                        title="Êtes-vous sûr de vouloir supprimer l'événement ?"
                        @close="closeDeleteModal()"
                        @confirm="deleteEvent()" />
    </dash-wrap>
</template>
<script>
    import List from '@/components/utils/List';
    import Filters from '@/components/filters/Filters';
    import ConfirmDialog from "@/components/utils/ConfirmDialog";
    import localMoment from "@/utils/localMoment";
    import iziToast from 'izitoast';
    import {ALL_EVENTS} from "@/graphql/event/all-events-query";
    import {ALL_EVENT_MODELS} from "@/graphql/event/all-event-models-query";
    import {EVENT_TYPES} from "@/enum/EventTypesEnum";
    import {LOGGED_USER} from "@/graphql/security/logged-user-query";
    import {DELETE_EVENT_MODEL} from "@/graphql/event/delete-event-model-mutation";
    import {DELETE_EVENT} from "@/graphql/event/delete-event-mutation";
    import {EVENT_STATUS} from "@/enum/eventStatusEnum";
    import {PROGRAM_STATUS} from "@/enum/programStatusEnum";
    import {COACH} from "@/enum/userTypeEnum";

    export default {
        name: "eventsList",
        components: {
            List,
            Filters,
            ConfirmDialog,
        },
        data: () => ({
            header: [
                {key: 'name', label: 'Nom', sortable: true},
                {key: 'invited', label: 'Invité', sortable: true},
                {key: 'type', label: 'Type', sortable: true},
                {key: 'status', label: 'Statut', sortable: true},
                {key: 'program', label: 'Prestation', sortable: true},
                {key: 'dateEvent', label: 'Date Evenement', sortable: true},
                {key: 'updatedAt', label: 'Date modification', sortable: true},
                {key: 'actions', label: 'Actions', actions: ['see', 'edit', 'delete']},
            ],
            headerModel: [
                {key: 'name', label: 'Nom', sortable: true},
                {key: 'description', label: 'Description', sortable: true},
                {key: 'type', label: 'Type', sortable: true},
                {key: 'updatedAt', label: 'Date modification', sortable: true},
                {key: 'actions', label: 'Actions', actions: ['edit', 'delete']},
            ],
            allEvents: {
                items: [],
                count: 0,
            },
            allEventModels: {
                items: [],
                count: 0,
            },
            filterList: [
                {key: 'search', type: 'text', label: 'Nom ou Description'},
                {key: "dateEvent", type: "datepicker", label: "Date", attributes: {range: true}},
                {key: 'status', type: 'select', label: 'Statut', attributes: {
                    options: [
                        {value: 'created', label: 'Créé'},
                        {value: 'upcoming', label: 'A venir'},
                        {value: 'ongoing', label: 'En cours'},
                        {value: 'finished', label: 'Terminé'},
                    ],
                }},
                {key: 'user', type: 'userAutocomplete', label: 'Invité'}
            ],
            filters: {},
            sortColumn: 'createdAt',
            sortDirection: 'desc',
            loading: false,
            offset: 0,
            sortModelColumn: 'createdAt',
            sortModelDirection: 'desc',
            offsetModel: 0,
            deleteModal: {
                event: {},
                show: false,
            },
        }),
        created() {
            if (this.isAdmin) {
                this.filterList.push({key: 'organizer', type: 'userAutocomplete', label: 'Organisateur', attributes: {
                  types: [COACH]
                }});
                this.filterList.push({ key: "model", type: "checkbox", label: "Modèle" });
            }
        },
        apollo: {
            me: {
                query: LOGGED_USER
            },
            allEvents: {
                query: ALL_EVENTS,
                variables () {
                  return this.queryVariables;
                }
            },
            allEventModels: {
                query: ALL_EVENT_MODELS,
                variables() {
                    return {
                        ...this.filters,
                        limit: 10,
                        offset: this.offsetModel,
                        sortColumn: this.sortModelColumn,
                        sortDirection: this.sortModelDirection,
                    }
                }
            }
        },
        computed: {
            queryVariables () {
                return {
                    ...this.filters,
                    startDate: this.filters.dateEvent ? this.filters.dateEvent.split(',')[0] : null,
                    endDate: this.filters.dateEvent ? this.filters.dateEvent.split(',')[1] : null,
                    limit: 10,
                    offset: this.offset,
                    sortColumn: this.sortColumn,
                    sortDirection: this.sortDirection,
                }
            },
            listItems () {
                return this.allEvents.items
                    .map(item => ({
                        id: item.id,
                        name: item.name,
                        invited: item.users && item.users.length > 0 ? item.users[0].firstName + ' ' + item.users[0].lastName.toUpperCase() : '',
                        type: this.eventTypes.find(e => e.value ===item.type).label,
                        status: EVENT_STATUS[item.status],
                        program: item.program ? item.program.name : '',
                        programId: item.program ? item.program.id : null,
                        dateEvent: item.dateEvent ? localMoment(item.dateEvent).format('DD/MM/YYYY, HH:mm') + ' à ' + localMoment(item.dateEventEnd).format('HH:mm') : null,
                        updatedAt: localMoment(item.updatedAt ? item.updatedAt : item.createdAt).format('DD/MM/YYYY'),
                        _actions: {
                          edit: this.me.rights.includes('ROLE_UPDATE_EVENT'),
                          delete: this.me.rights.includes('ROLE_DELETE_EVENT'),
                        }
                    }));
            },
            listModelItems () {
                return this.allEventModels.items
                    .map(item => ({
                        id: item.id,
                        name: item.name,
                        description: item.description,
                        type: this.eventTypes.find(e => e.value ===item.type).label,
                        updatedAt: localMoment(item.updatedAt ? item.updatedAt : item.createdAt).format('DD/MM/YYYY hh:mm'),
                        eventCount: item.events.count
                    }));
            },
            isAdmin() {
                return this.me && this.me.type === "admin";
            },
            eventTypes() {
                return EVENT_TYPES;
            },
        },
        methods: {
            updateSort ($event) {
                this.sortColumn = $event.column;
                this.sortDirection = $event.direction;
                this.resetOffset();
            },
            updatePage (page) {
                this.offset = (page - 1) * 10;
            },
            updateModelSort ($event) {
                this.sortModelColumn = $event.column;
                this.sortModelDirection = $event.direction;
                this.resetOffset();
            },
            updateModelPage (page) {
                this.offsetModel = (page - 1) * 10;
            },
            updateFilters (filters) {
                this.filters = {...filters};
                this.resetOffset();
            },
            resetOffset () {
                this.offset = 0;
                this.offsetModel = 0;
            },
            closeDeleteModal () {
                this.deleteModal.show = false;
            },
            showDeleteModal (event) {
                if (!event.status && event.eventCount) {
                    iziToast.error({
                        position: 'topRight',
                        title: 'Erreur',
                        message: "Nous ne pouvons pas supprimer car il a des events."
                    });
                    return;
                }
                this.deleteModal.event = event;
                this.deleteModal.show = true;
            },
            deleteEvent () {
                this.closeDeleteModal();
                let isModel = !this.deleteModal.event.status;
                
                this.$apollo.mutate({
                    mutation: isModel ? DELETE_EVENT_MODEL : DELETE_EVENT,
                    variables: isModel ? { eventModelId: this.deleteModal.event.id } : { eventId: this.deleteModal.event.id },
                }).then(() => {
                    this.$apollo.queries[isModel ? 'allEventModels' : 'allEvents'].refetch();
                    iziToast.success({
                        position: 'topRight',
                        title: 'Succès',
                        message: "L'événement a bien été supprimé",
                    });
                });
            },
            viewEvent (event) {
                if (event.programId) {
                    this.$router.push({name: 'ProgramEventForm', params: {id: event.programId, event: 'event', eid: event.id, mode: 'view'}});
                } else {
                    this.$router.push({name: 'EventForm', params: {id: event.id, mode: 'view'}});
                }
            },
            editEvent (event) {
                if (event.programId) {
                    this.$router.push({name: 'ProgramEventForm', params: {id: event.programId, event: 'event', eid: event.id}});
                } else {
                    this.$router.push({name: 'EventForm', params: {id: event.id}});
                }
            },
            editEventModel (event) {
                this.$router.push({name: 'EventForm', params: {id: event.id, mode: 'model'}});
            },
            getFilterQuery () {
                let string = [];
                for (let i in this.queryVariables) {
                  if (this.queryVariables[i]) {
                    string.push(i + "=" + encodeURIComponent(this.queryVariables[i]));
                  }
                }

                return string.join("&");
            },
            exportEvents() {
                let statusLabels = JSON.stringify({
                    ...EVENT_STATUS,
                    ...PROGRAM_STATUS,
                });
                window.open(
                    process.env.VUE_APP_GRAPHQL_HTTP.substr(0, process.env.VUE_APP_GRAPHQL_HTTP.lastIndexOf('/'))
                    + '/export/events?statusLabels=' + encodeURIComponent(statusLabels) + '&' + this.getFilterQuery(),
                    '_blank'
                );
            },
            exportEventsCrossTalent() {
                window.open(
                    process.env.VUE_APP_GRAPHQL_HTTP.substr(0, process.env.VUE_APP_GRAPHQL_HTTP.lastIndexOf('/'))
                    + '/export/events-cross-talent?' + this.getFilterQuery(),
                    '_blank'
                );
            },
        }
    };
</script>
