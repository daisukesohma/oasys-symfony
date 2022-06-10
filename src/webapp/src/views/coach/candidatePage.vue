<template>
    <dash-wrap mode="coach" article="Bonjour" :title="me ? me.firstName : ''" @create-event="$router.push({name: 'ProgramEventForm', params: {id: candidate.program.id, event: 'event'}, query: {fromCandidate: candidate.user.id}})">
        <template v-slot:headeractions>
            <div class="d-none d-md-flex">
                <button class="btn btn-white-icon" @click="$event => $router.push({name: 'ProgramForm'})">
                    <img src="@/assets/img/prestation-add.svg" alt />
                    <span>Créer prestation</span>
                </button>
                <button class="btn btn-white-icon ml-3" @click="$event => $listeners['create-event'] ? $emit('create-event', $event) : $router.push({name: 'EventForm'})">
                    <img src="@/assets/img/icon-event-add_primary.svg" alt />
                    <span>Créer évenement</span>
                </button>
            </div>

            <div class="d-flex d-md-none coache-header-buttons coache-header-buttons--mini">
                <button class="btn" @click="todoPanelActive = true">
                    <img src="@/assets/img/to-do-white.svg" alt />
                </button>
            </div>
        </template>
        <template v-slot:mainaction>
            <a class="nav-link main-link" :class="{'active':mainNavActive}" @click="mainNavActive= !mainNavActive">
                <img v-if="mainNavActive" src="@/assets/img/croix-white.svg" alt="">
                <img v-else src="@/assets/img/burger-white.svg" alt="">
            </a>
            <div class="link-menu d-flex d-md-none" :class="{'active':mainNavActive}">
                <a class="btn nav-link" @click="$event => $router.push({name: 'ProgramForm'})">
                <img src="@/assets/img/prestation-add-white.svg" alt />
                </a>
                <a class="btn nav-link" @click="$event => $listeners['create-event'] ? $emit('create-event', $event) : $router.push({name: 'EventForm'})">
                <img src="@/assets/img/event-add-white.svg" alt />
                </a>
            </div>
        </template>
        <div class="d-flex" v-if="loading">
            Loading...
        </div>
        <div class="d-flex justify-content-center flex-wrap">
            <div class="user-card-wrap">
                <div class="user-card-view" :class="{'active':userCardActive}">
                    <button class="btn coache-close-btn btn-sm" @click="userCardActive=false" v-if="userCardActive">
                        <img src="@/assets/img/plus-primary.svg" />
                    </button>
                    <span class="coache-tag">Informations</span>
                    <template v-if="userCardActive">
                        <div class="card-bg">
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="spec spec--coaching">
                                    <span class="spec-1">{{ candidate.completedEventsCount }}/{{ candidate.eventsCount }}</span>
                                    <span class="spec-2">Coaching</span>
                                </div>
                                <div class="mx-4 user-avatar">
                                    <img v-if="!candidate.user.profilePicture" src="@/assets/img/user-primary.svg" height="88px" width="88px" />
                                    <img v-else :src="getImageUrl(candidate.user.profilePicture.id)" height="88px" width="88px" />
                                </div>
                                <div class="spec spec--to-do">
                                    <span class="spec-1">{{ candidate.program.todos.count }}</span>
                                    <span class="spec-2">To Do</span>
                                </div>
                            </div>
                            <div class="text-center mt-2">
                                <span class="font-size-18 font-weight-bold">{{ candidate.user.firstName }} {{ candidate.user.lastName }}</span>
                            </div>
                            <div class="px-4 py-2 mt-2">
                                <div class="contact-item">
                                    <img src="@/assets/img/phone.svg" />
                                    <span>{{ candidate.user.phone }}</span>
                                </div>
                                <div class="contact-item">
                                    <img src="@/assets/img/mail.svg" />
                                    <span>{{ candidate.user.email }}</span>
                                </div>
                                <div class="contact-item">
                                    <img src="@/assets/img/home.svg" />
                                    <span>{{ candidate.user.address + " " + candidate.user.userCodePostal + " " + candidate.user.userCity }}</span>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap mt-1">
                                <button class="btn btn-outline-consultant-light font-size-14 btn-block mb-2" @click="openProgramList">
                                    <img src="@/assets/img/prestation.svg" alt />
                                    Voir les prestations
                                </button>
                                <button class="btn btn-outline-consultant-light font-size-14 btn-block" @click="toggleTodoPanel" :disabled="!todoPanelActive">
                                    <img src="@/assets/img/to-do.svg" alt />
                                    Masquer ma To Do List
                                </button>
                            </div>
                            <div class="mt-3 pt-2">
                                <coache-title-design :wide="false">Société</coache-title-design>
                                <div class="address-details">
                                    <p>{{ candidate.user.company.name }}</p>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="card-bg mini-card">
                            <div class="d-flex align-items-center">
                                <div>
                                    <div class="user-avatar">
                                        <img v-if="!candidate.user.profilePicture" src="@/assets/img/user-primary.svg" height="88px" width="88px" />
                                        <img v-else :src="getImageUrl(candidate.user.profilePicture.id)" height="88px" width="88px" />
                                    </div>
                                </div>
                                <div class="ml-2">
                                    <div>
                                        <span class="font-size-18 font-weight-bold">{{ candidate.user.firstName }} {{ candidate.user.lastName }}</span>
                                    </div>
                                    <div class="d-flex mt-1">
                                          <div class="spec spec--coaching">
                                            <span class="spec-1">{{ candidate.completedEventsCount }}/{{ candidate.eventsCount }}</span>
                                            <span class="spec-2">Coaching</span>
                                        </div>
                                        <div class="spec spec--to-do ml-4">
                                            <span class="spec-1">{{ candidate.program.todos.count }}</span>
                                            <span class="spec-2">To Do</span>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-coache-user-mini" @click="userCardActive=true" >
                                    <img src="@/assets/img/plus-gray.svg" />
                                </button>
                            </div>
                        </div>

                    </template>
                </div>
            </div>
            <div class="coache-info-wrap">
                <div class="coache-alarm-card bg-carriere" v-if="!isProgramArchived && candidate.nextEvent && (candidate.nextEvent.status === 'ongoing' || isEventDue(candidate.nextEvent))">
                    <div class="d-flex">
                        <img class="bg" src="@/assets/img/alarm.svg" alt />
                        <div class="cac-content cac-content--pri">
                            <div class="d-flex cac-content--title">
                                <span class="font-size-21 font-weight-bold">{{ candidate.nextEvent.name}}</span>
                                <div class="d-flex tags">
                                    <template v-if="candidate.nextEvent.meetingPlace === 'presential'">
                                        <span class="page-coach-tag style-one">Présentielle</span>
                                        <span class="page-coach-tag style-two">{{ candidate.nextEvent.meetingRoom }}</span>
                                    </template>
                                    <span class="page-coach-tag style-one" v-if="candidate.nextEvent.meetingPlace === 'visio'">Visio</span>
                                </div>
                            </div>
                            <span class="text-white font-weight-bold mt-3">Maintenant de {{ getEventTime(candidate.nextEvent) }}</span>
                        </div>
                        <div class="cac-content ml-auto" v-if="candidate.nextEvent.meetingPlace === 'visio'">
                            <button class="btn btn-outline-white d-none d-md-block" @click="openEventLink(candidate.nextEvent)">Commencer</button>
                            <button class="btn btn-sm p-0 d-inline-block d-md-none" @click="openEventLink(candidate.nextEvent)">
                                <img src="@/assets/img/commencer-arrow.svg" class="m-0" alt="">
                            </button>
                        </div>
                    </div>
                </div>

                <!-- tabs -->
                <div class="tabs-wrap coache-tabs">
                    <ul class="nav nav-tabs custom-nav coache-nav">
                        <li class="nav-item">
                            <a class="nav-link" :class="{'active': activeNav===1}" @click="activeNav=1">
                                <img src="@/assets/img/prestation.svg" />
                                <span>Prestation</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" :class="{'active': activeNav===2}" @click="activeNav=2">
                                <span>Complémentaire</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade" :class="{ 'active show' : activeNav === 1 }">
                            <span class="text-gris_70 font-size-18 font-weight-bold">{{ candidate.program.name }}</span>
                            <div class="d-flex mt-1 coache-tab-item--list">
                                <div class="coache-tab-item">
                                    <img src="@/assets/img/cal.svg" alt />
                                    <div class="d-flex flex-wrap coache-tab-item--content">
                                        <span class="coache-tab-item--title">
                                            <span class="d-block mr-1">du</span>
                                            <span class="text-l">{{ dateFormatter(candidate.program.dateStart) }}</span>
                                            <span class="d-block mx-1">au</span>
                                            <span class="text-l">{{ dateFormatter(candidate.program.dateEnd) }}</span>
                                        </span>
                                    </div>
                                </div>
                                <div class="coache-tab-item ml-auto">
                                    <img src="@/assets/img/coaching.svg" alt />
                                    <div class="d-flex flex-wrap coache-tab-item--content">
                                        <span class="coache-tab-item--title">
                                        <span class="text-l mr-1">{{ candidate.completedEventsCount }}/{{ candidate.eventsCount }}</span>
                                        séances
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap justify-content-center mt-4">
                                <a class="btn btn-primary" :href="'/documents-list?programId=' + candidate.program.id + '&livrables=true'" target="_blank">
                                  <img src="@/assets/img/icon-livrable-alt.svg" /> Consulter les livrables
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <perfect-scrollbar class="coache-ps">
                    <div class="page-coach-item" :class="{'page-coach-item--large': eventShown[event.id]}"
                         v-for="(event, index) in events" :data-tag="events.length - index" :key="index">
                         <template v-if="!eventShown[event.id]">
                            <img src="@/assets/img/page-coach-2.png" class="d-none d-md-inline-block pci-img" alt="" />
                            <img src="@/assets/img/page-coach-sm.png" class="d-inline-block d-md-none pci-img pci-img--sm" alt="" />
                         </template>
                        <img src="@/assets/img/page-coach-large.png" class="pci-img lg" alt="" v-else>
                        <div class="page-coach-item-content" @click="toggleEventDetails(event)">
                            <div class="pci-title">
                                <span class="page-coach-title">{{ event.name }}</span>
                                <div class="page-coach-tags">
                                    <span class="page-coach-tag style-one" v-if="event.status === 'created'">Crée</span>
                                    <span class="page-coach-tag style-five" v-if="event.status === 'ongoing'">En cours</span>
                                    <span class="page-coach-tag style-two" v-if="event.status === 'upcoming'">à venir</span>
                                    <span class="page-coach-tag style-three" v-if="event.status === 'finished'">Terminée</span>
                                    <span class="page-coach-tag style-four" v-if="event.status === 'archived'">Archivé</span>
                                    <template v-if="event.meetingPlace === 'presential'">
                                        <span class="page-coach-tag style-one">Présentielle</span>
                                        <span class="page-coach-tag style-one">{{ event.meetingRoom }}</span>
                                    </template>
                                    <span class="page-coach-tag style-one" v-if="event.meetingPlace === 'visio'">Visio</span>
                                </div>
                            </div>
                            <div v-if="event.dateEvent">
                                <span class="text-carriere-darker font-size-14">{{ getEventDate(event) }} de {{ getEventTime(event) }}</span>
                            </div>
                            <div v-if="event.rating !== null" class="mt-1">
                                <rate :length="5" :value="event.rating" :readonly="true" />
                                <p>{{ event.rateNote }}</p>
                            </div>
                            <div class="mt-3 overflow-hidden event-description" @click="$event => $event.stopPropagation()"
                                 v-if="eventShown[event.id]">
                                <coache-title-design :wide="false" v-if="event.memo">Memo</coache-title-design>
                                <p class="mt-1 font-size-14 memo-p" v-if="event.memo">
                                    {{ event.memo }}
                                </p>
                                <documents-event :event="event" :me="me" :loading="loadingCreateDocument" @createDocument="createDocument" :compact="true" />
                            </div>
                        </div>
                        <div class="ml-auto align-self-start pci--options">
                            <button class="btn btn-sm" @click="toggleItemOptions(index)">
                                <img src="@/assets/img/option.svg" alt />
                            </button>
                            <div class="pci--options-list" :class="{'active': activeOptions == index}">
                                <ul>
                                    <li @click="$event => openMemoModal(event)">
                                        <div class="d-flex">
                                            <img src="@/assets/img/edit.svg" alt />
                                            <span v-if="!event.memo">Ecrire un mémo</span>
                                            <span v-if="event.memo">Modifier un mémo</span>
                                        </div>
                                    </li>
                                    <li @click="$router.push({name: 'ProgramEventForm', params: {id: candidate.program.id, event: 'event', eid: event.id}, query: {back: $route.path}})">
                                        <div class="d-flex">
                                            <img src="@/assets/img/edit.svg" alt />
                                            <span>Modifier séance</span>
                                        </div>
                                    </li>
                                    <li @click="showDeleteModal(event)">
                                        <div class="d-flex">
                                            <img src="@/assets/img/croix-2.svg" alt />
                                            <span>Annuler séance</span>
                                        </div>
                                    </li>
                                    <li @click="$event => sendEvaluationSurvey(event)" v-if="event.type === 'tripartite_meeting'">
                                        <div class="d-flex">
                                            <img src="@/assets/img/mail-primary.svg" alt />
                                            <span>Envoyer l'enquête</span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </perfect-scrollbar>
            </div>
        </div>
        <memo-modal v-if="memoModal.show"
                    :event="memoModal.event"
                    @close="memoModal.show = false"
                    @confirm="$event => updateEventMemo($event)" />
        <todo-panel v-if="candidate.program && candidate.program.id"
                    :active="todoPanelActive"
                    @toggle="toggleTodoPanel"
                    :program="candidate.program"
                    :candidate="candidate" />
        <confirm-dialog v-if="deleteModal.show"
                        title="Êtes-vous sûr de vouloir supprimer l'événement ?"
                        @close="closeDeleteModal()"
                        @confirm="deleteEvent()" />
    </dash-wrap>
