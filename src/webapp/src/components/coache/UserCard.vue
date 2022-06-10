<template>
  <div class="coache-card user-card">
    <span class="tag d-block text-right font-weight-bold font-size-14">{{trimString(info.program.name, 20)}}</span>
    <div class="d-flex flex-column card-bg">
      <div class="d-flex flex-column align-items-center uc-l1" @click="$event => $router.push({name: 'ViewCandidate', params: {id: info.user.id, program: info.program.id}})">
        <div class="user-avatar">
          <img v-if="!info.user.profilePicture" src="@/assets/img/user-primary.svg" />
          <img v-else :src="getImageUrl(info.user.profilePicture.id)" />
        </div>
        <span class="d-block text-center font-size-18 font-weight-bold">
          {{ info.user.firstName }} {{ info.user.lastName.toUpperCase() }}
        </span>
        <span class="d-block text-center font-size-11 font-weight-bold text-gris_70">{{ info.user.company ? info.user.company.name : 'Non'}}</span>

        <div class="d-flex justify-content-around uc-l2">
          <div class="spec spec--coaching mr-4">
            <span class="spec-1">{{ info.completedEventsCount }}/{{ info.eventsCount }}</span>
            <span class="spec-2">Coaching</span>
          </div>
          <div class="spec spec--to-do ml-4">
            <span class="spec-1">{{ todoCount }}</span>
            <span class="spec-2">To Do</span>
          </div>
        </div>
      </div>
      <div class="bg d-flex overflow-hidden uc-l3">
        <span class="text-uppercase font-weight-bold text-gris_80 flex-grow-1 uc-l4 white-space-nowrap">
            {{ info.program.status === 'finished' ? 'Prestation terminé' : 'Prochain coaching' }}
        </span>
        <img class="d-none d-md-block" width="96px" height="19px" src="@/assets/img/separateur.svg" alt />
      </div>
      <div class="d-flex align-items-center pl-4 mt-1">
        <template v-if="info.program.status === 'finished'">
          <div class="ml-2 mt-1">
            <span class="d-block font-size-14 entity-name">{{ info.program.name }}</span>
          </div>
        </template>
        <template v-else-if="info.nextEvent && (info.nextEvent.status === 'ongoing' || isEventDue(info.nextEvent))">
          <div class="date-circle">
            <img src="@/assets/img/date.svg" height="20px" width="20px" />
          </div>
          <span class="d-block font-size-14 ml-2">{{ info.nextEvent.name }}</span>
          <div class="ml-auto pr-3" v-if="info.nextEvent.meetingPlace === 'visio'">
            <button class="d-none d-md-block btn btn-gradient-primary btn-sm btn-no-shadow" @click="openTeamsLink">Commencer</button>
            <button class="d-inline-block d-md-none btn btn-commencer btn-no-shadow" @click="openTeamsLink">
              <img src="@/assets/img/arrow-right-primary.svg"/>
            </button>
          </div>
        </template>

        <template v-else-if="info.nextEvent">
          <div class="date-circle flex-column text-carriere">
            <div class="pt-1">
              <span class="d-block font-weight-bold">{{ getDate(info.nextEvent.dateEvent) }}</span>
              <span class="d-block font-size-8">{{ getMonth(info.nextEvent.dateEvent) }}</span>
            </div>
          </div>
          <div class="ml-2 mt-1">
            <span class="d-block font-size-14 entity-name">{{ info.nextEvent.name }}</span>
            <span class="d-block font-size-12 font-weight-bold text-carriere">{{ getTime(info.nextEvent.dateEvent) }}</span>
          </div>
          <div class="ml-auto pr-3">
            <button class="btn btn-tag action-btn btn-outline-gris_44">{{info.nextEvent.meetingPlace === "visio" ? "Visio" : "Présentielle"}}</button>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>
<script>
import localMoment from '@/utils/localMoment';
import { FILE_PATH } from "@/enum/FilePathConstant";

export default {
  name: "CoacheUserCard",
  props: {
    info: {
      type: Object,
      required: true,
      default: () => {
        return {
          tag: "Coaching",
          name: "Christophe Martin"
        };
      }
    }
  },
  data() {
    return {};
  },
  computed: {
    action() {
      return this.info.action ? this.info.action : 2;
    },
    todoCount() {
      return this.info.program.todos.items.filter(item => item.user.id === this.info.user.id).length;
    }
  },
  methods: {
    getDate(date) {
      return localMoment(date).format('D');
    },
    getMonth(date) {
      return localMoment(date).format('MMM');
    },
    getTime(date) {
      return localMoment(date).format('kk:mm');
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
    openTeamsLink() {
      window.open(this.info.nextEvent.teamsLink, "_blank");
    },
    isEventDue(event) {
      return localMoment().isSameOrAfter(localMoment(event.dateEvent).subtract(15, 'minutes'));
    }
  },
};
</script>
