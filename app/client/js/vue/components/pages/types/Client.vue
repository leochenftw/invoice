<template>
<section class="section" v-if="site_data">
  <v-container>
    <v-form method="post" @submit.prevent="submit">
      <v-row>
        <v-col
          cols="12"
          sm="4"
        >
          <v-text-field
            label="Client name"
            v-model="site_data.title"
            required
          ></v-text-field>
          <v-text-field
            label="Code"
            v-model="site_data.code"
            required
          ></v-text-field>
          <v-text-field
            label="Business name"
            v-model="site_data.entity"
            required
          ></v-text-field>
          <v-text-field
            label="Email"
            v-model="site_data.email"
            required
          ></v-text-field>
          <v-text-field
            label="Phone"
            v-model="site_data.phone"
          ></v-text-field>
          <v-textarea
            label="Address"
            v-model="site_data.address"
          ></v-textarea>
          <div class="actions text-right">
            <v-btn
              color="blue darken-1"
              text
              type="submit"
            >
              Update
            </v-btn>
          </div>
        </v-col>
        <v-col cols="auto">
          <v-divider vertical></v-divider>
        </v-col>
        <v-col>
          <h2 class="text-h4">Client activities</h2>
          <v-row v-if="!activities">
            <v-col cols="12" >
              <v-progress-circular
                :size="70"
                color="primary"
                indeterminate
              ></v-progress-circular>
            </v-col>
          </v-row>
          <template v-else>
            <v-row>
              <v-col cols="auto">
                <v-subheader class="pl-0 pr-0">Projects to day</v-subheader>
                <p class="text-h2 text-center">{{ activities.projects.count }}</p>
              </v-col>
              <v-col cols="auto"><v-divider vertical></v-divider></v-col>
              <v-col>
                <v-data-table
                  :headers="activities.projects.headers"
                  :items="activities.projects.list"
                >
                  <template v-slot:item.actions="props">
                    <v-btn small icon :to="`/projects/${props.item.slug}`">
                      <v-icon>mdi-magnify</v-icon>
                    </v-btn>
                  </template>
                </v-data-table>
              </v-col>
            </v-row>
            <v-divider></v-divider>
            <v-row>
              <v-col cols="auto">
                <v-subheader class="pl-0 pr-0">Invoices to day</v-subheader>
                <p class="text-h2 text-center">{{ activities.invoices.count }}</p>
              </v-col>
              <v-col cols="auto"><v-divider vertical></v-divider></v-col>
              <v-col>
                <v-data-table
                  :headers="activities.invoices.headers"
                  :items="activities.invoices.list"
                >
                  <template v-slot:item.actions="props">
                    <v-btn small icon :to="`/invoices/${props.item.id}`">
                      <v-icon>mdi-magnify</v-icon>
                    </v-btn>
                  </template>
                  <template v-slot:item.paid="{ item }">
                    {{ item.paid ? 'Yes' : 'No' }}
                  </template>
                </v-data-table>
              </v-col>
            </v-row>
          </template>
        </v-col>
      </v-row>
    </v-form>
  </v-container>
</section>
</template>
<script>

export default {
  name: "Client",
  components: { },
  data() {
    return {
      saving: false,
      activities: null,
    }
  },
  computed: {
    Menu() {
      return []
    }
  },
  created() {
    this.$store.state.page_menu = this.Menu
  },
  mounted() {
    this.$store.dispatch("getClientActivities", this.site_data.slug).then(resp => {
      this.activities = resp.data
    })
  },
  methods: {
    submit() {
      if (this.saving) {
        return false
      }

      this.saving = true

      const data = new FormData()

      data.append("id", this.site_data.id)
      data.append("title", this.site_data.title)
      data.append("code", this.site_data.code)

      if (this.site_data.entity) {
        data.append("entity", this.site_data.entity)
      }

      if (this.site_data.address) {
        data.append("address", this.site_data.address)
      }

      if (this.site_data.email) {
        data.append("email", this.site_data.email)
      }

      if (this.site_data.phone) {
        data.append("phone", this.site_data.phone)
      }

      this.$store.dispatch("updateClient", data).then(resp => {
        this.saving = false
      }).catch(error => {
        this.saving = false
      })
    }
  }
}
</script>
