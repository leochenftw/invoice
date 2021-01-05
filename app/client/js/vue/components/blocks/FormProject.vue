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
            <v-combobox
              v-model="client"
              :items="clients"
              label="Client"
              item-text="title"
              item-value="id"
              :loading="searching"
              :search-input.sync="search"
              hide-selected
            ></v-combobox>
          </v-col>
        </v-row>
        <div class="custom-file-container" data-upload-id="myUniqueUploadId">
          <label>
            Project background image
            <a
              href="javascript:void(0)"
              class="custom-file-container__image-clear"
              title="Clear Image"
            >&times;</a>
          </label>
          <label class="custom-file-container__custom-file">
              <input
                type="file"
                class="custom-file-container__custom-file__custom-file-input"
                accept="image/x-png,image/gif,image/jpeg"
                aria-label="Choose File"
                ref="fileuploader"
              />
              <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
              <span
                class="custom-file-container__custom-file__custom-file-control"
              ></span>
          </label>
          <div class="custom-file-container__image-preview"></div>
      </div>
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
import FileUploadWithPreview from "file-upload-with-preview"
export default {
  name: "FormProject",
  data() {
    return {
      submitting: false,
      searching: false,
      dialog: false,
      title: null,
      description: null,
      clients: [],
      client: null,
      search: null
    }
  },
  watch: {
    dialog(nv) {
      if (nv) {
        this.$nextTick().then(() => {
          const upload = new FileUploadWithPreview("myUniqueUploadId")
        })
      }
    },
    search(nv) {
      console.log(this.client)
      if (nv && nv.trim().length) {
        this.searching = true
        this.$store.dispatch("searchClient", nv).then(resp => {
          this.searching = false
          this.clients = resp.data
        }).catch(error => {
          this.searching = false
        })
      }
    }
  },
  methods: {
    submit() {
      this.submitting = true

      const data = new FormData();
      data.append("title", this.title)

      if (this.description) {
        data.append("description", this.description)
      }

      if (this.search && this.search.trim().length && !this.client) {
        this.client = this.search.trim()
      }

      if (this.client) {
        if (typeof this.client === "string") {
          data.append("client", JSON.stringify({id: false, title: this.client}))
        } else {
          data.append("client", JSON.stringify(this.client))
        }
      }

      if (this.$refs.fileuploader.files.length) {
        data.append("bgimage", this.$refs.fileuploader.files[0])
      }

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
