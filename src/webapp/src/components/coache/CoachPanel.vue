<template>
  <div class="todo-panel" :class="{ 'active': active }">
    <button @click="togglePanel" class="btn btn-white-primary todo-panel--trigger" v-if="active">
      <img src="@/assets/img/croix-primary.svg" />
    </button>
    <div class="todo-panel--wrap">
      <button class="btn todo-sm-close d-inline-block d-md-none" @click="togglePanel">
        <img src="@/assets/img/croix.svg" alt="">
      </button>
      <h1 class="font-weight-bold font-size-21 text-uppercase">Votre Coach</h1>
      <div class="d-flex align-items-center justify-content-center mt-4">
        <div class="mx-4 user-avatar">
          <img v-if="!coach.profilePicture" src="@/assets/img/user-primary.svg" height="88px" width="88px" />
          <img v-else :src="getImageUrl(coach.profilePicture.id)" height="50px" width="88px" />
        </div>
      </div>
      <div class="text-center mt-2">
        <div class="font-size-18">
          {{ coach.firstName }} {{ coach.lastName }}
        </div>
      </div>
      <div class="px-2 py-2 mt-2 font-size-16">
        <div>
          <img src="@/assets/img/phone-primary.svg" class="mr-3" />
          <span>{{ coach.phone }}</span>
        </div>
        <div class="mt-2">
          <img src="@/assets/img/mail-primary.svg" class="mr-3" />
          <span><a :href="'mailto:' + coach.email">{{ coach.email }}</a></span>
        </div>
        <div class="mt-2">
          <img src="@/assets/img/home-primary.svg" class="mr-3" />
          <span>{{ coach.address }}</span>
        </div>
      </div>
    </div>
    <div class="todo-panel--bd" @click="togglePanel"></div>
  </div>
</template>
<script>
  import { FILE_PATH } from "@/enum/FilePathConstant";

  export default {
    name: "CoachPanel",
    props: {
      active: {
        type: Boolean,
        default: false
      },
      coach: {
        type: Object,
        required: true,
      }
    },
    methods: {
      togglePanel() {
        this.$emit("toggle");
      },
      getImageUrl(profilePhotoId) {
        let baseUrl = process.env.VUE_APP_GRAPHQL_HTTP.substr(
                0,
                process.env.VUE_APP_GRAPHQL_HTTP.lastIndexOf("/")
        );
        return baseUrl + FILE_PATH + profilePhotoId;
      },
    },
  };
</script>