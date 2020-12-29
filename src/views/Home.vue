<template>
  <div class="home">
    <CognitionList />
    <v-btn color="blue" dark fixed bottom right fab @click="overlay = !overlay">
      <v-icon>mdi-plus</v-icon>
    </v-btn>
    <AddOverlay :overlay="overlay" :z-index ="zIndex"/>
  </div>
</template>

<script>
// @ is an alias to /src
import CognitionList from "@/components/CognitionList.vue";
import AddOverlay from "@/components/AddOverlay.vue";

export default {
  name: "Home",
  components: {
    CognitionList,
    AddOverlay,
  },

  data: () => ({
    overlay: false,
    zIndex: 0,
  }),
};
</script>