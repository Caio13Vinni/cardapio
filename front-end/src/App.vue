<template>

  <NavBar v-if="!ocultarLayout" />

  <router-view />

  <AppFooter v-if="!ocultarLayout" />

</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

import NavBar from './components/NavBar.vue'
import AppFooter from './components/AppFooter.vue'

const route = useRoute()

const ocultarLayout = computed(() => {
  return ['/login', '/cadastro'].includes(route.path)
})
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

#app {
  width: 100%;
}
</style>