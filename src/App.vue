<template>
  <Login
    v-if="!isAuthenticated"
    @login-success="handleLoginSuccess"
  />

  <Dashboard
    v-else
    @logout="handleLogout"
  />
</template>

<script setup>
import { ref } from 'vue'
import Login from './components/Login.vue'
import Dashboard from './components/Dashboard.vue'

const isAuthenticated = ref(
  !!localStorage.getItem('washup_token')
)

const handleLoginSuccess = () => {
  isAuthenticated.value = true
}

const handleLogout = () => {
  isAuthenticated.value = false
}
</script>