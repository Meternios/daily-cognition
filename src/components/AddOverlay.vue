<template>
  <v-dialog v-model="overlay" width="500">
    <template v-slot:activator="{ on, attrs }">
      <v-btn
        color="blue"
        dark
        fixed
        bottom
        right
        fab
        v-bind="attrs"
        v-on="on"
        @click="getTags()"
      >
        <v-icon>mdi-plus</v-icon>
      </v-btn>
    </template>
    <v-card class="white">
      <v-card-title class="primary white--text mb-2"
        >{{mode}} Cognition</v-card-title
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
            hint="Maximum of 5 tags"
            label="Add some tags"
            multiple
            light
            :rules="comboboxRules"
            persistent-hint
            chips
          >
            <template v-slot:selection="data">
              <v-chip
                :key="JSON.stringify(data.item)"
                v-bind="data.attrs"
                :input-value="data.selected"
                :disabled="data.disabled"
                @click:close="data.parent.selectItem(data.item)"
              >
                <v-avatar
                  class="accent white--text"
                  left
                  v-text="data.item.slice(0, 1).toUpperCase()"
                ></v-avatar>
                {{ data.item }}
              </v-chip>
            </template></v-combobox
          >
        </v-form>
      </v-card-text>
      <v-card-actions>
        <v-btn class="white--text" color="blue" @click="updateData()">
          {{mode}}
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
    mode: "Add",
    tags: [],
    search: null,
    valid: false,
    overlay: false,
    cogId: 0,
    items: [],
    rules: [
      (value) => !!value || "Required.",
      (value) => (value && value.length >= 3) || "Min 3 characters",
    ],
    comboboxRules: [(value) => (value && value.length >= 1) || "Min 1 tag"],
  }),

  methods: {
    updateData: function () {
      this.$refs.form.validate();
      if (this.valid) {
        this.overlay = false;
        if(this.mode == "Add"){
          axios
          .post(process.env.VUE_APP_API_ENDPOINT, {
            action: "insert",
            title: this.title,
            description: this.description,
            tags: this.tags,
            items: this.items,
          })
          .then((response) => {
            this.$emit("updateData");
            this.$refs.form.reset();
            console.log(response.data.message);
          });
        }else if(this.mode == "Update"){
          axios
          .post(process.env.VUE_APP_API_ENDPOINT, {
            action: "update",
            title: this.title,
            description: this.description,
            cogID: this.cogId,
            tags: this.tags,
            items: this.items,
          })
          .then((response) => {
            this.$emit("updateData");
            this.$refs.form.reset();
            console.log(response.data.message);
          });
        }
      }
    },
    getTags: function () {
      this.mode = "Add";
      this.$refs.form.reset();
      axios
        .post(process.env.VUE_APP_API_ENDPOINT, {
          action: "getTags",
        })
        .then((response) => {
          if (response.data) {
            response.data.tags.forEach((entry) => {
              this.items.push(entry);
            });
            console.log(response.data.message);
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
