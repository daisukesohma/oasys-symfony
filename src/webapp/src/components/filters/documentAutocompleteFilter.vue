<template>
  <div class="form-group mr-2 pl-3">
    <document-autocomplete
                       ref="autocomplete"
                       :initial-query="attributes.item ? attributes.item.name : ''"
                       @input="$event => $event ? $emit('input', $event) : null"
                       @item-selected="$event => $emit('update-attributes', {item: $event})"
                       :query-livrable="attributes.queryLivrable"
                       :types="attributes.types"
                       :placeholder="label" />
  </div>
</template>
<script>
import DocumentAutocomplete from "@/components/autocomplete/DocumentAutocomplete";

export default {
  name: "documentAutocompleteFilter",
  components: {
    DocumentAutocomplete
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
      if (!newValue) {
        this.$refs.autocomplete.resetQuery();
      }
    }
  }
};
</script>
