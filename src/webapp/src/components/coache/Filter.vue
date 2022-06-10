<style>
  .center-radio-filters {
    margin-bottom: 5% !important;
  }
</style>
<template>
  <div class="filters">
    <form @submit.prevent="$emit('filter', filters)">
      <div class="filter-group filter-group--l">
        <div class="filters-coach col-12">
          <div class="col-md-12 mb-1 d-flex">
            <div class="form-group mr-1">
              <input
                      class="form-control"
                      placeholder="Email"
                      type="text"
                      v-model="filters.email"
              />
            </div>
            <div class="form-group mr-1">
              <input
                      class="form-control"
                      placeholder="Nom"
                      type="text"
                      v-model="filters.lastName"
              />
            </div>
            <div class="form-group mr-1">
              <input
                      class="form-control"
                      placeholder="Prénom"
                      type="text"
                      v-model="filters.firstName"
              />
            </div>
            <div class="form-group event-type-select select mr-1 no-label mb-0">
              <select name="type" class="form-control" v-model="filters.eventType">
                <option value disabled>Type d'événement</option>
                <option v-for="type in eventTypes" :key="type.id" :value="type.value">{{ type.label }}</option>
              </select>
            </div>

            <el-date-picker
                    class="date-picker"
                    v-model="filters.date"
                    name="dateStart"
                    format="dd/MM/yyyy"
                    placeholder="Date évènement"
                    :calendar-button="true"
                    :bootstrap-styling="true"
            />
            <div class="form-group radio  center-radio-filters mb-0 coache-filter-radio d-flex">
              <div class="radio-items align-self-center">
                <small class="radio-item" v-for="(option,i) in eventStatusOptions" :key="i">
                  <input
                          type="checkbox"
                          :id="`montrer-seulement-${i}`"
                          :value="option.id"
                          v-model="filters.eventStatuses"
                          name="eventStatuses"
                  />
                  <label :for="`montrer-seulement-${i}`">{{option.label}}</label>
                </small>
              </div>
            </div>
            <div class="ml-2 d-flex center-radio-filters">
              <button class="btn btn-search-round btn-gris_80 align-self-center" @click="$emit('filter', filters)">
                <img src="@/assets/img/search-white.svg" />
              </button>
              <button class="btn btn-outline-dark btn-sm ml-2 btn-search-round align-self-center" @click="resetFilters()">
                <i class="fa fa-undo" aria-hidden="true"></i>
              </button>
            </div>
          </div>
          </div>

      </div>
      <div class="filter-group filter-group--sm justify-content-end">
        <button
          @click="filterModalActive = true"
          type="button"
          class="btn bg-transparent text-primary btn-outline-primary ml-4 btn-no-hover btn-no-focus"
        >
          <img class="mr-3" src="@/assets/img/filtre.svg" />
          Filtres
        </button>
        <mobile-modal :active="filterModalActive" @close="filterModalActive = false">
          <span class="font-weight-bold font-size-18">Filtres :</span>
          <div class="filters-form">
            <div class="filter-input">
              <input
                  class="form-control mr-1 col-md-4"
                  placeholder="Email"
                  type="text"
                  v-model="filters.email"
                />
            </div>
            <div class="filter-input">
              <input
                class="form-control mr-1"
                placeholder="Nom"
                type="text"
                v-model="filters.lastName"
              />
            </div>
            <div class="filter-input">
              <input
                class="form-control"
                placeholder="Prénom"
                type="text"
                v-model="filters.firstName"
              />
            </div>
            <div class="filter-input">
              <el-date-picker
                v-model="filters.date"
                name="dateStart"
                format="dd/MM/yyyy"
                placeholder="Date évènement"
                :calendar-button="true"
                :bootstrap-styling="true"
              />
            </div>
            <div class="filter-input form-group radio mb-0 coache-filter-radio">
              <div class="radio-items">
                <small class="radio-item" v-for="(option,i) in eventStatusOptions" :key="i">
                  <input
                    type="checkbox"
                    :id="`montrer-seulement-${i}`"
                    :value="option.id"
                    v-model="filters.eventStatuses"
                    name="eventStatuses"
                  />
                  <label :for="`montrer-seulement-${i}`">{{option.label}}</label>
                </small>
              </div>
            </div>
            <div class="filter-input">
              <select id="type" name="type" class="form-control" v-model="filters.eventType">
                <option value>Type d'événement</option>
                <option v-for="type in eventTypes" :key="type.id" :value="type.value">{{ type.label }}</option>
              </select>
            </div>
          </div>

          <button class="btn btn-white-primary btn-block" type="button" @click="$emit('filter', filters)">filtrer</button>
          <button
            class="btn btn-outline-dark btn-block mt-2 btn-white-secondary btn-uppercase"
            @click="resetFilters()" type="button"
          >Réinitialiser</button>
        </mobile-modal>
      </div>
    </form>
  </div>
</template>
<script>
import { EVENT_TYPES } from "@/enum/EventTypesEnum";
import MobileModal from "@/components/utils/MobileModal";
export default {
  name: "CoacheFilter",
  components: {
    MobileModal
  },
  data() {
    return {
      filterModalActive: false,
      filters: {
        email: '',
        firstName: '',
        lastName: '',
        eventStatuses: [],
        eventType: '',
        date: '',
      },
      eventStatusOptions: [
        {
          label: "En cours",
          id: 'ongoing',
        },
        {
          label: "À venir",
          id: 'upcoming',
        },
        {
          label: "Terminé",
          id: 'finished',
        }
      ],
      programTypeOptions: [
        {
          label: "Coaching Individuel",
          id: "individual",
        },
        {
          label: "Coaching Collectif",
          id: "group",
        },
      ]
    };
  },
  computed: {
    eventTypes() {
      return EVENT_TYPES;
    }
  },
  methods: {
    applyFilters(){},
    resetFilters () {
      for (let i in this.filters) {
        this.filters[i] = i === 'eventStatuses' ? [] : '';
      }
      this.$emit('filter', this.filters);
    }
  }
};
</script>