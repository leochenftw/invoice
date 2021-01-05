<template>
<section class="section" v-if="site_data">
  <v-container>
    <v-form class="invoice-form" ref="form" method="post" @submit.prevent="submit">
      <v-row>
        <v-col cols="9">
          <template v-if="!editmode">
            <h2 class="h2 entity color--purple">{{ site_data.entity }}</h2>
            <div class="entity-address content" v-html="site_data.entity_address"></div>
          </template>
          <template v-else>
            <v-text-field v-model="site_data.entity" label="Entity"></v-text-field>
            <v-textarea v-model="site_data.entity_address" label="Entity Address" hide-details></v-textarea>
          </template>
        </v-col>
        <v-col cols="3">
          <template v-if="!editmode">
            <p class="tax-no color--purple">{{ site_data.gst_registered ? 'GST' : 'IRD' }} No. {{ site_data.tax_no }}</p>
            <p v-if="!site_data.gst_registered"><strong>Not GST Registered</strong></p>
          </template>
          <template v-else>
            <v-text-field v-model="site_data.tax_no" label="IRD/GST No." hide-details></v-text-field>
            <v-checkbox v-model="site_data.gst_registered" label="is GST registered"></v-checkbox>
          </template>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" class="pb-0"><h2 class="h1 color--dark-blue">Invoice</h2></v-col>
        <v-col cols="9">
          <v-row>
            <v-col v-if="!editmode" class="pt-0 pb-0 pink--text" v-html="site_data.content"></v-col>
            <v-col v-else class="pt-0 pb-0">
              <v-textarea v-model="site_data.content" rows="2" label="Description" hide-details></v-textarea>
            </v-col>
            <v-col v-if="!editmode" class="pt-0 pb-0" v-html="site_data.sidenote"></v-col>
            <v-col v-else class="pt-0 pb-0">
              <v-textarea v-model="site_data.sidenote" rows="2" label="Note" hide-details></v-textarea>
            </v-col>
            <v-col cols="12">
              <h3 class="h4">Invoice to</h3>
              <p class="h3">{{ site_data.client.entity }}</p>
              <div class="content" v-html="site_data.client.address"></div>
            </v-col>
          </v-row>
        </v-col>
        <v-col cols="3">
          <h3 class="h3">Invoice #</h3>
          <p v-if="!editmode">{{ site_data.title }}</p>
          <v-text-field v-else v-model="site_data.title"></v-text-field>
          <h3 class="h3">Due date</h3>
          <p v-if="!editmode">{{ site_data.due }}</p>
          <v-text-field v-else v-model="site_data.due" hide-details type="date"></v-text-field>
        </v-col>
      </v-row>
      <v-divider></v-divider>
      <v-data-table
        :headers="site_data.logs.headers"
        :items="site_data.logs.list"
        hide-default-footer
        disable-pagination
        @contextmenu:row="rightClickHandler"
      >
        <template v-slot:item.hours="{ item }">
          {{ item.hours.toFixed(2) }}
        </template>
        <template v-slot:item.hourly_rate="{ item }">
          {{ $formatter.format(item.hourly_rate) }}
        </template>
        <template v-slot:item.sum="{ item }">
          {{ $formatter.format(item.sum) }}
        </template>
        <template slot="body.append">
            <tr>
                <td class="text-right" colspan="3">Subtotal</td>
                <td class="text-right">{{ $formatter.format(sumField('sum')) }}</td>
            </tr>
            <tr v-if="site_data.gst_registered">
                <td class="text-right" colspan="3">GST</td>
                <td class="text-right">{{ $formatter.format(sumField('sum') * this.site_data.gst_rate) }}</td>
            </tr>
            <tr class="pink--text" v-if="site_data.gst_registered">
                <th class="title text-right" colspan="3">Grand Total</th>
                <th class="title text-right grand-total">{{ $formatter.format(sumField('sum') * (1 + this.site_data.gst_rate)) }}</th>
            </tr>
            <tr class="pink--text" v-else>
                <th colspan="4" class="title text-right grand-total">{{ $formatter.format(sumField('sum')) }}</th>
            </tr>
        </template>
      </v-data-table>
      <v-row class="invoice-footer">
        <v-col v-if="!editmode" cols="12" v-html="site_data.footer"></v-col>
        <v-col v-else cols="12">
          <editor
            v-model="site_data.footer"
            branding: false
            :init="{
              height: 120,
              menubar: false,
              statusbar: false,
              toolbar: false
            }"
          />
        </v-col>
      </v-row>
      <span v-if="site_data.paid" class="stamp is-approved">PAID</span>
    </v-form>
    <v-menu v-model="showMenu" :position-x="x" :position-y="y" absolute offset-y>
      <v-list>
        <v-list-item-group>
          <v-list-item @click.prevent="removeHandler">
            <v-list-item-content>
              <v-list-item-title v-text="`Remove`"></v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list-item-group>
      </v-list>
    </v-menu>
  </v-container>
</section>
</template>
<script>
import Editor from '@tinymce/tinymce-vue'

export default {
  name: "Invoice",
  components: { "editor": Editor },
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
      const menu = []

      if (!this.site_data.id) {
        menu.push(
          {
            title: 'Save',
            method: this.SaveInvoice
          }
        )
      } else {
        if (this.site_data.paid) {
          menu.push(
            {
              title: 'Export',
              method: this.exportPDF
            }
          )
        } else if (!this.editmode) {
          menu.push(
            {
              title: 'Edit',
              method: () => {
                this.editmode = true
              }
            },
            {
              title: 'Set Paid',
              method: this.setInvoicePaid
            },
            {
              title: 'Delete',
              method: this.deleteInvoice
            },
            {
              title: 'Export',
              method: this.exportPDF
            }
          )
        } else {
          menu.push(
            {
              title: 'Save',
              method: this.SaveInvoice
            },
            {
              title: 'Canel',
              method: () => {
                this.editmode = false
              }
            }
          )
        }
      }

      return menu
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
