<template>
    <dash-wrap mode="coach" article="Bonjour" :title="me ? me.firstName : ''">

        <div class="d-flex" v-if="loading">
            Loading...
        </div>
        <div class="d-flex justify-content-center" v-if="!loading">
            <div class="candidat-info-wrap">
                <div class="coache-alarm-card bg-carriere" v-if="candidate.nextEvent && (candidate.nextEvent.status === 'ongoing' || isEventDue(candidate.nextEvent))">
                    <div class="d-flex">
                        <img class="bg" src="@/assets/img/alarm.svg" alt />
                        <div class="cac-content cac-content--pri">
                            <div class="d-flex cac-content--title">
                                <span class="font-size-21 font-weight-bold">{{ candidate.nextEvent.name}}</span>
                                <div class="d-flex tags">
                                    <span class="page-coach-tag style-one" v-if="candidate.nextEvent.meetingPlace === 'presential'">Présentielle</span>
                                    <span class="page-coach-tag style-two" v-if="candidate.nextEvent.meetingPlace === 'presential'">{{ candidate.nextEvent.meetingRoom }}</span>
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
                <div class="tabs-wrap coache-tabs">
                    <ul class="nav nav-tabs custom-nav coache-nav">
                        <li class="nav-item" v-for="(program, index) in programs" :key="index">
                            <a class="nav-link" :class="{'active': activeNav === index}" @click="activeNav = index">
                                <img src="@/assets/img/prestation.svg" />
                                <span>{{ program.name }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" :class="{'active': activeNav === programs.length}" @click="activeNav = programs.length">
                                <span>Complémentaire</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" v-if="activeNav < programs.length">
                        <div class="tab-pane fade active show">
                            <div class="d-flex flex-column flex-md-row mt-1">
                                <div :class="[!getCoachForProgram(program) ? 'col-md-10' : 'col-md-8']">
                                    <!--<div>
                                        <span class="text-gris_70 font-size-18 font-weight-bold">{{ programs[activeNav].type === 'individual' ? 'Coaching Individuel' : 'Coaching Collectif' }}</span>
                                    </div>-->
                                    <div class="row d-flex">
                                        <div class="col-sm-12 col-md-6 coache-tab-item">
                                            <img src="@/assets/img/cal.svg" alt />
                                            <div class="d-flex flex-wrap coache-tab-item--content">
                                                <span class="coache-tab-item--title">
                                                    <span class="d-block mr-1">du</span>
                                                    <span class="text-l">{{ dateFormatter(programs[activeNav].dateStart) }}</span>
                                                    <span class="d-block mx-1">au</span>
                                                    <span class="text-l">{{ dateFormatter(programs[activeNav].dateEnd) }}</span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6 d-flex">
                                            <div class="coache-tab-item ml-auto">
                                                <img src="@/assets/img/coaching.svg" alt />
                                                <div class="d-flex flex-wrap coache-tab-item--content">
                                                    <span class="text-l mr-1">{{ candidate.completedEventsCount }}/{{ candidate.eventsCount }}</span>
                                                    séances
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap justify-content-center mt-4">
                                      <a class="btn btn-primary" :href="'/documents-list?programId=' + programs[activeNav].id" target="_blank">
                                        <img src="@/assets/img/icon-livrable-alt.svg" /> Consulter les livrables
                                      </a>
                                    </div>
                                </div>
                                <div class="mx-4 d-none d-md-block border-right" />
                                <div class="candidate-coach ml-auto mr-auto" @click="coachPanelActive = !coachPanelActive" v-if="getCoachForProgram(program)">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="mx-4 user-avatar">
                                            <img v-if="!getCoachForProgram(programs[activeNav]).profilePicture" src="@/assets/img/user-primary.svg" height="50px" width="50px" />
                                            <img v-else :src="getImageUrl(getCoachForProgram(programs[activeNav]).profilePicture.id)" height="50px" width="50px" />
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div class="font-size-14">
                                            {{ getCoachForProgram(programs[activeNav]).firstName }} {{ getCoachForProgram(programs[activeNav]).lastName }}
                                        </div>
                                        <div class="font-size-12 text-primary">
                                            Coach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <perfect-scrollbar class="coache-ps">
                    <div class="page-coach-item" :class="{'page-coach-item--medium': eventShown[event.id]}"
                         v-for="(event, index) in events" :data-tag="events.length - index" :key="index">
                        <template v-if="!eventShown[event.id]">
                            <img src="@/assets/img/page-coach-2.png" class="d-none d-md-inline-block pci-img" alt="" />
                            <img src="@/assets/img/page-coach-sm.png" class="d-inline-block d-md-none pci-img pci-img--sm" alt="" />
                        </template>
                        <img src="@/assets/img/page-coach-large.png" class="pci-img" alt="" v-show="eventShown[event.id]">
                        <div class="page-coach-item-content" @click="toggleEventDetails(event)">
                            <div class="page-coach-item-top">
                                <span class="page-coach-title">{{ event.name }}</span>
                                <div class="page-coach-tags">
                                    <span class="page-coach-tag style-one" v-if="event.status === 'created'">Crée</span>
                                    <span class="page-coach-tag style-one" v-if="event.status === 'ongoing'">En cours</span>
                                    <span class="page-coach-tag style-two" v-if="event.status === 'upcoming'">à venir</span>
                                    <span class="page-coach-tag style-three" v-if="event.status === 'finished'">Terminée</span>
                                    <span class="page-coach-tag style-four" v-if="event.status === 'archived'">Archivé</span>
                                    <template v-if="event.meetingPlace === 'presential'">
                                        <span class="page-coach-tag style-one">Présentielle</span>
                                        <span class="page-coach-tag style-two">{{ event.meetingRoom }}</span>
                                    </template>
                                    <span class="page-coach-tag style-one" v-if="event.meetingPlace === 'visio'">Visio</span>
                                </div>
                            </div>
                            <div v-if="event.dateEvent">
                                <span class="text-carriere-darker font-size-14">{{ getEventDate(event) }} de {{ getEventTime(event) }}</span>
                            </div>
                            <div v-if="event.isRated" class="mt-1">
                                <rate :length="5" :value="event.userRating.starsNumber" :readonly="true" />
                            </div>
                            <div class="mt-3 overflow-hidden event-description" @click="$event => $event.stopPropagation()"
                                 v-if="eventShown[event.id]">
                                <coache-title-design :wide="false" v-if="event.memo">Memo</coache-title-design>
                                <p class="mt-1 font-size-14 memo-p" v-if="event.memo">
                                    {{ event.memo }}
                                </p>
                                <coache-title-design :wide="false" v-if="event.isRated">Votre commentaire</coache-title-design>
                                <p v-if="event.isRated" class="mt-1 font-size-14">
                                    {{ event.userRating.rateNote }}
                                </p>
                                <documents-event :event="event" :me="me" :loading="loadingCreateDocument" @createDocument="createDocument"/>
                            </div>
                        </div>
                        <div class="ml-auto align-self-start pci--options">
                            <button class="btn btn-sm" @click="toggleItemOptions(index)">
                                <img src="@/assets/img/option.svg" alt />
                            </button>
                            <div class="pci--options-list" :class="{'active': activeOptions === index}">
                                <ul>
                                    <li @click="openEventLink(event)" v-if="event.meetingPlace === 'visio'">
                                        <div class="d-flex">
                                            <img src="@/assets/img/edit.svg" alt />
                                            <span>Démarrer la séance visio</span>
                                        </div>
                                    </li>
                                    <li @click="openRateModal(event)" v-if="!event.isRated && event.status === 'finished' && event.type === 'individual_session'">
                                        <div class="d-flex">
                                            <img src="@/assets/img/plus-primary.svg" alt />
                                            <span>Noter la séance</span>
                                        </div>
                                    </li>
                                    <!--<li>
                                        <div class="d-flex">
                                            <img src="@/assets/img/edit.svg" alt />
                                            <span>Signer la feuille de présence</span>
                                        </div>
                                    </li>-->
                                </ul>
                            </div>
                        </div>
                    </div>
                </perfect-scrollbar>
            </div>
        </div>
        <coach-panel :active="coachPanelActive"
                     :coach="programs.length && activeNav < programs.length ? getCoachForProgram(programs[activeNav]) : {}"
                     @toggle="coachPanelActive = !coachPanelActive" />
        <event-rate-modal :event="rateModal.event"
                          v-if="rateModal.show"
                          @close="rateModal.show = false"
                          @confirm="saveEventRate" />
    </dash-wrap>
</template>
<script>
    import CoacheTitleDesign from "@/components/coache/TitleDesign.vue";
    import ProgramFile from "@/components/coache/ProgramFile.vue";
    import DocumentsEvent from "@/components/utils/DocumentsEvent.vue";
    import { PerfectScrollbar } from "vue2-perfect-scrollbar";
    import { FILE_PATH } from "@/enum/FilePathConstant";
    import moment from 'moment';
    import CoachPanel from "@/components/coache/CoachPanel";
    import izitoast from "izitoast";
    import localMoment from "@/utils/localMoment";
    import EventRateModal from "@/components/coache/EventRateModal";

    import { CANDIDATE_FOR_DASHBOARD } from "@/graphql/user/candidate-for-dashboard-query";
    import { UPLOAD_FILE } from "@/graphql/file/upload-file-mutation";
    import { CREATE_DOCUMENT_FROM_DASHBOARD_EVENT } from "@/graphql/document/create-document-from-dashboard-event-mutation";
    import { UPDATE_EVENT_MEMO } from "@/graphql/event/update-event-memo-mutation";
    import { LOGGED_USER } from "@/graphql/security/logged-user-query";
    import { EVENT_TYPES } from "@/enum/EventTypesEnum";
    import {RATE_EVENT} from "@/graphql/event/rate-event-mutation";
    import DocumentFileCard from "@/components/docs/DocumentFileCard";
    import deleteQueriesFromApolloCache from "@/utils/deleteQueriesFromApolloCache";

    export default {
        name: "CandidateDashboardView",
        components: {
            CoacheTitleDesign,
            DocumentsEvent,
            PerfectScrollbar,
            CoachPanel,
            EventRateModal,
        },
        data() {
            return {
                chosenFiles: [],
                me: {},
                candidateById: {
                    user: {
                        programsByProgramsUsers: [],
                        eventsWithoutProgram: [],
                        company: {},
                    },
                },
                eventShown: {},
                coachPanelActive: false,
                loading: false,
                activeNav: 0,
                activeOptions: null,
                selectProgramForEvent: false,
                loadingCreateDocument: false,
                rateModal: {
                    show: false,
                    event: {},
                },
            };
        },
        computed: {
            candidate () {
                return this.candidateById;
            },
            user () {
                return this.candidate.user;
            },
            programs () {
                return this.candidate.user.programsByProgramsUsers;
            },
            events () {
                let events = [];
                if (this.activeNav < this.programs.length) {
                    events = [...this.programs[this.activeNav].eventsOrderedByDate.items].reverse();
                } else {
                    events = [...this.candidate.eventsWithoutProgram.items].reverse();
                }

                return events.filter(event => event.isAttending);
            },
        },
        apollo: {
            me: {
                query: LOGGED_USER,
            },
            candidateById: {
                query: CANDIDATE_FOR_DASHBOARD,
                variables () {
                    return {
                        id: this.me.id,
                        fetchOnlyAttendingEvents: true,
                    };
                },
                watchLoading (isLoading) {
                    this.loading = isLoading;
                },
                result (result) {
                    this.loading = typeof result.data === "undefined" && result.networkStatus === 1;

                    if (this.$route.query.event && this.selectProgramForEvent) {
                        let found = false,
                            eventId = this.$route.query.event;
                        result.candidateById.user.programsByProgramsUsers.forEach((program, index) => {
                            if (program.eventsOrderedByDate.items.find(e => e.id === eventId)) {
                                this.activeNav = index;
                                found = true;
                            }
                        });
                        if (!found && result.candidateById.eventsWithoutProgram.items.find(e => e.id === this.$route.query.event)) {
                            this.activeNav = result.candidateById.user.programsByProgramsUsers.length;
                        }
                        this.selectProgramForEvent = false;
                    }
                }
            },
        },
        created() {
            this.$apollo.queries.candidateById.refetch();
        },
        methods: {
            getEventDate(event) {
                return moment(event.dateEvent).format('dddd DD MMM Y');
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
            openEventLink (event) {
                window.open(event.teamsLink, "_blank");
            },
            createDocument (file, program, event) {
                this.loadingCreateDocument = true;
                this.$apollo.mutate({
                    mutation: UPLOAD_FILE,
                    variables: {
                        file: file,
                    },
                    update(proxy) {
                        deleteQueriesFromApolloCache(proxy, "allDocuments");
                        deleteQueriesFromApolloCache(proxy, "allDocumentsForCandidate");
                        deleteQueriesFromApolloCache(proxy, "allDocumentCategories");
                    }
                }).then(response => {
                    this.$apollo.mutate({
                        mutation: CREATE_DOCUMENT_FROM_DASHBOARD_EVENT,
                        variables: {
                            fileDescriptorId: response.data.uploadFile.id,
                            eventId: !event ? null : event.id,
                            name: file.name,
                            elaborationDate: moment().utc().toISOString(),
                        },
                    }).then(response => {
                        this.loadingCreateDocument = false;
                        event.documents.push(response.data.createDocumentFromDashboardEvent);
                    }).catch(() => {
                        izitoast.error({
                            position: 'topRight',
                            title: 'Erreur',
                            message: 'Le fichier n\'a pas pu être téléchargé, la taille était trop grande',
                        })
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
            openFile (document) {
                let baseUrl = process.env.VUE_APP_GRAPHQL_HTTP.substr(
                    0,
                    process.env.VUE_APP_GRAPHQL_HTTP.lastIndexOf("/")
                );
                window.open(baseUrl + FILE_PATH + document.fileDescriptor.id, "_blank");
            },
            toggleItemOptions(i) {
                this.activeOptions = this.activeOptions == null ? i : null;
            },
            getCoachForProgram (program) {
                return this.candidate.user.coach ? this.candidate.user.coach : {};
            },
            openRateModal (event) {
                this.rateModal.show = true;
                this.rateModal.event = event;
                this.activeOptions = null;
            },
            saveEventRate (info) {
                this.rateModal.show = false;

                let event;
                if (this.activeNav < this.programs.length) {
                    event = this.programs[this.activeNav].eventsOrderedByDate.items.find(event => event.id === this.rateModal.event.id);
                } else {
                    event = this.candidate.eventsWithoutProgram.items.find(event => event.id === this.rateModal.event.id);
                }
                event.isRated = true;
                event.userRating = {
                    starsNumber: info.starsNumber,
                    rateNote: info.comment,
                };

                this.$apollo.mutate({
                    mutation: RATE_EVENT,
                    variables: {
                        eventId: this.rateModal.event.id,
                        starsNumber: info.starsNumber,
                        comment: info.comment,
                    },
                });
            },
            isEventDue(event) {
                return localMoment().isSameOrAfter(localMoment(event.dateEvent).subtract(5, 'minutes'));
            }
        },
        mounted () {
            if (typeof this.$route.query.event !== 'undefined') {
                this.eventShown[this.$route.query.event] = true;
                this.selectProgramForEvent = true;
            }
        }
    };
</script>