<template>
  <dash-wrap mode="coach" article="Bonjour" :title="me ? me.firstName : ''">
    <template v-slot:headeractions>
      <div class="d-none d-md-flex coache-header-buttons">
          <button class="btn btn-white-icon" @click="$event => $router.push({name: 'ProgramForm'})">
            <img src="@/assets/img/prestation-add.svg" alt />
            <span>Créer prestation</span>
          </button>
          <button class="btn btn-white-icon ml-3" @click="$event => $listeners['create-event'] ? $emit('create-event', $event) : $router.push({name: 'EventForm'})">
            <img src="@/assets/img/icon-event-add_primary.svg" alt />
            <span>Créer évenement</span>
          </button>
      </div>
    </template>
    <template v-slot:mainaction>
      <a class="nav-link main-link" :class="{'active':mainNavActive}" @click="mainNavActive= !mainNavActive">
        <img src="@/assets/img/plus-white.svg" class="sb-img" alt="">
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
    <filter-coache @filter="$event => updateFilters($event)" />
    <div v-if="loading">Loading...</div>
    <div class="container-fluid">
      <div class="coache-list mt-5 row" v-if="!loading">
        <div class="coache-item col-12 col-sm-6 col-lg-4 col-xl-3" v-for="(item, index) in items" :key="index">
          <coache-user-card :info="item" />
        </div>
      </div>
    </div>
    <div class="mt-4 text-center" v-if="!loading">
      <paginate
        v-model="page"
        :total="allCandidates ? allCandidates.count : 0"
        :page-count="totalPages"
        :click-handler="$event => updatePage($event)"
        container-class="pagination justify-content-center"
        prev-text="chevron_left"
        next-text="chevron_right"
        page-class="page-item"
        page-link-class="page-link"
        prev-link-class="page-link previous md-icon"
        next-link-class="page-link next md-icon"
      />
    </div>
  </dash-wrap>
</template>
<script>
import FilterCoache from "@/components/coache/Filter";
import CoacheUserCard from "@/components/coache/UserCard.vue";
import Paginate from "vuejs-paginate";
import {ALL_CANDIDATES} from "@/graphql/user/all-candidates-query";
import { LOGGED_USER } from "@/graphql/security/logged-user-query";
import moment from "moment";

export default {
  name: "CoacheList",
  components: {
    FilterCoache,
    CoacheUserCard,
    Paginate
  },
  data() {
    return {
      filterPanelActive: false,
      page: 1,
      offset: 0,
      loading: false,
      allCandidates: {
        items: [],
        count: 0,
      },
      filters: {},
      mainNavActive: false,
    };
  },
  apollo: {
    allCandidates: {
      query: ALL_CANDIDATES,
      variables() {
        return {
          ...this.filters,
          date: this.filters.date ? moment(this.filters.date).format('Y-MM-DD') : "",
          limit: 8,
          offset: this.offset,
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
  computed: {
    items () {
      return this.allCandidates ? this.allCandidates.items : [];
    },
    totalPages () {
      return this.allCandidates ? this.allCandidates.count / 8 : 1;
    }
  },
  methods: {
    toggleFilterPanel () {
      this.filterPanelActive = !this.filterPanelActive
    },
    updatePage (page) {
      this.page = page;
      this.offset = (page - 1) * 8;
    },
    updateFilters (filters) {
      this.filters = {...filters};
    },
  },
};
</script>