<template>
  <dash-wrap active="admin" tab="programs">
    <template v-slot:mainaction>
      <router-link class="btn nav-link" :to="{name:'ProgramForm'}">
          <img src="@/assets/img/plus-white.svg" alt="">
      </router-link>
  </template>
    <!-- <h1>Liste des prestations</h1> -->
      <div class="d-flex">
        <filters name="programs" :filters="filterList" @filter="updateFilters($event)" />
        <div class="ml-auto d-none d-md-block">
          <button
                  class="btn btn-gradient-primary px-3 ml-2"
                  @click="() => $router.push({name: 'ProgramForm'})">
            <i class="fa fa-plus" aria-hidden="true"></i>
            Créer une prestation
          </button>
        </div>
      </div>

    <div class="row mt-2">
      <div class="col-md-12" v-if="filters.model">
        <list
          name="programModels"
          ref="list"
          :items="listModelItems"
          :total="allProgramModels.count"
          :header="headerModel"
          no-items-message="Aucun modèle deprestation ne correspond à ce filtre"
          :loading="loading"
          @edit="program => $router.push({name: 'ProgramModelForm', params: {id: program.id}})"
          @delete="program => showDeleteModal(program)"
          @paginate="updateModelPage($event)"
          @sort="updateModelSort($event)"
        />
      </div>
      <div class="col-md-12" v-else>
        <list
          name="programs"
          ref="list"
          no-items-message="Aucune prestation ne correspond à ce filtre"
          :items="listItems"
          :total="allPrograms.count"
          :header="header"
          :loading="loading"
          @see="program => $router.push({name: 'ProgramForm', params: {id: program.id, mode: 'view'}})"
          @edit="program => $router.push({name: 'ProgramForm', params: {id: program.id}})"
          @delete="program => showDeleteModal(program)"
          @sort="updateSort($event)"
          @paginate="updatePage($event)"
        />
      </div>
    </div>
    <confirm-dialog
      v-if="deleteModal.show"
      title="Etes-vous sûr de vouloir supprimer cette prestation ?"
      @close="closeDeleteModal()"
      @confirm="deleteProgram()"
    />
  </dash-wrap>
</template>
<script>
import List from "@/components/utils/List";
import Filters from "@/components/filters/Filters";
import ConfirmDialog from "@/components/utils/ConfirmDialog";
import deleteQueriesFromApolloCache from "@/utils/deleteQueriesFromApolloCache";
import iziToast from "izitoast";
import localMoment from '@/utils/localMoment';

import { DELETE_PROGRAM } from "@/graphql/program/delete-program-mutation";
import { DELETE_PROGRAM_MODEL } from "@/graphql/program/delete-program-model-mutation";
import { LOGGED_USER } from "@/graphql/security/logged-user-query";
import { ALL_PROGRAMS } from "@/graphql/program/all-programs-query";
import { ALL_PROGRAM_MODELS } from "@/graphql/program/all-program-models-query";
import { PROGRAM_STATUS } from "@/enum/programStatusEnum";

