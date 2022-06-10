<template>
  <div class="coache-card event-card">
    <span class="tag d-block text-right font-weight-bold font-size-14">Atelier</span>
    <div class="d-flex flex-column card-bg">
      <div class="d-flex flex-column align-items-center mt-3">
        <div class="img-wrap">
          <img src="@/assets/img/2.svg" height="64px" width="64px" />
        </div>
        <span class="d-block text-center font-size-18 font-weight-bold">{{info.title}}</span>
        <span class="d-block text-center font-size-14 font-weight-bold text-azure">{{info.date}}</span>
        <span class="d-block text-center font-size-11 font-weight-bold text-gris_70">Société Random</span>
      </div>
      <div class="mt-auto mx-3 mb-3 d-flex justify-content-end align-items-center p-2">
        <div class="text-azure personnes-label">
          <span class="font-size-12 font-weight-bold">{{personnesLength}}</span>
          <span class="pl-1 font-size-10">personnes</span>
        </div>
        <div class="personnes-list ml-2 pr-3">
          <div class="personnes-item" v-for="(item, index) in personnesLength" :key="index">
            <img src="@/assets/img/user-primary.svg" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "CoacheEventCard",
  props: {
    info: {
      type: Object,
      required: true
    }
  },
  computed: {
    personnesLength() {
      return 3;
    }
  }
};
</script>