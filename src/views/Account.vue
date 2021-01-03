<template>
  <div style="min-height: 100vh;" class="account d-flex justify-center align-center">
    <GoogleLogin :showLogout="showLogout"/>
  </div>
</template>

<script>
// @ is an alias to /src
import GoogleLogin from "@/components/GoogleLogin.vue";

export default {
  name: "Account",

  components: {
    GoogleLogin,
  },

  data: () => ({
    showLogout: true,
  }),

  methods: {}
};
</script>