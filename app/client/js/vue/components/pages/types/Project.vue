<template>
<section class="section">
  <v-row
    class="flex-nowrap root"
  >
    <v-col
      cols="12"
      sm="3"
      v-for="workflow in site_data.workflows"
      :key="workflow.id"
    >
      <Workflow :workflow="workflow" />
    </v-col>
    <v-col
      cols="12"
      sm="3"
    >
      <v-btn
        width="100%"
        v-if="!ShowNewWorkflowForm"
        @click.prevent="ShowNewWorkflowForm = true"
      >
        <v-icon
          left
          dark
        >
          mdi-plus
        </v-icon>
        Add another list
      </v-btn>
      <v-card
        v-if="ShowNewWorkflowForm"
        v-click-outside="closeNewWorkflowForm"
      >
        <v-card-text>
          <v-form
            @submit.prevent="CreateWorkflow"
          >
            <v-text-field
              v-model="NewWorkflow"
              label="Enter workflow title..."
              hide-details
              autocomplete="off"
              ref="input_new_workflow"
            ></v-text-field>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn
            class="primary"
            left
            dark
            type="submit"
          >Add Workflow</v-btn>
          <v-btn
            icon
            @click.prevent="closeNewWorkflowForm"
          >
            <v-icon dark>
              mdi-close
            </v-icon>
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-col>
  </v-row>
</section>
</template>
<script>
import Workflow from "../../blocks/Workflow"
export default {
  name: "Project",
  components: { Workflow },
  data() {
    return {
      NewWorkflow: null,
      ShowNewWorkflowForm: false,
    }
  },
  computed: {
    Menu() {
      return [
        {
          title: 'Edit',
          method: this.edit
        },
        {
          title: 'Delete',
          method: this.delete
        }
      ]
    }
  },
  created() {
    this.$store.state.page_menu = this.Menu
  },
  watch: {
    ShowNewWorkflowForm(nv) {
      if (nv) {
        this.$nextTick().then(() => {
          this.$refs.input_new_workflow.focus()
        })
      }
    }
  },
  methods: {
    closeNewWorkflowForm() {
      this.ShowNewWorkflowForm = false
    },
    edit() {
      console.log('edit');
    },
    delete() {
      console.log('delete');
    },
    CreateWorkflow() {
      const data = new FormData()
      data.append("project_id", this.site_data.id)
      data.append("title", this.NewWorkflow)
      this.NewWorkflow = null
      this.closeNewWorkflowForm()
      this.$store.dispatch("createWorkflow", data).then(resp => {
        this.$store.dispatch("setSiteData", resp.data)
      }).catch(error => {
        console.error(error)
      })
    }
  }
}
</script>
<style lang="scss" scoped>
.row.root {
  min-height: calc(100vh - 88px);
  overflow: hidden;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}
</style>

