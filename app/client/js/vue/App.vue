<template>
<v-app :class="{'has-bg': site_data ? site_data.background : false}">
  <v-navigation-drawer
    app
    absolute
    temporary
    v-model="drawer"
  >
    <v-list-item>
      <v-list-item-content>
        <v-list-item-title class="title">
          Application
        </v-list-item-title>
        <v-list-item-subtitle>
          subtext
        </v-list-item-subtitle>
      </v-list-item-content>
    </v-list-item>
    <v-divider></v-divider>

    <v-list
      dense
      nav
    >
      <v-list-item
        v-for="item in items"
        :key="item.title"
        :to="item.route"
      >
        <v-list-item-icon>
          <v-icon>{{ item.icon }}</v-icon>
        </v-list-item-icon>

        <v-list-item-content>
          <v-list-item-title>{{ item.title }}</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </v-list>
    <template v-slot:append>
      <div class="pa-2">
        <v-btn block>
          Logout
        </v-btn>
      </div>
    </template>
  </v-navigation-drawer>

  <v-app-bar
    app
  >
    <v-app-bar-nav-icon @click.prevent="drawer = !drawer"></v-app-bar-nav-icon>
    <h1 v-if="site_data" class="v-toolbar__title">{{ site_data.title }}</h1>
    <v-spacer></v-spacer>
    <v-menu
      v-if="$store.state.page_menu.length"
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
          v-for="item in $store.state.page_menu"
          :key="item.title"
          @click.prevent="item.method"
        >
          <v-list-item-title>{{ item.title }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
  </v-app-bar>

  <v-main>
    <v-container class="root" fluid>
      <router-view></router-view>
    </v-container>
  </v-main>
</v-app>
</template>
<script>
export default {
  name: "App",
  data () {
    return {
      drawer: false,
      items: [
        { title: 'Dashboard', icon: 'mdi-view-dashboard', route: "/" },
        { title: 'My Account', icon: 'mdi-account', route: "/me" },
        { title: 'Projects', icon: 'mdi-code-braces', route: "/projects" },
        { title: 'Invoice', icon: 'mdi-currency-usd', route: "/invoices" },
        { title: 'Users', icon: 'mdi-account-group-outline', route: "/users" },
      ],
    }
  },
  watch: {
    $route(to) {
      this.$store.state.page_menu = [];
      this.$store.dispatch("getPageData", to.fullPath)
    }
  },
  created() {
    this.$store.dispatch("setSiteData", window.appInitialData)
    console.log(this.site_data)
  },
  mounted() {
    if (this.site_data) {
      document.title = this.site_data.title
    }
  }
}
</script>
<style lang="scss" scoped>
.container {
  &.root {
    padding-top: 0;
    padding-bottom: 0;
  }
}
</style>
