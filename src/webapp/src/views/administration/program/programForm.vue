<template>
  <dash-wrap active="admin" :hide-tabs="true">
    <page-title
      pre-title="Prestation"
      :title="$route.params.id ? 'Modifier une prestation' : 'Créer une prestation'"
      @back="$router.push({name: 'ProgramsList'})" />
    <div class="col-12 col-md-10 offset-md-1">
      <div class="content">
        <div class="row" v-if="loading">
          <div class="col-md-11">Loading...</div>
        </div>
        <template v-else>
          <form>
            <div class="form-row" v-if="$route.params.id">
              <div class="col-md-12 text-right last-modified-at form-group mb-0">
                <label>Derniére modificaton</label> :
                <strong>
                  <router-link class="text-dark" :to="{name: 'UsersForm', params: {id: program.updatedBy !== null ? program.updatedBy.id : program.createdBy.id}}" target="_blank">
                    {{program.updatedBy ? program.updatedBy.firstName : program.createdBy.firstName}}
                  </router-link>
                  le {{ lastModifiedAt }}
                </strong>
              </div>
            </div>
            <fieldset>
              <div class="panel panel-default">
                <div class="panel-body">
                  <div class="form-row">
                    <div class="col-md-5 form-group radio" v-if="isAdmin">
                      <label>Est-ce que cette prestation est un modéle ?</label>
                      <div class="radio-items">
                        <div class="radio-item">
                          <input
                            class="form-check-input"
                            type="radio"
                            :value="true"
                            v-model="isModel"
                            id="model_yes"
                            :disabled="$route.params.id || !canEdit"
                          />
                          <label class="form-check-label" for="model_yes">Oui</label>
                        </div>
                        <div class="radio-item">
                          <input
                            class="form-check-input"
                            type="radio"
                            :value="false"
                            v-model="isModel"
                            id="model_no"
                            :disabled="$route.params.id || !canEdit"
                          />
                          <label class="form-check-label" for="model_no">Non</label>
                        </div>
                      </div>
                    </div>
                    <div
                      class="col-md-5 form-group select"
                      :class="{'offset-md-1': isAdmin}"
                      v-if="!isModel"
                    >
                      <label for="model">Quel modéle souhaitez-vous utiliser ?</label>
                      <select
                        id="model"
                        class="form-control"
                        v-model="modelIdInput"
                        @change="updateModel"
                        :disabled="!canEdit"
                      >
                        <option :value="null">Sélectionnez le modèle approprié à votre prestation</option>
                        <option
                          v-for="model in programModels"
                          :key="model.id"
                          :value="model.id"
                        >{{ model.name }}</option>
                      </select>
                    </div>
                    <div
                      class="col-md-5 form-group select"
                      :class="{'offset-md-1': !isAdmin}"
                      v-if="!isModel"
                    >
                      <label for="type">Type de prestation*</label>
                      <select
                        id="type"
                        name="type"
                        class="form-control"
                        :disabled="!canEdit"
                        v-model="program.type"
                        v-validate="'required'"
                        :class="{'is-invalid': errors.has('type')}"
                      >
                        <option value="individual">Coaching Individuel</option>
                        <option value="pic">e-PIC</option>
                        <option value="outplacement">Outplacement</option>
                      </select>
                      <div
                        v-if="errors.has('type')"
                        class="invalid-feedback"
                      >Le type est obligatoire.</div>
                    </div>
                    <div class="col-md-5 form-group" :class="{'offset-md-1': isAdmin}" v-if="!isModel">
                      <label>Entreprise*</label>

                      <div class="row">
                        <div class="col-md-12">
                          <company-autocomplete
                              :initial-query="program.company && program.company.id ? program.company.name : ''"
                              v-model="program.companyId"
                              v-validate="'required'"
                              name="company"
                              :class-list="{'is-invalid': errors.has('company')}"
                          />
                        </div>
                      </div>

                      <div v-if="errors.has('company')" class="invalid-feedback d-block">
                        <p>Le champ Entreprise est obligatoire</p>
                      </div>
                    </div>
                  </div>
                  <div class="form-row" v-if="!isModel && program.type === 'individual'">
                    <div class="col-md-5">
                      <div class="form-group">
                        <label>Nom du N+1*</label>
                          <input
                            v-model="program.lastName"
                            v-validate="'required'"
                            id="lastName"
                            name="lastName"
                            class="form-control"
                            :class="{ 'is-invalid': errors.has('lastName') }"
                            type="text"
                            placeholder="Nom..."
                          />
                          <div v-if="errors.has('lastName')" class="invalid-feedback">
                            <p>{{ " Le champ Nom est obligatoire " }}</p>
                          </div>
                      </div>
                    </div>
                    <div class="col-md-5 offset-md-1">
                      <div class="form-group">
                        <label>Prénom du N+1*</label>
                          <input
                            v-model="program.firstName"
                            v-validate="'required'"
                            id="firstName"
                            name="firstName"
                            class="form-control"
                            :class="{ 'is-invalid': errors.has('firstName') }"
                            type="text"
                            placeholder="Prénom..."
                          />
                          <div v-if="errors.has('firstName')" class="invalid-feedback">
                            <p>{{ " Le champ Prénom est obligatoire " }}</p>
                          </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-row" v-if="!isModel && program.type === 'individual'">
                    <div class="col-md-5">
                      <div class="form-group">
                        <label>Email du N+1*</label>
                          <input
                            v-model="program.email"
                            v-validate="'required|email'"
                            id="email"
                            name="email"
                            :class="{ 'is-invalid': errors.has('email') }"
                            type="email"
                            class="form-control"
                            placeholder="you@example.com"
                          />
                          <div
                            v-if="errors.has('email') && errors.firstByRule('email', 'email')"
                            class="invalid-feedback"
                          >
                            <p>{{ " Le format de l'email est invalide " }}</p>
                          </div>
                          <div v-else-if="errors.has('email')" class="invalid-feedback">
                            <p>{{ " L'email existe déjà" }}</p>
                          </div>
                      </div>
                    </div>
                    <div class="col-md-5 offset-md-1">
                      <div class="form-group">
                        <label>Téléphone N+1*</label>
                          <input
                              :class="{ 'is-invalid': errors.has('phone') }"
                              class="form-control"
                              id="phone"
                              name="phone"
                              placeholder="+33123456789"
                              v-model="program.phone"
                              v-validate="{required: true, regex: '^(?:(?:\\+|00)33|0)\\s*[1-9](?:[\\s.-]*\\d{2}){4}$' }" />
                          <div
                            v-if="errors.has('phone') && errors.firstByRule('phone', 'required')"
                            class="invalid-feedback"
                          >
                            <p>{{ " Le champ Téléphone est obligatoire " }}</p>
                          </div>
                          <div v-else-if="errors.has('phone')" class="invalid-feedback">
                            <p>{{ " Le numéro de téléphone est dans un format invalide " }}</p>
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
                      <input
                        class="form-control"
                        placeholder="Nom"
                        id="name"
                        name="name"
                        v-validate="'required'"
                        v-model="program.name"
                        :disabled="!canEdit"
                        :class="{'is-invalid': errors.has('name')}"
                      />
                      <div
                        v-if="errors.has('name')"
                        class="invalid-feedback"
                      >Le nom est obligatoire.</div>
                    </div>
                    <div class="col-md-5 offset-md-1 form-group" v-if="!isModel">
                      <label>Période*</label>
                      <div class="form-row">
                        <div class="col-md-1 mt-auto mb-auto text-center">Du</div>
                        <div class="col-md-5">
                          <el-date-picker
                            v-model="program.dateStart"
                            name="dateStart"
                            disabled
                            format="dd/MM/yyyy"
                            :calendar-button="true"
                            :required="true"
                            :bootstrap-styling="true" />
                        </div>
                        <div class="col-md-1 mt-auto mb-auto text-center">au</div>
                        <div class="col-md-5">
                          <el-date-picker
                            v-model="program.dateEnd"
                            name="dateEnd"
                            disabled
                            format="dd/MM/yyyy"
                            :calendar-button="true"
                            :required="true"
                            :bootstrap-styling="true" />
                        </div>
                      </div>
                      <small
                        v-if="(errors.has('dateStart') || errors.has('dateEnd'))"
                        class="text-danger"
                      >La période s’étend de la première date d’événement à la dernière</small>
                    </div>
                  </div>
                  <div class="form-row" v-if="!isModel">
                    <div class="col-md-5 form-group select">
                      <label>Durée</label>
                      <select id="period" class="form-control" v-model="program.period" :disabled="!canEdit">
                        <option value="null">Sélectionnez une durée</option>
                        <option v-for="period in periodOptions" :key="period.value" :value="period.value">
                          {{ period.label }}
                        </option>
                      </select>
                    </div>
                    <div class="col-md-5 offset-md-1 form-group radio">
                      <label>Email de fin d’accompagnement</label>
                      <div class="radio-items">
                        <div class="radio-item">
                          <input class="form-check-input" type="radio" :value="true" v-model="program.endSupportEmail" id="endsupport_yes" :disabled="!canEdit" />
                          <label class="form-check-label" for="endsupport_yes">Oui</label>
                        </div>
                        <div class="radio-item">
                          <input class="form-check-input" type="radio" :value="false" v-model="program.endSupportEmail" id="endsupport_no" :disabled="!canEdit" />
                          <label class="form-check-label" for="endsupport_no">Non</label>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-row" v-if="!isModel && program.type === 'pic'">
                    <div class="col-md-5 form-group select">
                      <label>Durée limite de prise de RDV</label>
                      <select id="appointmentTimeLimit" class="form-control" v-model="program.appointmentTimeLimit" :disabled="!canEdit">
                        <option :value="0">Sélectionnez une durée</option>
                        <option v-for="limit in limitDurationOptions" :key="limit.value" :value="limit.value">
                          {{ limit.label }}
                        </option>
                      </select>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="col-md-11 form-group">
                      <label for="description">Description*</label>
                      <textarea
                        class="form-control"
                        name="description"
                        id="description"
                        rows="5"
                        v-model="program.description"
                        v-validate="'required'"
                        :disabled="!canEdit"
                        :class="{'is-invalid': errors.has('description')}" />
                      <div v-if="errors.has('description')" class="invalid-feedback">
                        Le champ Description est obligatoire.
                      </div>
                    </div>
                  </div>
                  <div class="form-row" v-if="!isModel">
                    <div class="col-md-11 form-group">
                      <label>{{ program.type === 'pic' ? 'Consultants' : 'Créateur'}}</label>
                      <user-autocomplete
                        v-if="isAdmin && canEdit"
                        v-model="coachInput"
                        @item-selected="addCoach"
                        :types="queryUserTypes"
                        :empty-query-after-selection="true"
                        name="coach"
                      />
                      <div>
                        <single-user
                          v-for="coach in coaches"
                          :key="coach.id"
                          :user="coach"
                          :has-action="isAdmin && program.coaches.length ? true : false"
                          @click="$event => removeCoach(coach)"
                        />
                      </div>
                    </div>
                  </div>
                  <div class="form-row" v-if="!isModel">
                  <div class="col-md-5 form-group" v-if="$route.params.id && program.type !== 'pic'">
                    <label for="createdAt">Date de création</label>
                    <input
                      class="form-control created-date"
                      name="createdAt"
                      id="createdAt"
                      :value="dateFormatter(program.createdAt)"
                      disabled
                    />
                  </div>
                  </div>
                </div>
              </div>
            </fieldset>
            <fieldset v-if="!isModel && (program.type !== 'pic' || $route.params.id)">
              <legend>Invités</legend>
              <div class="panel panel-default">
                <div class="panel-body">
                  <div class="form-row" v-if="canEdit">
                    <div class="col-md-5 form-group">
                      <user-autocomplete
                        v-model="inviteeInput"
                        @item-selected="addUser"
                        :class-list="{'is-invalid': program.users.items.length === 0 && submitted}"
                        :empty-query-after-selection="true"
                        resource="getUsersToAssociateToProgram"
                        :company-id="program.companyId"
                      />
                    </div>
                  </div>
                  <div class="form-row" v-if="canEdit && program.type === 'pic'">
                    <div class="col-md-11">
                      <upload-file-drop
                        label="Cliquez ici pour ajouter votre fichier*"
                        :is-excel="true"
                        @select-file="saveFile"
                        @remove-file="removeFile" />
                      <small class="text-danger" v-if="submitted && fileUploadError" v-html="fileUploadError" />
                    </div>
                  </div>
                  <div class="form-row" v-if="program.users.items">
                    <div class="col-md-11">
                      <template v-for="user in program.users.items">
                        <single-user
                          v-if="program.type !== 'pic' || user.dirty"
                          :key="user.id"
                          :user="user"
                          :has-action="true"
                          @click="$event => removeUser(user)"
                        />
                      </template>
                    </div>
                  </div>
                  <div class="form-row" v-if="canEdit && program.type === 'pic' && program.users.items.length">
                    <div class="col-md-11">
                      <button type="button" class="btn btn-gradient-primary mr-2"
                              @click="mailProgramInvites"
                              v-if="!program.users.itemsHaveBeenInvited">
                        Mail de bienvenue
                      </button>
                      <button type="button" class="btn btn-gradient-primary"
                              @click="openProgramInvites">
                        Voir les invités
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </fieldset>
            <fieldset v-if="!isModel && $route.params.id && program.type === 'pic'">
              <legend>Lien d'inscription hors ligne</legend>
              <div class="panel panel-default">
                <div class="panel-header">
                  <a :href="participationLink" v-if="program.linkId">{{ participationLink }}</a>
                  <p class="text-primary" v-else>Le lien sera disponible une fois ce programme enregistré</p>
                </div>
              </div>
            </fieldset>
            <fieldset v-if="!isModel && program.type === 'pic'">
              <legend>Texte page d'inscription</legend>
              <div class="col-md-11 form-group">
                <ckeditor :editor="ckClassicEditor" v-model="program.inscriptionText"></ckeditor>
              </div>
            </fieldset>
            <template v-if="canEdit">
              <div :class="!$route.params.id  || isModel ? 'container pr-4': 'col-md-12 mb-3'">
                <div class="form-btn-wrap mt-1">
                  <button
                          type="button"
                          class="btn btn-outline-consultant-light"
                          @click="$router.push({name: 'ProgramsList'})"
                  >Annuler</button>
                  <button
                          type="button"
                          class="btn btn-gradient-primary"
                          @click="handleSubmit"
                  >Enregistrer</button>
                </div>
              </div>
            </template>
            <fieldset v-if="!isModel && $route.params.id">
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
            <document-dialog v-if="$route.params.mode === 'document' || documentDialogOpen" @close="closeDocumentDialog" @submit="submitDocumentDialog" />
          </form>
        </template>
      </div>
    </div>
    <div class="col-md-12 offset-md-5 mt-4" v-if="!loading && $route.params.id && program.type === 'pic'">
      <button type="button" class="btn btn-gradient-primary" @click="$router.push({name: 'ImportEvents', params: {programId: $route.params.id}})">
        Ouvrir des créneaux
      </button>
    </div>
    <div class="row" v-if="!loading">
      <div class="col-md-12 mt-4" v-if="($route.params.id || eventsList.length)" v-show="!loadingEvents">
          <list ref="list"
                  name="events"
                  :action-row="!!$route.params.id"
                  action-icon="fa-plus"
                  :pagination="true"
                  no-items-message="Aucun événement n'a encore été créé"
                  :items="eventsList"
                  :total="eventsCount"
                  :header="isModel || (modelIdInput && !routeParams.id) ? eventModelsListHeader : eventListHeader"
                  @action="addNewEvent"
                  @paginate="page => setEventListPage(page)"
                  @see="event => $router.push({name: this.eventFormRoute, params: {id: program.id ? program.id : modelIdInput, event: 'event', eid: event.id, mode: 'view'}})"
                  @edit="event => $router.push({name: this.eventFormRoute, params: {id: program.id ? program.id : modelIdInput, event: 'event', eid: event.id, mode: isModel || (modelIdInput && !routeParams.id) ? 'model' : null}})"
                  @delete="event => showDeleteModal(event)"
          />
      </div>
      <div class="col-md-12 mt-4" v-else>
        Loading...
      </div>
      <confirm-dialog v-if="deleteModal.show"
                  title="Êtes-vous sûr de vouloir supprimer l'événement ?"
                  @close="closeDeleteModal()"
                  @confirm="deleteEvent()" />
    </div>
  </dash-wrap>
