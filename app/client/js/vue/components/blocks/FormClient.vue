<template>
<v-dialog
  v-model="dialog"
  max-width="600px"
  :persistent="submitting"
>
  <v-card tag="form" @submit.prevent="submit">
    <v-card-title>
      <span class="headline">{{ $route.path.indexOf('/clients') == 0 ? 'New' : 'Edit'}} Client</span>
    </v-card-title>
    <v-card-text>
      <v-container>
        <v-row>
          <v-col cols="6">
            <v-text-field
              label="Title*"
              required
              v-model="client.title"
            ></v-text-field>
          </v-col>
          <v-col cols="6">
            <v-text-field
              label="Client Code*"
              required
              v-model="client.code"
            ></v-text-field>
          </v-col>
          <v-col cols="6">
            <v-text-field
              label="Client Email*"
              required
              v-model="client.email"
            ></v-text-field>
          </v-col>
          <v-col cols="6">
            <v-text-field
              label="Client Phone"
              v-model="client.phone"
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-text-field
              label="Business Name"
              v-model="client.entity"
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-textarea
              label="Client Address"
              v-model="client.address"
            ></v-textarea>
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
  name: "FormClient",
  props: {
    client: {
      type: Object,
      default() {
        return {
          title: null,
          code: null,
          entity: null,
          address: null,
          email: null,
          phone: null,
        }
      }
    }
  },
  data() {
    return {
      submitting: false,
      dialog: false,
    }
  },
  methods: {
    submit() {
      this.submitting = true

      const data = new FormData()

      data.append("title", this.client.title)
      data.append("code", this.client.code)

      if (this.client.entity) {
        data.append("entity", this.client.entity)
      }

      if (this.client.address) {
        data.append("address", this.client.address)
      }

      if (this.client.email) {
        data.append("email", this.client.email)
      }

      if (this.client.phone) {
        data.append("phone", this.client.phone)
      }

      this.$store.dispatch("createClient", data).then(resp => {
        this.dialog = this.submitting = false

        this.client.title = null
        this.client.code = null
        this.client.entity = null
        this.client.address = null
        this.client.email = null
        this.client.phone = null

        if (this.$route.path == '/clients' || this.$route.path == '/clients/') {
          this.$router.push(`/clients/${resp.data.slug}`)
        }
      }).catch(error => {
        this.submitting = false
      })
    }
  }
}
</script>
