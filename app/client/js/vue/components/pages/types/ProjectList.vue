<template>
<section class="section">
  <v-alert
    v-if="!site_data.list.length"
    type="warning"
    icon="mdi-firework"
    border="left"
  >
    No project, <a style="color: white; text-decoration: underline;" @click.prevent="$refs.form_project.dialog = true">create one first</a>
  </v-alert>
  <template v-else>
    <p class="text-right">
      <v-btn color="primary" @click.prevent="$refs.form_project.dialog = true"><v-icon left>mdi-plus-circle</v-icon> New project...</v-btn>
    </p>
    <v-divider></v-divider>
    <v-row>
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
  </template>
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
}
</script>
