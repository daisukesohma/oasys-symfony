<template>
  <div class="form-group mr-2 pl-3">
    <user-autocomplete ref="autocomplete"
                       :initial-query="attributes.item ? attributes.item.firstName + ' ' + attributes.item.lastName : ''"
                       @input="$event => $emit('input', $event)"
                       @item-selected="$event => $emit('update-attributes', {item: $event})"
                       :types="attributes.types"
                       :placeholder="label" />
  </div>
</template>
<script>
import UserAutocomplete from "@/components/autocomplete/UserAutocomplete";

export default {
  name: "userAutocompleteFilter",
  components: {UserAutocomplete},
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
      if (!newValue) {
        this.$refs.autocomplete.resetQuery();
      }
    }
  }
};
</script>
