<template>
  <div class="coache-card user-card">
    <span class="tag d-block text-right font-weight-bold font-size-14">{{ title }}</span>
    <div class="d-flex flex-column card-bg">
      <div class="d-flex flex-column align-items-center uc-l1">
        <div class="user-avatar">
          <img v-if="!coach.profilePicture" src="@/assets/img/user-primary.svg" />
          <img v-else :src="getImageUrl(coach.profilePicture.id)" />
        </div>
        <span class="d-block text-center font-size-18 font-weight-bold">
          {{ coach.firstName }} {{ coach.lastName.toUpperCase() }}
        </span>
      </div>
      <div class="d-flex justify-content-center">
        <small class="d-block text-center text-muted font-weight-bold text-gris_80">
          {{ coach.function }}
        </small>
      </div>
      <template v-if="event">
        <div class="d-flex justify-content-around uc-l2" v-if="event.isFull && !event.isAttending">
          <div class="spec spec--coaching">
            <span class="spec-1">Complet</span>
            <span class="spec-2">Complet</span>
          </div>
        </div>
        <div class="d-flex justify-content-around uc-l2" v-if="event.isAttending">
          <div class="spec spec--coaching">
            <span class="spec-1">Vous êtes inscrit</span>
            <span class="spec-2">Inscrit</span>
          </div>
        </div>
        <div class="bg d-flex overflow-hidden uc-l3">
          <span class="text-uppercase font-weight-bold text-gris_80 flex-grow-1 uc-l4 white-space-nowrap">
              Date De la Réunion
          </span>
          <img class="d-none d-md-block" width="96px" height="19px" src="@/assets/img/separateur.svg" alt />
        </div>
        <div class="d-flex align-items-center pl-4 mt-1">
          <div class="date-circle flex-column text-carriere">
            <div class="pt-1">
              <span class="d-block font-weight-bold">{{ getDate(event.dateEvent) }}</span>
              <span class="d-block font-size-8">{{ getMonth(event.dateEvent) }}</span>
            </div>
          </div>
          <span class="d-block font-size-14 ml-2">
            Atelier
            <small class="d-block text-carriere">{{ getTime(event.dateEvent) }} - {{ getTime(event.dateEventEnd)}}</small>
          </span>
          <div class="ml-auto pr-3">
            <button class="btn btn-tag action-btn btn-outline-gris_44">{{event.meetingPlace === "visio" ? "Visio" : "Présentielle"}}</button>
          </div>
        </div>
        <div class="d-flex justify-content-center pl-4 mt-2" v-if="!event.isFull && !event.isAttending">
          <button class="btn btn-gradient-primary btn-sm btn-no-shadow"
                  @click="$emit('join')">
            S'inscrire
          </button>
        </div>
      </template>
      <template v-else>
        <div class="d-flex justify-content-center mt-2">
          <button class="btn btn-outline-secondary btn-sm btn-no-shadow"
                  @click="openCV" v-if="coach.cvFile && coach.cvFile.id">
            Voir le profil
          </button>
        </div>
        <div class="d-flex justify-content-center mt-2 pl-2 pr-2">
          <button class="btn btn-gradient-primary btn-sm btn-no-shadow"
                  @click="showEventSelection" v-if="!eventSelectionVisible">
            Prendre RDV
          </button>
          <select ref="eventSelector" class="form-control" v-model="eventSelection"
                  v-if="eventSelectionVisible"
                  @blur="eventSelectionVisible = false"
                  @change="$emit('select-event', eventSelection)">
            <option :value="''">Sélectionner un créneau</option>
            <option v-for="event in coach.events" :key="event.id" :value="event.id">
              {{ getDate(event.dateEvent) }} {{ getMonth(event.dateEvent) }} de {{ getTime(event.dateEvent) }} à {{ getTime(event.dateEventEnd) }} - {{ event.name }}
            </option>
          </select>
        </div>
      </template>
    </div>
  </div>
</template>
<script>
import localMoment from "@/utils/localMoment";
import { FILE_PATH } from "@/enum/FilePathConstant";

export default {
  name: "CoachAndWorkshopCard",
  props: {
    title: {
      type: String,
      required: true,
    },
    coach: {
      type: Object,
      required: true,
    },
    event: {
      type: Object,
      required: false,
    },
  },
  data: () => ({
    eventSelectionVisible: false,
    eventSelection: "",
  }),
  methods: {
    getDate(date) {
      return localMoment(date).format('D');
    },
    getMonth(date) {
      return localMoment(date).format('MMM');
    },
    getTime(date) {
      return localMoment(date).format('HH:mm');
    },
    trimString(string, length) {
        if(string.length > length) {
            return string.substring(0, length) + '...';
        }
        return string;
    },
    getImageUrl(profilePhotoId) {
      let baseUrl = process.env.VUE_APP_GRAPHQL_HTTP.substr(
              0,
              process.env.VUE_APP_GRAPHQL_HTTP.lastIndexOf("/")
      );
      return baseUrl + FILE_PATH + profilePhotoId;
    },
    showEventSelection() {
      this.eventSelectionVisible = true;
      this.$nextTick(() => {
        this.$refs.eventSelector.focus();
      });
    },
    openCV() {
      window.open(this.getImageUrl(this.coach.cvFile.id), '_blank');
    }
  },
};
</script>
