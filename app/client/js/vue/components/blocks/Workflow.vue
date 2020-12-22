<template>
<v-card v-if="workflow">
  <v-card-title>
    {{ workflow.title }}
    <v-spacer></v-spacer>
    <v-menu
      right
      bottom
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          icon
          v-bind="attrs"
          v-on="on"
        >
          <v-icon>mdi-dots-vertical</v-icon>
        </v-btn>
      </template>

      <v-list>
        <v-list-item
          v-for="n in 10"
          :key="n"
          @click.prevent="() => {}"
        >
          <v-list-item-title>{{ `${n} option` }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
  </v-card-title>
  <v-card-text>
    <draggable class="list-group" :list="workflow.stories" group="stories" @change="log">
      <v-card
        class="user-story"
        v-for="item in workflow.stories"
        :key="item.id"
      >
        <v-card-title>{{ item.title }}</v-card-title>
      </v-card>
    </draggable>
    <v-form
      v-if="AddNew"
      @submit.prevent="addNewUserStory"
      v-click-outside="closeForm"
    >
      <v-textarea
        placeholder="Enter title of this card"
        hide-details="true"
        rows="2"
        v-model="UserStoryTitle"
        @keydown="keydownHandler"
        ref="inputfield"
      ></v-textarea>
      <v-btn
        class="primary mt-2"
        type="submit"
      >
        Add card
      </v-btn>
      <v-btn
        class="mt-2"
        icon
        @click.prevent="AddNew = false"
      >
        <v-icon dark>
          mdi-close
        </v-icon>
      </v-btn>
    </v-form>
  </v-card-text>
  <v-card-actions>
    <v-btn
      v-if="!AddNew"
      width="100%"
      elevation="0"
      @click.prevent="AddNew = true"
    >
      <v-icon
        left
        dark
      >
        mdi-plus
      </v-icon>
      Add another card
    </v-btn>
  </v-card-actions>
</v-card>
</template>

<script>
import draggable from "vuedraggable"

export default {
  name: "Workflow",
  props: ['workflow'],
  components: {
    draggable
  },
  data() {
    return {
      AddNew: false,
      UserStoryTitle: null,
    }
  },
  watch: {
    AddNew(nv) {
      if (nv) {
        this.$nextTick().then(() => {
          if (this.$refs.inputfield) {
            this.$refs.inputfield.focus()
          }
        })
      }
    }
  },
  methods: {
    closeForm() {
      if (this.$refs.inputfield) {
        this.AddNew = false
      }
    },
    keydownHandler(event) {
      if (event.keyCode === 13) {
        this.addNewUserStory()
      }
    },
    addNewUserStory() {
      const data = new FormData()

      data.append("id", this.workflow.id)
      data.append("title", this.UserStoryTitle)

      this.AddNew = false

      this.$store.dispatch("createWorkflowUserStory", data).then(resp => {
        this.UserStoryTitle = null
        this.workflow.stories.push(resp.data)
      }).catch(console.error)
    },
    log: function(e) {
      console.log(e)
    }
  }
}
</script>
<style lang="scss" scoped>
.v {
  &-card {
    &__title {
      font-size: 14px;
      padding: 0 0 0 12px;
    }

    &__actions,
    &__text {
      padding-left: 12px;
      padding-right: 12px;
    }

    &__text {
      padding-bottom: 0;
    }

    &.user-story {
      &:not(:last-child) {
        margin-bottom: .5rem;
      }
    }
  }
}
</style>
