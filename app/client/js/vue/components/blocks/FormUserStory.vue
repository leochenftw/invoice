
<template>
<v-dialog
  v-model="dialog"
  max-width="600px"
  :persistent="submitting || retrieving"
>
  <v-card v-if="!submitting && fullstory" tag="form" @submit.prevent="submit">
    <v-card-title>
      <v-text-field
        label="Title*"
        required
        v-model="fullstory.title"
        readonly
      ></v-text-field>
    </v-card-title>
    <v-card-text>
      <v-row>
        <v-col cols="12">
          <v-textarea
            label="Description"
            v-model="fullstory.content"
            hide-details
          ></v-textarea>
        </v-col>
        <v-col>
          <v-text-field
            label="Hours Allocated"
            v-model="fullstory.hours_allocated"
            hide-details
          ></v-text-field>
        </v-col>
        <template v-if="islogginghours">
          <v-col>
            <v-text-field
              label="Entering hours"
              v-model="logged_hours"
              ref="manual_hour_input"
              hide-details
              @keydown="enteringHours"
            ></v-text-field>
          </v-col>
          <v-col cols="auto" class="pl-0 pr-0">
            <v-btn
              icon
              class="icon-button-offset"
              @click.prevent="addHours"
            >
              <v-icon>mdi-check</v-icon>
            </v-btn>
          </v-col>
          <v-col cols="auto" class="pl-0">
            <v-btn
              icon
              class="icon-button-offset"
              @click.prevent="islogginghours = false"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-col>
        </template>
        <template v-else>
          <v-col>
            <v-text-field
              label="Hours spent"
              v-model="fullstory.hours_spent"
              readonly
              hide-details
            ></v-text-field>
          </v-col>
          <v-col cols="auto" class="pl-0 pr-0">
            <v-btn
              icon
              class="icon-button-offset"
            >
              <v-icon>mdi-alarm</v-icon>
            </v-btn>
          </v-col>
          <v-col cols="auto" class="pl-0">
            <v-btn
              icon
              class="icon-button-offset"
              @click.prevent="islogginghours = true"
            >
              <v-icon>mdi-plus-circle</v-icon>
            </v-btn>
          </v-col>
        </template>
        <v-col cols="12" v-if="fullstory && fullstory.logs.length">
          <h3 class="mb-2">Work log</h3>
          <div class="worklogs">
            <WorklogEntry
              v-for="log in fullstory.logs"
              :key="log.id"
              :log="log"
            />
          </div>
        </v-col>
      </v-row>
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
  <v-card
    v-else
    dark
  >
    <v-card-text>
      <span v-if="!fullstory">loading full story...</span>
      <span v-else>submitting</span>
      <v-progress-linear
        indeterminate
        color="white"
        class="mb-0"
      ></v-progress-linear>
    </v-card-text>
  </v-card>
</v-dialog>
</template>

<script>
import WorklogEntry from "./WorklogEntry"
export default {
  name: "FormUserStory",
  props: ["story"],
  components: { WorklogEntry },
  data() {
    return {
      retrieving: true,
      fullstory: null,
      submitting: false,
      dialog: false,
      islogginghours: false,
      logged_hours: null,
    }
  },
  watch: {
    dialog(nv) {
      if (nv && this.story) {
        this.fetchFullstory()
      }
    },
    islogginghours(nv) {
      if (nv) {
        this.$nextTick().then(() => {
          this.$refs.manual_hour_input.focus()
        })
      } else {
        this.logged_hours = null
      }
    }
  },
  methods: {
    enteringHours(e) {
      if (e.keyCode == 13) {
        e.preventDefault()
        this.addHours()
      }
    },
    addHours() {
      const data = {
        id: this.story.id,
        data: new FormData()
      }

      data.data.append("hours", this.logged_hours)

      this.islogginghours = false

      this.$store.dispatch("addHours", data).then(resp => {
        this.fullstory = resp.data
      })
    },
    fetchFullstory() {
      this.$store.dispatch("getFullstory", {id: this.story.id}).then(resp => {
        this.fullstory = resp.data
      })
    },
    submit() {
      this.submitting = true

      const data = {
        id: this.story.id,
        data: new FormData(),
      }

      for (let key in this.fullstory) {
        data.data.append(key, this.fullstory[key])
      }

      this.$store.dispatch("updateUserStory", data).then(resp => {
        this.dialog = false
        setTimeout(() => {
          this.submitting = false
          this.fullstory = null
        }, 300);
      }).catch(error => {
        this.submitting = false
      })
    }
  }
}
</script>
<style lang="scss" scoped>
.icon-button-offset {
  transform: translateY(1rem);
}
.worklogs {
  max-height: 10rem;
  padding: 6px 2px;
  margin-left: -2px;
  margin-right: -2px;
  overflow: hidden;
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}
</style>
