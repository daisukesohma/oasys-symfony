<template>
    <dash-wrap active="admin" :hide-tabs="true">
        <page-title
            :pre-title="programModel ? 'PRESTATION MODELE / MODIFIER UN MODELE' : (isProgramForm ? 'PRESTATION / MODIFIER UNE PRESTATION' : '')"
            :title="(isModify ? 'Modifier un Événement' : 'Créer un Événement') + (programModel ? ' Modele' : '')"
            @back="$router.push($route.query.back ? $route.query.back : {name: 'EventsList'})" />
        <div class="container">
            <div class="content">
                <div class="row" v-if="loading">
                    <div class="col-md-11">Loading...</div>
                </div>
                <template v-if="!loading">
                    <div class="col-md-12 mb-4" v-if="isProgramForm || (event.program && event.program.id)">
                        <div class="float-left mr-3">
                            <img :src="require(`@/assets/img/prestation-gray.svg`)" width="40" height="40" >
                            <span class="Bien-tre-au-travail">
                                {{event.program ? event.program.name : program.name}}
                            </span>
                        </div>
                        <div class="ml-3">
                            <img :src="require(`@/assets/img/cal.svg`)" width="40" height="40" >
                            <span class="Bien-tre-au-travail">
                                du {{event.program ? dateFormatter(event.program.dateStart) : dateFormatter(program.dateStart)}} au {{event.program ? dateFormatter(event.program.dateEnd) : dateFormatter(program.dateEnd)}}
                            </span>
                        </div>
                    </div>
                    <div class="form-row" v-if="isModify">
                        <div class="col-md-12 text-right last-modified-at form-group mb-0">
                            <label>Derniére modificaton</label> : 
                            <strong>
                                <router-link class="text-dark" :to="{name: 'UsersForm', params: {id: event.updatedBy !== null ? event.updatedBy.id : event.createdBy.id}}" target="_blank">
                                    {{event.updatedBy ? event.updatedBy.firstName : event.createdBy.firstName}}
                                </router-link> 
                                le {{ lastModifiedAt }}
                            </strong>
                        </div>
                    </div>
                    <form>
                        <fieldset>
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="form-row">
                                        <div class="col-md-5 form-group radio" v-if="isAdmin">
                                            <label>Est-ce que cet événement est un modèle ?</label>
                                            <div class="radio-items">
                                                <div class="radio-item">
                                                    <input class="form-check-input" type="radio" :value="true" v-model="event.isModel" id="model_yes" :disabled="isModify || !canEdit || isProgramForm" />
                                                    <label class="form-check-label" for="model_yes">Oui</label>
                                                </div>
                                                <div class="radio-item">
                                                    <input class="form-check-input" type="radio" :value="false" v-model="event.isModel" id="model_no" :disabled="isModify || !canEdit || isProgramForm" />
                                                    <label class="form-check-label" for="model_no">Non</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5 form-group select" :class="{'offset-md-1': isAdmin}" v-if="!event.isModel && event.eventModel">
                                            <label for="model">Quel modéle souhaitez-vous utiliser ?</label>
                                            <select id="model" class="form-control" v-model="event.eventModelId" @change="updateModel" :disabled="!canEdit">
                                                <option value="">Sélectionnez un modèle d'événement</option>
                                                <option v-for="model in eventModels" :key="model.id" :value="model.id">
                                                    {{ model.name }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-5 form-group select" :class="{'offset-md-1': (isAdmin && !(!event.isModel && event.eventModel)) || (!isAdmin && !event.isModel && event.eventModel)}">
                                            <label for="type">Type d'événement*</label>
                                            <select id="type" name="type" class="form-control"
                                                    :disabled="!canEdit"
                                                    v-model="event.type"
                                                    @change="eventTypeChange"
                                                    v-validate="'required'"
                                                    :class="{'is-invalid': errors.has('type')}">
                                                <option v-for="type in eventTypes" :key="type.id" :value="type.value">
                                                    {{ type.label }}
                                                </option>
                                            </select>
                                            <div v-if="errors.has('type')" class="invalid-feedback">
                                                Le champ type est obligatoire.
                                            </div>
                                        </div>
                                        <div class="col-md-5 form-group" :class="{'offset-md-1': (isAdmin && !event.isModel && event.eventModel) || (!isAdmin && !(!event.isModel && event.eventModel))}" v-if="!isProgramForm && !event.isModel">
                                            <label>Prestation</label>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <program-autocomplete v-model="event.programId"
                                                                          @item-selected="updateProgram"
                                                                          :empty-query-after-selection="true"
                                                                          name="coach" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>Informations</legend>
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="form-row">
                                        <div class="col-md-5 form-group">
                                            <label for="name">Nom*</label>
                                            <input class="form-control" placeholder="Nom" id="name" name="name" v-validate="'required'"
                                                    v-model="event.name"
                                                    :disabled="!canEdit"
                                                    :class="{'is-invalid': errors.has('name')}" />
                                            <div v-if="errors.has('name')" class="invalid-feedback">
                                                Le champ nom est obligatoire.
                                            </div>
                                        </div>
                                        <div class="col-md-5 offset-md-1 form-group" v-if="!event.isModel">
                                            <label>Date évènement*</label>
                                            <div class="form-row">
                                                <div class="col-md-5">
                                                    <el-date-picker
                                                        name="dateEvent"
                                                        v-model="event.dateEvent"
                                                        v-validate="isAdmin ? 'required' : 'afterToday|required'"
                                                        type="datetime"
                                                        format="dd/MM/yyyy"
                                                        :disabled="!canEdit"
                                                    />
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="form-row">
                                                        <div class="col-md-6">
                                                            <el-time-select
                                                                placeholder="début"
                                                                name="hourStart"
                                                                v-model="event.timeRange[0]"
                                                                v-validate="'required'"
                                                                :clearable="false"
                                                                :disabled="!canEdit"
                                                                @change="checkHourStart"
                                                                :picker-options="{
                                                                  start: '08:00',
                                                                  step: '00:15',
                                                                  end: '23:59'
                                                                }" />
                                                        </div>
                                                        <div class="col-md-6">
                                                            <el-time-select
                                                                placeholder="fin"
                                                                name="hourEnd"
                                                                v-model="event.timeRange[1]"
                                                                v-validate="'required'"
                                                                :clearable="false"
                                                                :disabled="!canEdit"
                                                                :picker-options="{
                                                                  start: '08:00',
                                                                  step: '00:15',
                                                                  end: '23:59',
                                                                  minTime: event.timeRange[0]
                                                                }" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <small v-if="errors.has('dateEvent')" class="invalid-feedback">
                                                <span v-if="errors.items.find(e=>e.field==='dateEvent').rule==='required'">Le champ date est obligatoire.</span>
                                                <span v-else>La date d’événement ne peut pas être dans le passé.</span>
                                            </small>
                                            <small v-if="eventTimeError" class="invalid-feedback">
                                                Un autre événement existe déjà à l'heure donnée
                                            </small>
                                            <!--<small v-if="errors.has('hourStart') || errors.has('hourEnd')" class="invalid-feedback">
                                                <span> Veuillez remplir l'heure</span>
                                            </small>-->
                                        </div>
                                    </div>
                                    <div class="form-row" v-if="!event.isModel">
                                        <div class="col-md-5 form-group select">
                                            <label for="teamsLink">Modalité*</label>
                                            <select class="form-control" id="meetingPlace" name="meetingPlace"
                                                    v-model="event.meetingPlace"
                                                    :disabled="!canEdit"
                                                    v-validate="'required'"
                                                    :class="{'is-invalid': errors.has('meetingPlace')}">
                                                <option value="presential">Présentielle</option>
                                                <option value="visio">Visio</option>
                                            </select>
                                            <div v-if="errors.has('meetingPlace')" class="invalid-feedback">
                                                Le champ présentielle est obligatoire.
                                            </div>
                                        </div>
                                        <div class="col-md-5 offset-md-1 form-group" v-if="event.meetingPlace === 'visio'">
                                            <label for="teamsLink">Lien Teams*</label>
                                            <input class="form-control" placeholder="Lien Teams" id="teamsLink" name="teamsLink"
                                                   v-validate="'required'"
                                                   v-model="event.teamsLink"
                                                   :disabled="!canEdit"
                                                   :class="{'is-invalid': errors.has('teamsLink')}" />
                                            <div v-if="errors.has('teamsLink')" class="invalid-feedback">
                                                Le champ lien teams est obligatoire.
                                            </div>
                                        </div>
                                        <div class="col-md-5 offset-md-1 form-group" v-if="event.meetingPlace === 'presential'">
                                            <label for="teamsLink">Salle*</label>
                                            <input class="form-control" placeholder="Salle" id="meetingRoom" name="meetingRoom"
                                                   v-model="event.meetingRoom"
                                                   v-validate="'required'"
                                                   :disabled="!canEdit"
                                                   :class="{'is-invalid': errors.has('meetingRoom')}" />
                                            <div v-if="errors.has('meetingRoom')" class="invalid-feedback">
                                                Le champ salle est obligatoire.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row" v-if="!event.isModel && (event.type === 'tripartite_meeting' || event.meetingPlace === 'presential')">
                                        <div class="col-md-5 form-group" v-if="event.type === 'tripartite_meeting'">
                                            <label for="teamsLink">Enquête d'évaluation</label>
                                            <input class="form-control" placeholder="Enquête d'évaluation" id="evaluationSurvey" name="evaluationSurvey"
                                                   v-model="event.evaluationSurvey"
                                                   :disabled="!canEdit"
                                                   :class="{'is-invalid': errors.has('evaluationSurvey')}" />
                                            <div v-if="errors.has('evaluationSurvey')" class="invalid-feedback">
                                                Le champ enquête d'évaluation teams est obligatoire.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row" v-if="!event.isModel && event.type === 'workshop'">
                                        <div class="col-md-5 form-group">
                                            <label for="numberMaxInvites">Nombre maximum d'invités</label>
                                            <input class="form-control" placeholder="0" type="number" id="numberMaxInvites" name="numberMaxInvites"
                                                   v-model="event.numberMaxInvites"
                                                   v-validate="'required'"
                                                   :disabled="!canEdit"
                                                   :class="{'is-invalid': errors.has('numberMaxInvites')}" />
                                            <div v-if="errors.has('numberMaxInvites')" class="invalid-feedback">
                                                Le champ enquête dnNombre maximum d'invités est obligatoire.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-11 form-group">
                                            <label for="description">Description*</label>
                                            <textarea class="form-control" name="description" id="description"
                                                        rows="5"
                                                        v-validate="'required'"
                                                        v-model="event.description"
                                                        :required="true"
                                                        :disabled="!canEdit"
                                                        :class="{'is-invalid': errors.has('description')}" />
                                            <div v-if="errors.has('description')" class="invalid-feedback">
                                                Le champ Description est obligatoire.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row" v-if="!event.isModel">
                                        <div class="col-md-5 form-group">
                                            <label>Organisateur</label>
                                            <user-autocomplete v-if="isAdmin && canEdit"
                                                                v-model="organizerInput"
                                                                @item-selected="updateOrganizer"
                                                                :types="organiserQueryTypes"
                                                                :empty-query-after-selection="true"
                                                                name="coach" />
                                            <div>
                                                <single-user :user="event.organizer && event.organizer.id ? event.organizer : me"
                                                                :has-action="event.organizer && event.organizer.id ? true : false"
                                                                @click="removeOrganizer" />
                                            </div>
                                        </div>
                                        <div class="col-md-5 offset-md-1 form-group" v-if="isModify">
                                            <label for="createdAt">Date de création</label>
                                            <input class="form-control created-date" name="createdAt" id="createdAt" :value="dateFormatter(event.createdAt)" disabled />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset v-if="!event.isModel">
                            <legend>Invités</legend>
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="form-row" v-if="canEdit">
                                        <div class="col-md-5 form-group">
                                            <user-autocomplete v-model="inviteeInput"
                                                                @item-selected="addUser"
                                                                :class-list="{'is-invalid': invitesInvalid}"
                                                                :empty-query-after-selection="true"
                                                                :company-id="program && program.company ? program.company.id : ''"
                                                                :types="invitesUserTypes" />
                                        </div>
                                    </div>
                                    <div class="form-row" v-if="event.users">
                                        <div class="col-md-11">
                                            <single-user v-for="user in event.users" :key="user.id" :user="user" :has-action="true" @click="$event => removeUser(user)" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <div class="row mb-3" v-if="canEdit">
                            <div class="col-md-12 mt-1">
                                <div class="form-btn-wrap">
                                    <button type="button" class="btn btn-outline-consultant-light" @click="cancel">
                                        Annuler
                                    </button>
                                    <button type="button" class="btn btn-gradient-primary" @click="handleSubmit">
                                        Enregistrer
                                    </button>
                                </div>
                            </div>
                        </div>
                        <fieldset v-if="!event.isModel && isModify">
                            <legend>Documents</legend>
                            <div class="panel panel-default">
                                <div class="panel-header">
                                    <button class="btn btn-primary float-right" @click.prevent="openDocumentList">
                                        <i class="fa fa-eye" /> Consulter les documents
                                    </button>
                                    <button class="btn btn-primary float-right mr-4" @click.prevent="addNewDocument">
                                        <i class="fa fa-plus" /> Ajouter un document
                                    </button>
                                </div>
                            </div>
                        </fieldset>
                        <document-dialog v-if="$route.params.document==='document' || $route.params.mode==='document'" @close="closeDocumentDialog" @submit="submitDocumentDialog" />
                    </form>
                </template>
            </div>
        </div>
    </dash-wrap>
