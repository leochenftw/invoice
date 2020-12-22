<template>
<v-card v-if="project" tag="a" :to="`/projects/${project.slug}`">
  <v-img
    class="white--text align-end"
    height="200px"
    :src="project.background"
  >
    <v-card-title>{{ project.title }}</v-card-title>
  </v-img>

  <v-card-subtitle>
    <span v-if="project.client">{{ project.client }}</span>
    <em v-else>client not set</em>
  </v-card-subtitle>
</v-card>
</template>

<script>
export default {
  name: "ProjectCard",
  props: ['project']
}
</script>
