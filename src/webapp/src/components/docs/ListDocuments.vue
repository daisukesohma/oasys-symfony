<template>
  <div class="list-documents">
    <h2 class="list-documents--title">Documents</h2>

    <div class="row list-documents--list" v-if="loading">
      <div class="col-sm-12">
        Loading...
      </div>
    </div>

    <div class="row list-documents--list" v-if="!loading">
      <div class="col-sm-12 col-md-6 col-lg-3" v-for="(item, index) in items" :key="index">
        <div
          class="list-document"
          :class="{ 'current-view': current == index}"
          @click="seeDocument(index)"
        >
          <span class="list-document--status protected" v-if="item.visibility === 'protected'">protégé</span>
          <span class="list-document--status private" v-else-if="item.visibility === 'private'">privé</span>
          <span class="list-document--status" v-else>public</span>
          <img :src="img(item.fileDescriptor.name)" alt="" />
          <p class="list-document--title">
            {{ item.name }}
          </p>

          <div class="d-flex justify-content-center" v-if="item.tags">
            <span class="list-document--tag">{{ item.tags.split(",")[0] }}</span>
          </div>
        </div>
      </div>
    </div>
    <div class="mt-4 text-center">
      <paginate
          v-if="items.length"
          :value="page"
          :page-count="totalPages"
          :click-handler="$event => paginate($event)"
          container-class="pagination justify-content-center"
          prev-text="chevron_left"
          next-text="chevron_right"
          page-class="page-item"
          page-link-class="page-link"
          prev-link-class="page-link previous md-icon"
          next-link-class="page-link next md-icon"
      />
    </div>
  </div>
</template>
<script>
import Paginate from "vuejs-paginate";
import {FILE_TYPES} from "@/enum/fileTypesEnum";

export default {
  name: "ListDocuments",
  components: {
    Paginate
  },
  props: {
    items: {
      type: Array,
      default: () => [],
    },
    current: {
      type: Number,
      default: null,
    },
    total: {
      type: Number,
      required: true
    },
    perPage: {
      type: Number,
      default: 10
    },
    loading: {
      type: Boolean,
    },
  },
  data: () => ({
    page: 1,
    totalPages: 1,
  }),
  watch: {
    total() {
      this.totalPages = Math.ceil(this.total / this.perPage);
    },
  },
  mounted() {
    this.totalPages = Math.ceil(this.total / this.perPage);
  },
  methods: {
    img(name) {
      let ext = name.split(".").pop();
      let type = ext && FILE_TYPES[ext] ? ext : "pdf";
      return require('@/assets/img/' + FILE_TYPES[type]);
    },
    seeDocument(index) {
      this.$emit("see", index);
    },
    paginate(page) {
      this.page = page;
      this.$emit('paginate', page);
    },
  },
};
</script>