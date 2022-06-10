<template>
  <div class="custom-form coache-add-file col-sm-2 mt-2">
    <label :for="id">
      <img src="@/assets/img/docs-btn-add-default.svg" alt />
    </label>
    <input @change="addFile" :id="id" type="file" />
  </div>
</template>
<script>
export default {
  name: "AddFile",
  computed: {
    id () {
      return 'file-id-' + Math.random();
    }
  },
  methods: {
    addFile($event) {
      var f = $event.target.files[0];
      console.log(f);
      
      if (!f || !f.size) {
        return;
      }

      this.$emit("added", f);
    }
  }
};
</script>