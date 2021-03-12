<template>
<v-card
  v-if="log"
  class="worklog"
>
  <template v-if="!edit_mode">
    <v-card-title>
      {{ log.hours }} hour{{ log.hours > 1 ? 's' : ''}}
      <v-btn
        class="btn-edit"
        icon
        @click.prevent="edit_mode = true"
      >
        <v-icon>mdi-pencil</v-icon>
      </v-btn>
    </v-card-title>
    <v-card-subtitle>
      <em>{{ log.start }} - {{ log.end }}</em>
    </v-card-subtitle>
  </template>
  <v-form v-else @submit.prevent="saveLog">
    <v-row class="start-end">
      <v-col><v-datetime-picker label="Start" v-model="start" /></v-col>
      <v-col><v-datetime-picker label="End" v-model="end" /></v-col>
      <v-col cols="auto">
        <v-btn
          class="btn-save"
          icon
          outlined
          :disabled="!duration || duration <= 0"
          @click.prevent="edit_mode = false"
        >
          <v-icon>mdi-check</v-icon>
        </v-btn>
      </v-col>
    </v-row>
    <template v-if="duration">
      <p class="duration px-3 mb-0" v-if="duration > 0"><strong>Duration</strong>: {{ duration }} hour{{ duration > 1 ? 's' : '' }}</p>
      <p v-else class="duration px-3 red--text mb-0">End time must be greater than the start time</p>
    </template>
  </v-form>
</v-card>
</template>
<script>
import dayjs from "dayjs"
import duration from "dayjs/plugin/duration"

dayjs.extend(duration)

export default {
  name: "WorklogEntry",
  props: ['log'],
  data() {
    return {
      edit_mode: false,
      start: null,
      end: null,
    }
  },
  computed: {
    duration() {
      if (this.log) {
        return Math.round(dayjs.duration(dayjs(this.end).diff(this.start)).asHours() * 100) * 0.01
      }

      return null
    }
  },
  created() {
    if (this.log) {
      this.start = dayjs(this.log.start).format("YYYY-MM-DD HH:mm")
      this.end = dayjs(this.log.end).format("YYYY-MM-DD HH:mm")
    }
  },
  methods: {
    saveLog() {
      if (!this.duration || this.duration <= 0) {
        return
      }
    }
  }
}
</script>
<style lang="scss" scoped>
.worklog {
  &:not(:last-child) {
    margin-bottom: 0.5rem;
  }

  .v-card {
    &__title {
      padding: 0 12px;
      font-size: 14px;
    }

    &__subtitle {
      font-size: 12px;
      padding: 12px;
    }
  }

  .btn-edit {
    position: absolute;
    top: 12px;
    right: 12px;
  }
}
</style>
