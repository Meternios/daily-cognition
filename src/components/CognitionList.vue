<template>
  <v-container>
    <v-row>
      <v-col
        v-for="(cognition, index) in cognitions"
        :key="cognition.cognition.ID"
        cols="12"
        sm="4"
      >
        <v-card>
          <v-card-text>
            <v-chip-group active-class="primary--text" column>
              <v-chip v-for="tag in cognition.tags" :key="tag.ID">
                {{ tag.Name }}
              </v-chip>
            </v-chip-group>
            <p class="display-1 text--primary">
              {{ cognition.cognition.Title }}
            </p>
            <div class="text--primary">
              {{ cognition.cognition.Description }}
            </div>
          </v-card-text>
          <v-card-actions>
            <v-btn text color="teal accent-4">
              See more
            </v-btn>
            <v-btn text color="teal accent-4" @click="editCognition(cognition)">
              Edit
            </v-btn>
            <v-btn text color="teal accent-4" @click="deleteCognition(index, cognition.cognition.ID)">
              Delete
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  name: "CognitionList",

  props: ["cognitions"],

  methods: {
    deleteCognition: function(key, cogID){
      this.$emit("removeData", {'key':key, 'cogID':cogID});
    },
    editCognition: function(cognition){
      this.$emit("editData", {'cognition':cognition});
    },
  },

  data: () => ({}),
};
</script>
