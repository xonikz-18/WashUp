<template>
  <!-- Public customer tracker route -->
  <CustomerTrackOrder
    v-if="isCustomerTrackRoute"
  />

  <Login
    v-else-if="!isAuthenticated"
    @login-success="handleLoginSuccess"
  />


  <Dashboard
    v-else-if="currentPage === 'Dashboard'"
    :orders="orders"
    @logout="handleLogout"
    @navigate="handleNavigation"
    @view-order="handleViewOrder"
  />


  <Orders
    v-else-if="currentPage === 'Orders'"
    :orders="orders"
    :orders-loading="ordersLoading"
    :orders-error="ordersError"
    @navigate="handleNavigation"
    @logout="handleLogout"
    @view-order="handleViewOrder"
  />


  <AddOrder
    v-else-if="currentPage === 'Add Order'"
    @navigate="handleNavigation"
    @logout="handleLogout"
    @order-created="handleOrderCreated"
  />


  <Services
    v-else-if="currentPage === 'Services & Add-ons'"
    :services="services"
    :addons="addons"
    @navigate="handleNavigation"
    @logout="handleLogout"
    @services-updated="handleServicesUpdated"
    @addons-updated="handleAddonsUpdated"
  />


  <Reports
    v-else-if="currentPage === 'Reports'"
    @navigate="handleNavigation"
    @logout="handleLogout"
  />


  <OrderDetails
    v-else-if="currentPage === 'Order Details'"
    :order-id="selectedOrderId"
    :order="selectedOrder"
    @navigate="handleNavigation"
    @logout="handleLogout"
    @back="handleBackToOrders"
    @order-updated="handleOrderUpdated"
  />

</template>


<script setup>
import {
  computed,
  onMounted,
  ref
} from 'vue'

import Login from './components/Login.vue'
import Dashboard from './components/Dashboard.vue'
import Orders from './components/Orders.vue'
import AddOrder from './components/AddOrder.vue'
import Services from './components/Services.vue'
import Reports from './components/Reports.vue'
import OrderDetails from './components/OrderDetails.vue'
import CustomerTrackOrder from './components/CustomerTrackOrder.vue'


const isAuthenticated = ref(
  !!localStorage.getItem('washup_token')
)

/*
 * /track-order is a public customer page.
 * It must stay outside the admin portal layout.
 */
const isCustomerTrackRoute = ref(
  window.location.pathname === '/track-order'
)

const currentPage = ref('Dashboard')

const selectedOrderId = ref(null)


const orders = ref([])

const ordersLoading = ref(false)

const ordersError = ref('')


const services = ref([])

const addons = ref([])

const optionsLoading = ref(false)


/* =========================
   DATE FORMAT
========================= */

const formatDate = (dateString) => {

  if (!dateString) {
    return '—'
  }

  const date = new Date(dateString)

  if (Number.isNaN(date.getTime())) {
    return '—'
  }

  return date.toLocaleDateString(
    'en-PH',
    {
      day: '2-digit',
      month: 'short',
      year: 'numeric'
    }
  )
}


/* =========================
   NORMALIZE ORDER
========================= */

const normalizeOrder = (order) => {

  if (!order) {
    return null
  }

  return {
    ...order,

    id: order.id,

    orderNumber: order.order_number,

    customer: order.customer_name,

    phone: order.phone_number,

    service: order.service ?? null,

    weight:
      Number(order.weight).toFixed(1),

    total:
      Number(order.total_price).toFixed(2),

    status:
      order.status,

    date:
      formatDate(order.date_created),

    estimatedPickup:
      order.estimated_pickup,

    rawDateCreated:
      order.date_created,

    addons:
      order.addons ?? []
  }
}


/* =========================
   FETCH ORDERS
========================= */

const fetchOrders = async ({
  showLoading = false
} = {}) => {

  const token =
    localStorage.getItem('washup_token')

  if (!token) {
    return
  }

  ordersError.value = ''

  if (
    showLoading &&
    orders.value.length === 0
  ) {
    ordersLoading.value = true
  }


  try {

    const response = await fetch(
      '/api/orders',
      {
        method: 'GET',

        headers: {
          Accept: 'application/json',

          Authorization:
            `Bearer ${token}`
        }
      }
    )


    if (response.status === 401) {

      throw new Error(
        'Your session has expired. Please log in again.'
      )

    }


    if (!response.ok) {

      let message =
        'Failed to load orders.'

      try {

        const errorData =
          await response.json()

        if (errorData.message) {
          message =
            errorData.message
        }

      } catch {}

      throw new Error(message)
    }


    const data =
      await response.json()


    if (!Array.isArray(data)) {

      throw new Error(
        'The server returned an unexpected orders response.'
      )

    }


    orders.value =
      data
        .map(normalizeOrder)
        .filter(Boolean)

  } catch (error) {

    console.error(
      'Failed to fetch orders:',
      error
    )

    ordersError.value =
      error.message ||
      'Unable to load orders. Please try again.'

  } finally {

    ordersLoading.value = false

  }

}


/* =========================
   FETCH SERVICES / ADD-ONS
========================= */

