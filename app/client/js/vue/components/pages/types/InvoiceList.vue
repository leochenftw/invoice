<template>
<section class="section">
  <v-data-table
    :headers="site_data.list.headers"
    :items="site_data.list.clients"
    :sort-by="['due']"
    :sort-desc="[true]"
  >
    <template v-slot:item.actions="{ item }">
      <v-btn small icon :to="`/invoices/${item.id}`">
        <v-icon>mdi-magnify</v-icon>
      </v-btn>
    </template>
    <template v-slot:item.hourly_rate="{ item }">
      {{ $formatter.format(item.hourly_rate) }}
    </template>
    <template v-slot:item.paid="{ item }">
      {{ item.paid ? 'Yes' : 'No' }}
    </template>
  </v-data-table>
</section>
</template>
<script>
export default {
  name: "InvoiceList",
  components: {},
  data () {
    return {}
  },
  computed: {
    Menu() {
      return [
        {
          title: 'New Invoice...',
          method: this.CalloutInvoiceform
        }
      ]
    }
  },
  created() {
    this.$store.state.page_menu = this.Menu
  },
  methods: {
    CalloutInvoiceform() {
      this.$router.push('/invoices/new')
    }
  }
}
</script>