</template>
<script>
import "vue-phone-number-input/dist/vue-phone-number-input.css";
import izitoast from "izitoast";
import moment from "moment";
import UserAutocomplete from "@/components/autocomplete/UserAutocomplete";
import SingleUser from "@/components/utils/SingleUser";
import deleteQueriesFromApolloCache from "@/utils/deleteQueriesFromApolloCache";
import List from "@/components/utils/List";
import ConfirmDialog from "@/components/utils/ConfirmDialog";
import localMoment from '@/utils/localMoment';
import DocumentDialog from '@/views/document/documentFormDialog';
import UploadFileDrop from "@/components/file/UploadFileDrop";
import CompanyAutocomplete from "@/components/autocomplete/CompanyAutocomplete";
import CKEditor from "@ckeditor/ckeditor5-vue2";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";

import { ALL_PROGRAM_MODELS } from "@/graphql/program/all-program-models-query";
import { PROGRAM_BY_ID } from "@/graphql/program/program-by-id-query";
import { CREATE_PROGRAM_MODEL } from "@/graphql/program/create-program-model-mutation";
import { UPDATE_PROGRAM_MODEL } from "@/graphql/program/update-program-model-mutation";
import { CREATE_PROGRAM } from "@/graphql/program/create-program-mutation";
import { CREATE_PROGRAM_COACHING_INDIVIDUAL } from "@/graphql/program/create-program-coaching-individual-mutation";
import { PROGRAM_MODEL_BY_ID } from "@/graphql/program/program-model-by-id-query";
import { UPDATE_PROGRAM } from "@/graphql/program/update-program-mutation";
import { ADMINISTRATOR, SUPPORT, COACH } from "@/enum/userTypeEnum";

