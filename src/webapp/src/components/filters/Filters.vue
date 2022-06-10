<template>
  <div class="filters flex-grow-1">
    <div class="filter-group filter-group--l">
      <span class="text-gris_44 filters-label">Filtres :</span>
      <div
          v-for="filter in filters"
          :key="filter.key"
          :class="'ml-2 ' + filter.classList"
      >
        <component
            :is="filterTypes[filter.type]"
            :id="filter.key"
            class="mr-2 mb-0"
            :label="filter.label"
            @enter="applyFilters"
            :attributes="{...filter.attributes, ...attributes[filter.key]}"
            v-model="filterValues[filter.key]"
            @update-attributes="$event => setFilterAttributes(filter.key, $event)"
            @input="$event => updateFilterValue(filter.key, $event)"
        />
      </div>
      <div class="ml-2 d-flex">
        <button class="btn btn-search-round btn-gris_80 align-self-center" @click="applyFilters">
          <img src="@/assets/img/search-white.svg"/>
        </button>
        <button class="btn btn-outline-dark btn-sm ml-2 btn-search-round align-self-center" @click="resetFilters()">
          <i class="fa fa-undo" aria-hidden="true"></i>
        </button>
      </div>
    </div>
    <div class="filter-group filter-group--sm justify-content-end">
      <button
          @click="filterModalActive = true"
          type="button"
          class="btn bg-transparent text-primary btn-outline-primary ml-4 btn-no-hover btn-no-focus"
      >
        <img class="mr-3" src="@/assets/img/filtre.svg"/>
        Filtres
      </button>

      <mobile-modal :active="filterModalActive" @close="filterModalActive = false">
        <div>
          <span class="font-weight-bold font-size-18">Filtres :</span>
          <div
              v-for="filter in filters"
              :key="filter.key"
              :class="{
                    ...(filter.classList ? filter.classList : {})
                 }"
          >
            <component
                :is="filterTypes[filter.type]"
                :id="filter.key"
                class
                :label="filter.label"
                :value="filterValues[filter.key]"
                :attributes="{...filter.attributes, ...attributes[filter.key]}"
                @input="$event => updateFilterValue(filter.key, $event)"
                @update-attributes="$event => setFilterAttributes(filter.key, $event)"
            />
          </div>
          <button class="btn btn-white-primary btn-block" @click="applyFilters">filtrer</button>
          <button
              class="btn btn-outline-dark btn-block mt-2 btn-white-secondary btn-uppercase"
              @click="resetFilters()"
          >Réinitialiser
          </button>
        </div>
      </mobile-modal>
    </div>
  </div>
</template>
<script>
import text from './text';
import select from './select';
import checkbox from './checkbox';
import MobileModal from "@/components/utils/MobileModal";
import userAutocompleteFilter from "@/components/filters/userAutocompleteFilter";
import documentAutocompleteFilter from "@/components/filters/documentAutocompleteFilter";
import programAutocompleteFilter from "@/components/filters/programAutocompleteFilter";
import datepicker from "@/components/filters/datepicker";
import {ALL_FILTERS} from "@/graphql/filters/filter";

export default {
  name: "List",
  components: {
    MobileModal
  },
  props: {
    name: {
      type: String,
      required: true
    },
    filters: {
      type: Array,
      required: true
    }
  },
  apollo: {
    filterList: {
      query: ALL_FILTERS,
      update: data => data.filters
    }
  },
  data() {
    return {
      filterList: [],
      filterValues: {},
      filterTypes: {
        text: text,
        select: select,
        checkbox: checkbox,
        userAutocomplete: userAutocompleteFilter,
        documentAutocomplete: documentAutocompleteFilter,
        programAutocomplete: programAutocompleteFilter,
        datepicker: datepicker,
      },
      filterModalActive: false,
      attributes: {},
    };
  },
  beforeMount() {
    const data = this.filterList.find(item => item.id === this.name);

    if (data) {
      for (let [key, value] of Object.entries(data.filters)) {
        this.filterValues = {
          ...this.filterValues,
          [value.key]: value.value
        }
        this.attributes[value.key] = value.attributes;
      }
    }
    this.$emit('filter', this.filterValues);
  },
  methods: {
    resetFilters() {
      this.filters.forEach(filter => {
        if (this.filterValues[filter.key]) {
          this.filterValues[filter.key] = filter.type === 'checkbox' ? false : '';
        }
      });

      this.$nextTick(() => {
        this.updateFilterCache(this.name, this.filterValues);
        this.$emit('reset');
        this.$emit('filter', this.filterValues);
        this.updateFilterBox();
      });
    },
    updateFilterValue(key, value) {
      this.filterValues[key] = value;
      this.updateFilterBox();
    },
    applyFilters() {
      this.updateFilterCache(this.name, this.filterValues);
      this.$emit('filter', this.filterValues);
    },
    jsonToCache(json) {
      let array = []
      for (let [key, value] of Object.entries(json)) {
        array.push({
          __typename: 'FilterValue',
          id: key + "_" + this.name,
          key: key,
          value: value,
          attributes: {...this.attributes[key]},
        })
      }
      return array;
    },
    updateFilterCache(key, filterValues) {
      let data = this.$apollo.getClient().cache.readQuery({query: ALL_FILTERS});
      const currentFilter = data.filters.find(item => item.id === key);
      if (currentFilter) {
        currentFilter.filters = this.jsonToCache(filterValues)
      } else {
        data.filters.push({
          __typename: 'Filter',
          id: key,
          filters: this.jsonToCache(filterValues),
        });
      }
      this.$apollo.getClient().cache.writeQuery({query: ALL_FILTERS, data})
    },
    updateFilterBox() {
      this.filterList = this.filterList.map(filter => {
        return {...filter, value: this.filterValues[filter.key]}
      })
    },
    setFilterAttributes(key, attributes) {
      this.attributes[key] = {...attributes};
    }
  },
};
</script>