export default {
  name: "programsList",
  components: {
    List,
    Filters,
    ConfirmDialog
  },
  data: () => ({
    header: [
      { key: "name", label: "Nom", sortable: true },
      { key: "invited", label: "Invité", sortable: false },
      { key: "period", label: "Période", sortable: false },
      { key: "status", label: "Statut", sortable: true },
      { key: "updatedAt", label: "Date modification", sortable: true },
      { key: "actions", label: "Actions", actions: ["see", "edit", "delete"] }
    ],
    headerModel: [
      { key: "name", label: "Nom", sortable: true },
      { key: "description", label: "Description", sortable: true },
      { key: "updatedAt", label: "Date modification", sortable: false },
      { key: "actions", label: "Actions", actions: ["edit", "delete"] }
    ],
    filterList: [
      { key: "search", type: "text", label: "Nom ou Description" },
      {
        key: "status",
        type: "select",
        label: "Statut",
        attributes: {
          options: Object.keys(PROGRAM_STATUS).map(status => ({value: status, label: PROGRAM_STATUS[status]})),
        }
      },
    ],
    filters: {},
    sortColumn: "createdAt",
    sortDirection: "desc",
    sortModelColumn: "createdAt",
    sortModelDirection: "desc",
    allPrograms: {
      items: [],
      count: 0
    },
    allProgramModels: {
      items: [],
      count: 0
    },
    loading: false,
    offset: 0,
    offsetModel: 0,
    deleteModal: {
      program: {},
      show: false
    },
  }),
  apollo: {
    me: {
      query: LOGGED_USER
    },
    allPrograms: {
      query: ALL_PROGRAMS,
      variables() {
        return {
          ...this.filters,
          limit: 10,
          offset: this.offset,
          sortColumn: this.sortColumn,
          sortDirection: this.sortDirection,
          candidateId: this.$route.query.candidateId ? this.$route.query.candidateId : null,
        };
      },
      watchLoading(isLoading) {
        this.loading = isLoading;
      },
      result(result) {
        this.loading =
          typeof result.data === "undefined" && result.networkStatus === 1;
      }
    },
    allProgramModels: {
      query: ALL_PROGRAM_MODELS,
      variables() {
        return {
          ...this.filters,
          limit: 10,
          offset: this.offsetModel,
          sortColumn: this.sortModelColumn,
          sortDirection: this.sortModelDirection
        };
      },
      watchLoading(isLoading) {
        this.loading = isLoading;
      },
      result(result) {
        this.loading =
          typeof result.data === "undefined" && result.networkStatus === 1;
      }
    }
  },
  computed: {
    listItems() {
      return this.allPrograms.items.map(item => ({
        id: item.id,
        name: item.name,
        invited: item.users.items && item.users.items.length > 0 ? item.users.items[0].firstName + ' ' + item.users.items[0].lastName.toUpperCase() : '',
        status: PROGRAM_STATUS[item.status],
        period: item.dateStart
          ? localMoment(item.dateStart).format("DD/MM/YYYY") +
            " au " +
            localMoment(item.dateEnd).format("DD/MM/YYYY")
          : "",
        updatedAt: localMoment(
          item.updatedAt ? item.updatedAt : item.createdAt
        ).format("DD/MM/YYYY"),
        _actions: {
          edit: this.me.rights.includes('ROLE_UPDATE_PROGRAM'),
          delete: this.me.rights.includes('ROLE_DELETE_PROGRAM'),
        }
      }));
    },
    listModelItems() {
      return this.allProgramModels.items.map(item => ({
        id: item.id,
        name: item.name,
        description: item.description,
        programCount: item.programs.count,
        updatedAt: localMoment(
          item.updatedAt ? item.updatedAt : item.createdAt
        ).format("DD/MM/YYYY")
      }));
    },
    isAdmin() {
      return this.me.type === "admin";
    }
  },
  created() {
    if (this.isAdmin) {
      this.filterList.push({ key: "model", type: "checkbox", label: "Modèle" });
    }
  },
  methods: {
    updateSort($event) {
      this.sortColumn = $event.column;
      this.sortDirection = $event.direction;
      this.resetOffset();
    },
    updateModelSort($event) {
      this.sortModelColumn = $event.column;
      this.sortModelDirection = $event.direction;
      this.resetOffset();
    },
    updatePage(page) {
      this.offset = (page - 1) * 10;
    },
    updateModelPage(page) {
      this.offsetModel = (page - 1) * 10;
    },
    updateFilters(filters) {
      this.filters = { ...filters };
      this.resetOffset();
    },
    resetOffset() {
      this.offset = 0;
      this.offsetModel = 0;
      this.$refs.list.resetPage();
    },
    closeDeleteModal() {
      this.deleteModal.show = false;
    },
    showDeleteModal(program) {
      if (!program.status && program.programCount) {
        iziToast.error({
          position: 'topRight',
          title: 'Erreur',
          message: "Nous ne pouvons pas supprimer car il a des programmes."
        });
        return;
      }
      this.deleteModal.program = program;
      this.deleteModal.show = true;
    },
    deleteProgram() {
      this.loading = true;
      this.closeDeleteModal();
      let isModel = this.deleteModal.program.status ? false : true; 
      this.$apollo
        .mutate({
          mutation: isModel ? DELETE_PROGRAM_MODEL : DELETE_PROGRAM,
          variables: isModel ? {programModelId: this.deleteModal.program.id} : {programId: this.deleteModal.program.id},
          update(proxy) {
            deleteQueriesFromApolloCache(proxy, isModel ? "allProgramModels" : "allPrograms");
            deleteQueriesFromApolloCache(proxy, "allCandidates");
            deleteQueriesFromApolloCache(proxy, "candidateById");
          }
        })
        .then(() => {
          iziToast.success({
            position: "topRight",
            title: "Succès",
            message: "La prestation a bien été supprimée"
          });

          if (isModel) {
            this.$apollo.queries.allProgramModels.refetch();
          } else {
            this.$apollo.queries.allPrograms.refetch();
          }
        });
    }
  }
};
</script>
