<template>
  <div class="filter-panel" :class="{'active': active}">
      <div class="filter-panel--wrap">
        <h1 class="font-weight-bold font-size-21 text-uppercase">Filters</h1>
      </div>
  </div>
</template>
<script>
export default {
  name: "FilterPanel",
  props: {
    active: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    togglePanel() {
      this.emit("toggle");
    }
  }
};
</script>