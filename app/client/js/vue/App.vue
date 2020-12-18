<template>
<v-app>
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
      v-if="$route.path != '/'"
      left
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
          v-for="n in 5"
          :key="n"
          @click="() => {}"
        >
          <v-list-item-title>Option {{ n }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
  </v-app-bar>

  <v-main>
    <v-container fluid>
      <router-view></router-view>
    </v-container>
  </v-main>

  <v-footer app>

  </v-footer>
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
      this.$store.dispatch("getPageData", to.fullPath)
    }
  },
  created() {
    console.log(this.site_data)
  }
}
</script>
