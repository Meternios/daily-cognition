<template>
  <div ref="signinBtn" class="google-sign-in-wrapper" v-if="!showLogout">
    <v-btn elevation="2">Google Signin</v-btn>
  </div>
  <div class="google-out-in-wrapper" v-else @click="signOut()">
    <v-btn elevation="2">Google Signout</v-btn>
  </div>
</template>

<script>
export default {
  name: "GoogleLogin",

  props: ['showLogout'],

  methods: {
    signOut: function () {
      var gapi;
      var auth2 = gapi.auth2.getAuthInstance();
      auth2.signOut().then(function () {
        console.log('User signed out.');
      });
    }
  },

  mounted() {
    window.gapi.load("auth2", () => {
      const auth2 = window.gapi.auth2.init({
        client_id:
          "936800110040-c1ppem4egh6a8oqb3m2pv2nr4stpmqm8.apps.googleusercontent.com",
        cookiepolicy: "single_host_origin",
      });
      auth2.attachClickHandler(
        this.$refs.signinBtn,
        {},
        (googleUser) => {
          this.$emit("done", googleUser);
        },
        (error) => console.log(error)
      );
    });
  },
};
</script>

<style scoped>
.google-sign-in-wrapper {
  display: inline-block;
}
</style>
