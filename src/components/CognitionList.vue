<template>
  <v-container>
    <v-row>
      <v-col
        v-for="n in 21"
        :key="n"
        cols="12"
        sm="4"
      >
        <v-card>
          <v-card-text>
            <div>Kategory 1</div>
            <p class="display-1 text--primary">Sei dir Treu</p>
            <div class="text--primary">
              relating to or dependent on charity; charitable.<br />
              "an eleemosynary educational institution."
            </div>
          </v-card-text>
          <v-card-actions>
            <v-btn text color="teal accent-4" @click="reveal = true">
              Ansehen
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

  data: () => ({}),
};
</script>
