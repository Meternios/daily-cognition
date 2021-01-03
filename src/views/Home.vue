<template>
  <v-card height="100vh" v-if="showLogin" class="d-flex justify-center align-center">
      <GoogleLogin @done="onUserLoggedIn"/>
  </v-card>
  <div class="home" style="min-height: 100vh" v-else>
    <CognitionList :cognitions="cognitions" @removeData="removeData"/>
    <AddOverlay
      @updateData="updateData"
    />
  </div>
</template>

<script>
// @ is an alias to /src
import GoogleLogin from "@/components/GoogleLogin.vue";
import CognitionList from "@/components/CognitionList.vue";
import AddOverlay from "@/components/AddOverlay.vue";
import axios from "axios";

export default {
  name: "Home",
  components: {
    GoogleLogin,
    CognitionList,
    AddOverlay,
  },

  data: () => ({
    showLogin: true,
    cognitions: [],
  }),

  created: function () {
    axios
        .post(process.env.VUE_APP_API_ENDPOINT, {
          action: "getLoggedInUser",
        })
        .then((response) => {
          if (response.data) {
            this.showLogin = !response.data.loggedIn;
            this.cognitions = response.data.cognitions;
            console.log(response.data.message)
          }
        })
        .catch(error => console.log(error));
        
  },

  methods: {
    updateData() {
      axios
        .post(
          process.env.VUE_APP_API_ENDPOINT,
          {
            action: "fetchData",
          }
        )
        .then((response) => {
          this.cognitions = response.data.cognitions;
          console.log(response.data.message);
        });
    },
    removeData(params) {
      this.cognitions.splice(params.key, 1);
      axios
        .post(
          process.env.VUE_APP_API_ENDPOINT,
          {
            action: "delete",
            cogID: params.cogID,
          }
        )
        .then((response) => {
          console.log(response.data.message);
        });
    },
    onUserLoggedIn(user) {
      var profile = user.getBasicProfile();

      axios
        .post(
          process.env.VUE_APP_API_ENDPOINT,
          {
            action: "googleSignin",
            user_id: profile.getId(),
          }
        )
        .then((response) => {
          this.showLogin = false;
          this.cognitions = response.data.cognitions;
          console.log(response.data.message);
        });
    },
  },
};
</script>