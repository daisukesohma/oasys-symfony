<template>
  <dash-wrap mode="candidate" article="" :title="'Contactez-nous'">
    <div class="container text-left">
      <div class="content">
        <page-title title="Contactez-nous" @back="$router.push({name: 'CandidateHome'})" />
        <div class="form-row">
          <div class="col-md-12 form-group">
            <label>Votre demande</label>
            <textarea v-model="comment" class="form-control" />
          </div>
        </div>
        <div class="form-btn-wrap mt-4 text-center">
          <button
                  type="button"
                  class="btn btn-outline-consultant-light"
                  @click="$router.push({name: 'CandidateHome'})">Annuler</button>
          <button
                  type="button"
                  class="btn btn-gradient-primary"
                  @click="submit">Envoyer</button>
        </div>
      </div>
    </div>
  </dash-wrap>
</template>
<script>
  import {LOGGED_USER} from "@/graphql/security/logged-user-query";
  import izitoast from "izitoast";
  import {CONTACT_US} from "@/graphql/user/contact-us-mutation";

  export default {
    name: "ContactUs",
    components: {
    },
    data: () => ({
      me: {},
      comment: '',
    }),
    apollo: {
      me: {
        query: LOGGED_USER,
      },
    },
    methods: {
      submit () {
        if (!this.comment) {
          izitoast.error({
            position: 'topRight',
            title: 'Erreur',
            message: "Un commentaire est requis",
          });
          return;
        }

        izitoast.success({
          position: 'topRight',
          title: 'Succès',
          message: "Votre e mail a été envoyé!",
        });

        this.$apollo.mutate({
          mutation: CONTACT_US,
          variables: {
            comment: this.comment,
          },
        });

        this.$router.push({name: 'CandidateHome'});
      }
    }
  };
</script>