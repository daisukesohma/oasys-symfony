<template>
    <div class="form-group no-label">
      <el-date-picker
          name="dateEvent"
          :value="attributes.range && value ? value.split(',') : value"
          :type="attributes.range ? 'daterange' : 'datetime'"
          format="dd/MM/yyyy"
          :placeholder="label"
          start-placeholder="du"
          end-placeholder="au"
          @input="onInput"
          popper-class="highlight"
      />
    </div>
</template>
<script>
import localMoment from "@/utils/localMoment";

export default {
  name: "DatepickerFilter",
  props: {
    value: {
      required: true,
    },
    label: {
      type: String,
      required: true,
    },
    attributes: {
      type: Object,
      required: true,
    },
  },
  methods: {
    onInput ($event) {
      this.$emit('input', $event
          ? (typeof $event === 'object' ? $event : [$event]).map(d => localMoment(d).format("YYYY-MM-DD")).join(',')
          : ''
      );
    }
  }
};
</script>

