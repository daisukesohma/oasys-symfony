<template>
    <dash-wrap mode="coach" :candidate-home="true">
        <template slot="header">
            <div class="candidate-header text-white font-weight-light" v-if="(isProgramPic !== undefined && !isProgramPic) && candidateById">
                <div class="sub-txt col-12">
                    Bonjour
                    <span class="font-weight-bold">{{ me.firstName }}</span>,
                    <br>
                    <span class="font-weight-bold">Bienvenue sur votre espace "Coaching"</span>
                </div>
                <p class="col-lg-10 col-md-10 col-sm-12 mt-4 mb-0 welcome-message">
                    Pour contribuer à la réussite d'un coaching, l'équipe Oasys partage deux exigences :
                </p>
                <div class="feature col-12 col-md-8">
                    • Celle d'un engagement mutuel entre les parties prenantes : Le bénéficiaire, sa hiérarchie, son DRH référent et le coach
                </div>
                <div class="feature col-12 col-md-8">
                    • Celle du respect : Pour le coaché, respect de la confidentialité des informations échangées ; pour l'entreprise,
                        respect de la lisibilité de l'action, de la formalisation du contrat jusqu'à l'appréciation finale des changements obtenus.
                </div>
            </div>
            <div class="candidate-header text-white font-weight-light" v-else>
                <div class="sub-txt col-12">
                    Bonjour
                    <span class="font-weight-bold">{{ me.firstName }}</span>,
                    <br>
                    <span class="font-weight-bold">Bienvenue sur votre espace {{ me.programType }}</span>
                </div>
                <p class="col-lg-10 col-md-10 col-sm-12 mt-4 mb-0 pic-text">
                    Oasys vous aide dans votre réflexion de mobilité professionnelle, dans le cadre des mesures mises en place par votre entreprise.
                    Rencontrez l'un de nos consultants pour réfléchir, faire le point sur votre situation, clarifier un projet professionnel
                    ou formaliser un dossier de départ volontaire.
                </p>
                <p class="col-lg-10 col-md-10 col-sm-12 mt-4 pic-text">
                    Les consultants Oasys vous accompagnent tout au long de ces démarches en vous garantissant une totale confidentialité. Inscrivez-vous à une réunion d'information et prenez un rendez-vous en ligne avec l'un de nos consultants, en accédant aux calendriers ci-dessous.
                </p>
                <p class="col-lg-10 col-md-10 col-sm-12 mt-4 pic-text">
                    Les "articles à la une" vous proposent des informations complémentaires sur le dispositif, n'hésitez pas à les consulter.
                </p>
            </div>
        </template>
        <perfect-scrollbar id="scrollArticles" class="candidate-home-event" v-if="!loading && documents.length">
            <div class="card-padding d-none d-md-block" />
            <div v-for="document in documents" class="card" :key="document.id" @click="openDocument(document)">
                <div class="card-img" v-if="document.fileDescriptor">
                    <img class="card-img-top" :src="getImageUrl(document.fileDescriptor.id)" alt="Image">
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ document.name }}</h5>
                    <p class="card-text">{{ document.fileDescriptor ? shortenDescription(document.description) : document.description }}</p>
                </div>
            </div>
        </perfect-scrollbar>



        <div class="your-coach" v-if="!loading && candidateById.user.coach">
            <h1 class="coach-title text-center">Votre Coach</h1>
            <div class="background-box"></div>
            <div class="coach-avatar">
                <img v-if="candidateById.user.coach.profilePicture" :src="getImageUrl(candidateById.user.coach.profilePicture.id)" />
            </div>
            <div class="action-wrapper">
                <div class="action-container">
                    <div class="font-weight-bold mb-2 text-center">{{ candidateById.user.coach.firstName }} {{ candidateById.user.coach.lastName }}</div>
                    <!--<div v-if="candidateById.user.programsByProgramsUsers.length">Prestation : {{candidateById.user.programsByProgramsUsers[0].name}}</div>
                    <div class="title" >Séance effectué :</div>
                    <div>{{ candidateById.completedEventsCount }}/{{ candidateById.eventsCount }} séances</div>
                    <div class="title" >Date prestation :</div>
                    <div v-if="candidateById.user.programsByProgramsUsers.length">{{ dateFormatter(candidateById.user.programsByProgramsUsers[0].dateStart) }} au {{ dateFormatter(candidateById.user.programsByProgramsUsers[0].dateEnd)}}
                    </div>
                    <div>Prochain événement : {{ candidateById.nextEvent.name }}</div>-->
                    <div class="px-2 py-2 mt-2 font-size-16">
                        <div>
                            <img src="@/assets/img/phone-primary.svg" class="mr-3" />
                            <span>{{ candidateById.user.coach.phone }}</span>
                        </div>
                        <div class="mt-2">
                            <img src="@/assets/img/mail-primary.svg" class="mr-3" />
                            <span><a :href="'mailto:' + candidateById.user.coach.email">{{ candidateById.user.coach.email }}</a></span>
                        </div>
                        <div class="mt-2">
                            <img src="@/assets/img/home-primary.svg" class="mr-3" />
                            <span>{{ candidateById.user.coach.address + ", " + candidateById.user.coach.userCodePostal + " " + candidateById.user.coach.userCity}}</span>
                        </div>

                        <div v-if="candidateById.user.coach.linkedin" class="mt-2">
                            <img src="@/assets/img/icon-linkedin-primary.svg" class="mr-3" />
                            <span><a :href=" candidateById.user.coach.linkedin" target="_blank">LinkedIn</a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

      <circle-spin v-if="(allDocuments.items && allDocuments.items.length == 0) || events.length == 0"></circle-spin>
        <h1 class="text-center mb-4 mt-4" v-if="!loading && candidateById.user.hasBeenTransferred">
          Suite à votre demande, vous avez été transféré sur la spécialité <br> {{ transferSpeciality }}
        </h1>
        <div class="your-progam col-12 col-md-8" v-if="!loading && candidateById.user.programsByProgramsUsers.length && candidateById.user.appointmentBooked">
            <div class="your-program-title text-center">Votre Prestation</div>
            <h1 class="program-title text-center"><img src="@/assets/img/prestation-home.svg" />{{candidateById.user.programsByProgramsUsers[0].name}}</h1>
            <div class="content">
                <div class="row mb-5">
                    <div class="col-12">
                        <div class="label">Date prestation</div>
                        <div class="info" v-if="candidateById.user.programsByProgramsUsers.length">
                            <img src="@/assets/img/cal-home.svg" alt /> du {{ dateFormatter(candidateById.user.programsByProgramsUsers[0].dateStart) }} au {{ dateFormatter(candidateById.user.programsByProgramsUsers[0].dateEnd)}}
                        </div>
                    </div>
                </div>
                <div class="row mb-5">
                    <div class="col-12">
                        <div class="label">Séances complétées</div>
                        <div class="info"><img src="@/assets/img/coaching-home.svg" alt />{{ candidateById.completedEventsCount }}/{{ candidateById.eventsCount }} séances</div>
                    </div>
                </div>
                <div class="row" v-if="nextEvent">
                    <div class="col-12">
                        <div class="label">Prochaine séance</div>
                        <div class="info">
                            <img class="icon-event" src="@/assets/img/date-home.svg" alt />
                            <div class="info-event">
                                {{ nextEvent.name }}
                                <span v-if="nextEvent.meetingPlace === 'presential'" class="page-coach-tag style-one">Présentielle</span>
                                <span class="page-coach-tag style-one" v-if="nextEvent.meetingPlace === 'visio'">Visio</span>
                                <div>
                                    <span class="date-event">
                                        {{dateFormatterEvent(nextEvent.dateEvent)}} de {{hourFormatter(nextEvent.dateEvent)}} à {{hourFormatter(nextEvent.dateEventEnd)}}
                                    </span>
                                    <span v-if="nextEvent.meetingPlace === 'presential'" class="page-coach-tag style-two">{{ nextEvent.meetingRoom }}</span>
                                    <span v-if="nextEvent.meetingPlace === 'visio'" class="event-start-visio"
                                          @click="openEventLink(nextEvent)">
                                        Démarrer la visio
                                    </span>
                                </div>
                                <div v-if="nextEvent.picEvent">
                                    <button class="d-none d-md-block btn btn-gradient-primary btn-sm btn-no-shadow" @click="$event => confirmCancelAppointment(nextEvent)">
                                        Annuler
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h1 class="text-center mb-4 mt-4" v-if="!loading && workshopEvents.length">
            Inscrivez-vous à une Réunion d'Information Collective
        </h1>
        <div class="program-pics row mb-5" v-if="!loading && workshopEvents.length">
            <div class="coache-item col-12 col-sm-6 col-lg-4 col-xl-3" v-for="event in workshopEvents" :key="event.id">
                <coach-and-workshop-card :key="event.id"
                                         title="Espace Information Projet"
                                         :coach="event.organizer"
                                         @join="$event => joinEvent(event)"
                                         :event="event" />
            </div>
        </div>

        <div class="col-md-12 mb-2 mt-4" v-if="!loading && canBookAppointment">
            <h1 class="text-center mb-4" v-if="!candidateById.user.hasBeenTransferred">
                Prenez le 1er rendez-vous disponible en fonction de votre projet
            </h1>
            <h1 class="text-center mb-4" v-else>
              Prenez rendez-vous avec l'un de nos consultants spécialistes
            </h1>
        </div>
        <div class="col-md-12 mb-4 mt-4 text-center" v-if="!loading && canBookAppointment && !candidateById.user.appointmentBooked && !candidateById.user.hasBeenTransferred">
            <template v-for="coachSpeciality in coachSpecialityList">
                <button :key="coachSpeciality.value"
                        v-if="hasSpeciality(coachSpeciality)"
                        class="btn ml-2"
                        @click="() => changeCoachSpecialityFilter(coachSpeciality.value)"
                        :class="[(coachSpeciality.value === filterCoachSpeciality ? 'btn-' : 'btn-outline-') + coachSpecialityClassNames[coachSpeciality.value]]">
                    {{ coachSpeciality.label }}
                </button>
            </template>
        </div>
        <div class="col-md-10 offset-md-1 bg-white border-top pt-4 border-primary pb-4 mb-4"
             v-if="!loading && canBookAppointment">
            <FullCalendar defaultView="dayGridMonth"
                          theme-system="bootstrap"
                          @eventClick="calendarEventClick"
                          slot-duration="01:00"
                          :header="{
                            left: 'today prev,next',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek'
                          }"
                          :datesRender="$event => handleCalendarDateClick($event)"
                          :locale="calendarLocale"
                          :events="calendarEvents"
                          :event-render="renderEvent"
                          :plugins="calendarPlugins" />
        </div>

        <h1 class="text-center mb-4" v-if="!loading && individualSessionEvents.length && canBookAppointment">
            Prenez rendez-vous avec le consultant de votre choix
        </h1>
        <div class="program-pics row mt-4 mb-4" v-if="!loading && individualSessionEvents.length && canBookAppointment">
            <div class="coache-item col-12 col-sm-6 col-lg-4 col-xl-3" v-for="coach in individualSessionCoaches" :key="coach.id">
                <coach-and-workshop-card :key="coach.id"
                                         :title="coach.userCity ? coach.userCity : 'Espace Information Projet'"
                                         @select-event="$event => calendarEventClick($event)"
                                         :coach="coach" />
            </div>
        </div>

        <div v-if="!loading && documents.length" class="col-lg-10 candidate-home-articles">
            <h2 class="text-center mb-4">
                Derniers articles
            </h2>
            <el-carousel :interval="5000" type="card" height="400px">
                <el-carousel-item v-for="document in documents" class="article-card" :key="document.id" @click="openDocument(document)">
                    <a target="_blank" :href="document.articleLink" :class="{'article-img-overlay d-flex flex-column text-white': document.fileDescriptor, 'card-body': !document.fileDescriptor}">
                        <div class="card-img-container" v-if="document.fileDescriptor">
                            <img class="articles-img" :src="getImageUrl(document.fileDescriptor.id)" alt="Image">
                            <div class="article-tags-container mt-auto">
                                <div class="article-tag mr-2 mb-2" v-for="(item, index) in getDocumentTags(document)" :key="index">{{item}}</div>
                            </div>
                        </div>

                        <div class="article-body">
                            <h2 class="article-title">{{ document.name }}</h2>
                            <p class="article-text">{{ document.fileDescriptor ? shortenDescription(document.description) : document.description }}</p>
                        </div>
                    </a>
                </el-carousel-item>
            </el-carousel>
        </div>

        <div v-if="isProgramPic" class="container-faq-button">
            <button
                    @click="$router.push({name: 'CandidateFaq'})"
                    class="btn btn-gradient-primary"
                    type="button">
                Questions les plus fréquentes
            </button>
        </div>

        <circle-spin v-show="allDocuments.items && allDocuments.items.length == 0"></circle-spin>
        <div class="footer-candidate-page">
            <div class="container_social row col-md-4 offset-md-4 justify-content-center mt-4">
                <a class="icon_link" :href="oasysLink" target="_blank">
                    <img src="@/assets/img/icon-site.svg" alt="">
                </a>
                <a class="icon_link" :href="linkedInLink" target="_blank">
                    <img src="@/assets/img/icon-linkedin.svg" alt="">
                </a>
                <a class="icon_link" :href="twitterLink" target="_blank">
                    <img src="@/assets/img/twitter-gray.svg" alt="">
                </a>
                <a class="icon_link" :href="infoLink" target="_blank">
                    <img src="@/assets/img/info.svg" alt="">
                </a>
            </div>
            <div class="info">
                Oasys Consultants
                <div class="info-italic">
                    Siège Social : 10 rue Cambacérès – 75008 Paris (France)<br>
                    Capital de 251 020 euros - RCS : 489 808 485<br>
                    TVA intracommunautaire : FR15489808485
                </div>
            </div>
        </div>

        <div class="col-md-12 mb-2 mt-4">
            <h2 class="text-center">
                Un problème technique ?
            </h2>
        </div>
        <div class="container-faq-button">
            <button
                    @click="$router.push({name: 'CandidateContactUs'})"
                    class="btn btn-gradient-primary"
                    type="button">
                Contactez-nous
            </button>
        </div>

        <confirm-dialog v-if="confirmAppointmentModal.show"
                        title="Voulez-vous prendre ce rendez-vous?"
                        :description="confirmAppointmentModal.description"
                        @close="closeBookAppointmentModal"
                        @confirm="confirmAppointment" />
        <confirm-dialog v-if="confirmAppointmentCancelModal.show"
                        title="Voulez-vous annuler ce rendez-vous?"
                        :description="confirmAppointmentCancelModal.description"
                        @close="closeCancelAppointmentModal"
                        @confirm="removeEvent" />
    </dash-wrap>