</template>
<script>
    import CoacheTitleDesign from "@/components/coache/TitleDesign.vue";
    import DocumentsEvent from "@/components/utils/DocumentsEvent.vue";
    import ProgramFile from "@/components/coache/ProgramFile.vue";
    import TodoPanel from "@/components/coache/ToDoPanel.vue";
    import { PerfectScrollbar } from "vue2-perfect-scrollbar";
    import { FILE_PATH } from "@/enum/FilePathConstant";
    import ConfirmDialog from "@/components/utils/ConfirmDialog";
    import MemoModal from "@/components/coache/MemoModal";
    import moment from 'moment';
    import izitoast from "izitoast";
    import localMoment from "@/utils/localMoment";

    import { CANDIDATE_BY_ID} from "@/graphql/user/candidate-by-id-query";
    import { UPLOAD_FILE } from "@/graphql/file/upload-file-mutation";
    import { CREATE_DOCUMENT_FROM_PROGRAM } from "@/graphql/document/create-document-from-program-mutation";
    import { CREATE_DOCUMENT_FROM_EVENT} from "@/graphql/document/create-document-from-event-mutation";
    import { UPDATE_EVENT_MEMO } from "@/graphql/event/update-event-memo-mutation";
    import { LOGGED_USER } from "@/graphql/security/logged-user-query";
    import { EVENT_TYPES } from "@/enum/EventTypesEnum";
    import { DELETE_EVENT } from "@/graphql/event/delete-event-mutation";
    import { NOTIFY_EVENT_EVALUATION_SURVEY } from "@/graphql/event/notify-event-evaluation-survey-mutation";

    export default {
        name: "CoachView",
        components: {
            CoacheTitleDesign,
            DocumentsEvent,
            TodoPanel,
            PerfectScrollbar,
            ConfirmDialog,
            MemoModal,
        },
        data() {
            return {
                me: {},
                chosenFiles: [],
                candidateById: {
                    user: {},
                    program: {
                        documents: []
                    },
                },
                eventShown: {},
                todoPanelActive: false,
                loading: false,
                activeNav: 1,
                activeOptions: null,
                deleteModal: {
                    event: {},
                    show: false,
                },
                memoModal: {
                    event: {},
                    show: false,
                },
                todoList: [],
                userCardActive:true,
                mainNavActive:false,
                loadingCreateDocument: false,
            };
        },
        computed: {
            candidate() {
                return this.candidateById;
            },
            events() {
                let events = [];
                if (this.activeNav === 1 && this.candidate.program && this.candidate.program.eventsOrderedByDate.items) {
                    events = [...this.candidate.program.eventsOrderedByDate.items].reverse();
                } else if (this.activeNav === 2 && this.candidate.user && this.candidate.eventsWithoutProgram.items.length) {
                    events = [...this.candidate.eventsWithoutProgram.items].reverse();
                } else {
                    events = [];
                }

                return events
                    .filter(event => event.users.find(user => user.id === this.candidateById.user.id))
                    .map(event => ({
                        ...event,
                        rateNote: event.eventsRates.length > 0 ? event.eventsRates.items.find(rate => rate.user.id === this.candidateById.user.id).rateNote : "",
                    }));
            },
            isProgramArchived() {
                return this.candidate.program && this.candidate.program.status === 'finished';
            },
            documents () {
                return this.candidate && this.candidate.program ? this.candidate.program.documents.filter(document => document.type === 'file') : [];
            },
        },
        apollo: {
            candidateById: {
                query: CANDIDATE_BY_ID,
                variables () {
                    return {
                        id: this.$route.params.id,
                        programId: this.$route.params.program,
                        fetchOnlyAttendingEvents: true,
                    };
                },
                watchLoading (isLoading) {
                    this.loading = isLoading;
                },
                result (result) {
                    this.loading = typeof result.data === "undefined" && result.networkStatus === 1;
                }
            },
            me: {
                query: LOGGED_USER,
            },
        },
        created() {
            this.$apollo.queries.candidateById.refetch();
        },
        methods: {
            addChosen(file) {
                this.chosenFiles.push(file);
            },
            toggleItemOptions(i) {
                this.activeOptions = this.activeOptions == null ? i : null;
            },
            toggleTodoPanel() {
                this.todoPanelActive = !this.todoPanelActive;
            },
            getEventDate(event) {
                return localMoment(event.dateEvent).format('dddd DD MMM Y');
            },
            getEventTime(event) {
                return localMoment(event.dateEvent).format('kk:mm') + ' à '
                    + localMoment(event.dateEventEnd).format('kk:mm');
            },
            dateFormatter (date) {
                return localMoment(date).format('DD/MM/YYYY');
            },
            getEventTypeName (event) {
                return EVENT_TYPES.find(type => type.value === event.type).label;
            },
            toggleEventDetails (event) {
                this.eventShown[event.id] = !this.eventShown[event.id];
                this.$forceUpdate();
            },
            getImageUrl(profilePhotoId) {
                let baseUrl = process.env.VUE_APP_GRAPHQL_HTTP.substr(
                    0,
                    process.env.VUE_APP_GRAPHQL_HTTP.lastIndexOf("/")
                );
                return baseUrl + FILE_PATH + profilePhotoId;
            },
            closeDeleteModal () {
                this.deleteModal.show = false;
            },
            showDeleteModal (event) {
                this.deleteModal.event = event;
                this.deleteModal.show = true;
            },
            deleteEvent () {
                this.loading = true;
                this.closeDeleteModal();

                this.$apollo.mutate({
                    mutation: DELETE_EVENT,
                    variables: { eventId: this.deleteModal.event.id },
                }).then(() => {
                    this.$apollo.queries.candidateById.refetch();
                    izitoast.success({
                        position: 'topRight',
                        title: 'Succès',
                        message: "L'événement a bien été supprimé",
                    });
                    this.loading = false;
                });
            },
            createDocument (file, isProgram, event) {
                this.loadingCreateDocument = true;
                this.$apollo.mutate({
                    mutation: UPLOAD_FILE,
                    variables: {
                        file: file,
                    }
                }).then(response => {
                    this.$apollo.mutate({
                        mutation: isProgram ? CREATE_DOCUMENT_FROM_PROGRAM : CREATE_DOCUMENT_FROM_EVENT,
                        variables: {
                            fileDescriptorId: response.data.uploadFile.id,
                            authorId: this.me.id,
                            programId: this.candidate.program.id,
                            categoryId: 'custom',
                            eventId: isProgram ? null : event.id,
                            name: file.name,
                            toSign: false,
                            description: "Créé à partir de la vue Candidat",
                            tags: "",
                            visibility: "private",
                            hidden: true,
                            elaborationDate: moment().toISOString(),
                        },
                    }).then(response => {
                        this.loadingCreateDocument = false;
                        if (isProgram) {
                            this.candidate.program.documents.push(response.data.createDocumentFromProgram);
                        } else {
                            event.documents.push(response.data.createDocumentFromEvent);
                        }
                    });
                }).catch(() => {
                    izitoast.error({
                        position: 'topRight',
                        title: 'Erreur',
                        message: 'Le fichier n\'a pas pu être téléchargé, la taille était trop grande',
                    });
                });
            },
            openMemoModal (event) {
                this.activeOptions = null;
                this.memoModal.event = event;
                this.memoModal.show = true;
            },
            updateEventMemo (memo) {
                this.memoModal.show = false;
                this.memoModal.event.memo = memo;

                this.$apollo.mutate({
                    mutation: UPDATE_EVENT_MEMO,
                    variables: {
                        id: this.memoModal.event.id,
                        memo: memo,
                    },
                });
            },
            openEventLink (event) {
                window.open(event.teamsLink, "_blank");
            },
            openProgramList () {
                window.open("/programs-list?candidateId=" + this.candidate.user.id, "_blank");
            },
            openFile (document) {
                let baseUrl = process.env.VUE_APP_GRAPHQL_HTTP.substr(
                    0,
                    process.env.VUE_APP_GRAPHQL_HTTP.lastIndexOf("/")
                );
                window.open(baseUrl + FILE_PATH + document.fileDescriptor.id, "_blank");
            },
            sendEvaluationSurvey (event) {
                this.$apollo.mutate({
                    mutation: NOTIFY_EVENT_EVALUATION_SURVEY,
                    variables: {
                        eventId: event.id,
                        userId: this.candidate.user.id,
                    },
                });
                izitoast.success({
                    position: 'topRight',
                    title: 'Succès',
                    message: 'L\'email a été envoyé à l\'utilisateur',
                });
            },
            isEventDue (event) {
                return localMoment().isSameOrAfter(localMoment(event.dateEvent).subtract(15, 'minutes'));
            }
        }
    };
</script>