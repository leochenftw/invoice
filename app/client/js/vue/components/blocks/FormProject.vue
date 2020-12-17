<template>
<v-dialog
  v-model="dialog"
  max-width="600px"
  :persistent="submitting"
>
  <v-card tag="form" @submit.prevent="submit">
    <v-card-title>
      <span class="headline">New project</span>
    </v-card-title>
    <v-card-text>
      <v-container>
        <v-row>
          <v-col cols="12">
            <v-text-field
              label="Title*"
              required
              v-model="title"
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-textarea
              label="Description"
              v-model="description"
            ></v-textarea>
          </v-col>
          <v-col
            cols="12"
          >
            <v-autocomplete
              :items="['Skiing', 'Ice hockey', 'Soccer', 'Basketball', 'Hockey', 'Reading', 'Writing', 'Coding', 'Basejump']"
              label="Client"
            ></v-autocomplete>
          </v-col>
        </v-row>
      </v-container>
      <small>*indicates required field</small>
    </v-card-text>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn
        color="blue darken-1"
        text
        @click="dialog = false"
      >
        Close
      </v-btn>
      <v-btn
        color="blue darken-1"
        text
        type="submit"
      >
        Save
      </v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>
</template>

<script>
export default {
  name: "FormProject",
  data() {
    return {
      submitting: false,
      dialog: false,
      title: null,
      description: null,
    }
  },
  methods: {
    submit() {
      this.submitting = true

      const data = new FormData();
      data.append("title", this.title)
      data.append("description", this.description)

      this.$store.dispatch("createProject", data).then(resp => {
        this.dialog = this.submitting = false
        this.$store.dispatch("setSiteData", resp.data)
      }).catch(error => {
        this.submitting = false
      })
    }
  }
}
</script>
