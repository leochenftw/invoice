<template>
<section class="section" v-if="site_data">
  <v-container>

  </v-container>
</section>
</template>
<script>

export default {
  name: "Client",
  components: { },
  data() {
    return {
      editmode: false,
      saving: false,
      showMenu: false,
      x: 0,
      y: 0,
      selecetedLog: null,
    }
  },
  computed: {
    Menu() {
      return [
        {
          title: 'Edit',
          method: this.CallEditForm,
        }
      ]
    }
  },
  watch: {
    showMenu(nv) {
      if (!nv) {
        this.selecetedLog = null
      }
    },
    editmode(nv) {
      if (nv) {
        this.site_data.entity_address = this.site_data.entity_address.replace(/\<br \/\>/gi, "")
        this.site_data.content = this.site_data.content.replace(/\<br \/\>/gi, "")
        this.site_data.sidenote = this.site_data.sidenote.replace(/\<br \/\>/gi, "")
      }

      setTimeout(() => {
        this.$store.state.page_menu = this.Menu
      }, 300);
    }
  },
  created() {
    this.$store.state.page_menu = this.Menu
    this.editmode = this.site_data.id ? false : true
  },
  methods: {
    setInvoicePaid(item) {
      if (!confirm("Has this invoice been paid?")) {
        return false
      }

      const data = new FormData()
      data.append("data", JSON.stringify({ id: this.site_data.id }))
      this.$store.dispatch("setInvoicePaid", data).then(resp => {
        item.paid = resp.data.paid
      })
    },
    deleteInvoice(e) {
      if (!confirm("Sure?")) {
        return false
      }
      const data = new FormData()
      data.append("data", JSON.stringify({
        'id': this.site_data.id
      }))

      this.$store.dispatch("DeleteInvoice", data).then(resp => {
        this.saving = false
        this.$router.replace("/invoices")
      }).catch(error => {
        this.saving = false
      })
    },

    exportPDF(e) {
      window.open(`/invoices/${this.site_data.id}/export`, '_blank')
    },
    SaveInvoice(e) {
      this.submit()
    },
    removeHandler(e, item) {
      if (this.selecetedLog) {
        this.site_data.logs.list.splice(this.selecetedLog.index, 1)
      }
    },
    rightClickHandler(e, item) {
      e.preventDefault()
      if (this.editmode) {
        this.selecetedLog = item.item
        this.showMenu = false
        this.x = e.clientX
        this.y = e.clientY
        this.$nextTick(() => {
          this.showMenu = true;
        })
      }
    },
    sumField(key) {
      return this.site_data.logs.list.reduce((a, b) => a + (b[key] || 0), 0)
    },
    submit() {
      if (this.saving) {
        return false
      }

      const data = new FormData()
      data.append("data", JSON.stringify(this.site_data))

      this.$store.dispatch("SaveInvoice", data).then(resp => {
        this.saving = false
        if (!this.site_data.id) {
          this.$router.replace(`/invoices/${resp.data.id}`)
        } else {
          this.editmode = false
        }
      }).catch(error => {
        this.saving = false
      })
    }
  }
}
</script>
