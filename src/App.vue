<template>
  <!-- LOGIN -->
  <Login
    v-if="!isAuthenticated"
    @login-success="handleLoginSuccess"
  />

  <!-- DASHBOARD -->
  <Dashboard
    v-else-if="currentPage === 'Dashboard'"
    @logout="handleLogout"
    @navigate="handleNavigation"
  />

  <!-- ORDERS -->
  <Orders
  v-else-if="currentPage === 'Orders'"
  @navigate="handleNavigation"
  @logout="handleLogout"
/>

<!-- ADD ORDER -->
<AddOrder
  v-else-if="currentPage === 'Add Order'"
  @navigate="handleNavigation"
  @logout="handleLogout"
/>
</template>

<script setup>
import { ref } from 'vue'

import Login from './components/Login.vue'
import Dashboard from './components/Dashboard.vue'
import Orders from './components/Orders.vue'
import AddOrder from './components/AddOrder.vue'

const isAuthenticated = ref(
  !!localStorage.getItem('washup_token')
)

const currentPage = ref('Dashboard')

const handleLoginSuccess = () => {
  isAuthenticated.value = true
  currentPage.value = 'Dashboard'
}

const handleLogout = () => {
  isAuthenticated.value = false
  currentPage.value = 'Dashboard'
}

const handleNavigation = (page) => {
  currentPage.value = page
}
</script>