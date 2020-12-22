<template>
<section class="section">
  <v-alert
    v-if="!site_data.list.length"
    type="warning"
    icon="mdi-firework"
    border="left"
  >
    No project, <a style="color: white; text-decoration: underline;" @click.prevent="CalloutProjectForm">create one first</a>
  </v-alert>
  <v-row v-else>
    <v-col
      v-for="item in site_data.list"
      cols="12"
      lg="3"
      md="4"
      sm="6"
      :key="item.id"
    >
      <ProjectCard :project="item" />
    </v-col>
  </v-row>
  <FormProject ref="form_project" />
</section>
</template>
<script>
import FormProject from "../../blocks/FormProject"
import ProjectCard from "../../blocks/ProjectCard"
export default {
  name: "ProjectList",
  components: { FormProject, ProjectCard },
  data () {
    return {}
  },
  computed: {
    Menu() {
      return [
        {
          title: 'New Project...',
          method: this.CalloutProjectForm
        }
      ]
    }
  },
  created() {
    this.$store.state.page_menu = this.Menu
  },
  methods: {
    CalloutProjectForm() {
      this.$refs.form_project.dialog = true
    }
  }
}
</script>