</template>
<script>
    import 'vue-phone-number-input/dist/vue-phone-number-input.css';
    import izitoast from 'izitoast';
    import moment from 'moment';
    import UserAutocomplete from '@/components/autocomplete/UserAutocomplete';
    import SingleUser from '@/components/utils/SingleUser';
    import DocumentDialog from '@/views/document/documentFormDialog';
    import localMoment from '@/utils/localMoment';
    import ProgramAutocomplete from "@/components/autocomplete/ProgramAutocomplete";
    import {EVENT_BY_ID} from "@/graphql/event/event-by-id-query";
    import {EVENT_MODEL_BY_ID} from "@/graphql/event/event-model-by-id-query";
    import {PROGRAM_BY_ID} from "@/graphql/program/program-by-id-query";
    import {ALL_EVENT_MODELS} from "@/graphql/event/all-event-models-query";
    import {LOGGED_USER} from "@/graphql/security/logged-user-query";
    import { EVENT_TYPES, INDIVIDUAL_SESSION } from "@/enum/EventTypesEnum";
    import {PROGRAM_MODEL_BY_ID} from "@/graphql/program/program-model-by-id-query";
    import {UPDATE_EVENT_MODEL} from "@/graphql/event/update-event-model-mutation";
    import {CREATE_EVENT_MODEL} from "@/graphql/event/create-event-model-mutation";
    import {UPDATE_EVENT} from "@/graphql/event/update-event-mutation";
    import {CREATE_EVENT} from "@/graphql/event/create-event-mutation";
    import {CANDIDATE_BY_ID} from "@/graphql/user/candidate-by-id-query";
    import {CANDIDATE, COACH} from "@/enum/userTypeEnum";

    export default {
        name: "Event",
        components : {
            UserAutocomplete,
            SingleUser,
            DocumentDialog,
            ProgramAutocomplete,
        },
        data() {
            return {
                INDIVIDUAL_SESSION: INDIVIDUAL_SESSION,
                me: {},
                event: {
                    name: '',
                    isModel: this.$route.name.startsWith('ProgramModel'),
                    dateEvent: '',
                    eventModelId: '',
                    eventModel: {
                        id: ''
                    },
                    description: '',
                    organizerId: '',
                    organizer: {},
                    programId: '',
                    program: {},
                    updatedBy: '',
                    createdBy: '',
                    createdAt: '',
                    users: [],
                    timeRange: ['', ''],
                    meetingPlace: '',
                    meetingRoom: '',
                    evaluationSurvey: '',
                    inviteeInput: '',
                    type: '',
                    teamsLink: '',
                },
                program: {
                },
                eventModels: [],
                inviteeInput: '',
                organizerInput: '',
                loading: 0,
                submitted: false,
                routeName: this.$route.name,
                routeMode: this.$route.params.mode,
                routeParams: this.$route.params,
                programModel: this.$route.name.startsWith('ProgramModel'),
                isProgramForm: this.$route.name.startsWith('Program'),
                organiserQueryTypes: [COACH],
                invitesUserTypes: [CANDIDATE, COACH],
                eventTimeError: false,
            };
        },
        computed: {
            isAdmin () {
                return this.me && this.me.type === 'admin';
            },
            previousPage() {
                if (this.$route.query.back) {
                    return this.$route.query.back;
                }
                if(this.isProgramForm) {
                    return {name: this.programModel ? 'ProgramModelForm' : 'ProgramForm', params: {id: this.$route.params.id}}
                } else {
                    return {name: 'EventsList'}
                }
            },
            lastModifiedAt () {
                if (!this.isModify) {
                    return '';
                }
                let date = this.event.updatedAt !== null ? this.event.updatedAt : this.event.createdAt;
                return localMoment(date).format('DD/MM/YYYY');
            },
            canEdit () {
                return this.$route.params.mode !== 'view';
            },
            isModify () {
                if (this.isProgramForm) {
                    return !!this.$route.params.eid;
                } else {
                    return !!this.$route.params.id;
                }
            },
            eventTypes() {
                return EVENT_TYPES;
            },
            invitesInvalid() {
                return this.submitted && this.event.type !== 'workshop' && this.event.type !== 'individual_session' && this.event.users.length === 0 && !this.event.isModel;
            }
        },
        apollo: {
            me: {
                query: LOGGED_USER,
            },
            event: {
                query()  {
                    return this.routeMode === 'model' ? EVENT_MODEL_BY_ID : EVENT_BY_ID;
                },
                variables() {
                    let id;
                    let variableName = this.routeMode === 'model' ? 'eventModelId' : 'eventId';
                    if(this.routeName === 'ProgramEventForm' || this.routeName === 'ProgramEventDocumentForm' || this.routeName === 'ProgramModelEventForm') {
                        id = this.routeParams.eid;
                    } else {
                        id = this.routeParams.id
                    }
                    return {
                       [variableName]: id,
                    }
                },
                update(data) {
                    let result = data.eventById || data.eventModelById;
                    result.isModel = this.routeMode === 'model';
                    if (result.isModel) {
                        result.users = [];
                    } else {
                        if (result.dateEvent) {
                            let dateEvent = localMoment(result.dateEvent);
                            let dateEventEnd = localMoment(result.dateEventEnd);

                            result.dateEvent = dateEvent.toDate();
                            result.dateEventEnd = dateEventEnd.toDate();
                            result.timeRange = [dateEvent.format('HH:mm'), dateEventEnd.format('HH:mm')];
                        } else {
                            result.dateEvent = '';
                            result.dateEventEnd = '';
                            result.timeRange = [];
                        }

                        result.eventModelId = result.eventModel ? result.eventModel.id : '';
                        result.programId = result.program ? result.program.id : '';
                    }
                    return result
                },
                skip() {
                    if (this.isProgramForm) {
                        return !this.routeParams.eid;
                    } else {
                        return !this.routeParams.id;
                    }
                },
                watchLoading (isLoading) {
                    this.loading = isLoading;
                },
            },
            eventModels: {
                query: ALL_EVENT_MODELS,
                update: data => data.allEventModels.items,
                watchLoading (isLoading) {
                    this.loading = isLoading;
                },
            },
            program: {
                query() {
                    return this.routeName.startsWith('ProgramModel') ? PROGRAM_MODEL_BY_ID : PROGRAM_BY_ID;
                },
                variables() {
                    let variableName = this.routeName.startsWith('ProgramModel') ? 'programModelId' : 'programId';
                    return {
                        [variableName]: this.routeParams.id
                    }
                },
                update: data => data.programModelById || data.programById,
                skip() {
                    return !this.routeName.startsWith('Program');
                },
            }
        },
        watch: {
            event: {
                deep: true,
                handler () {
                    if (this.submitted) {
                        this.$validator.validate();
                    }

                },
            }
        },
        methods: {
            handleSubmit (e) {
                this.$validator.resume();
                this.submitted = true;
                this.loading = true;
                this.eventTimeError = false;
                this.$validator.validate().then(async valid => {
                    if (!valid || this.invitesInvalid) {
                        izitoast.error({
                            position: 'topRight',
                            title: 'Erreur',
                            message: 'Veuillez vérifier le formulaire pour les erreurs',
                        });
                        this.loading = false;
                        return;
                    }

                    let mutation,
                        variables = {
                            ...this.event,
                        };
                    if (this.event.isModel) {
                        mutation = this.isModify ? UPDATE_EVENT_MODEL : CREATE_EVENT_MODEL;
                        if (this.isProgramForm) {
                            variables.programModelId = this.program.id;
                        }
                    } else {
                        mutation = this.isModify ? UPDATE_EVENT : CREATE_EVENT;
                        variables.userIds = this.event.users.map(user => user.id);
                        variables.organizerId = this.event.organizer && this.event.organizer.id ? this.event.organizer.id : this.me.id;
                        variables.programId = this.isProgramForm ? this.$route.params.id : (this.event.program ? this.event.program.id : '');

                        let dateEventFormatted = moment(this.event.dateEvent).format('YYYY-MM-DD') + ' ' + this.event.timeRange[0];
                        let dateEventEndFormatted = moment(this.event.dateEvent).format('YYYY-MM-DD') + ' ' + this.event.timeRange[1];
                        variables.dateEvent = moment(dateEventFormatted).format();
                        variables.dateEventEnd = moment(dateEventEndFormatted).format();

                    }

                    this.$apollo.mutate({
                        mutation: mutation,
                        variables: variables,
                        refetchQueries: [{
                            query: this.programModel ? PROGRAM_MODEL_BY_ID : PROGRAM_BY_ID,
                            variables: {
                                [this.programModel ? 'programModelId' :  'programId']: this.routeParams.id
                            },
                            skip() {
                                return !this.isProgramForm;
                            }
                        },{
                            query: ALL_EVENT_MODELS
                        }],
                    }).then(res => {
                        izitoast.success({
                            position: 'topRight',
                            title: 'Succès',
                            message: this.isModify ? 'L\'événement a été mis à jour avec succès' : 'L\'événement a été créé avec succès',
                        });
                        this.$router.push(this.$route.query.back ? this.$route.query.back : {name: 'EventsList'});
                    }, response => {
                        if (response.networkError.result.errors[0].extensions.category === 'event time invalid') {
                            this.loading = false;
                            izitoast.error({
                                position: 'topRight',
                                title: 'Erreur',
                                message: "Un autre événement " + response.networkError.result.errors[0].extensions.message + " existe déjà à l'heure donnée",
                            });
                            this.eventTimeError = true;
                        }
                        console.log(err);
                    });
                });
            },
            dateFormatter (date) {
                return localMoment(date).format('DD/MM/YYYY');
            },
            updateOrganizer (organizer) {
                this.event.organizer = organizer;
            },
            updateProgram (program) {
                this.event.program = program;
            },
            addUser (user) {
                if(this.event.type === INDIVIDUAL_SESSION && this.event.users.length >= 1) {
                    this.event.users = [user];
                } else if(!this.event.users.find(o => o.id === user.id)) {
                    this.event.users.push(user);
                }
            },
            removeUser (user) {
                this.event.users = this.event.users.filter(u => u.id !== user.id);
            },
            eventTypeChange() {
                if(this.event.type === INDIVIDUAL_SESSION && this.event.users.length >= 1) {
                    this.event.users = [this.event.users[0]];
                }
            },
            removeOrganizer () {
                this.event.organizer = {};
            },
            updateModel () {
                if (!this.event.isModel && this.event.eventModelId) {
                    let eventModel = this.eventModels.find(eventModel => eventModel.id === this.event.eventModelId);
                    this.event.name = eventModel.name;
                    this.event.description = eventModel.description;
                    this.event.type = eventModel.type;
                } else if (!this.event.isModel) {
                    this.event.name = '';
                    this.event.description = '';
                    this.event.type = '';
                }
            },
            cancel () {
                this.$router.push(this.previousPage);
            },
            addNewDocument () {
                if (this.isProgramForm) {
                    this.$router.push({name: 'ProgramEventDocumentForm', params: {id: this.$route.params.id, eid: this.$route.params.eid, event: 'event', document: 'document'}, query: this.$route.query})
                } else {
                    this.$router.push({name: 'EventDocumentForm', params: {id: this.$route.params.id, document: 'document' }, query: this.$route.query})
                }
            },
            closeDocumentDialog () {
                if (this.isProgramForm) {
                    this.$router.push({name: 'ProgramEventForm', params: {id: this.$route.params.id, eid: this.$route.params.eid, event: 'event'}, query: this.$route.query})
                } else {
                    this.$router.push({name: 'EventForm', params: {id: this.$route.params.id}, query: this.$route.query})
                }
            },
            async submitDocumentDialog () {
                await this.closeDocumentDialog();
                await this.$apollo.queries.event.refetch();
            },
            checkHourStart($event) {
                let startTime = moment($event, 'kk:mm'),
                    endTime = moment(this.event.timeRange[1], 'kk:mm');

                if (startTime.isSameOrAfter(endTime, 'minute')) {
                    this.event.timeRange[1] = startTime.add(2, 'hours').format('kk:mm');
                    this.$forceUpdate();
                }
            },
            openDocumentList () {
                window.open("/documents-list?eventId=" + this.event.id, "_blank");
            }
        },
        beforeCreate () {
            this.$validator.extend('afterToday', value => {
                return new Promise(resolve => {
                    if (!value) {
                        return false;
                    }

                    // todo: revert this change after testing
                    let today = moment(),
                        dateEventFormatted = moment(value).format('YYYY-MM-DD') + ' ' + this.event.timeRange[0];

                    resolve(moment(dateEventFormatted).isSameOrAfter(today, 'minute'));
                });
            });
        },
        mounted () {
            if (this.program.users && this.event.users.length === 0 && this.program.type !== 'pic') {
                this.event.users = this.program.users ? this.program.users.items : []
            }

            this.$validator.pause();
            if (this.program && this.program.isModel) {
                this.event.isModel = true;
            }
            if (this.$route.query.fromCandidate) {
                // candidateById is not required here, but if coach is coming from candidate view then this will be cached
                this.$apollo.addSmartQuery('candidateById', {
                    query: CANDIDATE_BY_ID,
                        variables () {
                        return {
                            id: this.$route.query.fromCandidate,
                        };
                    },
                    watchLoading (isLoading) {
                        this.loading = isLoading;
                    },
                    result (result) {
                        this.loading = typeof result.data === "undefined" && result.networkStatus === 1;
                        if (!this.loading) {
                            this.event.users.push(result.data.candidateById.user);
                        }
                    }
                });
            }
        },
    }
</script>