import {IZITOAST_CONSTANTS} from "@/enum/IzitoastConstants";
import { EVENT_STATUS } from "@/enum/eventStatusEnum";
import {ALL_EVENT_MODELS} from "@/graphql/event/all-event-models-query";
import { EVENT_TYPES } from "@/enum/EventTypesEnum";
import {DELETE_EVENT_MODEL} from "@/graphql/event/delete-event-model-mutation";
import {DELETE_EVENT} from "@/graphql/event/delete-event-mutation";
import {LOGGED_USER} from "@/graphql/security/logged-user-query";
import {USER_BY_ID} from "@/graphql/user/user-by-id-query";
import {CREATE_PROGRAM_PIC} from "@/graphql/program/create-program-pic-mutation";
import {UPDATE_PROGRAM_PIC} from "@/graphql/program/update-program-pic-mutation";
import {CREATE_PROGRAM_OUTPLACEMENT} from "@/graphql/program/create-program-outplacement-mutation";
import {MAIL_PROGRAM_USERS_CREATE_PASSWORD} from "@/graphql/program/mail-program-users-create-password-mutation";
import {PROGRAM_EVENTS} from "@/graphql/program/program-events-query";

export default {
  name: "programForm",
  components: {
    UserAutocomplete,
    SingleUser,
    ConfirmDialog,
    DocumentDialog,
    List,
    UploadFileDrop,
    CompanyAutocomplete,
    ckeditor: CKEditor.component,
  },
  data() {
    return {
      me: '',
      documentDialogOpen: false,
      modelIdInput: null,
      submitted: false,
      loading: 0,
      inviteeInput: '',
      coachInput: '',
      program: {
        updatedBy: '',
        createdBy: '',
        users: {
          items: [],
        },
        coaches: [],
        period: null,
        eventModels: {
            items: []
        },
        programModel: {
            id: ''
        },
        type: 'individual',
        companyId: '',
        endSupportEmail: true,
        appointmentTimeLimit: 0,
        inscriptionText: ''
      },
      programEvents: {
        eventsOrderedByDate: {
          count: 0,
          items: [],
        }
      },
      eventListHeader: [
        {key: 'name', label: 'Nom', sortable: false},
        {key: 'type', label: 'Type', sortable: false},
        {key: 'status', label: 'Statut', sortable: false},
        {key: 'dateEvent', label: ' Date Événement', sortable: false},
        {key: 'updatedAt', label: 'Date Modification', sortable: false}
      ],
      eventModelsListHeader: [
        {key: 'name', label: 'Nom', sortable: false},
        {key: 'description', label: 'Description', sortable: false},
        {key: 'type', label: 'Type', sortable: false},
        {key: 'updatedAt', label: 'Date Modification', sortable: false},
        ...(this.$route.params.id ? [{key: 'actions', label: 'Actions', actions: ['edit', 'delete']}] : []),
      ],
      deleteModal: {
        event: {},
        show: false,
      },
      routeName: this.$route.name,
      routeParams: this.$route.params,
      isModel: this.$route.name === 'ProgramModelForm',
      showDocumentModal: false,
      fileUploadError: null,
      file: null,
      usersHaveBeenInvited: true,
      eventListPage: 1,
      loadingEvents: 0,
      ckClassicEditor: ClassicEditor,
    };
  },
  apollo: {
    me: {
      query: LOGGED_USER,
      watchLoading (isLoading, countModifier) {
        this.loading += countModifier;
      },
    },
    programModels: {
      query: ALL_PROGRAM_MODELS,
      loadingKey: "loading",
      update: data => data.allProgramModels.items,
      watchLoading (isLoading, countModifier) {
        this.loading += countModifier;
      },
    },
    program: {
      query() {
          return this.routeName === 'ProgramModelForm' ? PROGRAM_MODEL_BY_ID : PROGRAM_BY_ID;
      },
      variables() {
          return this.routeName === 'ProgramModelForm' ? {programModelId: this.routeParams.id} : {programId: this.routeParams.id};
      },
      update(data) {
          let r = data.programModelById || data.programById;
          this.modelIdInput = r.programModel && r.programModel.id;
          r.companyId = r.company ? r.company.id : '';
          return r;
      },
      skip() {
          return !this.routeParams.id
      },
      watchLoading (isLoading, countModifier) {
          this.loading += countModifier;
      },
    },
    programEvents: {
      query: PROGRAM_EVENTS,
      variables() {
        return {
          programId: this.routeParams.id,
          limit: 10,
          offset: (this.eventListPage - 1) * 10,
        };
      },
      skip() {
        return this.routeName === 'ProgramModelForm' || !this.routeParams.id;
      },
      update: data => data.programById,
      watchLoading (isLoading, countModifier) {
        this.loadingEvents += countModifier;
      },
    },
    eventModels: {
        query: ALL_EVENT_MODELS,
        update: data => data.allEventModels,
        watchLoading (isLoading, countModifier) {
          this.loading += countModifier;
        },
    }
  },
  computed: {
    eventsList() {
        let events = [];
        if (this.isModel || (this.modelIdInput && !this.routeParams.id)) {
            events = this.program.eventModels.items.slice((this.eventListPage - 1) * 10, this.eventListPage * 10);
        } else {
            events = this.programEvents.eventsOrderedByDate.items
        }

        return events.sort((a, b) => {
          if (!a.dateEvent) {
            return -1;
          } else if (!b.dateEvent) {
            return 1;
          }
          return localMoment(a.dateEvent).isBefore(localMoment(b.dateEvent)) ? -1 : 1;
        }).map(event => ({
          ...event,
          dateEvent: event.dateEvent ? localMoment(event.dateEvent).format('DD/MM/YYYY, HH:mm') + ' à ' + localMoment(event.dateEventEnd).format('HH:mm') : '',
          type: EVENT_TYPES.find(e => e.value === event.type).label,
          status: EVENT_STATUS[event.status],
          updatedAt: localMoment(event.updatedAt !== null ? event.updatedAt : event.createdAt).format('DD/MM/YYYY'),
        }))
    },
    eventsCount() {
      return this.isModel || (this.modelIdInput && !this.routeParams.id) ? this.program.eventModels.items.length : this.programEvents.eventsOrderedByDate.count;
    },
    isCandidate() {
      return this.user.typeId === "candidate";
    },
    eventFormRoute() {
        if (this.routeParams.id) {
            return this.isModel ? "ProgramModelEventForm" : "ProgramEventForm";
        } else {
            return "ProgramModelEventForm";
        }
    },
    isAdmin() {
      return this.me && this.me.type === "admin";
    },
    lastModifiedAt() {
      if (!this.$route.params.id) {
        return "";
      }

      let date =
        this.program.updatedAt !== null
          ? this.program.updatedAt
          : this.program.createdAt;

      return localMoment(date).format("DD/MM/YYYY");
    },
    canEdit() {
      return this.$route.params.mode !== "view";
    },
    coaches() {
      return this.program.coaches.length ? this.program.coaches : (this.program.type === 'pic' ? [] : [this.me]);
    },
    queryUserTypes() {
      return this.program.type === 'pic' ? [COACH] : [ADMINISTRATOR, COACH, SUPPORT];
    },
    periodOptions() {
      return [
        {value: 1, label: "1 mois", picOnly: true},
        {value: 2, label: "2 mois", picOnly: true},
        {value: 3, label: "3 mois"},
        {value: 4, label: "4 mois", picOnly: true},
        {value: 5, label: "5 mois", picOnly: true},
        {value: 6, label: "6 mois"},
        {value: 8, label: "8 mois"},
        {value: 12, label: "12 mois"},
        {value: 18, label: "18 mois"},
      ].filter(option => !option.picOnly || this.program.type === 'pic');
    },
    limitDurationOptions() {
      return [
        {value: 24, label: "24h"},
        {value: 48, label: "48h"},
        {value: 72, label: "72h"},
      ];
    },
    participationLink() {
      let baseParticipationLink = process.env.VUE_APP_BASE_PARTICIPATION_LINK;
      return baseParticipationLink + "/" + this.program.linkId;
    }
  },
  methods: {
    handleSubmit(e) {
      const isNew = !this.$route.params.id;
      this.$validator.resume();
      this.submitted = true;
      this.loading = true;
      this.$validator.validate().then(valid => {
        if (
          !valid ||
          (!this.isModel && this.program.users.items.length === 0 && this.program.type !== 'pic')
        ) {
          izitoast.error({
            timeout: IZITOAST_CONSTANTS.TIME_OUT,
            position: "topRight",
            title: "Erreur",
            message: "Veuillez vérifier le formulaire pour les erreurs"
          });
          this.loading = false;
          return;
        }

        let mutation,
          variables = {
            ...this.program,
            isModel: this.isModel
          };
        if (this.isModel) {
          mutation = this.$route.params.id
            ? UPDATE_PROGRAM_MODEL
            : CREATE_PROGRAM_MODEL;
        } else {
          if (variables.type === 'pic') {
            mutation = this.$route.params.id ? UPDATE_PROGRAM_PIC : CREATE_PROGRAM_PIC;
            variables.file = this.file;
          } else {
            mutation = this.$route.params.id ? UPDATE_PROGRAM : (variables.type === 'individual' ? CREATE_PROGRAM_COACHING_INDIVIDUAL : (variables.type === 'outplacement' ? CREATE_PROGRAM_OUTPLACEMENT : CREATE_PROGRAM));
          }
          variables.modelId = this.modelIdInput;
          variables.coachId = this.program.coaches.length ? this.program.coaches[0].id : null;
          variables.dateStart = this.program.dateStart
            ? moment(this.program.dateStart, "DD/MM/YYYY").toISOString()
            : null;
          variables.dateEnd = this.program.dateEnd
            ? moment(this.program.dateEnd, "DD/MM/YYYY").toISOString()
            : null;
          variables.userIds = this.program.users.items.map(user => user.id);

          // Only used for type PIC
          variables.coachIds = [...(!this.program.coaches.length ? [this.me] : this.program.coaches)].map(coach => coach.id);
        }
        this.saveProgramMutation(mutation, variables, isNew);

      });
    },
    saveProgramMutation(mutation, variables, isNew) {
      this.$apollo
          .mutate({
            mutation: mutation,
            variables: variables,
            update(proxy) {
              if (isNew) {
                deleteQueriesFromApolloCache(proxy, "allPrograms");
                deleteQueriesFromApolloCache(proxy, "allProgramModels");
              }

              if (variables.file) {
                deleteQueriesFromApolloCache(proxy, "allUsers");
              }

              deleteQueriesFromApolloCache(proxy, "allCandidates");
              deleteQueriesFromApolloCache(proxy, "candidateById");
              deleteQueriesFromApolloCache(proxy, "userById");
            }
          })
          .then(response => {
            izitoast.success({
              position: "topRight",
              title: "Succès",
              message: this.$route.params.id
                ? "Le prestation a été mis à jour avec succès"
                : "Le prestation a été créé avec succès"
            });

            this.loading = false;
            this.program = {
              ...this.program,
              ...response.data[Object.keys(response.data)[0]],
            };
            if (this.program.type === 'pic' && !this.$route.params.id) {
              this.$router.push({name: "ProgramForm", params: {id: response.data.createProgramPic.id}});
            } else if (!this.isModel || this.$route.params.id) {
              this.$router.push({name: "ProgramsList"});
            } else {
              this.$router.push({name: this.isModel ? "ProgramModelForm" : "ProgramForm", params: {id: response.data.createProgramModel.id}});
            }
          })
          .catch(response => {
            this.loading = false;
            this.fileUploadError = response.networkError.result.errors[0].message;
            izitoast.error({
              timeout: IZITOAST_CONSTANTS.TIME_OUT,
              position: "topRight",
              title: "Erreur",
              message: "Une erreur s'est produite lors de l'importation d'utilisateurs, veuillez consulter le formulaire",
            });
          });
    },
    dateFormatter(date) {
      return localMoment(date).format("DD/MM/YYYY");
    },
    addCoach(coach) {
      if (this.program.type !== 'pic') {
        this.program.coaches = [coach];
      } else {
        this.program.coaches.push(coach);
      }
    },
    addNewDocument () {
        this.documentDialogOpen = true;
    },
    closeDocumentDialog () {
        this.documentDialogOpen = false;
    },
    async submitDocumentDialog () {
        this.loading = true;
        await this.closeDocumentDialog();
        await this.$apollo.queries.program.refetch();
        this.loading = false;
    },
    addUser(user) {
      user.dirty = true;
      if (this.program.type === 'individual' && this.program.users.items.length === 0) {
        this.program.firstName = user.nFirstName;
        this.program.lastName = user.nLastName;
        this.program.email = user.nEmail;
        this.program.phone = user.nPhone;
      }
      if (this.program.type === 'individual' && this.program.users.items.length>0) {
        this.program.users.items = [user];
        return;
      }
      this.program.users.items.push(user);
    },
    removeUser(user) {
      this.program.users.items = this.program.users.items.filter(u => u.id !== user.id);
    },
    removeCoach(coach) {
      this.program.coaches = this.program.coaches.filter(c => c.id !== coach.id);
    },
    updateModel() {
      if (!this.isModel && this.modelIdInput) {
          let programModel = this.programModels.find(
          programModel => programModel.id === this.modelIdInput
        );
        this.program.name = programModel.name;
        this.program.description = programModel.description;
        this.program.eventModels = programModel.eventModels;
      } else if (!this.isModel) {
        this.program.name = "";
        this.program.description = "";
      }
    },
    closeDeleteModal () {
        this.deleteModal.show = false;
    },
    showDeleteModal (event) {
        this.deleteModal.event = event;
        this.deleteModal.show = true;
    },
    deleteEvent () {
        this.closeDeleteModal();
        this.$apollo.mutate({
            mutation: this.isModel ? DELETE_EVENT_MODEL : DELETE_EVENT,
            variables: this.isModel ? { eventModelId: this.deleteModal.event.id } : { eventId: this.deleteModal.event.id },
        }).then(() => {
            this.$apollo.queries.program.refetch();
            iziToast.success({
                position: 'topRight',
                title: 'Succès',
                message: "L'événement a bien été supprimé",
            });
        });
        // TODO : Implement correct error handling
        /* .catch((error) => {
         izitoast.error({
             position: 'topRight',
             title: 'Erreur',
             message: 'L\'événement ne peut être supprimé car il est utilisé par des événements',
         });
        });*/
    },
    addNewEvent () {
        this.$router.push({name: this.eventFormRoute, params: {id: this.$route.params.id}})
    },
    saveFile(file) {
      let filename = file.name.toLowerCase();
      if (filename.slice(-3) !== 'xls' && filename.slice(-4) !== 'xlsx') {
        izitoast.error({
          timeout: IZITOAST_CONSTANTS.TIME_OUT,
          position: "topRight",
          title: "Erreur",
          message: "Le fichier doit être xls et xlsx",
        });
        return;
      }
      this.file = file;
      this.fileUploadError = null;
    },
    removeFile() {
      this.file = null;
    },
    openProgramInvites() {
      window.open('/users-list?programId=' + this.program.id, '_blank');
    },
    mailProgramInvites() {
      this.$apollo.mutate({
        mutation: MAIL_PROGRAM_USERS_CREATE_PASSWORD,
        variables: {
          programId: this.program.id,
        },
        optimisticResponse: {
          __typename: "Mutation",
          mailProgramUsersCreatePassword: {
            __typename: "ProgramPic",
            id: this.program.id,
            usersHaveBeenInvited: true,
            users: this.program.users.items.map(user => ({
              __typename: "User",
              id: user.id,
              hasReceivedWelcomeMail: true,
            }))
          },
        },
      });

      izitoast.success({
        timeout: IZITOAST_CONSTANTS.TIME_OUT,
        position: "topRight",
        title: "Succès",
        message: "Les invitations ont été envoyées",
      });
    },
    setEventListPage (page) {
      this.eventListPage = page;
    },
    openDocumentList () {
      window.open("/documents-list?programId=" + this.program.id, "_blank");
    }
  },
  created() {
    this.$validator.pause();
  }
};
</script>