</template>
<script>
    import { PerfectScrollbar } from "vue2-perfect-scrollbar";
    import {FILE_PATH} from "@/enum/FilePathConstant";
    import {INFO_PDF_FILE_PATH} from "@/enum/FilePathConstant";
    import CoachAndWorkshopCard from "@/components/coache/CoachAndWorkshipCard";
    import izitoast from "izitoast";
    import localMoment from "@/utils/localMoment";
    import ConfirmDialog from "@/components/utils/ConfirmDialog";

    import {BOOK_APPOINTMENT_BY_CANDIDATE} from "@/graphql/event/book-appointment-by-candidate-mutation";
    import {CANDIDATE_FOR_DASHBOARD} from "@/graphql/user/candidate-for-dashboard-query";
    import { LOGGED_USER } from "@/graphql/security/logged-user-query";
    import {ALL_DOCUMENTS} from "@/graphql/document/all-documents-query";
    import {USER_BY_ID} from "@/graphql/user/user-by-id-query";
    import {UNSUBSCRIBE_CANDIDATE_FROM_BOOKED_APPOINTMENT} from "@/graphql/event/unsubscribe-candidate-from-booked-appointment-mutation";

    import FullCalendar from '@fullcalendar/vue'
    import dayGridPlugin from '@fullcalendar/daygrid';
    import timeGridPlugin from '@fullcalendar/timegrid';
    import frLocale from '@fullcalendar/core/locales/fr';
    import bootstrapPlugin from '@fullcalendar/bootstrap';
    import tippy from 'tippy.js';
    import '@fullcalendar/core/main.css';
    import '@fullcalendar/daygrid/main.css';
    import '@fullcalendar/timegrid/main.css';
    import '@fullcalendar/bootstrap/main.css';
    import 'tippy.js/dist/tippy.css';
    import 'tippy.js/themes/material.css';
    import {
      AGE_MEASUREMENTS,
      BUSINESS_CREATION,
      COACH_SPECIALITY_LIST,
      EMPLOYMENT_TRAINING, GENERALIST
    } from "@/enum/coachSpecialityEnum";
    import {CANDIDATE_HOME} from "@/graphql/user/candidate-home-query";
    import {CANDIDATE_HOME_EVENTS} from "@/graphql/user/candidate-home-events-query";

    export default {
        name: "CandidateHome",
        components: {
            PerfectScrollbar,
            CoachAndWorkshopCard,
            FullCalendar,
            ConfirmDialog
        },
        data: () => ({
            me: {},
            candidateById: {
                user: {
                    programsByProgramsUsers: [],
                    eventsWithoutProgram: [],
                    company: {},
                },
            },
            loading: false,
            allDocuments: {
                items: [],
                count: 0,
            },
            calendarPlugins: [dayGridPlugin, timeGridPlugin, bootstrapPlugin],
            calendarLocale: frLocale,
            confirmAppointmentModal: {
                show: false,
                description: '',
                event: null,
            },
            confirmAppointmentCancelModal: {
                show: false,
                description: '',
                event: null,
            },
            filterCoachSpeciality: '',
            coachSpecialityClassNames: {
              [EMPLOYMENT_TRAINING]: 'success',
              [BUSINESS_CREATION]: 'primary',
              [AGE_MEASUREMENTS]: 'secondary',
              [GENERALIST]: 'generalist',
            },
            events: [],
            loadingProgramEvents: false,
            currentDate: null,
            appointmentTimeLimit: 0,
        }),
        computed: {
            oasysLink () {
                return process.env.VUE_APP_SITE_OASYS_LINK;
            },
            twitterLink () {
                return process.env.VUE_APP_TWITTER_LINK;
            },
            linkedInLink () {
                return process.env.VUE_APP_LINKEDIN_LINK;
            },
            infoLink () {
                let baseUrl = process.env.VUE_APP_GRAPHQL_HTTP.substr(
                        0,
                        process.env.VUE_APP_GRAPHQL_HTTP.lastIndexOf("/")
                );
                return baseUrl + INFO_PDF_FILE_PATH;
            },
            transferSpeciality() {
              return this.coachSpecialityList.find(speciality => speciality.value === this.candidateById.user.coachSpeciality.id).label;
            },
            workshopEvents () {
                return this.events.filter(
                    event => event.type === 'workshop' && (event.status === 'upcoming' || event.status === 'created')
                );
            },
            individualSessionEvents() {
                if (!this.events.length || !this.candidateById.user.id) {
                    return [];
                }

                return this.events.filter(
                    event => event.type === 'individual_session'
                ).filter(event =>
                    !this.candidateById.user.appointmentBooked
                    || this.candidateById.user.hasBeenTransferred
                    || (this.candidateById.user.coach && event.organizer.id === this.candidateById.user.coach.id)
                    || event.isAttending
                ).filter(event =>
                    event.isAttending
                    || !this.appointmentTimeLimit
                    || localMoment(event.dateEvent).isAfter(localMoment().add(this.appointmentTimeLimit, 'hours'))
                )
            },
            attendingOrCreatedEvents () {
              return this.individualSessionEvents
                  .filter(event => event.status === 'created' || event.isAttending);
            },
            coachSpecialityFilteredEvents () {
              return this.attendingOrCreatedEvents
                  .filter(event =>
                      event.isAttending
                      || !this.candidateById.user.hasBeenTransferred
                      || (event.organizer.coachSpeciality && event.organizer.coachSpeciality.id === this.candidateById.user.coachSpeciality.id)
                  ).filter(event =>
                      !this.filterCoachSpeciality
                      || (event.organizer.coachSpeciality && this.filterCoachSpeciality === event.organizer.coachSpeciality.id)
                  );
            },
            individualSessionCoaches() {
                return Object.values(this.individualSessionEvents.reduce(
                    (coaches, event) => ({
                        ...coaches,
                        [event.organizer.id]: {
                            ...event.organizer,
                            events: [
                                ...(typeof coaches[event.organizer.id] !== 'undefined' ? coaches[event.organizer.id].events : []),
                                ...(event.isAttending ? [] : [event]),
                            ],
                        },
                    }), {}
                )).map(coach => ({
                    ...coach,
                    events: coach.events.filter(
                        event =>
                            event.status === 'created'
                            && localMoment(event.dateEvent).isAfter(localMoment())
                            && (!this.filterCoachSpeciality || this.filterCoachSpeciality === event.organizer.coachSpeciality.id)
                            && (!this.candidateById.user.hasBeenTransferred || this.candidateById.user.coachSpeciality.id === event.organizer.coachSpeciality.id)
                    )
                })).filter(coach => coach.events.length > 0);
            },
            calendarEvents () {
                return this.coachSpecialityFilteredEvents
                        .map(event => {
                            return {
                                id: event.id,
                                title: event.name,
                                start: localMoment(event.dateEvent).format('YYYY-MM-DD HH:mm'),
                                end: localMoment(event.dateEventEnd).format('YYYY-MM-DD HH:mm'),
                                classNames: [
                                    event.isAttending ? 'bg-primary text-white' : 'bg-light',
                                    event.isAttending ? 'border-primary' : 'border-' + (!event.organizer.coachSpeciality ? '' : this.coachSpecialityClassNames[event.organizer.coachSpeciality.id]),
                                    !event.organizer.coachSpeciality ? '' : 'text-' + this.coachSpecialityClassNames[event.organizer.coachSpeciality.id],
                                ],
                            };
                        });
            },
            nextEvent() {
                if (!this.candidateById.nextEvent) {
                    return null;
                }

                let event = {...this.candidateById.nextEvent};
                if (this.individualSessionEvents.map(e => e.id).includes(event.id)) {
                    event.picEvent = true;
                }
                return event;
            },
            isProgramPic() {
                return this.candidateById && this.candidateById.user.programsByProgramsUsers.find(program => program.type === 'pic');
            },
            canBookAgain() {
                return this.me.rights.includes('ROLE_BOOK_APPOINTMENT');
            },
            canBookAppointment() {
                return this.activePrograms.length && (!this.candidateById.user.appointmentBooked || this.canBookAgain);
            },
            programPicDocuments() {
                return this.candidateById.user.programsByProgramsUsers
                    .find(program => program.type === 'pic')
                    .documents
                    .filter(document => document.toBeDisplayedInHomePage && document.type === 'article');
            },
            documents() {
                return this.isProgramPic ? this.programPicDocuments : this.allDocuments.items.filter(document => document.programs.length === 0);
            },
            coachSpecialityList() {
                return COACH_SPECIALITY_LIST;
            },
            activePrograms() {
                return this.candidateById.user.programsByProgramsUsers.filter(
                    program => program.status !== "finished"
                );
            }
        },
        apollo: {
            me: {
                query: LOGGED_USER,
            },
            candidateById: {
                query: CANDIDATE_HOME,
                variables() {
                    return {
                        id: this.me.id,
                    };
                },
            },
            allDocuments: {
                query: ALL_DOCUMENTS,
                variables: {
                    limit: 4,
                    type: 'article',
                    displayedInHomePage: true,
                    sortColumn: 'createdAt',
                    sortDirection: 'desc',
                }
            }
        },
        methods: {
            dateFormatter (date) {
                return localMoment(date).format('DD/MM/YYYY');
            },
            dateFormatterEvent (date) {
                return localMoment(date).format('dddd DD MMM YYYY');
            },
            hourFormatter(date) {
                return localMoment(date).format('HH:mm');
            },
            getDocumentTags(document) {
              return document.tags.split(",")[0]  ? document.tags.split(",") : []
            },
            openEventLink (event) {
                window.open(event.teamsLink, "_blank");
            },
            getImageUrl(fileDescriptorId) {
                let baseUrl = process.env.VUE_APP_GRAPHQL_HTTP.substr(
                    0,
                    process.env.VUE_APP_GRAPHQL_HTTP.lastIndexOf("/")
                );
                return baseUrl + FILE_PATH + fileDescriptorId;
            },
            shortenDescription(description) {
                if (description.length > 100) {
                    description = description.substr(0, 80);
                    description = description.substr(0, description.lastIndexOf(' ')) + '...';
                }
                return description;
            },
            openDocument(document) {
                window.open(document.articleLink, "_blank");
            },
            joinEvent(event) {
                this.loading = true;
                this.$apollo.mutate({
                    mutation: BOOK_APPOINTMENT_BY_CANDIDATE,
                    variables: {
                        eventId: event.id,
                        userId: this.me.id,
                    },
                }).then(() => {
                    this.$apollo.queries.candidateById.refetch().then(() => {
                        this.loading = false;
                    });
                    izitoast.success({
                        position: "topRight",
                        title: "Succès",
                        message: "Vous vous êtes inscrit avec succès"
                    });
                });
            },
            calendarEventClick(event) {
                event = this.individualSessionEvents.find(e => e.id === (event.event ? event.event.id : event));
                if (event.isAttending) {
                    return;
                }

                this.confirmAppointmentModal.show = true;
                this.confirmAppointmentModal.event = event;
                this.confirmAppointmentModal.description =
                    'Coach : ' + event.organizer.firstName + ' ' + event.organizer.lastName.toUpperCase() + '<br />'
                    + 'Date et heure : ' + localMoment(event.dateEvent).format('DD MMMM YYYY [de] HH:mm')
                    + ' à ' + localMoment(event.dateEventEnd).format('HH:mm');
            },
            closeBookAppointmentModal() {
                this.confirmAppointmentModal.show = false;
            },
            confirmAppointment() {
                this.closeBookAppointmentModal();
                this.loading = true;
                this.$apollo.mutate({
                    mutation: BOOK_APPOINTMENT_BY_CANDIDATE,
                    variables: {
                        userId: this.me.id,
                        eventId: this.confirmAppointmentModal.event.id,
                    }
                }).then(() => {
                    this.$apollo.queries.candidateById.refetch().then(() => {
                        this.loading = false;
                    });
                    izitoast.success({
                        position: "topRight",
                        title: "Succès",
                        message: "Votre rendez-vous a été enregistré"
                    });
                }).catch(() => {
                    this.loading = false;

                    this.confirmAppointmentModal.event.status = 'upcoming';
                    izitoast.error({
                        position: "topRight",
                        title: "Erreur",
                        message: "Le rendez-vous n'est plus disponible"
                    });
                });
            },
            removeEvent() {
                this.closeCancelAppointmentModal();
                this.loading = true;
                let event = this.confirmAppointmentCancelModal.event;
                this.$apollo.mutate({
                    mutation: UNSUBSCRIBE_CANDIDATE_FROM_BOOKED_APPOINTMENT,
                    variables: {
                        userId: this.me.id,
                        eventId: event.id,
                    }
                }).then(() => {
                    this.$apollo.queries.candidateById.refetch().then(() => {
                        this.loading = false;
                    });
                    izitoast.success({
                        position: "topRight",
                        title: "Succès",
                        message: "Votre rendez-vous a été annulé"
                    });
                });
            },
            renderEvent(event) {
                let eventModel = this.individualSessionEvents.find(e => e.id === event.event.id);
                tippy(event.el, {
                    content: eventModel.organizer.firstName + ' ' + eventModel.organizer.lastName.toUpperCase() + '<br />' + eventModel.organizer.function,
                    allowHTML: true,
                    theme: "material"
                });
            },
            confirmCancelAppointment(event) {
                this.confirmAppointmentCancelModal.show = true;
                this.confirmAppointmentCancelModal.event = event;
                this.confirmAppointmentCancelModal.description = 'Etes-vous sûr de vouloir annuler ce rendez-vous ?';
            },
            closeCancelAppointmentModal() {
                this.confirmAppointmentCancelModal.show = false;
            },
            changeCoachSpecialityFilter(coachSpeciality) {
                this.filterCoachSpeciality = coachSpeciality === this.filterCoachSpeciality ? '' : coachSpeciality;
            },
            handleCalendarDateClick (event) {
                this.fetchEvents(localMoment(event.view.activeStart).startOf('day'), localMoment(event.view.activeEnd).endOf('day'));
            },
            fetchEvents (startDate, endDate) {
                if (startDate.isBefore(localMoment())) {
                    startDate = localMoment().startOf('day');
                }

                this.$apollo.query({
                    query: CANDIDATE_HOME_EVENTS,
                    variables: {
                        id: this.me.id,
                        programId: this.activePrograms[0].id,
                        dateStart: startDate.format('YYYY-MM-DD HH:mm:ss'),
                        dateEnd: endDate.format('YYYY-MM-DD HH:mm:ss'),
                    },
                    watchLoading (isLoading) {
                        this.loadingProgramEvents = isLoading;
                    },
                }).then(response => {
                    if (response.data.programById.eventsOrderedByDate.items.length) {
                        let eventIds = this.events.map(e => e.id);
                        this.events = this.events.concat(
                            response.data.programById.eventsOrderedByDate.items
                            .filter(event => !eventIds.includes(event.id))
                        );
                        this.appointmentTimeLimit = response.data.programById.appointmentTimeLimit;
                    }
                });
            },
            hasSpeciality (speciality) {
                let found = false;
                this.individualSessionEvents.forEach(event => {
                   if (event.organizer.coachSpeciality.id === speciality.value) {
                      found = true;
                   }
                });

                return found;
            }
        },
        created() {
            let getClosest = function (elem, selector) {
                for ( ; elem && elem !== document; elem = elem.parentNode ) {
                    if ( elem.matches( selector ) ) return elem;
                }
                return null;
            };

            window.addEventListener('wheel', function(e) {
                let item = document.querySelector("#scrollArticles");
                if(getClosest(event.target, '#scrollArticles') && e.deltaX === 0) {
                    if (e.deltaY > 0) item.scrollLeft += 100;
                    else item.scrollLeft -= 100;

                    e.preventDefault();
                }
            });
        }
    };
</script>