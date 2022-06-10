<template>
  <div class="coache-title-design d-flex align-items-center overflow-hidden">
    <span class="text-uppercase text-primary font-weight-bold white-space-nowrap">
      <slot />
    </span>
    <img class="flex-grow-1 ml-3" :src="require(`@/assets/img/separateur-primary-wide.svg`)" />
  </div>
</template>
<script>
export default {
  name: "CoacheTitleDesign",
  props: {
    wide: {
      type: Boolean,
      default: true
    }
  },
};
</script>
<style lang="scss" scoped>
span {
  line-height: 19px;
  padding-top: 3px;
}
img {
  object-position: 0%;
  object-fit: contain;
  height: 15px;
}
</style>