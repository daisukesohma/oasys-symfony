<template>
  <div class="form-group mr-2 pl-3">
    <program-autocomplete
                       ref="autocomplete"
                       :initial-query="attributes.item ? attributes.item.name : ''"
                       @input="$event => $event ? $emit('input', $event) : null"
                       @item-selected="$event => $emit('update-attributes', {item: $event})"
                       :types="attributes.types"
                       :placeholder="label" />
  </div>
</template>
<script>
import ProgramAutocomplete from "@/components/autocomplete/ProgramAutocomplete";

export default {
  name: "programAutocompleteFilter",
  components: {
    ProgramAutocomplete
  },
  props: {
    value: {
      required: false,
    },
    label: {
      type: String,
      required: true,
    },
    attributes: {
      type: Object,
      required: false,
    },
  },
  data: () => ({
    displayValue: '',
  }),
  watch: {
    value (newValue) {
      if (!newValue && this.$refs.autocomplete) {
        this.$refs.autocomplete.resetQuery();
        this.$emit('update-attributes', {item: {}});
      }
    }
  }
};
</script>
