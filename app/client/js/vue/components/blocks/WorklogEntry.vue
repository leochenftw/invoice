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
  <v-form v-else>
    <v-text-field
      v-model="start"
      hide-details
    ></v-text-field>
    <v-text-field
      v-model="end"
      hide-details
    ></v-text-field>
  </v-form>
</v-card>
</template>
<script>
export default {
  name: "WorklogEntry",
  props: ['log'],
  data() {
    return {
      edit_mode: false,
      start: null,
      end: null,
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