const fetchOrderOptions = async ({
  showLoading = false
} = {}) => {

  const token =
    localStorage.getItem('washup_token')

  if (!token) {
    return
  }


  if (
    showLoading &&
    services.value.length === 0 &&
    addons.value.length === 0
  ) {

    optionsLoading.value = true

  }


  try {

    const [
      servicesResponse,
      addonsResponse
    ] = await Promise.all([

      fetch(
        '/api/services',
        {
          method: 'GET',

          headers: {
            Accept: 'application/json',

            Authorization:
              `Bearer ${token}`
          }
        }
      ),

      fetch(
        '/api/addons',
        {
          method: 'GET',

          headers: {
            Accept: 'application/json',

            Authorization:
              `Bearer ${token}`
          }
        }
      )

    ])


    if (
      servicesResponse.status === 401 ||
      addonsResponse.status === 401
    ) {
      return
    }


    if (
      !servicesResponse.ok ||
      !addonsResponse.ok
    ) {

      throw new Error(
        'Failed to load services and add-ons.'
      )

    }


    const [
      servicesData,
      addonsData
    ] = await Promise.all([

      servicesResponse.json(),

      addonsResponse.json()

    ])


    if (Array.isArray(servicesData)) {
      services.value = servicesData
    }


    if (Array.isArray(addonsData)) {
      addons.value = addonsData
    }


  } catch (error) {

    console.error(
      'Failed to fetch services and add-ons:',
      error
    )

  } finally {

    optionsLoading.value = false

  }

}


/* =========================
   SELECTED ORDER
========================= */

const selectedOrder = computed(() => {

  if (!selectedOrderId.value) {
    return null
  }

  return (
    orders.value.find(
      order =>
        Number(order.id) ===
        Number(selectedOrderId.value)
    ) ?? null
  )

})


/* =========================
   LOGIN
========================= */

const handleLoginSuccess = () => {

  isAuthenticated.value = true

  currentPage.value =
    'Dashboard'

  selectedOrderId.value =
    null


  fetchOrders({
    showLoading: false
  })


  fetchOrderOptions({
    showLoading: false
  })

}


/* =========================
   NAVIGATION
========================= */

const handleNavigation = (page) => {

  currentPage.value = page


  if (page === 'Orders') {

    fetchOrders({
      showLoading: false
    })

  }


  if (page === 'Services & Add-ons') {

    fetchOrderOptions({
      showLoading: false
    })

  }

}


/* =========================
   VIEW ORDER
========================= */

const handleViewOrder = (orderId) => {

  selectedOrderId.value =
    orderId

  currentPage.value =
    'Order Details'

}


/* =========================
   ORDER CREATED
========================= */

const handleOrderCreated = (
  createdOrder
) => {

  if (!createdOrder) {
    return
  }


  const normalizedOrder =
    normalizeOrder(createdOrder)


  if (!normalizedOrder) {
    return
  }


  const existingIndex =
    orders.value.findIndex(
      order =>
        Number(order.id) ===
        Number(normalizedOrder.id)
    )


  if (existingIndex !== -1) {

    orders.value[existingIndex] =
      normalizedOrder

  } else {

    orders.value.unshift(
      normalizedOrder
    )

  }


  fetchOrders({
    showLoading: false
  })

}


/* =========================
   ORDER UPDATED
========================= */

const handleOrderUpdated = (
  updatedOrder
) => {

  if (!updatedOrder) {
    return
  }


  const normalizedOrder =
    normalizeOrder(updatedOrder)


  if (!normalizedOrder) {
    return
  }


  const index =
    orders.value.findIndex(
      order =>
        Number(order.id) ===
        Number(normalizedOrder.id)
    )


  if (index === -1) {

    orders.value.unshift(
      normalizedOrder
    )

    return

  }


  orders.value[index] = {

    ...orders.value[index],

    ...normalizedOrder

  }

}


/* =========================
   SERVICES UPDATED
========================= */

const handleServicesUpdated = (
  updatedServices
) => {

  if (Array.isArray(updatedServices)) {

    services.value =
      updatedServices

  }


  fetchOrderOptions({
    showLoading: false
  })

}


/* =========================
   ADD-ONS UPDATED
========================= */

const handleAddonsUpdated = (
  updatedAddons
) => {

  if (Array.isArray(updatedAddons)) {

    addons.value =
      updatedAddons

  }


  fetchOrderOptions({
    showLoading: false
  })

}


/* =========================
   BACK TO ORDERS
========================= */

const handleBackToOrders = () => {

  selectedOrderId.value =
    null

  currentPage.value =
    'Orders'


  fetchOrders({
    showLoading: false
  })

}


/* =========================
   LOGOUT
========================= */

const handleLogout = async () => {

  const token =
    localStorage.getItem('washup_token')


  try {

    if (token) {

      await fetch(
        '/api/logout',
        {
          method: 'POST',

          headers: {
            Accept: 'application/json',

            Authorization:
              `Bearer ${token}`
          }
        }
      )

    }

  } catch (error) {

    console.error(
      'Logout error:',
      error
    )

  } finally {

    localStorage.removeItem(
      'washup_token'
    )

    localStorage.removeItem(
      'washup_user'
    )


    orders.value = []

    services.value = []

    addons.value = []


    selectedOrderId.value =
      null


    ordersError.value =
      ''

    ordersLoading.value =
      false

    optionsLoading.value =
      false


    isAuthenticated.value =
      false

    currentPage.value =
      'Dashboard'

  }

}


/* =========================
   INITIAL LOAD
========================= */

onMounted(() => {

  if (isAuthenticated.value) {

    fetchOrders({
      showLoading: false
    })


    fetchOrderOptions({
      showLoading: false
    })

  }

})

</script>