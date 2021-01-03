<template>
  <v-dialog v-model="overlay" width="500">
    <template v-slot:activator="{ on, attrs }">
      <v-btn color="blue" dark fixed bottom right fab v-bind="attrs" v-on="on" @click="getTags()">
        <v-icon>mdi-plus</v-icon>
      </v-btn>
    </template>
    <v-card class="white">
      <v-card-title class="primary white--text mb-2"
        >Add Cognition</v-card-title
      >
      <v-card-text>
        <v-form ref="form" v-model="valid">
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
            v-model="tags"
            :items="items"
            :search-input.sync="search"
            hide-selected
            hint="Maximum of 5 tags"
            label="Add some tags"
            multiple
            light
            :rules="comboboxRules"
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
  </v-dialog>
</template>

<script>
import axios from "axios";

export default {
  name: "AddOverlay",

  data: () => ({
    title: "",
    description: "",
    tags: [],
    search: null,
    valid: false,
    overlay: false,
    items: [],
    rules: [
      (value) => !!value || "Required.",
      (value) => (value && value.length >= 3) || "Min 3 characters",
    ],
    comboboxRules: [(value) => (value && value.length >= 1) || "Min 1 tag"],
  }),

  methods: {
    insertData: function () {
      var self = this;
      this.$refs.form.validate();
      if (this.valid) {
        this.overlay = false;
        axios
          .post(process.env.VUE_APP_API_ENDPOINT, {
            action: "insert",
            title: this.title,
            description: this.description,
            tags: this.tags,
            items: this.items,
          })
          .then(function (response) {
            self.$emit("updateData");
            this.$refs.form.reset();
            console.log(response.data.message);
          });
      }
    },
    getTags: function () {
      axios
        .post(process.env.VUE_APP_API_ENDPOINT, {
          action: "getTags",
        })
        .then((response) => {
          if (response.data) {
            response.data.tags.forEach((entry) => {
              this.items.push(entry);
            });
            console.log(response.data.message)
          }
        });
    },
  },

  watch: {
    tags(val) {
      if (val.length > 5) {
        this.$nextTick(() => this.tags.pop());
      }
    },
  },
};
</script>
