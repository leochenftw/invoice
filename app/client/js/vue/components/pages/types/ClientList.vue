<template>
<section class="section">
  <v-data-table
    :headers="site_data.list.headers"
    :items="site_data.list.clients"
    :sort-by="['title']"
    :sort-desc="[true]"
  >
    <template v-slot:item.actions="props">
      <v-btn small icon :to="`/invoices/new?client=${props.item.id}`">
        <v-icon>mdi-cash-plus</v-icon>
      </v-btn>
      <v-btn small icon :to="`/clients/${props.item.slug}`">
        <v-icon>mdi-magnify</v-icon>
      </v-btn>
    </template>
  </v-data-table>
  <FormClient ref="form_client" />
</section>
</template>
<script>
import FormClient from "../../blocks/FormClient"
export default {
  name: "ClientList",
  components: { FormClient },
  data () {
    return {}
  },
  computed: {
    Menu() {
      return [
        {
          title: 'New Client...',
          method: this.CalloutClientform
        }
      ]
    }
  },
  created() {
    this.$store.state.page_menu = this.Menu
  },
  methods: {
    CalloutClientform() {
      this.$refs.form_client.dialog = true
    }
  }
}
</script>
