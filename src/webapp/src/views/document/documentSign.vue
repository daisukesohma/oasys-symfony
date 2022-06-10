<template>
  <div class="iframe-yousign-container" tabindex="-1" role="dialog">
    <iframe class="iframe-yousign" :src="urlYouSign"></iframe>

    <div class="iframe-yousign-nav-bar">
      <button @click="$router.push($route.query.back ? $route.query.back : '/')" class="btn btn-gradient-primary">
        Revenir à l'accueil
      </button>
    </div>
  </div>
</template>

<script>
    export default {
        name: "DocumentSign",
        computed: {
          urlYouSign() {
            let url = process.env.VUE_APP_YOUSIGN_URL;
            let signatureId = process.env.VUE_APP_YOUSIGN_SIGNATURE_ID;

            return url + "members=/members/" + this.$route.params.member + "&signatureUi=/signature_uis/" + signatureId;
          }
        },
    }
</script>