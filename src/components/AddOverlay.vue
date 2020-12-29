<template>
  <v-overlay :z-index="zIndex" :value="overlay">
      <v-card class="white">
        <v-card-title class="primary white--text mb-2"
          >Add Cognition</v-card-title
        >
        <v-card-text>
          <v-form>
            <v-text-field
              label="Title"
              :rules="rules"
              hide-details="auto"
              light
              class="mb-2"
              v-model="title"
            ></v-text-field>
            <v-textarea
              name="input-7-1"
              label="Description"
              light
              counter
              clearable
              clear-icon="mdi-close-circle"
              rows="2"
              row-height="15"
              class="mb-2"
              :rules="rules"
              v-model="description"
            ></v-textarea>
            <v-combobox
              v-model="model"
              :items="items"
              :search-input.sync="search"
              hide-selected
              hint="Maximum of 5 tags"
              label="Add some tags"
              multiple
              light
              persistent-hint
              small-chips
            ></v-combobox>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn class="white--text" color="blue" @click="insertData()">
            Add
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-overlay>
</template>

<script>
import axios from "axios";


export default {
  name: "AddOverlay",

  props: ['zIndex', 'overlay'],

  data: () => ({
    title: "",
    description: "",
    items: ["Gaming", "Programming", "Vue", "Vuetify"],
    model: ["Vuetify"],
    search: null,
    rules: [
      (value) => !!value || "Required.",
      (value) => (value && value.length >= 3) || "Min 3 characters",
    ],
  }),

  methods: {
    insertData: function () {
      axios
        .post("action.php", {
          action: "insert",
          title: this.title,
          description: this.description,
        })
        .then(function (response) {
          alert(response.data.message);
        });
    },
  },

  watch: {
    model(val) {
      if (val.length > 5) {
        this.$nextTick(() => this.model.pop());
      }
    },
  },
};
</script>
