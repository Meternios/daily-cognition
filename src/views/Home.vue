<template>
  <div class="home">
    <CognitionList />
    <v-btn color="blue" dark fixed bottom right fab @click="getTags()">
      <v-icon>mdi-plus</v-icon>
    </v-btn>
    <AddOverlay :overlay="overlay" :z-index="zIndex" :items="items" @updateOverlay="updateOverlay"/>
  </div>
</template>

<script>
// @ is an alias to /src
import CognitionList from "@/components/CognitionList.vue";
import AddOverlay from "@/components/AddOverlay.vue";
import axios from "axios";

export default {
  name: "Home",
  components: {
    CognitionList,
    AddOverlay,
  },

  data: () => ({
    overlay: false,
    zIndex: 0,
    items: [],
  }),

  methods: {
    getTags: function(){
      this.overlay = true;
      axios
        .post("/api", {
          action: "getTags",
        })
        .then(response => {
          if(response.data){
            response.data.forEach(entry => {
              this.items.push(entry);
            });
          }
        });
    },
    updateOverlay(overlayStatus){
        this.overlay=overlayStatus;
    }
  }
};
</script>