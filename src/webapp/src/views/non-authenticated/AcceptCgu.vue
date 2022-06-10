<template>
  <auth-wrap page="conditions">
    <div class="auth-form-wrap">
      <form class="form-signin">
        <div class>
          <div class="text-center my-3">
            <p
              v-show="displaySuccess"
              class="alert alert-success"
            >Success. Redirecting to dashboard.</p>
            <p v-show="acceptError" class="alert alert-danger">An error occured.</p>
          </div>
          <div class="conditions-card text-justify">
            <h5 class="text-uppercase mx-4 mb-4 title-text">Conformité avec le Règlement Général sur la Protection des Données (RGPD)</h5>
            <perfect-scrollbar style="height:315px">
              <vue-markdown>{{this.cguText}}</vue-markdown>
            </perfect-scrollbar>
          </div>
        </div>

        <div class="btn-wrap mt-4 d-flex">
          <a href="/#/connexion" class="btn btn-white-secondary wide bg-white">Refuser</a>
          <button
            class="btn btn-white-primary wide"
            @click.prevent="submit"
          >Accepter</button>
        </div>
      </form>
    </div>
  </auth-wrap>
</template>

<script>
import { PerfectScrollbar } from "vue2-perfect-scrollbar";
import {LOGGED_USER} from "@/graphql/security/logged-user-query";
import {ACCEPT_CGU} from "@/graphql/security/accept-cgu-mutation";
import VueMarkdown from 'vue-markdown'
import CguText from '@/assets/markdowns/CguText.md'
import deleteQueriesFromApolloCache from "@/utils/deleteQueriesFromApolloCache";

export default {
  name: "AcceptCgu",
  components: {
    PerfectScrollbar,
    VueMarkdown
  },
  data() {
    return {
      cguAccepted: false,
      displaySuccess: false,
      acceptError: false,
      router: this.$router,
      cguText: CguText
    };
  },
  apollo: {
    me: {
      query: LOGGED_USER
    }
  },
  methods: {
    submit() {
      this.$apollo.mutate({
          mutation: ACCEPT_CGU,
          update(cache) {
              const data = cache.readQuery({query: LOGGED_USER});
              if (!data.me.id) {
                deleteQueriesFromApolloCache(cache, 'me');
              } else {
                data.me.cguAccepted = true;
                cache.writeQuery({query: LOGGED_USER, data})
              }
          }
        }).then(() => {
          this.displaySuccess = true;
          this.$nextTick(() => {
            this.$apollo.query({
              query: LOGGED_USER,
            }).then(response => {
              let me = response.data.me;
              if (me.type === 'admin') {
                this.$router.push({name: 'Users'});
              } else if (me.type === 'candidate') {
                this.$router.push({name: 'CandidateHome'});
              } else if (me.type === 'coach') {
                this.$router.push({name: 'CoachDashboard'});
              }
            });
          });
        });
    }
  }
};
</script>

<style scoped>
  .title-text{
    text-align: center;
  }
</style>
