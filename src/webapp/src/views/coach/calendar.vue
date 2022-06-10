<template>
  <dash-wrap mode="coach" article="Bonjour" :title="me ? me.firstName : ''">
    <div class="col-md-12 mb-2 mt-4" v-if="loading">
      Loading...
    </div>
    <div class="col-md-12 mb-2 mt-4" v-if="!loading">
      <h1 class="text-center mb-4">
        Vos rendez-vous et événements
      </h1>
    </div>
    <div class="col-md-10 offset-md-1 bg-white border-top pt-4 border-primary pb-4 mb-4" v-if="!loading">
      <FullCalendar defaultView="dayGridMonth"
                    theme-system="bootstrap"
                    slot-duration="01:00"
                    :header="{
                            left: 'today prev,next',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,timeGridDay'
                          }"
                    :locale="calendarLocale"
                    :events="calendarEvents"
                    :plugins="calendarPlugins" />
    </div>
  </dash-wrap>
</template>
<script>
import { LOGGED_USER } from "@/graphql/security/logged-user-query";
import {ALL_EVENTS} from "@/graphql/event/all-events-query";
import localMoment from "@/utils/localMoment";
import {INDIVIDUAL_SESSION} from "@/enum/EventTypesEnum";

import FullCalendar from '@fullcalendar/vue';
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import bootstrapPlugin from "@fullcalendar/bootstrap";
import frLocale from '@fullcalendar/core/locales/fr';

import '@fullcalendar/core/main.css';
import '@fullcalendar/daygrid/main.css';
import '@fullcalendar/timegrid/main.css';
import '@fullcalendar/bootstrap/main.css';

export default {
  name: "CoacheList",
  components: {
    FullCalendar,
  },
  data() {
    return {
      loading: false,
      events: [],
      me: {},
      calendarPlugins: [dayGridPlugin, timeGridPlugin, bootstrapPlugin],
      calendarLocale: frLocale,
    };
  },
  apollo: {
    me: {
      query: LOGGED_USER,
    },
    events: {
      query: ALL_EVENTS,
      variables() {
        return {
          organizer: this.me.id,
        };
      },
      skip () {
        return !this.me.id
      },
      watchLoading (isLoading, countModifier) {
        this.loading += countModifier;
      },
      update: response => response.allEvents.items,
    },
  },
  computed: {
    calendarEvents () {
      return this.events
        .filter(event => event.status === 'created' || event.status === 'upcoming')
        .map(event => {
          return {
            id: event.id,
            title:
              event.status === 'upcoming' && event.type === INDIVIDUAL_SESSION && event.users && event.users.length > 0?
                event.users[0].firstName + ' ' + event.users[0].lastName.toUpperCase()
              : event.name,
            start: localMoment(event.dateEvent).format('YYYY-MM-DD HH:mm'),
            end: localMoment(event.dateEventEnd).format('YYYY-MM-DD HH:mm'),
            classNames: [
              event.status === 'created' ? 'bg-success' : 'bg-primary',
              event.status === 'upcoming' && event.users && event.users.length > 0 ? 'border-success' : 'border-primary'
            ],
          };
        });
    },
  },
  methods: {
  },
};
</script>